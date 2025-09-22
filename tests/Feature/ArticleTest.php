<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->member = Member::factory()->create();
    }

    /**
     * Test admin can create article.
     */
    public function test_admin_can_create_article()
    {
        $category = ArticleCategory::factory()->create();

        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
            'article_category_id' => $category->id,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.articles.store'), $articleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
        ]);
    }

    /**
     * Test admin can view articles.
     */
    public function test_admin_can_view_articles()
    {
        Article::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    /**
     * Test admin can edit article.
     */
    public function test_admin_can_edit_article()
    {
        $article = Article::factory()->create();
        $category = ArticleCategory::factory()->create();

        $updatedData = [
            'title' => 'Updated Article Title',
            'content' => 'Updated article content.',
            'article_category_id' => $category->id,
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.articles.update', $article->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Updated Article Title',
        ]);
    }

    /**
     * Test admin can delete article.
     */
    public function test_admin_can_delete_article()
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.articles.destroy', $article->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    /**
     * Test member can view published articles.
     */
    public function test_member_can_view_published_articles()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-article', $article->id));

        $response->assertStatus(200);
        $response->assertViewHas('article');
    }

    /**
     * Test member cannot view draft articles.
     */
    public function test_member_cannot_view_draft_articles()
    {
        $article = Article::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-article', $article->id));

        $response->assertStatus(403);
    }

    /**
     * Test article search functionality.
     */
    public function test_article_search_functionality()
    {
        Article::factory()->create(['title' => 'Laravel Testing']);
        Article::factory()->create(['title' => 'PHP Development']);
        Article::factory()->create(['title' => 'JavaScript Basics']);

        $response = $this->get(route('member.search') . '?query=laravel');

        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    /**
     * Test article category filtering.
     */
    public function test_article_category_filtering()
    {
        $category = ArticleCategory::factory()->create(['name' => 'Technology']);
        Article::factory()->count(3)->create(['article_category_id' => $category->id]);

        $response = $this->get(route('member.journal', $category->id));

        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    /**
     * Test article bookmark functionality.
     */
    public function test_article_bookmark_functionality()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('bookmark', $article->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookmarks', [
            'member_id' => $this->member->id,
            'article_id' => $article->id,
        ]);
    }

    /**
     * Test article download functionality.
     */
    public function test_article_download_functionality()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('download-article', $article->id));

        $response->assertStatus(200);
    }

    /**
     * Test article comment functionality.
     */
    public function test_article_comment_functionality()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $commentData = [
            'content' => 'This is a test comment.',
            'article_id' => $article->id,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('comments.store'), $commentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment.',
        ]);
    }

    /**
     * Test admin can manage article categories.
     */
    public function test_admin_can_manage_article_categories()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.article-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('articleCategories');
    }

    /**
     * Test admin can create article category.
     */
    public function test_admin_can_create_article_category()
    {
        $categoryData = [
            'name' => 'New Category',
            'description' => 'New category description',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.article-categories.store'), $categoryData);

        $response->assertRedirect();
        $this->assertDatabaseHas('article_categories', [
            'name' => 'New Category',
        ]);
    }
}
