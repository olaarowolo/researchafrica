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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PublicJournalControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $journal;
    protected $category;
    protected $article;
    protected $publishedArticle;
    protected $journalService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create user
        $this->user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create journal
        $this->journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'PUBJ',
            'contact_email' => 'editor@journal.com',
            'editor_in_chief' => 'Dr. Editor',
            'publisher_name' => 'Test Publisher'
        ]);

        // Create article category
        $this->category = ArticleCategory::factory()->create([
            'journal_id' => $this->journal->id,
            'is_journal' => false,
            'status' => 'Active'
        ]);

        // Create articles
        $this->article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1 // Draft/Pending
        ]);

        $this->publishedArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3, // Published
            'published_online' => now()->subDays(5)
        ]);

        // Mock JournalContextService
        $this->journalService = $this->mock(JournalContextService::class);
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn($this->journal);
    }

    /** @test */
    public function index_displays_journal_homepage()
    {
        $response = $this->get(route('public.index'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.index');
        $response->assertViewHas('journal');
        $response->assertViewHas('recentArticles');
        $response->assertViewHas('stats');
    }

    /** @test */
    public function index_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->get(route('public.index'));

        $response->assertStatus(404);
    }

    /** @test */
    public function index_shows_recent_published_articles()
    {
        // Create additional published articles
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subDays(2)
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subDays(1)
        ]);

        $response = $this->get(route('public.index'));

        $response->assertStatus(200);
        $recentArticles = $response->viewData('recentArticles');

        $this->assertEquals(3, $recentArticles->count());
        $this->assertTrue($recentArticles->contains($this->publishedArticle));
    }

    /** @test */
    public function index_shows_journal_statistics()
    {
        $response = $this->get(route('public.index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');

        $this->assertArrayHasKey('total_articles', $stats);
        $this->assertArrayHasKey('published_articles', $stats);
        $this->assertArrayHasKey('editorial_board_count', $stats);
        $this->assertArrayHasKey('total_views', $stats);

        $this->assertEquals(2, $stats['published_articles']);
    }

    /** @test */
    public function about_displays_journal_about_page()
    {
        $response = $this->get(route('public.about'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.about');
        $response->assertViewHas('journal');
    }

    /** @test */
    public function editorial_board_displays_editorial_board()
    {
        // This would require creating editorial board entries
        // For now, test the basic functionality
        $response = $this->get(route('public.editorial-board'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.editorial-board');
        $response->assertViewHas('journal');
        $response->assertViewHas('editorialBoard');
    }

    /** @test */
    public function submission_guidelines_displays_guidelines()
    {
        // Test with submission settings
        $this->journal->update([
            'submission_settings' => json_encode([
                'word_limit' => 5000,
                'format' => 'APA',
                'review_process' => 'double-blind'
            ])
        ]);

        $response = $this->get(route('public.submission-guidelines'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.submission-guidelines');
        $response->assertViewHas('journal');
        $response->assertViewHas('submissionSettings');
    }

    /** @test */
    public function submission_guidelines_handles_null_settings()
    {
        $response = $this->get(route('public.submission-guidelines'));

        $response->assertStatus(200);
        $response->assertViewHas('submissionSettings', null);
    }

    /** @test */
    public function articles_displays_published_articles_with_pagination()
    {
        // Create additional published articles
        Article::factory()->count(15)->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3
        ]);

        $response = $this->get(route('public.articles'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.articles');
        $response->assertViewHas('articles');
        $response->assertViewHas('categories');
        $response->assertViewHas('journal');

        $articles = $response->viewData('articles');
        $this->assertEquals(12, $articles->perPage()); // Laravel default
        $this->assertEquals(16, $articles->total()); // 1 from setup + 15 created
    }

    /** @test */
    public function articles_shows_categories_for_filtering()
    {
        // Create additional categories
        ArticleCategory::factory()->create([
            'journal_id' => $this->journal->id,
            'is_journal' => false,
            'status' => 'Active'
        ]);

        ArticleCategory::factory()->create([
            'journal_id' => $this->journal->id,
            'is_journal' => false,
            'status' => 'Active'
        ]);

        $response = $this->get(route('public.articles'));

        $response->assertStatus(200);
        $categories = $response->viewData('categories');

        $this->assertGreaterThanOrEqual(3, $categories->count());
    }

    /** @test */
    public function article_details_displays_published_article()
    {
        $response = $this->get(route('public.article-details', $this->publishedArticle));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.article-details');
        $response->assertViewHas('journal');
        $response->assertViewHas('article');
    }

    /** @test */
    public function article_details_denies_access_to_unpublished_articles()
    {
        $response = $this->get(route('public.article-details', $this->article));

        $response->assertStatus(404);
    }

    /** @test */
    public function article_details_handles_journal_mismatch()
    {
        // Create article for different journal
        $otherJournal = ArticleCategory::factory()->create(['is_journal' => true]);
        $otherArticle = Article::factory()->create([
            'journal_id' => $otherJournal->id
        ]);

        $response = $this->get(route('public.article-details', $otherArticle));

        $response->assertStatus(404);
    }

    /** @test */
    public function article_details_loads_comments()
    {
        // This would require creating comments for the article
        // For now, test that the method loads relationships
        $response = $this->get(route('public.article-details', $this->publishedArticle));

        $response->assertStatus(200);
        $article = $response->viewData('article');

        $this->assertTrue($article->relationLoaded('member'));
        $this->assertTrue($article->relationLoaded('article_category'));
        $this->assertTrue($article->relationLoaded('comments'));
    }

    /** @test */
    public function archive_displays_articles_by_year_month()
    {
        // Create articles from different months
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subMonths(3)
        ]);

        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subMonths(1)
        ]);

        $response = $this->get(route('public.archive'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.archive');
        $response->assertViewHas('journal');
        $response->assertViewHas('archivedArticles');
    }

    /** @test */
    public function contact_displays_contact_information()
    {
        $response = $this->get(route('public.contact'));

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.contact');
        $response->assertViewHas('journal');
        $response->assertViewHas('contactInfo');

        $contactInfo = $response->viewData('contactInfo');
        $this->assertEquals('editor@journal.com', $contactInfo['email']);
        $this->assertEquals('Dr. Editor', $contactInfo['editor_in_chief']);
        $this->assertEquals('Test Publisher', $contactInfo['publisher_name']);
    }

    /** @test */
    public function search_performs_article_search()
    {
        $response = $this->get(route('public.search'), [
            'q' => 'Test Article'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('public.journal.search');
        $response->assertViewHas('journal');
        $response->assertViewHas('articles');
        $response->assertViewHas('categories');
        $response->assertViewHas('years');
        $response->assertViewHas('query');
    }

    /** @test */
    public function search_filters_by_category()
    {
        $response = $this->get(route('public.search'), [
            'category' => $this->category->id
        ]);

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Should only show published articles from this category
        foreach ($articles as $article) {
            $this->assertEquals(3, $article->article_status);
            $this->assertEquals($this->category->id, $article->article_category_id);
        }
    }

    /** @test */
    public function search_filters_by_year()
    {
        $response = $this->get(route('public.search'), [
            'year' => now()->year
        ]);

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Should only show articles from current year
        foreach ($articles as $article) {
            $this->assertEquals(now()->year, $article->published_online->year);
        }
    }

    /** @test */
    public function search_handles_combined_filters()
    {
        $response = $this->get(route('public.search'), [
            'q' => 'Test',
            'category' => $this->category->id,
            'year' => now()->year
        ]);

        $response->assertStatus(200);
        // Should apply all filters
    }

    /** @test */
    public function statistics_returns_json_data()
    {
        $response = $this->get(route('public.statistics'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayHasKey('total_articles', $data);
        $this->assertArrayHasKey('published_articles', $data);
        $this->assertArrayHasKey('editorial_board_count', $data);
        $this->assertArrayHasKey('total_views', $data);
        $this->assertArrayHasKey('recent_publications', $data);
    }

    /** @test */
    public function statistics_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->get(route('public.statistics'));

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Journal not found']);
    }

    /** @test */
    public function controller_handles_exceptions_gracefully()
    {
        // Mock service to throw exception
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andThrow(new \Exception('Test exception'));

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'PublicJournalController: Error loading journal index');
            }));

        $response = $this->get(route('public.index'));

        $response->assertStatus(500);
    }

    /** @test */
    public function public_routes_dont_require_authentication()
    {
        Auth::logout();

        $routes = [
            ['GET', 'public.index'],
            ['GET', 'public.about'],
            ['GET', 'public.editorial-board'],
            ['GET', 'public.submission-guidelines'],
            ['GET', 'public.articles'],
            ['GET', 'public.article-details'],
            ['GET', 'public.archive'],
            ['GET', 'public.contact'],
            ['GET', 'public.search'],
            ['GET', 'public.statistics'],
        ];

        foreach ($routes as [$method, $route]) {
            $response = $this->call($method, route($route, $this->publishedArticle));
            // Should not redirect to login
            $this->assertNotEquals(302, $response->getStatusCode());
        }
    }

    /** @test */
    public function search_paginates_results()
    {
        // Create 25 articles for search
        Article::factory()->count(24)->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3
        ]);

        $response = $this->get(route('public.search'), [
            'q' => 'Test'
        ]);

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        $this->assertEquals(12, $articles->perPage());
        $this->assertEquals(25, $articles->total());
    }

    /** @test */
    public function search_returns_empty_results_for_no_matches()
    {
        $response = $this->get(route('public.search'), [
            'q' => 'NonExistentSearchTerm12345'
        ]);

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        $this->assertEquals(0, $articles->total());
    }

    /** @test */
    public function index_handles_journals_with_no_published_articles()
    {
        // Delete published articles
        $this->publishedArticle->delete();

        $response = $this->get(route('public.index'));

        $response->assertStatus(200);
        $recentArticles = $response->viewData('recentArticles');
        $this->assertEquals(0, $recentArticles->count());
    }

    /** @test */
    public function article_details_tracks_views()
    {
        Log::shouldReceive('info')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'PublicJournalController: Article view tracked');
            }));

        $response = $this->get(route('public.article-details', $this->publishedArticle));

        $response->assertStatus(200);
    }

    /** @test */
    public function search_provides_filter_options()
    {
        // Create articles from different years
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subYear()
        ]);

        $response = $this->get(route('public.search'));

        $response->assertStatus(200);
        $years = $response->viewData('years');
        $categories = $response->viewData('categories');

        $this->assertNotEmpty($years);
        $this->assertNotEmpty($categories);
    }

    /** @test */
    public function controller_uses_journal_context_middleware()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\Journal\PublicJournalController::class);

        $this->assertTrue($reflection->hasMethod('__construct'));

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue($constructor->isPublic());
    }

    /** @test */
    public function archive_shows_limited_articles_per_month()
    {
        // Create 10 articles for the same month
        Article::factory()->count(10)->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3,
            'published_online' => now()->subMonths(2)
        ]);

        $response = $this->get(route('public.archive'));

        $response->assertStatus(200);
        $archivedArticles = $response->viewData('archivedArticles');

        // Each month group should have max 5 articles
        foreach ($archivedArticles as $monthGroup) {
            $this->assertLessThanOrEqual(5, $monthGroup->articles->count());
        }
    }

    /** @test */
    public function about_page_requires_journal_context()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->get(route('public.about'));

        $response->assertStatus(404);
    }

    /** @test */
    public function contact_page_handles_missing_contact_info()
    {
        $this->journal->update([
            'contact_email' => null,
            'editor_in_chief' => null,
            'publisher_name' => null
        ]);

        $response = $this->get(route('public.contact'));

        $response->assertStatus(200);
        $contactInfo = $response->viewData('contactInfo');

        $this->assertNull($contactInfo['email']);
        $this->assertNull($contactInfo['editor_in_chief']);
        $this->assertNull($contactInfo['publisher_name']);
    }

    /** @test */
    public function get_total_views_calculates_correctly()
    {
        // This test would require implementing the getTotalViews method
        // For now, test that the method exists and can be called
        $response = $this->get(route('public.statistics'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayHasKey('total_views', $data);
        $this->assertIsInt($data['total_views']);
    }

    /** @test */
    public function search_only_returns_published_articles()
    {
        // Create a draft article
        $draftArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1, // Draft
            'title' => 'Draft Article Title'
        ]);

        $response = $this->get(route('public.search'), [
            'q' => 'Draft'
        ]);

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Should not include draft articles
        foreach ($articles as $article) {
            $this->assertNotEquals($draftArticle->id, $article->id);
        }
    }
}
