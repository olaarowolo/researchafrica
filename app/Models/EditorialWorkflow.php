<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditorialWorkflow extends Model
{
    use HasFactory;

    protected $table = 'editorial_workflows';

    protected $fillable = [
        'name',
        'description',
        'journal_id',
        'stages',
        'is_active',
        'review_deadline_days',
        'max_review_rounds',
        'required_roles',
    ];

    protected $casts = [
        'stages' => 'array',
        'is_active' => 'boolean',
        'required_roles' => 'array',
    ];

    /**
     * Get the journal that owns this workflow
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'journal_id');
    }

    /**
     * Get the stages for this workflow
     */
    public function workflowStages(): HasMany
    {
        return $this->hasMany(EditorialWorkflowStage::class)->orderBy('order');
    }

    /**
     * Get the articles using this workflow
     */
    public function articleProgress(): HasMany
    {
        return $this->hasMany(ArticleEditorialProgress::class);
    }

    /**
     * Scope to only include active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by journal
     */
    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    /**
     * Get the default workflow for a journal
     */
    public static function getDefaultForJournal($journalId)
    {
        return static::where('journal_id', $journalId)
                    ->active()
                    ->first();
    }

    /**
     * Check if workflow is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get workflow stages as array
     */
    public function getStages(): array
    {
        return $this->stages ?? [];
    }

    /**
     * Get required roles for the workflow
     */
    public function getRequiredRoles(): array
    {
        return $this->required_roles ?? [];
    }
}