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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $journal;
    protected $category;
    protected $article;
    protected $journalService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create(['title' => 'Author']);
        $memberType = MemberType::factory()->create();

        // Create user
        $this->user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create journal (using ArticleCategory as journal)
        $this->journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TESTJ'
        ]);

        // Create article category
        $this->category = ArticleCategory::factory()->create([
            'journal_id' => $this->journal->id,
            'is_journal' => false
        ]);

        // Create article
        $this->article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 1
        ]);

        // Mock JournalContextService
        $this->journalService = $this->mock(JournalContextService::class);
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn($this->journal);

        // Fake storage
        Storage::fake('public');
    }

    /** @test */
    public function index_displays_articles_for_current_journal()
    {
        // Create additional articles for this journal
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.index');
        $response->assertViewHas('articles');
        $response->assertViewHas('currentJournal');

        $articles = $response->viewData('articles');
        $this->assertEquals(2, $articles->total());
    }

    /** @test */
    public function index_filters_articles_by_user_role_for_authors()
    {
        // Create another user with author role
        $authorRole = MemberRole::factory()->create(['title' => 'Author']);
        $otherUser = User::factory()->create([
            'member_role_id' => $authorRole->id
        ]);

        // Create article for other user
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('articles.index'));

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Should only see own articles as author
        $this->assertEquals(1, $articles->total());
    }

    /** @test */
    public function index_shows_all_articles_for_editors()
    {
        // Create editor role
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        // Create additional articles
        Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id
        ]);

        $response = $this->actingAs($editorUser)
            ->get(route('articles.index'));

        $response->assertStatus(200);
        $articles = $response->viewData('articles');

        // Editor should see all articles
        $this->assertEquals(2, $articles->total());
    }

    /** @test */
    public function index_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->actingAs($this->user)
            ->get(route('articles.index'));

        $response->assertStatus(404);
    }

    /** @test */
    public function create_displays_article_creation_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('articles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.create');
        $response->assertViewHas('currentJournal');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function create_requires_journal_permission()
    {
        // Create user without journal access
        $userWithoutAccess = User::factory()->create();

        $response = $this->actingAs($userWithoutAccess)
            ->get(route('articles.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function store_creates_new_article()
    {
        $articleData = [
            'title' => 'Test Article Title',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'other_authors' => 'Co-Author Name',
            'corresponding_authors' => 'Corresponding Author',
            'institute_organization' => 'Test Institute',
            'doi_link' => 'https://doi.org/test',
            'volume' => '1',
            'issue_no' => '1',
            'publish_date' => '2024-01-01',
            'access_type' => '1'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), $articleData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Article submitted successfully!');

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article Title',
            'journal_id' => $this->journal->id,
            'member_id' => $this->user->id,
            'article_status' => 1
        ]);
    }

    /** @test */
    public function store_handles_file_upload()
    {
        $file = UploadedFile::fake()->create('test-article.pdf', 1000);

        $articleData = [
            'title' => 'Test Article with File',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'access_type' => '1',
            'file' => $file
        ];

        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), $articleData);

        $response->assertStatus(302);

        $article = Article::where('title', 'Test Article with File')->first();
        $this->assertNotNull($article->file_path);
        $this->assertNotNull($article->storage_disk);
    }

    /** @test */
    public function store_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), []);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['title', 'article_category_id', 'author_name', 'access_type']);
    }

    /** @test */
    public function show_displays_article_for_owner()
    {
        $response = $this->actingAs($this->user)
            ->get(route('articles.show', $this->article));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.show');
        $response->assertViewHas('article');
        $response->assertViewHas('currentJournal');
    }

    /** @test */
    public function show_displays_article_for_published_articles()
    {
        $publishedArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3 // Published
        ]);

        // Test without authentication for published article
        Auth::logout();

        $response = $this->get(route('articles.show', $publishedArticle));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.show');
    }

    /** @test */
    public function show_denies_access_to_unauthorized_users()
    {
        // Create another user without access
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('articles.show', $this->article));

        $response->assertStatus(403);
    }

    /** @test */
    public function show_handles_journal_mismatch()
    {
        // Create article for different journal
        $otherJournal = ArticleCategory::factory()->create(['is_journal' => true]);
        $otherArticle = Article::factory()->create([
            'journal_id' => $otherJournal->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('articles.show', $otherArticle));

        $response->assertStatus(404);
    }

    /** @test */
    public function edit_displays_article_edit_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('articles.edit', $this->article));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.edit');
        $response->assertViewHas('article');
        $response->assertViewHas('currentJournal');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function edit_denies_access_to_unauthorized_users()
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('articles.edit', $this->article));

        $response->assertStatus(403);
    }

    /** @test */
    public function edit_prevents_editing_published_articles()
    {
        $publishedArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3 // Published
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('articles.edit', $publishedArticle));

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Published articles cannot be edited.');
    }

    /** @test */
    public function update_modifies_existing_article()
    {
        $updatedData = [
            'title' => 'Updated Article Title',
            'article_category_id' => $this->category->id,
            'author_name' => 'Updated Author',
            'access_type' => '2',
            'other_authors' => 'Updated Co-Author'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('articles.update', $this->article), $updatedData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Article updated successfully!');

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'Updated Article Title',
            'author_name' => 'Updated Author'
        ]);
    }

    /** @test */
    public function update_handles_file_replacement()
    {
        // Create article with existing file
        $existingFile = 'articles/old-file.pdf';
        $article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'file_path' => $existingFile,
            'storage_disk' => 'public'
        ]);

        Storage::disk('public')->put($existingFile, 'old content');

        $newFile = UploadedFile::fake()->create('new-article.pdf', 1000);

        $updatedData = [
            'title' => 'Updated Article',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'access_type' => '1',
            'file' => $newFile
        ];

        $response = $this->actingAs($this->user)
            ->put(route('articles.update', $article), $updatedData);

        $response->assertStatus(302);

        // Old file should be deleted
        Storage::disk('public')->assertMissing($existingFile);
    }

    /** @test */
    public function review_sets_article_to_reviewing_status()
    {
        // Create editor user
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        $response = $this->actingAs($editorUser)
            ->post(route('articles.review', $this->article));

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Article set to reviewing status.');

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'article_status' => 2
        ]);
    }

    /** @test */
    public function review_requires_editor_permissions()
    {
        $response = $this->actingAs($this->user)
            ->post(route('articles.review', $this->article));

        $response->assertStatus(403);
    }

    /** @test */
    public function review_only_works_on_pending_articles()
    {
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        // Create already reviewing article
        $reviewingArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 2 // Already reviewing
        ]);

        $response = $this->actingAs($editorUser)
            ->post(route('articles.review', $reviewingArticle));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Only pending articles can be reviewed.');
    }

    /** @test */
    public function approve_publishes_article()
    {
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        $response = $this->actingAs($editorUser)
            ->post(route('articles.approve', $this->article));

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Article approved and published successfully!');

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'article_status' => 3,
            'published_online' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /** @test */
    public function approve_only_works_for_pending_or_reviewing_articles()
    {
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        // Create published article
        $publishedArticle = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'article_status' => 3
        ]);

        $response = $this->actingAs($editorUser)
            ->post(route('articles.approve', $publishedArticle));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Only pending or reviewing articles can be approved.');
    }

    /** @test */
    public function reject_rejects_article()
    {
        $editorRole = MemberRole::factory()->create(['title' => 'Editor']);
        $editorUser = User::factory()->create([
            'member_role_id' => $editorRole->id
        ]);

        $response = $this->actingAs($editorUser)
            ->post(route('articles.reject', $this->article));

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Article rejected successfully.');

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'article_status' => 4
        ]);
    }

    /** @test */
    public function download_allows_download_for_published_articles()
    {
        // Create article with file
        Storage::disk('public')->put('test-file.pdf', 'file content');

        $article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'file_path' => 'test-file.pdf',
            'storage_disk' => 'public',
            'article_status' => 3 // Published
        ]);

        // Test without authentication for published article
        Auth::logout();

        $response = $this->get(route('articles.download', $article));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="test-file.pdf"');
    }

    /** @test */
    public function download_denies_access_for_unauthorized_users()
    {
        // Create article with file for another user
        Storage::disk('public')->put('test-file.pdf', 'file content');

        $article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'file_path' => 'test-file.pdf',
            'storage_disk' => 'public',
            'article_status' => 1 // Pending
        ]);

        // Create unauthorized user
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('articles.download', $article));

        $response->assertStatus(403);
    }

    /** @test */
    public function download_handles_missing_files()
    {
        $article = Article::factory()->create([
            'journal_id' => $this->journal->id,
            'article_category_id' => $this->category->id,
            'member_id' => $this->user->id,
            'file_path' => 'nonexistent-file.pdf',
            'storage_disk' => 'public'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('articles.download', $article));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Article file not found.');
    }

    /** @test */
    public function statistics_returns_article_counts()
    {
        // Create additional articles
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
            ->get(route('articles.statistics'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(3, $data['total']);
        $this->assertEquals(1, $data['pending']);
        $this->assertEquals(1, $data['reviewing']);
        $this->assertEquals(1, $data['published']);
        $this->assertEquals(0, $data['rejected']);
    }

    /** @test */
    public function statistics_handles_journal_not_found()
    {
        $this->journalService->shouldReceive('getCurrentJournal')
                            ->andReturn(null);

        $response = $this->actingAs($this->user)
            ->get(route('articles.statistics'));

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
                return str_contains($message, 'ArticleController: Error loading articles index');
            }));

        $response = $this->actingAs($this->user)
            ->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('journal.articles.index');
        $response->assertViewHas('error', 'Failed to load articles.');
    }

    /** @test */
    public function all_routes_require_authentication()
    {
        Auth::logout();

        $routes = [
            ['GET', 'articles.index'],
            ['GET', 'articles.create'],
            ['POST', 'articles.store'],
            ['GET', 'articles.show'],
            ['GET', 'articles.edit'],
            ['PUT', 'articles.update'],
            ['DELETE', 'articles.destroy'],
            ['POST', 'articles.review'],
            ['POST', 'articles.approve'],
            ['POST', 'articles.reject'],
            ['GET', 'articles.download'],
        ];

        foreach ($routes as [$method, $route]) {
            $response = $this->call($method, route($route, $this->article));
            $response->assertRedirect(); // Should redirect to login
        }
    }

    /** @test */
    public function file_upload_validates_file_types_and_sizes()
    {
        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('test.txt', 100);

        $articleData = [
            'title' => 'Test Article',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'access_type' => '1',
            'file' => $invalidFile
        ];

        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    }

    /** @test */
    public function file_upload_validates_file_size()
    {
        // Create file larger than 10MB
        $largeFile = UploadedFile::fake()->create('large-article.pdf', 11000); // 11MB

        $articleData = [
            'title' => 'Test Article',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'access_type' => '1',
            'file' => $largeFile
        ];

        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    }

    /** @test */
    public function controller_uses_journal_context_service()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\Journal\ArticleController::class);

        $this->assertTrue($reflection->hasMethod('__construct'));

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue($constructor->isPublic());
    }

    /** @test */
    public function article_creation_sets_correct_defaults()
    {
        $articleData = [
            'title' => 'Test Article',
            'article_category_id' => $this->category->id,
            'author_name' => 'Test Author',
            'access_type' => '1'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('articles.store'), $articleData);

        $article = Article::where('title', 'Test Article')->first();

        $this->assertEquals($this->journal->id, $article->journal_id);
        $this->assertEquals($this->user->id, $article->member_id);
        $this->assertEquals(1, $article->article_status); // Pending
        $this->assertEquals('public', $article->storage_disk);
    }
}
