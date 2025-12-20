<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\EditorialWorkflow;
use App\Models\EditorialWorkflowStage;
use App\Models\ArticleEditorialProgress;
use App\Services\EditorialWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EditorialWorkflowController extends Controller
{
    protected EditorialWorkflowService $workflowService;

    public function __construct(EditorialWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    // ========================================
    // WORKFLOW MANAGEMENT
    // ========================================

    /**
     * Display a listing of workflows for a journal
     */
    public function index(Request $request): View
    {
        $journalId = $request->get('journal_id');

        $editorialWorkflows = EditorialWorkflow::when($journalId, function ($query) use ($journalId) {
            return $query->where('journal_id', $journalId);
        })->with(['journal', 'workflowStages'])->paginate(15);

        return view('admin.editorial-workflows.index', compact('editorialWorkflows'));
    }

    /**
     * Show the form for creating a new workflow
     */
    public function create(): View
    {
        $journals = \App\Models\ArticleCategory::journals()->active()->get();

        return view('admin.editorial-workflows.create', compact('journals'));
    }

    /**
     * Store a newly created workflow
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'journal_id' => 'required|exists:article_categories,id',
            'is_active' => 'boolean',
        ]);

        $workflow = EditorialWorkflow::create($validated);

        return redirect()->route('admin.editorial-workflows.show', $workflow)
                        ->with('success', 'Editorial workflow created successfully.');
    }

    /**
     * Display the specified workflow
     */
    public function show(EditorialWorkflow $workflow): View
    {
        $editorialWorkflow = $workflow->load([
            'journal',
            'workflowStages' => function ($query) {
                $query->ordered();
            },
            'articleProgress.article',
            'workflowStages.articleProgress'
        ]);

        $memberTypes = \App\Models\MemberType::pluck('name', 'id')->toArray();

        // Get articles that are not already in any workflow
        $availableArticles = Article::where('journal_id', $workflow->journal_id)
            ->whereDoesntHave('editorialProgress')
            ->get();

        return view('admin.editorial-workflows.show', compact('editorialWorkflow', 'memberTypes', 'availableArticles'));
    }

    /**
     * Show the form for editing the specified workflow
     */
    public function edit(EditorialWorkflow $workflow): View
    {
        $journals = \App\Models\ArticleCategory::journals()->active()->get();
        $memberTypes = \App\Models\MemberType::pluck('name', 'id')->toArray();

        return view('admin.editorial-workflows.edit', compact('workflow', 'journals', 'memberTypes'));
    }

    /**
     * Update the specified workflow
     */
    public function update(Request $request, EditorialWorkflow $workflow): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'journal_id' => 'required|exists:article_categories,id',
            'is_active' => 'boolean',
        ]);

        $workflow->update($validated);

        return redirect()->route('admin.editorial-workflows.show', $workflow)
                        ->with('success', 'Editorial workflow updated successfully.');
    }

    /**
     * Remove the specified workflow
     */
    public function destroy(EditorialWorkflow $workflow): RedirectResponse
    {
        $workflow->delete();

        return redirect()->route('admin.editorial-workflows.index')
                        ->with('success', 'Editorial workflow deleted successfully.');
    }

    /**
     * Assign an article to a workflow
     */
    public function assignArticle(Request $request, EditorialWorkflow $workflow): RedirectResponse
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        $article = Article::findOrFail($validated['article_id']);

        // Check if article is already in a workflow
        if ($article->editorialProgress) {
            return redirect()->back()->with('error', 'Article is already assigned to a workflow.');
        }

        // Check if article belongs to the same journal as the workflow
        if ($article->journal_id !== $workflow->journal_id) {
            return redirect()->back()->with('error', 'Article must belong to the same journal as the workflow.');
        }

        try {
            $this->workflowService->assignWorkflowToArticle($article, $workflow);

            return redirect()->back()->with('success', 'Article assigned to workflow successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign article to workflow: ' . $e->getMessage());
        }
    }

    // ========================================
    // WORKFLOW STAGE MANAGEMENT
    // ========================================

    /**
     * Store a new stage for a workflow
     */
    public function storeStage(Request $request, EditorialWorkflow $workflow): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stage_order' => 'required|integer|min:1',
            'required_roles' => 'array',
            'allowed_actions' => 'array',
            'deadline_days' => 'required|integer|min:1',
        ]);

        $validated['editorial_workflow_id'] = $workflow->id;

        EditorialWorkflowStage::create($validated);

        return redirect()->back()->with('success', 'Stage added successfully.');
    }

    /**
     * Update a workflow stage
     */
    public function updateStage(Request $request, EditorialWorkflowStage $stage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stage_order' => 'required|integer|min:1',
            'required_roles' => 'array',
            'allowed_actions' => 'array',
            'deadline_days' => 'required|integer|min:1',
        ]);

        $stage->update($validated);

        return redirect()->back()->with('success', 'Stage updated successfully.');
    }

    /**
     * Delete a workflow stage
     */
    public function destroyStage(EditorialWorkflowStage $stage): RedirectResponse
    {
        $stage->delete();

        return redirect()->back()->with('success', 'Stage deleted successfully.');
    }

    // ========================================
    // ARTICLE WORKFLOW MANAGEMENT
    // ========================================

    /**
     * Assign workflow to article
     */
    public function assignWorkflow(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'workflow_id' => 'required|exists:editorial_workflows,id',
        ]);

        $workflow = EditorialWorkflow::findOrFail($validated['workflow_id']);

        $progress = $this->workflowService->assignWorkflowToArticle($article, $workflow);

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => 'Workflow assigned to article successfully.'
        ]);
    }

    /**
     * Submit article for review
     */
    public function submitForReview(Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $success = $this->workflowService->submitArticleForReview($article, $member);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Article submitted for review.' : 'Failed to submit article.'
        ]);
    }

    /**
     * Start review process
     */
    public function startReview(Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $success = $this->workflowService->startReviewProcess($article, $member);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Review process started.' : 'Failed to start review process.'
        ]);
    }

    /**
     * Move article to next stage
     */
    public function moveToNextStage(Request $request, Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $success = $this->workflowService->moveToNextStage($article, $member, $validated['comments'] ?? null);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Article moved to next stage.' : 'Failed to move article.'
        ]);
    }

    /**
     * Request revision
     */
    public function requestRevision(Request $request, Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $success = $this->workflowService->requestRevision($article, $member, $validated['comments']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Revision requested.' : 'Failed to request revision.'
        ]);
    }

    /**
     * Approve current stage
     */
    public function approveStage(Request $request, Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $success = $this->workflowService->approveStage($article, $member, $validated['comments'] ?? null);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Stage approved.' : 'Failed to approve stage.'
        ]);
    }

    /**
     * Reject article
     */
    public function rejectArticle(Request $request, Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $success = $this->workflowService->rejectArticle($article, $member, $validated['comments']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Article rejected.' : 'Failed to reject article.'
        ]);
    }

    /**
     * Publish article
     */
    public function publishArticle(Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $success = $this->workflowService->publishArticle($article, $member);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Article published successfully.' : 'Failed to publish article.'
        ]);
    }

    /**
     * Assign reviewers to article
     */
    public function assignReviewers(Request $request, Article $article): JsonResponse
    {
        $member = Auth::guard('member')->user();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'reviewer_ids' => 'required|array|min:1',
            'reviewer_ids.*' => 'exists:members,id',
        ]);

        $success = $this->workflowService->assignReviewers($article, $validated['reviewer_ids'], $member);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Reviewers assigned successfully.' : 'Failed to assign reviewers.'
        ]);
    }

    /**
     * Get workflow statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        $journalId = $request->get('journal_id');

        if (!$journalId) {
            return response()->json(['error' => 'Journal ID required.'], 400);
        }

        $stats = $this->workflowService->getJournalWorkflowStats($journalId);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get overdue articles
     */
    public function getOverdueArticles(): JsonResponse
    {
        $overdueArticles = $this->workflowService->getOverdueArticles();

        return response()->json([
            'success' => true,
            'articles' => $overdueArticles,
        ]);
    }
}
