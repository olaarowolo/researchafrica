<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Services\JournalContextService;
use App\Services\EditorialBoardService;
use App\Services\JournalMembershipService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    protected $journalService;
    protected $editorialBoardService;
    protected $membershipService;

    public function __construct(
        JournalContextService $journalService,
        EditorialBoardService $editorialBoardService,
        JournalMembershipService $membershipService
    ) {
        $this->journalService = $journalService;
        $this->editorialBoardService = $editorialBoardService;
        $this->membershipService = $membershipService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of journals
     */
    public function index(): View
    {
        try {
            $journals = ArticleCategory::where('is_journal', true)
                                     ->with(['journalArticles' => function($query) {
                                         $query->select('id', 'article_category_id', 'article_status');
                                     }])
                                     ->withCount(['journalArticles as articles_count'])
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(15);

            return view('admin.journals.index', compact('journals'));

        } catch (\Exception $e) {
            Log::error('JournalController: Error loading journals index', [
                'error' => $e->getMessage()
            ]);
            return view('admin.journals.index')->with('error', 'Failed to load journals.');
        }
    }

    /**
     * Show the form for creating a new journal
     */
    public function create(): View
    {
        $memberTypes = \App\Models\MemberType::all();
        return view('admin.journals.create', compact('memberTypes'));
    }

    /**
     * Store a newly created journal
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'journal_acronym' => 'required|string|max:10|unique:article_categories,journal_acronym',
                'journal_slug' => 'required|string|max:255|unique:article_categories,journal_slug',
                'description' => 'nullable|string',
                'aim_scope' => 'nullable|string',
                'issn' => 'nullable|string|max:20',
                'online_issn' => 'nullable|string|max:20',
                'doi_link' => 'nullable|url',
                'journal_url' => 'nullable|url',
                'publisher_name' => 'nullable|string|max:255',
                'editor_in_chief' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email',
                'status' => 'required|in:Active,Inactive',
            ]);

            $journal = ArticleCategory::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'journal_acronym' => $validated['journal_acronym'],
                'journal_slug' => $validated['journal_slug'],
                'description' => $validated['description'] ?? null,
                'aim_scope' => $validated['aim_scope'] ?? null,
                'issn' => $validated['issn'] ?? null,
                'online_issn' => $validated['online_issn'] ?? null,
                'doi_link' => $validated['doi_link'] ?? null,
                'journal_url' => $validated['journal_url'] ?? null,
                'publisher_name' => $validated['publisher_name'] ?? null,
                'editor_in_chief' => $validated['editor_in_chief'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'is_journal' => true,
                'status' => $validated['status'],
            ]);

            Log::info('JournalController: Journal created successfully', [
                'journal_id' => $journal->id,
                'journal_acronym' => $journal->journal_acronym,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.journals.show', $journal)
                           ->with('success', 'Journal created successfully!');

        } catch (\Exception $e) {
            Log::error('JournalController: Error creating journal', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to create journal. Please try again.');
        }
    }

    /**
     * Display the specified journal
     */
    public function show(ArticleCategory $journal): View
    {
        try {
            // Load journal with relationships
            $journal->load([
                'journalArticles' => function($query) {
                    $query->with('member')
                          ->orderBy('created_at', 'desc')
                          ->limit(10);
                },
                'editorialBoard' => function($query) {
                    $query->with('member')
                          ->where('is_active', true)
                          ->orderBy('display_order', 'asc');
                },
                'activeMemberships' => function($query) {
                    $query->with(['member', 'memberType'])
                          ->orderBy('created_at', 'desc');
                }
            ]);

            // Get analytics data
            $analytics = [
                'total_articles' => $journal->journalArticles()->count(),
                'published_articles' => $journal->journalArticles()->where('article_status', 3)->count(),
                'pending_articles' => $journal->journalArticles()->where('article_status', 1)->count(),
                'under_review_articles' => $journal->journalArticles()->where('article_status', 2)->count(),
                'total_members' => $journal->activeMemberships()->count(),
                'editorial_board_count' => $journal->editorialBoard()->count(),
            ];

            return view('admin.journals.show', compact('journal', 'analytics'));

        } catch (\Exception $e) {
            Log::error('JournalController: Error loading journal details', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load journal details.');
        }
    }

    /**
     * Show the form for editing the specified journal
     */
    public function edit(ArticleCategory $journal): View
    {
        $memberTypes = \App\Models\MemberType::all();
        return view('admin.journals.edit', compact('journal', 'memberTypes'));
    }

    /**
     * Update the specified journal
     */
    public function update(Request $request, ArticleCategory $journal): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'journal_acronym' => 'required|string|max:10|unique:article_categories,journal_acronym,' . $journal->id,
                'journal_slug' => 'required|string|max:255|unique:article_categories,journal_slug,' . $journal->id,
                'description' => 'nullable|string',
                'aim_scope' => 'nullable|string',
                'issn' => 'nullable|string|max:20',
                'online_issn' => 'nullable|string|max:20',
                'doi_link' => 'nullable|url',
                'journal_url' => 'nullable|url',
                'publisher_name' => 'nullable|string|max:255',
                'editor_in_chief' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email',
                'status' => 'required|in:Active,Inactive',
            ]);

            $journal->update($validated);

            Log::info('JournalController: Journal updated successfully', [
                'journal_id' => $journal->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.journals.show', $journal)
                           ->with('success', 'Journal updated successfully!');
        } catch (\Exception $e) {
            Log::error('JournalController: Error updating journal', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to update journal. Please try again.');
        }
    }

    /**
     * Remove the specified journal
     */
    public function destroy(ArticleCategory $journal): RedirectResponse
    {
        try {
            // Check if journal has articles
            if ($journal->journalArticles()->count() > 0) {
                return back()->with('error', 'Cannot delete journal with existing articles.');
            }

            // Check if journal has editorial board members
            if ($journal->editorialBoard()->where('is_active', true)->count() > 0) {
                return back()->with('error', 'Cannot delete journal with active editorial board members.');
            }

            $journal->delete();

            Log::info('JournalController: Journal deleted successfully', [
                'journal_id' => $journal->id,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('admin.journals.index')
                           ->with('success', 'Journal deleted successfully!');
        } catch (\Exception $e) {
            Log::error('JournalController: Error deleting journal', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to delete journal. Please try again.');
        }
    }

    /**
     * Show journal settings page
     */
    public function settings(ArticleCategory $journal): View
    {
        return view('admin.journals.settings', compact('journal'));
    }

    /**
     * Update journal settings
     */
    public function updateSettings(Request $request, ArticleCategory $journal): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'theme_config' => 'nullable|json',
                'email_settings' => 'nullable|json',
                'submission_settings' => 'nullable|json',
                'subdomain' => 'nullable|string|max:100|unique:article_categories,subdomain,' . $journal->id,
                'custom_domain' => 'nullable|string|max:255|unique:article_categories,custom_domain,' . $journal->id,
            ]);

            $journal->update($validated);

            Log::info('JournalController: Journal settings updated', [
                'journal_id' => $journal->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.journals.settings', $journal)
                           ->with('success', 'Journal settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('JournalController: Error updating journal settings', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to update journal settings. Please try again.');
        }
    }

    /**
     * Show journal analytics
     */
    public function analytics(ArticleCategory $journal): View
    {
        try {
            // Get detailed analytics
            $analytics = [
                'articles_by_status' => $journal->journalArticles()
                                               ->selectRaw('article_status, COUNT(*) as count')
                                               ->groupBy('article_status')
                                               ->pluck('count', 'article_status')
                                               ->toArray(),

                'articles_by_month' => $journal->journalArticles()
                                              ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                              ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                                              ->orderByRaw('year DESC, month DESC')
                                              ->limit(12)
                                              ->get(),

                'membership_by_type' => $journal->activeMemberships()
                                              ->with('memberType')
                                              ->get()
                                              ->groupBy('memberType.name')
                                              ->map->count(),

                'editorial_board_analytics' => $this->editorialBoardService->getBoardAnalytics($journal->id),
            ];

            return view('admin.journals.analytics', compact('journal', 'analytics'));
        } catch (\Exception $e) {
            Log::error('JournalController: Error loading journal analytics', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load journal analytics.');
        }
    }
}
