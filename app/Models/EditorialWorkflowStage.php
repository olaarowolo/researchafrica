<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditorialWorkflowStage extends Model
{
    use HasFactory;

    protected $table = 'editorial_workflow_stages';

    protected $fillable = [
        'editorial_workflow_id',
        'name',
        'description',
        'order',
        'required_roles',
        'allowed_actions',
        'deadline_days',
        'is_mandatory',
        'requires_consensus',
        'min_reviewers',
        'max_reviewers',
        'stage_config',
    ];

    protected $casts = [
        'required_roles' => 'array',
        'allowed_actions' => 'array',
        'is_mandatory' => 'boolean',
        'requires_consensus' => 'boolean',
        'stage_config' => 'array',
    ];

    /**
     * Get the workflow that owns this stage
     */
    public function editorialWorkflow(): BelongsTo
    {
        return $this->belongsTo(EditorialWorkflow::class);
    }

    /**
     * Get articles currently in this stage
     */
    public function articleProgress(): HasMany
    {
        return $this->hasMany(ArticleEditorialProgress::class, 'current_stage_id');
    }

    /**
     * Scope to order stages by their order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to filter by workflow
     */
    public function scopeForWorkflow($query, $workflowId)
    {
        return $query->where('editorial_workflow_id', $workflowId);
    }

    /**
     * Check if stage is mandatory
     */
    public function isMandatory(): bool
    {
        return $this->is_mandatory;
    }

    /**
     * Check if stage requires consensus
     */
    public function requiresConsensus(): bool
    {
        return $this->requires_consensus;
    }

    /**
     * Get required roles for this stage
     */
    public function getRequiredRoles(): array
    {
        return $this->required_roles ?? [];
    }

    /**
     * Get allowed actions for this stage
     */
    public function getAllowedActions(): array
    {
        return $this->allowed_actions ?? [];
    }

    /**
     * Check if an action is allowed in this stage
     */
    public function allowsAction(string $action): bool
    {
        return in_array($action, $this->getAllowedActions());
    }

    /**
     * Get stage configuration
     */
    public function getStageConfig(): array
    {
        return $this->stage_config ?? [];
    }

    /**
     * Get the next stage in the workflow
     */
    public function getNextStage()
    {
        return static::where('editorial_workflow_id', $this->editorial_workflow_id)
                    ->where('order', '>', $this->order)
                    ->orderBy('order')
                    ->first();
    }

    /**
     * Get the previous stage in the workflow
     */
    public function getPreviousStage()
    {
        return static::where('editorial_workflow_id', $this->editorial_workflow_id)
                    ->where('order', '<', $this->order)
                    ->orderBy('order', 'desc')
                    ->first();
    }
}