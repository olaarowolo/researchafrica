<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\JournalContextService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PublicJournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
        $this->middleware('set.journal.context');
    }

    /**
     * Display the journal homepage
     */
    public function index(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            // Get recent published articles
            $recentArticles = $journal->publishedArticles()
                                     ->with(['member', 'article_category'])
                                     ->orderBy('published_online', 'desc')
                                     ->limit(6)
                                     ->get();

            // Get journal statistics
            $stats = [
                'total_articles' => $journal->journalArticles()->count(),
                'published_articles' => $journal->publishedArticles()->count(),
                'editorial_board_count' => $journal->editorialBoard()->count(),
                'total_views' => $this->getTotalViews($journal),
            ];

            return view('public.journal.index', compact('journal', 'recentArticles', 'stats'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading journal index', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load journal homepage');
        }
    }

    /**
     * Display the journal about page
     */
    public function about(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            return view('public.journal.about', compact('journal'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading about page', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load about page');
        }
    }

    /**
     * Display the editorial board page
     */
    public function editorialBoard(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            $editorialBoard = $journal->editorialBoard()
                                     ->with('member')
                                     ->orderBy('display_order', 'asc')
                                     ->get()
                                     ->groupBy('position');

            return view('public.journal.editorial-board', compact('journal', 'editorialBoard'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading editorial board', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load editorial board');
        }
    }

    /**
     * Display submission guidelines
     */
    public function submissionGuidelines(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            // Get submission settings if available
            $submissionSettings = null;
            if ($journal->submission_settings) {
                $submissionSettings = json_decode($journal->submission_settings, true);
            }

            return view('public.journal.submission-guidelines', compact('journal', 'submissionSettings'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading submission guidelines', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load submission guidelines');
        }
    }

    /**
     * Display all published articles
     */
    public function articles(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            // Get published articles with pagination
            $articles = $journal->publishedArticles()
                              ->with(['member', 'article_category'])
                              ->orderBy('published_online', 'desc')
                              ->paginate(12);

            // Get article categories for filtering
            $categories = ArticleCategory::where('journal_id', $journal->id)
                                       ->orWhere('is_journal', false)
                                       ->where('status', 'Active')
                                       ->orderBy('name')
                                       ->get();

            return view('public.journal.articles', compact('journal', 'articles', 'categories'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading articles', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load articles');
        }
    }

    /**
     * Display single article details
     */
    public function articleDetails(Article $article): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal || $article->journal_id !== $journal->id) {
                abort(404, 'Article not found');
            }

            // Only show published articles publicly
            if ($article->article_status !== 3) {
                abort(404, 'Article not found');
            }

            // Load relationships
            $article->load(['member', 'article_category', 'comments' => function($query) {
                $query->where('status', 'approved')
                      ->with('member')
                      ->orderBy('created_at', 'desc');
            }]);

            // Track view (optional - can be implemented with a service)
            $this->trackArticleView($article);

            return view('public.journal.article-details', compact('journal', 'article'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading article details', [
                'article_id' => $article->id,
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load article details');
        }
    }

    /**
     * Display journal archive
     */
    public function archive(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            // Get articles grouped by year and month
            $archivedArticles = $journal->publishedArticles()
                                      ->with(['member', 'article_category'])
                                      ->selectRaw('YEAR(published_online) as year, MONTH(published_online) as month, COUNT(*) as count')
                                      ->groupByRaw('YEAR(published_online), MONTH(published_online)')
                                      ->orderByRaw('year DESC, month DESC')
                                      ->get()
                                      ->map(function ($item) use ($journal) {
                                          $item->articles = $journal->publishedArticles()
                                                                   ->whereYear('published_online', $item->year)
                                                                   ->whereMonth('published_online', $item->month)
                                                                   ->with(['member', 'article_category'])
                                                                   ->orderBy('published_online', 'desc')
                                                                   ->limit(5)
                                                                   ->get();
                                          return $item;
                                      });

            return view('public.journal.archive', compact('journal', 'archivedArticles'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading archive', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load archive');
        }
    }

    /**
     * Display contact page
     */
    public function contact(): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            // Get contact information
            $contactInfo = [
                'email' => $journal->contact_email,
                'editor_in_chief' => $journal->editor_in_chief,
                'publisher_name' => $journal->publisher_name,
            ];

            return view('public.journal.contact', compact('journal', 'contactInfo'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error loading contact page', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to load contact page');
        }
    }

    /**
     * Search articles within a journal
     */
    public function search(Request $request): View
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                abort(404, 'Journal not found');
            }

            $query = $request->get('q');
            $category = $request->get('category');
            $year = $request->get('year');

            // Build search query
            $articlesQuery = $journal->publishedArticles()
                                   ->with(['member', 'article_category'])
                                   ->where('article_status', 3); // Only published articles

            // Apply search filters
            if ($query) {
                $articlesQuery->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('author_name', 'LIKE', "%{$query}%")
                      ->orWhere('other_authors', 'LIKE', "%{$query}%");
                });
            }

            if ($category) {
                $articlesQuery->where('article_category_id', $category);
            }

            if ($year) {
                $articlesQuery->whereYear('published_online', $year);
            }

            $articles = $articlesQuery->orderBy('published_online', 'desc')
                                    ->paginate(12);

            // Get filter options
            $categories = ArticleCategory::where('journal_id', $journal->id)
                                       ->orWhere('is_journal', false)
                                       ->where('status', 'Active')
                                       ->orderBy('name')
                                       ->get();

            $years = $journal->publishedArticles()
                           ->selectRaw('YEAR(published_online) as year')
                           ->distinct()
                           ->orderBy('year', 'desc')
                           ->pluck('year');

            return view('public.journal.search', compact('journal', 'articles', 'categories', 'years', 'query', 'category', 'year'));
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error searching articles', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to search articles');
        }
    }

    /**
     * Get journal statistics as JSON
     */
    public function statistics(): JsonResponse
    {
        try {
            $journal = $this->journalService->getCurrentJournal();

            if (!$journal) {
                return response()->json(['error' => 'Journal not found'], 404);
            }

            $stats = [
                'total_articles' => $journal->journalArticles()->count(),
                'published_articles' => $journal->publishedArticles()->count(),
                'editorial_board_count' => $journal->editorialBoard()->count(),
                'total_views' => $this->getTotalViews($journal),
                'recent_publications' => $journal->publishedArticles()
                                                ->where('published_online', '>=', now()->subMonths(6))
                                                ->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('PublicJournalController: Error getting statistics', [
                'journal_id' => $this->journalService->getCurrentJournal()?->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }

    /**
     * Get total views for a journal
     */
    private function getTotalViews($journal): int
    {
        // This would typically come from a view tracking system
        // For now, return a calculated value or implement view tracking
        return $journal->journalArticles()
                      ->withCount('views')
                      ->get()
                      ->sum('views_count');
    }

    /**
     * Track article view
     */
    private function trackArticleView(Article $article): void
    {
        try {
            // Implement view tracking logic here
            // This could increment a view counter or log the view for analytics
            Log::info('PublicJournalController: Article view tracked', [
                'article_id' => $article->id,
                'journal_id' => $article->journal_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::warning('PublicJournalController: Error tracking article view', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
