<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Member;
use App\Models\ArticleCategory;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test admin dashboard access.
     */
    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /**
     * Test admin can manage users.
     */
    public function test_admin_can_manage_users()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    /**
     * Test admin can create user.
     */
    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'Test Admin User',
            'email' => 'testadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'testadmin@test.com',
        ]);
    }

    /**
     * Test admin can manage roles.
     */
    public function test_admin_can_manage_roles()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    /**
     * Test admin can manage permissions.
     */
    public function test_admin_can_manage_permissions()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
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
            'name' => 'Test Category',
            'description' => 'Test category description',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.article-categories.store'), $categoryData);

        $response->assertRedirect();
        $this->assertDatabaseHas('article_categories', [
            'name' => 'Test Category',
        ]);
    }

    /**
     * Test admin can manage articles.
     */
    public function test_admin_can_manage_articles()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    /**
     * Test admin can manage members.
     */
    public function test_admin_can_manage_members()
    {
        Member::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.members.index'));

        $response->assertStatus(200);
        $response->assertViewHas('members');
    }

    /**
     * Test admin can manage comments.
     */
    public function test_admin_can_manage_comments()
    {
        Comment::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }

    /**
     * Test admin can manage settings.
     */
    public function test_admin_can_manage_settings()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertViewHas('settings');
    }

    /**
     * Test admin can update settings.
     */
    public function test_admin_can_update_settings()
    {
        $settingsData = [
            'app_name' => 'Updated App Name',
            'app_description' => 'Updated app description',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $settingsData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test admin can manage FAQ categories.
     */
    public function test_admin_can_manage_faq_categories()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqCategories');
    }

    /**
     * Test admin can manage FAQ questions.
     */
    public function test_admin_can_manage_faq_questions()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq-questions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqQuestions');
    }

    /**
     * Test admin can manage content pages.
     */
    public function test_admin_can_manage_content_pages()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.content-pages.index'));

        $response->assertStatus(200);
        $response->assertViewHas('contentPages');
    }

    /**
     * Test admin can manage content categories.
     */
    public function test_admin_can_manage_content_categories()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.content-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('contentCategories');
    }

    /**
     * Test admin logout functionality.
     */
    public function test_admin_can_logout()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest('web');
    }

    /**
     * Test admin cannot access member areas.
     */
    public function test_admin_cannot_access_member_areas()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('member.profile'));

        $response->assertRedirect(route('admin.login'));
    }
}
