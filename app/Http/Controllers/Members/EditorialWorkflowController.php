<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleEditorialProgress;
use App\Services\EditorialWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class EditorialWorkflowController extends Controller
{
    protected EditorialWorkflowService $workflowService;

    public function __construct(EditorialWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Show editorial workflow dashboard for the member
     */
    public function dashboard(): View
    {
        $member = Auth::guard('member')->user();

        // Get articles in workflow for this member
        $myArticles = Article::where('member_id', $member->id)
            ->whereHas('editorialProgress')
            ->with(['editorialProgress.currentStage', 'editorialProgress.editorialWorkflow'])
            ->get();

        // Get articles assigned to this member for review/editing
        $assignedArticles = ArticleEditorialProgress::whereHas('article', function ($query) use ($member) {
            // Articles where this member has been assigned as reviewer/editor
            // This would need to be implemented based on your assignment logic
            $query->where('member_id', '!=', $member->id); // Exclude own articles
        })
        ->where('status', 'under_review')
        ->whereHas('currentStage', function ($query) use ($member) {
            // Check if member can perform actions on current stage
            $query->where(function ($q) use ($member) {
                // This is a simplified check - you might need more complex logic
                $q->whereJsonContains('required_roles', (string)$member->member_type_id)
                  ->orWhere('required_roles', 'like', '%"' . $member->member_type_id . '"%');
            });
        })
        ->with(['article', 'currentStage', 'editorialWorkflow'])
        ->get();

        // Statistics
        $stats = [
            'my_articles_count' => $myArticles->count(),
            'assigned_articles_count' => $assignedArticles->count(),
            'published_count' => $myArticles->where('editorialProgress.status', 'published')->count(),
            'under_review_count' => $myArticles->where('editorialProgress.status', 'under_review')->count(),
            'draft_count' => $myArticles->where('editorialProgress.status', 'draft')->count(),
        ];

        return view('member.editorial-workflows.dashboard', compact('myArticles', 'assignedArticles', 'stats'));
    }

    /**
     * Show articles owned by the member in editorial workflows
     */
    public function myArticles(): View
    {
        $member = Auth::guard('member')->user();

        $articles = Article::where('member_id', $member->id)
            ->whereHas('editorialProgress')
            ->with(['editorialProgress.currentStage', 'editorialProgress.editorialWorkflow', 'article_category'])
            ->paginate(10);

        return view('member.editorial-workflows.my-articles', compact('articles'));
    }

    /**
     * Show articles assigned to the member for review/editing
     */
    public function assignedArticles(): View
    {
        $member = Auth::guard('member')->user();

        // This is a simplified query - you might need to implement proper assignment tracking
        $assignedArticles = ArticleEditorialProgress::whereHas('article', function ($query) use ($member) {
            $query->where('member_id', '!=', $member->id);
        })
        ->where('status', 'under_review')
        ->whereHas('currentStage', function ($query) use ($member) {
            $query->where(function ($q) use ($member) {
                $q->whereJsonContains('required_roles', (string)$member->member_type_id)
                  ->orWhere('required_roles', 'like', '%"' . $member->member_type_id . '"%');
            });
        })
        ->with(['article', 'currentStage', 'editorialWorkflow'])
        ->paginate(10);

        return view('member.editorial-workflows.assigned-articles', compact('assignedArticles'));
    }

    /**
     * Submit article for review in editorial workflow
     */
    public function submitForReview(Article $article): RedirectResponse
    {
        $member = Auth::guard('member')->user();

        // Check if member owns the article
        if ($article->member_id !== $member->id) {
            return redirect()->back()->with('error', 'You can only submit your own articles.');
        }

        // Check if article has editorial progress
        if (!$article->editorialProgress) {
            return redirect()->back()->with('error', 'This article is not assigned to an editorial workflow.');
        }

        try {
            $this->workflowService->submitArticleForReview($article, $member->id);
            return redirect()->back()->with('success', 'Article submitted for review successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit article: ' . $e->getMessage());
        }
    }

    /**
     * Request revision for an article
     */
    public function requestRevision(Request $request, Article $article): RedirectResponse
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'comments' => 'required|string|max:1000',
        ]);

        // Check if member can perform this action
        if (!$this->workflowService->canPerformAction($article, $member, 'request_revision')) {
            return redirect()->back()->with('error', 'You are not authorized to request revision for this article.');
        }

        try {
            $this->workflowService->requestRevision($article, $member->id, $validated['comments']);
            return redirect()->back()->with('success', 'Revision requested successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to request revision: ' . $e->getMessage());
        }
    }

    /**
     * Approve current stage
     */
    public function approveStage(Request $request, Article $article): RedirectResponse
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        // Check if member can perform this action
        if (!$this->workflowService->canPerformAction($article, $member, 'approve')) {
            return redirect()->back()->with('error', 'You are not authorized to approve this article.');
        }

        try {
            $this->workflowService->approveStage($article, $member->id, $validated['comments'] ?? null);
            return redirect()->back()->with('success', 'Article approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve article: ' . $e->getMessage());
        }
    }

    /**
     * Reject current stage
     */
    public function rejectStage(Request $request, Article $article): RedirectResponse
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'comments' => 'required|string|max:1000',
        ]);

        // Check if member can perform this action
        if (!$this->workflowService->canPerformAction($article, $member, 'reject')) {
            return redirect()->back()->with('error', 'You are not authorized to reject this article.');
        }

        try {
            $this->workflowService->rejectArticle($article, $member->id, $validated['comments']);
            return redirect()->back()->with('success', 'Article rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject article: ' . $e->getMessage());
        }
    }
}