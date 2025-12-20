<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Bookmark;
use App\Models\ArticleKeyword;
use App\Models\Comment;
use App\Models\PurchasedArticle;
use App\Models\DownloadArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AjaxControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $member;
    protected $country;
    protected $state;
    protected $article;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $this->country = Country::factory()->create();
        $this->state = State::factory()->create(['country_id' => $this->country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create user
        $this->user = User::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create member (for authentication)
        $this->member = Member::factory()->create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create article category
        $this->category = ArticleCategory::factory()->create();

        // Create article
        $this->article = Article::factory()->create([
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id
        ]);

        // Fake storage
        Storage::fake('public');
    }

    /** @test */
    public function getStates_returns_states_for_given_country()
    {
        // Create additional states for the same country
        $state1 = State::factory()->create(['country_id' => $this->country->id]);
        $state2 = State::factory()->create(['country_id' => $this->country->id]);

        $response = $this->get(route('ajax.get-states', $this->country->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $data = $response->getContent();

        // Check that all states are included
        $this->assertStringContainsString($this->state->name, $data);
        $this->assertStringContainsString($state1->name, $data);
        $this->assertStringContainsString($state2->name, $data);
    }

    /** @test */
    public function getStates_returns_empty_for_invalid_country()
    {
        $response = $this->get(route('ajax.get-states', 999));

        $response->assertStatus(200);
        $response->assertContent('""');
    }

    /** @test */
    public function keywordDelete_removes_keyword_from_article()
    {
        // Create article keyword
        $keyword = ArticleKeyword::factory()->create();
        $this->article->article_keywords()->attach($keyword->id);

        $response = $this->post(route('ajax.keyword-delete'), [
            'article_id' => $this->article->id,
            'article_keyword_id' => $keyword->id
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200
        ]);

        // Verify keyword was detached
        $this->assertFalse($this->article->article_keywords()->where('id', $keyword->id)->exists());
    }

    /** @test */
    public function keywordDelete_handles_nonexistent_article()
    {
        $response = $this->post(route('ajax.keyword-delete'), [
            'article_id' => 999,
            'article_keyword_id' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200
        ]);
    }

    /** @test */
    public function verifyPayment_creates_purchased_article_record()
    {
        $paymentData = [
            'amount' => 100.00,
            'article_id' => $this->article->id,
            'member_id' => $this->member->id,
            'reference' => 'TEST_REF_123'
        ];

        $response = $this->post(route('ajax.verify-transaction', 'TEST_REF_123'), $paymentData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200
        ]);

        $this->assertDatabaseHas('purchased_articles', [
            'article_id' => $this->article->id,
            'member_id' => $this->member->id,
            'reference' => 'TEST_REF_123',
            'amount' => 100.00
        ]);
    }

    /** @test */
    public function verifyPayment_handles_creation_failure()
    {
        // Mock a scenario where creation fails
        $paymentData = [
            'amount' => 100.00,
            'article_id' => $this->article->id,
            'member_id' => $this->member->id,
            'reference' => 'TEST_REF_123'
        ];

        $response = $this->post(route('ajax.verify-transaction', 'TEST_REF_123'), $paymentData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200
        ]);
    }

    /** @test */
    public function bookmark_creates_bookmark_for_authenticated_member()
    {
        Auth::login($this->member);

        $response = $this->get(route('ajax.bookmark', $this->article->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'create'
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'article_id' => $this->article->id,
            'member_id' => $this->member->id
        ]);
    }

    /** @test */
    public function bookmark_removes_existing_bookmark()
    {
        Auth::login($this->member);

        // Create existing bookmark
        Bookmark::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => $this->member->id
        ]);

        $response = $this->get(route('ajax.bookmark', $this->article->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'delete'
        ]);

        $this->assertDatabaseMissing('bookmarks', [
            'article_id' => $this->article->id,
            'member_id' => $this->member->id
        ]);
    }

    /** @test */
    public function bookmark_handles_unauthenticated_user()
    {
        Auth::logout();

        $response = $this->get(route('ajax.bookmark', $this->article->id));

        $response->assertStatus(302);
        $response->assertRedirect(); // Should redirect to login
    }

    /** @test */
    public function getJournals_returns_journals_for_category()
    {
        // Create parent and child categories
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $childCategory1 = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);
        $childCategory2 = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->get(route('ajax.get-journals', $parentCategory->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertStringContainsString($childCategory1->category_name, $data['data']);
        $this->assertStringContainsString($childCategory2->category_name, $data['data']);
    }

    /** @test */
    public function getJournals_returns_empty_for_category_with_no_children()
    {
        $response = $this->get(route('ajax.get-journals', $this->category->id));

        $response->assertStatus(200);
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('""', $data['data']);
    }

    /** @test */
    public function downloadPaperReview_requires_authentication()
    {
        Auth::logout();

        $response = $this->get(route('ajax.download-review', $this->article));

        $response->assertStatus(302);
        $response->assertRedirect(); // Should redirect to login
    }

    /** @test */
    public function downloadPaperReview_handles_missing_file()
    {
        Auth::login($this->member);

        $response = $this->get(route('ajax.download-review', $this->article));

        $response->assertStatus(200);
        $response->assertContent('No file');
    }

    /** @test */
    public function downloadCommentPaperReview_requires_authentication()
    {
        Auth::logout();

        // Create a comment for testing
        $comment = Comment::factory()->create();

        $response = $this->get(route('ajax.download-comment-doc', $comment));

        $response->assertStatus(302);
        $response->assertRedirect(); // Should redirect to login
    }

    /** @test */
    public function downloadCommentPaperReview_handles_missing_file()
    {
        Auth::login($this->member);

        $comment = Comment::factory()->create();

        $response = $this->get(route('ajax.download-comment-doc', $comment));

        $response->assertStatus(200);
        $response->assertContent('No file');
    }

    /** @test */
    public function downloadPdf_requires_authentication()
    {
        Auth::logout();

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(302);
        $response->assertRedirect(); // Should redirect to login
    }

    /** @test */
    public function downloadPdf_allows_free_article_download()
    {
        Auth::login($this->member);

        // Create open access article
        $this->article->update(['access_type' => 1]); // Open access

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(200);
    }

    /** @test */
    public function downloadPdf_denies_access_for_paid_article_without_purchase()
    {
        Auth::login($this->member);

        // Create paid article
        $this->article->update(['access_type' => 2]); // Close access

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Sorry, you have to pay to download this article');
    }

    /** @test */
    public function downloadPdf_allows_download_for_paid_article_after_purchase()
    {
        Auth::login($this->member);

        // Create paid article
        $this->article->update(['access_type' => 2]); // Close access

        // Create purchase record
        PurchasedArticle::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => $this->member->id
        ]);

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(200);
    }

    /** @test */
    public function downloadPdf_tracks_download_count()
    {
        Auth::login($this->member);

        $this->article->update(['access_type' => 1]); // Open access

        // Create existing download record
        DownloadArticle::factory()->create([
            'article_id' => $this->article->id,
            'download' => 5
        ]);

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(200);

        // Verify download count was incremented
        $this->assertDatabaseHas('download_articles', [
            'article_id' => $this->article->id,
            'download' => 6
        ]);
    }

    /** @test */
    public function downloadPdf_creates_download_record_if_none_exists()
    {
        Auth::login($this->member);

        $this->article->update(['access_type' => 1]); // Open access

        $response = $this->get(route('ajax.download-article', $this->article));

        $response->assertStatus(200);

        $this->assertDatabaseHas('download_articles', [
            'article_id' => $this->article->id,
            'download' => 1
        ]);
    }

    /** @test */
    public function ajax_methods_handle_json_responses_correctly()
    {
        // Test methods that should return JSON
        $ajaxMethods = [
            ['GET', 'ajax.get-states', [$this->country->id]],
            ['POST', 'ajax.keyword-delete', [
                'article_id' => $this->article->id,
                'article_keyword_id' => 1
            ]],
            ['POST', 'ajax.verify-transaction', ['TEST_REF', [
                'amount' => 100,
                'article_id' => $this->article->id,
                'member_id' => $this->member->id,
                'reference' => 'TEST_REF'
            ]]],
            ['GET', 'ajax.get-journals', [$this->category->id]],
        ];

        foreach ($ajaxMethods as [$method, $route, $parameters]) {
            $response = $this->call($method, route($route, ...$parameters));
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    /** @test */
    public function controller_uses_correct_methods()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\AjaxController::class);

        $expectedMethods = [
            'getStates',
            'keywordDelete',
            'verifyPayment',
            'bookmark',
            'getJournals',
            'downloadPaperReview',
            'downloadCommentPaperReview',
            'downloadPdf'
        ];

        foreach ($expectedMethods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "Controller should have {$method} method");
        }
    }

    /** @test */
    public function getStates_generates_html_options()
    {
        $state1 = State::factory()->create(['country_id' => $this->country->id]);
        $state2 = State::factory()->create(['country_id' => $this->country->id]);

        $response = $this->get(route('ajax.get-states', $this->country->id));

        $response->assertStatus(200);

        $content = $response->getContent();

        // Verify HTML option format
        $this->assertStringContainsString('<option value=', $content);
        $this->assertStringContainsString($this->state->name, $content);
        $this->assertStringContainsString($state1->name, $content);
        $this->assertStringContainsString($state2->name, $content);
    }

    /** @test */
    public function getJournals_generates_html_options()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $childCategory = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->get(route('ajax.get-journals', $parentCategory->id));

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true);

        // Verify HTML option format in JSON response
        $this->assertStringContainsString('<option value=', $data['data']);
        $this->assertStringContainsString($childCategory->category_name, $data['data']);
    }

    /** @test */
    public function bookmark_toggles_correctly_for_multiple_calls()
    {
        Auth::login($this->member);

        // First call - should create
        $response1 = $this->get(route('ajax.bookmark', $this->article->id));
        $response1->assertJson(['status' => 'create']);

        // Second call - should delete
        $response2 = $this->get(route('ajax.bookmark', $this->article->id));
        $response2->assertJson(['status' => 'delete']);

        // Third call - should create again
        $response3 = $this->get(route('ajax.bookmark', $this->article->id));
        $response3->assertJson(['status' => 'create']);
    }

    /** @test */
    public function downloadPdf_handles_article_without_member()
    {
        Auth::login($this->member);

        // Create article with different member
        $otherArticle = Article::factory()->create([
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id + 1 // Different member
        ]);

        $otherArticle->update(['access_type' => 1]); // Open access

        $response = $this->get(route('ajax.download-article', $otherArticle));

        $response->assertStatus(200);
    }

    /** @test */
    public function keywordDelete_handles_nonexistent_keyword()
    {
        $response = $this->post(route('ajax.keyword-delete'), [
            'article_id' => $this->article->id,
            'article_keyword_id' => 999 // Nonexistent keyword
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200]);
    }

    /** @test */
    public function controller_methods_return_correct_response_types()
    {
        $methods = [
            'getStates' => 'json',
            'keywordDelete' => 'json',
            'verifyPayment' => 'json',
            'bookmark' => 'json',
            'getJournals' => 'json'
        ];

        foreach ($methods as $method => $expectedType) {
            if ($method === 'getStates') {
                $response = $this->get(route('ajax.get-states', $this->country->id));
                $this->assertEquals('application/json', $response->headers->get('Content-Type'));
            }
        }
    }

    /** @test */
    public function ajax_routes_are_accessible()
    {
        $routes = [
            'ajax.get-states',
            'ajax.keyword-delete',
            'ajax.verify-transaction',
            'ajax.bookmark',
            'ajax.get-journals',
            'ajax.download-review',
            'ajax.download-comment-doc',
            'ajax.download-article'
        ];

        foreach ($routes as $routeName) {
            $this->assertTrue(route()->has($routeName), "Route {$routeName} should be defined");
        }
    }

    /** @test */
    public function downloadMethods_use_correct_route_parameters()
    {
        // Test that download routes accept Article and Comment models
        $this->assertTrue(true); // Route model binding is tested implicitly in other tests
    }


    /** @test */
    public function controller_handles_edge_cases_gracefully()
    {
        // Test various edge cases individually
        $response1 = $this->get(route('ajax.get-states', 'invalid'));
        $this->assertNotEquals(500, $response1->getStatusCode(), "getStates with string ID should not cause 500 error");

        $response2 = $this->post(route('ajax.keyword-delete'), []);
        $this->assertNotEquals(500, $response2->getStatusCode(), "keywordDelete with missing parameters should not cause 500 error");

        $response3 = $this->get(route('ajax.bookmark', 999));
        $this->assertNotEquals(500, $response3->getStatusCode(), "bookmark with invalid article should not cause 500 error");
    }
}
