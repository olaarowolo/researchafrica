<?php

namespace App\Services;

use App\Models\Article;
use App\Models\EditorialWorkflow;
use App\Models\EditorialWorkflowStage;
use App\Models\ArticleEditorialProgress;
use App\Models\JournalMembership;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditorialWorkflowService
{
    /**
     * Assign a workflow to an article
     *
     * @param Article $article
     * @param EditorialWorkflow $workflow
     * @return ArticleEditorialProgress
     */
    public function assignWorkflowToArticle(Article $article, EditorialWorkflow $workflow): ArticleEditorialProgress
    {
        DB::transaction(function () use ($article, $workflow) {
            // Create or update progress record
            $progress = ArticleEditorialProgress::updateOrCreate(
                [
                    'article_id' => $article->id,
                    'editorial_workflow_id' => $workflow->id,
                ],
                [
                    'status' => ArticleEditorialProgress::STATUS_DRAFT,
                    'current_stage_id' => null,
                    'is_active' => true,
                ]
            );

            // Update article to reference the workflow
            $article->update(['editorial_workflow_id' => $workflow->id]);

            Log::info("Workflow {$workflow->id} assigned to article {$article->id}");
        });

        return $article->editorialProgress;
    }

    /**
     * Submit an article for review
     *
     * @param Article $article
     * @param Member $submittedBy
     * @return bool
     */
    public function submitArticleForReview(Article $article, Member $submittedBy): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || $progress->status !== ArticleEditorialProgress::STATUS_DRAFT) {
            return false;
        }

        DB::transaction(function () use ($progress, $submittedBy) {
            $progress->submitForReview();

            Log::info("Article {$progress->article_id} submitted for review by member {$submittedBy->id}");
        });

        return true;
    }

    /**
     * Start the review process for an article
     *
     * @param Article $article
     * @param Member $startedBy
     * @return bool
     */
    public function startReviewProcess(Article $article, Member $startedBy): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || $progress->status !== ArticleEditorialProgress::STATUS_SUBMITTED) {
            return false;
        }

        DB::transaction(function () use ($progress, $startedBy) {
            // Get the first stage of the workflow
            $firstStage = $progress->editorialWorkflow->workflowStages()->ordered()->first();

            if ($firstStage) {
                $progress->current_stage_id = $firstStage->id;
                $progress->startReview();
            }

            Log::info("Review process started for article {$progress->article_id} by member {$startedBy->id}");
        });

        return true;
    }

    /**
     * Move article to next stage
     *
     * @param Article $article
     * @param Member $movedBy
     * @param string|null $comments
     * @return bool
     */
    public function moveToNextStage(Article $article, Member $movedBy, ?string $comments = null): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || !$progress->currentStage) {
            return false;
        }

        return $progress->moveToNextStage($movedBy->id, $comments);
    }

    /**
     * Move article to previous stage (revision requested)
     *
     * @param Article $article
     * @param Member $movedBy
     * @param string|null $comments
     * @return bool
     */
    public function requestRevision(Article $article, Member $movedBy, ?string $comments = null): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress) {
            return false;
        }

        DB::transaction(function () use ($progress, $movedBy, $comments) {
            $progress->requestRevision($movedBy->id, $comments);

            Log::info("Revision requested for article {$progress->article_id} by member {$movedBy->id}");
        });

        return true;
    }

    /**
     * Approve current stage
     *
     * @param Article $article
     * @param Member $approvedBy
     * @param string|null $comments
     * @return bool
     */
    public function approveStage(Article $article, Member $approvedBy, ?string $comments = null): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress) {
            return false;
        }

        return $progress->approveStage($approvedBy->id, $comments);
    }

    /**
     * Reject article
     *
     * @param Article $article
     * @param Member $rejectedBy
     * @param string|null $comments
     * @return bool
     */
    public function rejectArticle(Article $article, Member $rejectedBy, ?string $comments = null): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress) {
            return false;
        }

        DB::transaction(function () use ($progress, $rejectedBy, $comments) {
            $progress->rejectStage($rejectedBy->id, $comments);

            Log::info("Article {$progress->article_id} rejected by member {$rejectedBy->id}");
        });

        return true;
    }

    /**
     * Publish article
     *
     * @param Article $article
     * @param Member $publishedBy
     * @return bool
     */
    public function publishArticle(Article $article, Member $publishedBy): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || $progress->status !== ArticleEditorialProgress::STATUS_APPROVED) {
            return false;
        }

        DB::transaction(function () use ($article, $progress, $publishedBy) {
            $progress->status = ArticleEditorialProgress::STATUS_PUBLISHED;
            $progress->save();

            // Update article status to published
            $article->update(['article_status' => 3]); // Published status

            Log::info("Article {$article->id} published by member {$publishedBy->id}");
        });

        return true;
    }

    /**
     * Assign reviewers to current stage
     *
     * @param Article $article
     * @param array $reviewerIds
     * @param Member $assignedBy
     * @return bool
     */
    public function assignReviewers(Article $article, array $reviewerIds, Member $assignedBy): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || !$progress->currentStage) {
            return false;
        }

        $currentStage = $progress->currentStage;

        // Validate reviewer count
        if (count($reviewerIds) < $currentStage->min_reviewers ||
            ($currentStage->max_reviewers && count($reviewerIds) > $currentStage->max_reviewers)) {
            return false;
        }

        // Validate reviewers have required roles for this journal
        $journalId = $progress->editorialWorkflow->journal_id;
        foreach ($reviewerIds as $reviewerId) {
            $hasAccess = JournalMembership::where('journal_id', $journalId)
                ->where('member_id', $reviewerId)
                ->where('status', JournalMembership::STATUS_ACTIVE)
                ->whereIn('member_type_id', $currentStage->getRequiredRoles())
                ->exists();

            if (!$hasAccess) {
                return false;
            }
        }

        DB::transaction(function () use ($progress, $reviewerIds, $assignedBy) {
            $assignments = $progress->getReviewAssignments();
            $assignments[$progress->current_stage_id] = [
                'reviewers' => $reviewerIds,
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now()->toISOString(),
            ];
            $progress->review_assignments = $assignments;
            $progress->save();

            Log::info("Reviewers assigned to article {$progress->article_id} stage {$progress->current_stage_id}");
        });

        return true;
    }

    /**
     * Get articles that are overdue
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueArticles()
    {
        return ArticleEditorialProgress::with(['article', 'currentStage', 'editorialWorkflow.journal'])
            ->overdue()
            ->get();
    }

    /**
     * Get workflow statistics for a journal
     *
     * @param int $journalId
     * @return array
     */
    public function getJournalWorkflowStats(int $journalId): array
    {
        $stats = ArticleEditorialProgress::whereHas('editorialWorkflow', function ($query) use ($journalId) {
            $query->where('journal_id', $journalId);
        })->selectRaw('status, COUNT(*) as count')
          ->groupBy('status')
          ->pluck('count', 'status')
          ->toArray();

        return [
            'draft' => $stats[ArticleEditorialProgress::STATUS_DRAFT] ?? 0,
            'submitted' => $stats[ArticleEditorialProgress::STATUS_SUBMITTED] ?? 0,
            'under_review' => $stats[ArticleEditorialProgress::STATUS_UNDER_REVIEW] ?? 0,
            'revision_requested' => $stats[ArticleEditorialProgress::STATUS_REVISION_REQUESTED] ?? 0,
            'approved' => $stats[ArticleEditorialProgress::STATUS_APPROVED] ?? 0,
            'rejected' => $stats[ArticleEditorialProgress::STATUS_REJECTED] ?? 0,
            'published' => $stats[ArticleEditorialProgress::STATUS_PUBLISHED] ?? 0,
        ];
    }

    /**
     * Check if member can perform action on article
     *
     * @param Article $article
     * @param Member $member
     * @param string $action
     * @return bool
     */
    public function canPerformAction(Article $article, Member $member, string $action): bool
    {
        $progress = $article->editorialProgress;

        if (!$progress || !$progress->currentStage) {
            return false;
        }

        $currentStage = $progress->currentStage;

        // Check if action is allowed in current stage
        if (!$currentStage->allowsAction($action)) {
            return false;
        }

        // Check if member has required role for this journal
        $journalId = $progress->editorialWorkflow->journal_id;
        return JournalMembership::where('journal_id', $journalId)
            ->where('member_id', $member->id)
            ->where('status', JournalMembership::STATUS_ACTIVE)
            ->whereIn('member_type_id', $currentStage->getRequiredRoles())
            ->exists();
    }
}