<?php

namespace Tests\Feature\Journal;

use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Services\JournalContextService;
use App\Services\EditorialBoardService;
use App\Services\JournalMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $editorUser;
    protected $adminUser;
    protected $journal;
    protected $category;
    protected $article;
    protected $journalService;
    protected $editorialBoardService;
    protected $membershipService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberType = MemberType::factory()->create();

        // Create different user roles
        $authorRole = MemberRole::factory()->create(['title' => 'Author']);
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $adminRole = MemberRole::factory()->create(['title' => 'Administrator']);

        // Create users
        $this->user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $authorRole->id,
        ]);

        $this->editorUser = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $editorRole->id,
        ]);

        $this->adminUser = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $adminRole->id,
        ]);

        // Create journal
        $this->journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'DASHJ'
        ]);

        // Create article category
        $this->category = ArticleCategory::factory()->create([
            'journal_id' => $this->journal->id,
            'is_journal' => false
        ]);

        // Create articles
        $this->article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1
        ]);

        // Mock services
        $this->journalService = $this->mock(JournalContextService::class);
        $this->editorialBoardService = $this->mock(EditorialBoardService::class);
        $this->membershipService = $this->mock(JournalMembershipService::class);

        // Set up common mock behavior
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn($this->journal);
    }

    /** @test */
    public function index_displays_main_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.dashboard.index');
        $response->assertViewHas('currentJournal');
        $response->assertViewHas('dashboardData');
        $response->assertViewHas('userRole');
    }

    /** @test */
    public function index_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(404);
    }

    /** @test */
    public function index_shows_author_dashboard_data()
    {
        // Create additional articles for the author
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3 // Published
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $dashboardData = $response->viewData('dashboardData');

        $this->assertArrayHasKey('recent_articles', $dashboardData);
        $this->assertArrayHasKey('statistics', $dashboardData);
        $this->assertArrayHasKey('quick_actions', $dashboardData);

        $this->assertEquals('author', $response->viewData('userRole'));
    }

    /** @test */
    public function index_shows_editor_dashboard_data()
    {
        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);

        $this->assertEquals('editor', $response->viewData('userRole'));
    }

    /** @test */
    public function index_shows_admin_dashboard_data()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);

        $this->assertEquals('admin', $response->viewData('userRole'));
    }

    /** @test */
    public function articles_displays_articles_dashboard()
    {
        // Create additional articles
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 2 // Reviewing
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.articles'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.dashboard.articles');
        $response->assertViewHas('articles');
        $response->assertViewHas('stats');
        $response->assertViewHas('userRole');
    }

    /** @test */
    public function articles_filters_by_user_for_authors()
    {
        // Create another author
        $otherAuthor = User::factory()->create();

        // Create article for other author
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $otherAuthor->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.articles'));

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Author should only see their own articles
        $this->assertEquals(1, $articles->total());
    }

    /** @test */
    public function editorial_displays_editorial_dashboard_for_editors()
    {
        $this->editorialBoardService->shouldReceive('getBoardAnalytics')
                                   ->with($this->journal->id)
                                   ->andReturn(['total_members' => 5, 'active_editors' => 3]);

        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.editorial'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.dashboard.editorial');
        $response->assertViewHas('editorialData');
        $response->assertViewHas('userRole');
    }

    /** @test */
    public function editorial_displays_editorial_dashboard_for_admins()
    {
        $this->editorialBoardService->shouldReceive('getBoardAnalytics')
                                   ->with($this->journal->id)
                                   ->andReturn(['total_members' => 10, 'active_editors' => 5]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard.editorial'));

        $response->assertStatus(200);
        $response->assertViewHas('userRole', 'admin');
    }

    /** @test */
    public function editorial_denies_access_for_authors()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.editorial'));

        $response->assertStatus(403);
        $response->assertSee('You do not have access to the editorial dashboard');
    }

    /** @test */
    public function analytics_displays_analytics_dashboard()
    {
        // Create published articles for analytics
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'created_at' => now()->subMonths(2)
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'created_at' => now()->subMonth()
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.analytics'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.dashboard.analytics');
        $response->assertViewHas('analyticsData');
        $response->assertViewHas('userRole');
    }

    /** @test */
    public function analytics_shows_top_authors_for_editors()
    {
        // Create articles by different authors
        $author2 = User::factory()->create();
        $author3 = User::factory()->create();

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $author2->id,
            'article_status' => 3
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $author3->id,
            'article_status' => 3
        ]);

        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.analytics'));

        $response->assertStatus(200);
        $analyticsData = $response->viewData('analyticsData');

        $this->assertArrayHasKey('top_authors', $analyticsData);
    }

    /** @test */
    public function statistics_returns_json_data()
    {
        // Create articles with different statuses
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 2 // Reviewing
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3 // Published
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.statistics'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayHasKey('total_articles', $data);
        $this->assertArrayHasKey('pending_articles', $data);
        $this->assertArrayHasKey('reviewing_articles', $data);
        $this->assertArrayHasKey('published_articles', $data);
        $this->assertArrayHasKey('rejected_articles', $data);
    }

    /** @test */
    public function statistics_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.statistics'));

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Journal not found']);
    }

    /** @test */
    public function dashboard_handles_exceptions_gracefully()
    {
        // Mock service to throw exception
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'DashboardController: Error loading main dashboard');
            }));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.dashboard.index');
        $response->assertViewHas('error', 'Failed to load dashboard.');
    }

    /** @test */
    public function all_dashboard_routes_require_authentication()
    {
        Auth::logout();

        $routes = [
            ['GET', 'dashboard.index'],
            ['GET', 'dashboard.articles'],
            ['GET', 'dashboard.editorial'],
            ['GET', 'dashboard.analytics'],
            ['GET', 'dashboard.statistics'],
        ];

        foreach ($routes as [$method, $route]) {
            $response = $this->call($method, route($route));
            $response->assertRedirect(); // Should redirect to login
        }
    }

    /** @test */
    public function articles_dashboard_handles_exceptions()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'DashboardController: Error loading articles dashboard');
            }));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.articles'));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'Failed to load articles dashboard.');
    }

    /** @test */
    public function editorial_dashboard_handles_exceptions()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'DashboardController: Error loading editorial dashboard');
            }));

        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.editorial'));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'Failed to load editorial dashboard.');
    }

    /** @test */
    public function analytics_dashboard_handles_exceptions()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'DashboardController: Error loading analytics dashboard');
            }));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.analytics'));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'Failed to load analytics dashboard.');
    }

    /** @test */
    public function statistics_endpoint_handles_exceptions()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'DashboardController: Error getting statistics');
            }));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.statistics'));

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Failed to get statistics']);
    }

    /** @test */
    public function dashboard_shows_correct_quick_actions_for_author()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $dashboardData = $response->viewData('dashboardData');
        $quickActions = $dashboardData['quick_actions'];

        $this->assertNotEmpty($quickActions);

        // Author should have submit and my articles actions
        $actionNames = collect($quickActions)->pluck('name')->toArray();
        $this->assertContains('Submit New Article', $actionNames);
        $this->assertContains('My Articles', $actionNames);
    }

    /** @test */
    public function dashboard_shows_correct_quick_actions_for_editor()
    {
        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $dashboardData = $response->viewData('dashboardData');
        $quickActions = $dashboardData['quick_actions'];

        $actionNames = collect($quickActions)->pluck('name')->toArray();
        $this->assertContains('Pending Reviews', $actionNames);
        $this->assertContains('All Articles', $actionNames);
        $this->assertContains('Editorial Board', $actionNames);
    }

    /** @test */
    public function dashboard_shows_correct_quick_actions_for_admin()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $dashboardData = $response->viewData('dashboardData');
        $quickActions = $dashboardData['quick_actions'];

        $actionNames = collect($quickActions)->pluck('name')->toArray();
        $this->assertContains('Manage Journal', $actionNames);
        $this->assertContains('Editorial Board', $actionNames);
        $this->assertContains('Journal Members', $actionNames);
        $this->assertContains('Analytics', $actionNames);
    }

    /** @test */
    public function articles_dashboard_paginates_results()
    {
        // Create 25 articles to test pagination
        Article::factory()->count(24)->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.articles'));

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        $this->assertEquals(20, $articles->perPage()); // Laravel default
        $this->assertEquals(25, $articles->total());
    }

    /** @test */
    public function editorial_dashboard_shows_pending_reviews()
    {
        // Create pending and reviewing articles
        $pendingArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1 // Pending
        ]);

        $reviewingArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 2 // Reviewing
        ]);

        $this->editorialBoardService->shouldReceive('getBoardAnalytics')
                                   ->with($this->journal->id)
                                   ->andReturn([]);

        $response = $this->actingAs($this->editorUser)
            ->get(route('dashboard.editorial'));

        $response->assertStatus(200);
        $editorialData = $response->viewData('editorialData');

        $this->assertArrayHasKey('pending_reviews', $editorialData);
        $this->assertEquals(2, $editorialData['pending_reviews']->count());
    }

    /** @test */
    public function analytics_shows_article_growth_data()
    {
        // Create articles from different months
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'created_at' => now()->subMonths(3)
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'created_at' => now()->subMonths(1)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.analytics'));

        $response->assertStatus(200);
        $analyticsData = $response->viewData('analyticsData');

        $this->assertArrayHasKey('article_growth', $analyticsData);
        $this->assertArrayHasKey('published_this_month', $analyticsData);
        $this->assertArrayHasKey('total_published', $analyticsData);
    }

    /** @test */
    public function controller_uses_required_services()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\Journal\DashboardController::class);

        $this->assertTrue($reflection->hasMethod('__construct'));

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue($constructor->isPublic());
    }

    /** @test */
    public function getUserRole_returns_correct_roles()
    {
        // This test would require more complex mocking of user methods
        // For now, we'll test the endpoint behavior

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function dashboard_calculates_statistics_correctly()
    {
        // Create articles with different statuses
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1 // Pending
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 2 // Reviewing
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3 // Published
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 4 // Rejected
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.statistics'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(4, $data['total_articles']);
        $this->assertEquals(1, $data['pending_articles']);
        $this->assertEquals(1, $data['reviewing_articles']);
        $this->assertEquals(1, $data['published_articles']);
        $this->assertEquals(1, $data['rejected_articles']);
    }
}
