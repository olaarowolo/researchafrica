<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\JournalContextService;
use App\Services\EditorialBoardService;
use App\Services\JournalMembershipService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
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
    }

    /**
     * Display the main dashboard
     */
    public function index(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $user = Auth::user();

            // Get user's role in this journal
            $userRole = $this->getUserRole($user, $currentJournal->id);

            // Get dashboard data based on user role
            $dashboardData = $this->getDashboardData($currentJournal, $user, $userRole);

            return view('journal.dashboard.index', compact('currentJournal', 'dashboardData', 'userRole'));
        } catch (\Exception $e) {
            Log::error('DashboardController: Error loading main dashboard', [
                'error' => $e->getMessage()
            ]);
            return view('journal.dashboard.index')->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Display the articles dashboard
     */
    public function articles(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $user = Auth::user();
            $userRole = $this->getUserRole($user, $currentJournal->id);

            // Get articles based on user role
            $query = Article::where('journal_id', $currentJournal->id);

            if ($userRole === 'author') {
                $query->where('member_id', $user->id);
            }

            $articles = $query->with(['member', 'article_category'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

            // Get statistics
            $stats = $this->getArticleStatistics($currentJournal, $user, $userRole);

            return view('journal.dashboard.articles', compact('currentJournal', 'articles', 'stats', 'userRole'));
        } catch (\Exception $e) {
            Log::error('DashboardController: Error loading articles dashboard', [
                'error' => $e->getMessage()
            ]);
            return view('journal.dashboard.articles')->with('error', 'Failed to load articles dashboard.');
        }
    }

    /**
     * Display the editorial dashboard
     */
    public function editorial(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $user = Auth::user();
            $userRole = $this->getUserRole($user, $currentJournal->id);

            // Only editors and admins can access editorial dashboard
            if (!in_array($userRole, ['editor', 'admin'])) {
                abort(403, 'You do not have access to the editorial dashboard.');
            }

            // Get editorial data
            $editorialData = $this->getEditorialData($currentJournal, $user);

            return view('journal.dashboard.editorial', compact('currentJournal', 'editorialData', 'userRole'));
        } catch (\Exception $e) {
            Log::error('DashboardController: Error loading editorial dashboard', [
                'error' => $e->getMessage()
            ]);
            return view('journal.dashboard.editorial')->with('error', 'Failed to load editorial dashboard.');
        }
    }

    /**
     * Display the analytics dashboard
     */
    public function analytics(): View
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                abort(404, 'Journal not found');
            }

            $user = Auth::user();
            $userRole = $this->getUserRole($user, $currentJournal->id);

            // Get analytics data
            $analyticsData = $this->getAnalyticsData($currentJournal, $user, $userRole);

            return view('journal.dashboard.analytics', compact('currentJournal', 'analyticsData', 'userRole'));
        } catch (\Exception $e) {
            Log::error('DashboardController: Error loading analytics dashboard', [
                'error' => $e->getMessage()
            ]);
            return view('journal.dashboard.analytics')->with('error', 'Failed to load analytics dashboard.');
        }
    }

    /**
     * Get dashboard statistics as JSON
     */
    public function statistics(): JsonResponse
    {
        try {
            $currentJournal = $this->journalService->getCurrentJournal();

            if (!$currentJournal) {
                return response()->json(['error' => 'Journal not found'], 404);
            }

            $user = Auth::user();
            $userRole = $this->getUserRole($user, $currentJournal->id);

            $stats = $this->getDashboardStatistics($currentJournal, $user, $userRole);

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('DashboardController: Error getting statistics', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }

    /**
     * Get user role in the current journal
     */
    private function getUserRole($user, $journalId): string
    {
        if (!$user->hasJournalAccess($journalId)) {
            return 'none';
        }

        if ($user->isEditorFor($journalId)) {
            return 'editor';
        }

        if ($user->isReviewerFor($journalId)) {
            return 'reviewer';
        }

        // Check if user is admin (global admin has access to all journals)
        if ($user->hasRole('admin')) {
            return 'admin';
        }

        return 'author';
    }

    /**
     * Get dashboard data based on user role
     */
    private function getDashboardData($journal, $user, $userRole): array
    {
        $query = Article::where('journal_id', $journal->id);

        if ($userRole === 'author') {
            $query->where('member_id', $user->id);
        }

        // Recent articles
        $recentArticles = $query->with(['member', 'article_category'])
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        // Statistics
        $stats = $this->getDashboardStatistics($journal, $user, $userRole);

        // Quick actions based on role
        $quickActions = $this->getQuickActions($userRole);

        return [
            'recent_articles' => $recentArticles,
            'statistics' => $stats,
            'quick_actions' => $quickActions,
        ];
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStatistics($journal, $user, $userRole): array
    {
        $query = Article::where('journal_id', $journal->id);

        if ($userRole === 'author') {
            $query->where('member_id', $user->id);
        }

        return [
            'total_articles' => $query->count(),
            'pending_articles' => $query->where('article_status', 1)->count(),
            'reviewing_articles' => $query->where('article_status', 2)->count(),
            'published_articles' => $query->where('article_status', 3)->count(),
            'rejected_articles' => $query->where('article_status', 4)->count(),
        ];
    }

    /**
     * Get quick actions based on user role
     */
    private function getQuickActions($userRole): array
    {
        $actions = [];

        switch ($userRole) {
            case 'author':
                $actions = [
                    ['name' => 'Submit New Article', 'route' => 'journal.articles.create', 'icon' => 'plus'],
                    ['name' => 'My Articles', 'route' => 'journal.articles.index', 'icon' => 'document-text'],
                ];
                break;

            case 'editor':
                $actions = [
                    ['name' => 'Pending Reviews', 'route' => 'journal.dashboard.editorial', 'icon' => 'clock'],
                    ['name' => 'All Articles', 'route' => 'journal.dashboard.articles', 'icon' => 'document-text'],
                    ['name' => 'Editorial Board', 'route' => 'admin.editorial-board.index', 'icon' => 'users'],
                ];
                break;

            case 'admin':
                $actions = [
                    ['name' => 'Manage Journal', 'route' => 'admin.journals.show', 'icon' => 'cog'],
                    ['name' => 'Editorial Board', 'route' => 'admin.editorial-board.index', 'icon' => 'users'],
                    ['name' => 'Journal Members', 'route' => 'admin.journal-memberships.index', 'icon' => 'user-group'],
                    ['name' => 'Analytics', 'route' => 'journal.dashboard.analytics', 'icon' => 'chart-bar'],
                ];
                break;
        }

        return $actions;
    }

    /**
     * Get article statistics
     */
    private function getArticleStatistics($journal, $user, $userRole): array
    {
        $query = Article::where('journal_id', $journal->id);

        if ($userRole === 'author') {
            $query->where('member_id', $user->id);
        }

        // Articles by status
        $articlesByStatus = $query->selectRaw('article_status, COUNT(*) as count')
                                ->groupBy('article_status')
                                ->pluck('count', 'article_status')
                                ->toArray();

        // Articles by month (last 6 months)
        $articlesByMonth = $query->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                ->where('created_at', '>=', now()->subMonths(6))
                                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                                ->orderByRaw('year DESC, month DESC')
                                ->get()
                                ->map(function ($item) {
                                    $item->month_name = Carbon::create()->month($item->month)->format('M');
                                    return $item;
                                });

        return [
            'by_status' => $articlesByStatus,
            'by_month' => $articlesByMonth,
            'total' => $query->count(),
        ];
    }

    /**
     * Get editorial dashboard data
     */
    private function getEditorialData($journal, $user): array
    {
        // Get articles that need editorial attention
        $pendingReviews = Article::where('journal_id', $journal->id)
                               ->whereIn('article_status', [1, 2]) // Pending and Reviewing
                               ->with(['member', 'article_category'])
                               ->orderBy('created_at', 'asc')
                               ->limit(10)
                               ->get();

        // Get editorial board statistics
        $editorialStats = $this->editorialBoardService->getBoardAnalytics($journal->id);

        // Get recent activity
        $recentActivity = Article::where('journal_id', $journal->id)
                               ->where('article_status', '!=', 1) // Not pending
                               ->with(['member', 'article_category'])
                               ->orderBy('updated_at', 'desc')
                               ->limit(5)
                               ->get();

        return [
            'pending_reviews' => $pendingReviews,
            'editorial_stats' => $editorialStats,
            'recent_activity' => $recentActivity,
        ];
    }

    /**
     * Get analytics dashboard data
     */
    private function getAnalyticsData($journal, $user, $userRole): array
    {
        $query = Article::where('journal_id', $journal->id);

        if ($userRole === 'author') {
            $query->where('member_id', $user->id);
        }

        // Article growth over time
        $articleGrowth = $query->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                              ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                              ->orderByRaw('year DESC, month DESC')
                              ->limit(12)
                              ->get()
                              ->map(function ($item) {
                                  $item->month_name = Carbon::create()->month($item->month)->format('M Y');
                                  return $item;
                              });

        // Publication rate
        $publishedThisMonth = Article::where('journal_id', $journal->id)
                                    ->where('article_status', 3)
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();

        $totalPublished = Article::where('journal_id', $journal->id)
                                ->where('article_status', 3)
                                ->count();

        // Top authors (if admin/editor)
        $topAuthors = [];
        if (in_array($userRole, ['editor', 'admin'])) {
            $topAuthors = Article::where('journal_id', $journal->id)
                                ->where('article_status', 3)
                                ->with('member')
                                ->get()
                                ->groupBy('member_id')
                                ->map(function ($articles, $memberId) {
                                    return [
                                        'member' => $articles->first()->member,
                                        'count' => $articles->count(),
                                    ];
                                })
                                ->sortByDesc('count')
                                ->take(5)
                                ->values();
        }

        return [
            'article_growth' => $articleGrowth,
            'published_this_month' => $publishedThisMonth,
            'total_published' => $totalPublished,
            'top_authors' => $topAuthors,
        ];
    }
}
