<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\JournalContextService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of articles for the current journal
     */
    public function index(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $user = Auth::user();
            $query = Article::where('journal_id', $currentJournal->id);

            // Role-based filtering
            if ($user->hasRole('author') && !$user->hasRole('editor') && !$user->hasRole('admin')) {
                $query->where('member_id', $user->id);
            }

            $articles = $query->with(['member', 'article_category', 'journal_category'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

            return view('journal.articles.index', compact('articles', 'currentJournal'));
        } catch (\Exception $e) {
            Log::error('ArticleController: Error loading articles index', [
                'error' => $e->getMessage()
            ]);
            return view('journal.articles.index')->with('error', 'Failed to load articles.');
        }
    }

    /**
     * Show the form for creating a new article
     */
    public function create(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            // Check if user has permission to submit articles
            if (!Auth::user()->hasJournalAccess($currentJournal->id, 1)) { // 1 = Author role
                abort(403, 'You do not have permission to submit articles to this journal.');
            }

            // Get article categories for this journal
            $categories = ArticleCategory::where('journal_id', $currentJournal->id)
                                       ->orWhere('is_journal', false)
                                       ->orderBy('name')
                                       ->get();

            return view('journal.articles.create', compact('currentJournal', 'categories'));
        } catch (\Exception $e) {
            Log::error('ArticleController: Error loading create form', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load article creation form.');
        }
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'article_category_id' => 'required|exists:article_categories,id',
                'article_sub_category_id' => 'nullable|exists:article_categories,id',
                'author_name' => 'required|string|max:255',
                'other_authors' => 'nullable|string',
                'corresponding_authors' => 'nullable|string',
                'institute_organization' => 'nullable|string',
                'doi_link' => 'nullable|url',
                'volume' => 'nullable|string|max:50',
                'issue_no' => 'nullable|string|max:50',
                'publish_date' => 'nullable|date',
                'access_type' => 'required|in:1,2', // 1=Open Access, 2=Close Access
                'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            ]);

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('articles', $fileName, 'public');
            }

            $article = Article::create([
                'journal_id' => $currentJournal->id,
                'member_id' => Auth::id(),
                'title' => $validated['title'],
                'article_category_id' => $validated['article_category_id'],
                'article_sub_category_id' => $validated['article_sub_category_id'] ?? null,
                'author_name' => $validated['author_name'],
                'other_authors' => $validated['other_authors'],
                'corresponding_authors' => $validated['corresponding_authors'],
                'institute_organization' => $validated['institute_organization'],
                'doi_link' => $validated['doi_link'],
                'volume' => $validated['volume'],
                'issue_no' => $validated['issue_no'],
                'publish_date' => $validated['publish_date'],
                'access_type' => $validated['access_type'],
                'article_status' => 1, // Pending
                'storage_disk' => 'public',
                'file_path' => $filePath,
            ]);

            Log::info('ArticleController: Article created', [
                'article_id' => $article->id,
                'journal_id' => $currentJournal->id,
                'member_id' => Auth::id(),
                'title' => $article->title
            ]);

            return redirect()->route('journal.articles.show', $article)
                           ->with('success', 'Article submitted successfully!');
        } catch (\Exception $e) {
            Log::error('ArticleController: Error creating article', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to submit article. Please try again.');
        }
    }

    /**
     * Display the specified article
     */
    public function show(Article $article): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            // Check access permissions
            $user = Auth::user();

            // Public access for published articles
            if ($article->article_status === 3) {
                $article->load(['member', 'article_category', 'journal_category']);
                return view('journal.articles.show', compact('article', 'currentJournal'));
            }

            // Private access for non-published articles
            if (!$user) {
                abort(403, 'You do not have access to this article.');
            }

            // Check if user is the author or has editorial access
            if ($article->member_id !== $user->id &&
                !$user->hasJournalAccess($currentJournal->id, [2, 3, 4])) { // Editor, Reviewer, Admin
                abort(403, 'You do not have access to this article.');
            }

            $article->load(['member', 'article_category', 'journal_category', 'comments']);

            return view('journal.articles.show', compact('article', 'currentJournal'));
        } catch (\Exception $e) {
            Log::error('ArticleController: Error loading article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load article.');
        }
    }

    /**
     * Show the form for editing the specified article
     */
    public function edit(Article $article): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            // Check if user can edit this article
            $user = Auth::user();

            if ($article->member_id !== $user->id &&
                !$user->hasJournalAccess($currentJournal->id, [2, 4])) { // Editor, Admin
                abort(403, 'You do not have permission to edit this article.');
            }

            // Don't allow editing of published articles
            if ($article->article_status === 3) {
                return back()->with('error', 'Published articles cannot be edited.');
            }

            $categories = ArticleCategory::where('journal_id', $currentJournal->id)
                                       ->orWhere('is_journal', false)
                                       ->orderBy('name')
                                       ->get();

            return view('journal.articles.edit', compact('article', 'currentJournal', 'categories'));
        } catch (\Exception $e) {
            Log::error('ArticleController: Error loading edit form', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load article edit form.');
        }
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            $user = Auth::user();

            if ($article->member_id !== $user->id &&
                !$user->hasJournalAccess($currentJournal->id, [2, 4])) { // Editor, Admin
                abort(403, 'You do not have permission to edit this article.');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'article_category_id' => 'required|exists:article_categories,id',
                'article_sub_category_id' => 'nullable|exists:article_categories,id',
                'author_name' => 'required|string|max:255',
                'other_authors' => 'nullable|string',
                'corresponding_authors' => 'nullable|string',
                'institute_organization' => 'nullable|string',
                'doi_link' => 'nullable|url',
                'volume' => 'nullable|string|max:50',
                'issue_no' => 'nullable|string|max:50',
                'publish_date' => 'nullable|date',
                'access_type' => 'required|in:1,2',
                'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file
                if ($article->file_path && Storage::disk($article->storage_disk)->exists($article->file_path)) {
                    Storage::disk($article->storage_disk)->delete($article->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('articles', $fileName, 'public');

                $validated['file_path'] = $filePath;
            }

            $article->update($validated);

            Log::info('ArticleController: Article updated', [
                'article_id' => $article->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('journal.articles.show', $article)
                           ->with('success', 'Article updated successfully!');
        } catch (\Exception $e) {
            Log::error('ArticleController: Error updating article', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to update article. Please try again.');
        }
    }

    /**
     * Review an article (for editors and reviewers)
     */
    public function review(Article $article): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            $user = Auth::user();

            if (!$user->hasJournalAccess($currentJournal->id, [2, 3])) { // Editor, Reviewer
                abort(403, 'You do not have permission to review articles.');
            }

            if ($article->article_status !== 1) { // Only pending articles can be reviewed
                return back()->with('error', 'Only pending articles can be reviewed.');
            }

            $article->update(['article_status' => 2]); // Set to reviewing

            Log::info('ArticleController: Article set to reviewing', [
                'article_id' => $article->id,
                'reviewed_by' => Auth::id()
            ]);

            return back()->with('success', 'Article set to reviewing status.');
        } catch (\Exception $e) {
            Log::error('ArticleController: Error reviewing article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to review article. Please try again.');
        }
    }

    /**
     * Approve an article
     */
    public function approve(Article $article): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            $user = Auth::user();

            if (!$user->hasJournalAccess($currentJournal->id, [2])) { // Editor only
                abort(403, 'You do not have permission to approve articles.');
            }

            if (!in_array($article->article_status, [1, 2])) { // Pending or Reviewing
                return back()->with('error', 'Only pending or reviewing articles can be approved.');
            }

            $article->update([
                'article_status' => 3, // Published
                'published_online' => now()
            ]);

            Log::info('ArticleController: Article approved and published', [
                'article_id' => $article->id,
                'approved_by' => Auth::id()
            ]);

            return back()->with('success', 'Article approved and published successfully!');
        } catch (\Exception $e) {
            Log::error('ArticleController: Error approving article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to approve article. Please try again.');
        }
    }

    /**
     * Reject an article
     */
    public function reject(Article $article): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            $user = Auth::user();

            if (!$user->hasJournalAccess($currentJournal->id, [2])) { // Editor only
                abort(403, 'You do not have permission to reject articles.');
            }

            if (!in_array($article->article_status, [1, 2])) { // Pending or Reviewing
                return back()->with('error', 'Only pending or reviewing articles can be rejected.');
            }

            $article->update(['article_status' => 4]); // Rejected

            Log::info('ArticleController: Article rejected', [
                'article_id' => $article->id,
                'rejected_by' => Auth::id()
            ]);

            return back()->with('success', 'Article rejected successfully.');
        } catch (\Exception $e) {
            Log::error('ArticleController: Error rejecting article', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to reject article. Please try again.');
        }
    }

    /**
     * Download article file
     */
    public function download(Article $article): RedirectResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal || $article->journal_id !== $currentJournal->id) {
                abort(404, 'Article not found');
            }

            // Check access permissions
            $user = Auth::user();

            // Published articles are publicly accessible
            if ($article->article_status === 3) {
                // Allow download
            } elseif (!$user) {
                abort(403, 'You must be logged in to access this article.');
            } elseif ($article->member_id !== $user->id &&
                      !$user->hasJournalAccess($currentJournal->id, [2, 3, 4])) { // Editor, Reviewer, Admin
                abort(403, 'You do not have access to this article.');
            }

            if (!$article->file_path || !Storage::disk($article->storage_disk)->exists($article->file_path)) {
                return back()->with('error', 'Article file not found.');
            }

            return Storage::disk($article->storage_disk)->download($article->file_path);
        } catch (\Exception $e) {
            Log::error('ArticleController: Error downloading article file', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to download article file.');
        }
    }

    /**
     * Get article statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                return response()->json(['error' => 'Journal not found'], 404);
            }

            $user = Auth::user();
            $query = Article::where('journal_id', $currentJournal->id);

            // Role-based filtering
            if ($user->hasRole('author') && !$user->hasRole('editor') && !$user->hasRole('admin')) {
                $query->where('member_id', $user->id);
            }

            $stats = [
                'total' => $query->count(),
                'pending' => $query->where('article_status', 1)->count(),
                'reviewing' => $query->where('article_status', 2)->count(),
                'published' => $query->where('article_status', 3)->count(),
                'rejected' => $query->where('article_status', 4)->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('ArticleController: Error getting statistics', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }
}
