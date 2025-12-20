<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ArticleEditorialProgress extends Model
{
    use HasFactory;

    protected $table = 'article_editorial_progress';

    protected $fillable = [
        'article_id',
        'editorial_workflow_id',
        'current_stage_id',
        'status',
        'submitted_at',
        'current_stage_started_at',
        'current_stage_deadline',
        'current_round',
        'max_rounds_reached',
        'stage_history',
        'review_assignments',
        'current_comments',
        'is_active',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'current_stage_started_at' => 'datetime',
        'current_stage_deadline' => 'datetime',
        'stage_history' => 'array',
        'review_assignments' => 'array',
        'is_active' => 'boolean',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';

    /**
     * Get the article this progress belongs to
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the workflow this progress is following
     */
    public function editorialWorkflow(): BelongsTo
    {
        return $this->belongsTo(EditorialWorkflow::class);
    }

    /**
     * Get the current stage
     */
    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(EditorialWorkflowStage::class, 'current_stage_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by active progress
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by overdue items
     */
    public function scopeOverdue($query)
    {
        return $query->where('current_stage_deadline', '<', now())
                    ->where('status', self::STATUS_UNDER_REVIEW);
    }

    /**
     * Check if the progress is overdue
     */
    public function isOverdue(): bool
    {
        return $this->current_stage_deadline && $this->current_stage_deadline->isPast()
               && $this->status === self::STATUS_UNDER_REVIEW;
    }

    /**
     * Get stage history
     */
    public function getStageHistory(): array
    {
        return $this->stage_history ?? [];
    }

    /**
     * Get review assignments
     */
    public function getReviewAssignments(): array
    {
        return $this->review_assignments ?? [];
    }

    /**
     * Add a stage transition to history
     */
    public function addStageTransition($stageId, $action, $comments = null, $userId = null)
    {
        $history = $this->getStageHistory();
        $history[] = [
            'stage_id' => $stageId,
            'action' => $action,
            'comments' => $comments,
            'user_id' => $userId,
            'timestamp' => now()->toISOString(),
        ];

        $this->stage_history = $history;
        $this->save();
    }

    /**
     * Move to next stage
     */
    public function moveToNextStage($userId = null, $comments = null)
    {
        $nextStage = $this->currentStage->getNextStage();

        if ($nextStage) {
            // Note: History recording is handled by the calling method (e.g., approveStage)
            $this->current_stage_id = $nextStage->id;
            $this->current_stage_started_at = now();
            $this->current_stage_deadline = now()->addDays($nextStage->deadline_days);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Move to previous stage
     */
    public function moveToPreviousStage($userId = null, $comments = null)
    {
        $previousStage = $this->currentStage->getPreviousStage();

        if ($previousStage) {
            $this->addStageTransition($this->current_stage_id, 'moved_to_previous', $comments, $userId);
            $this->current_stage_id = $previousStage->id;
            $this->current_stage_started_at = now();
            $this->current_stage_deadline = now()->addDays($previousStage->deadline_days);
            $this->current_round += 1;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Approve current stage
     */
    public function approveStage($userId = null, $comments = null)
    {
        $this->addStageTransition($this->current_stage_id, 'approved', $comments, $userId);

        if ($this->moveToNextStage($userId)) { // Don't pass comments again
            return true;
        } else {
            // No more stages, mark as approved
            $this->status = self::STATUS_APPROVED;
            $this->save();
            return true;
        }
    }

    /**
     * Reject current stage
     */
    public function rejectStage($userId = null, $comments = null)
    {
        $this->addStageTransition($this->current_stage_id, 'rejected', $comments, $userId);
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }

    /**
     * Request revision
     */
    public function requestRevision($userId = null, $comments = null)
    {
        $this->addStageTransition($this->current_stage_id, 'revision_requested', $comments, $userId);
        $this->status = self::STATUS_REVISION_REQUESTED;
        $this->current_comments = $comments;
        $this->save();
    }

    /**
     * Submit for review
     */
    public function submitForReview()
    {
        $this->status = self::STATUS_SUBMITTED;
        $this->submitted_at = now();
        $this->save();
    }

    /**
     * Start review process
     */
    public function startReview()
    {
        $this->status = self::STATUS_UNDER_REVIEW;
        $this->current_stage_started_at = now();
        if ($this->currentStage) {
            $this->current_stage_deadline = now()->addDays($this->currentStage->deadline_days);
        }
        $this->save();
    }

    /**
     * Check if can transition to a specific status
     */
    public function canTransitionTo(string $status): bool
    {
        $allowedTransitions = [
            self::STATUS_DRAFT => [self::STATUS_SUBMITTED],
            self::STATUS_SUBMITTED => [self::STATUS_UNDER_REVIEW, self::STATUS_DRAFT],
            self::STATUS_UNDER_REVIEW => [self::STATUS_REVISION_REQUESTED, self::STATUS_APPROVED, self::STATUS_REJECTED],
            self::STATUS_REVISION_REQUESTED => [self::STATUS_SUBMITTED, self::STATUS_DRAFT],
            self::STATUS_APPROVED => [self::STATUS_PUBLISHED],
            self::STATUS_REJECTED => [], // Terminal state
            self::STATUS_PUBLISHED => [], // Terminal state
        ];

        return in_array($status, $allowedTransitions[$this->status] ?? []);
    }
}