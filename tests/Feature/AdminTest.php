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

        // Create admin role
        $adminRole = \App\Models\Role::firstOrCreate(
            ['title' => 'Admin'],
            ['title' => 'Admin']
        );

        // Create and assign permissions
        $permissions = [
            'user_access', 'user_create',
            'role_access',
            'permission_access',
            'article_category_access', 'article_category_create',
            'article_access',
            'member_access',
            'comment_access',
            'setting_access', 'setting_edit',
            'faq_category_access',
            'faq_question_access',
            'content_page_access',
            'content_category_access',
        ];

        foreach ($permissions as $permission) {
            $perm = \App\Models\Permission::firstOrCreate(['title' => $permission]);
            $adminRole->permissions()->attach($perm->id);
        }

        // Create admin user using Admin model
        $this->admin = \App\Models\Admin::factory()->create();

        // Assign admin role to the user associated with the admin
        // Since Admin extends User and uses same table, this works
        $user = User::find($this->admin->id);
        $user->roles()->attach($adminRole->id);

        // Create initial settings
        \App\Models\Setting::factory()->create();
    }

    /**
     * Test admin dashboard access.
     */
    public function test_admin_can_access_dashboard()
    {
        // Use 'admin' guard
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.home'));


        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /**
     * Test admin can manage users.
     */
    public function test_admin_can_manage_users()
    {
        $response = $this->actingAs($this->admin, 'admin')
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
            'roles' => [\App\Models\Role::first()->id]
        ];

        $response = $this->actingAs($this->admin, 'admin')
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
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    /**
     * Test admin can manage permissions.
     */
    public function test_admin_can_manage_permissions()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
    }

    /**
     * Test admin can manage article categories.
     */
    public function test_admin_can_manage_article_categories()
    {
        $response = $this->actingAs($this->admin, 'admin')
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
            'category_name' => 'Test Category',
            'description' => 'Test category description',
        ];

        $response = $this->actingAs($this->admin, 'admin')
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
        $response = $this->actingAs($this->admin, 'admin')
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

        $response = $this->actingAs($this->admin, 'admin')
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

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }

    /**
     * Test admin can manage settings.
     */
    public function test_admin_can_manage_settings()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertViewHas('setting');
    }

    /**
     * Test admin can update settings.
     */
    public function test_admin_can_update_settings()
    {
        $settingsData = [
            'website_name' => 'Updated Website Name',
            'phone_number' => '1234567890',
            'address' => 'Updated Address',
            'status' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.settings.update'), $settingsData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test admin can manage FAQ categories.
     */
    public function test_admin_can_manage_faq_categories()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.faq-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqCategories');
    }

    /**
     * Test admin can manage FAQ questions.
     */
    public function test_admin_can_manage_faq_questions()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.faq-questions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqQuestions');
    }

    /**
     * Test admin can manage content pages.
     */
    public function test_admin_can_manage_content_pages()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-pages.index'));

        $response->assertStatus(200);
        $response->assertViewHas('contentPages');
    }

    /**
     * Test admin can manage content categories.
     */
    public function test_admin_can_manage_content_categories()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('contentCategories');
    }

    /**
     * Test admin logout functionality.
     */
    public function test_admin_can_logout()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('web');
    }

    /**
     * Test admin cannot access member areas.
     */
    public function test_admin_cannot_access_member_areas()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('member.profile'));

        $response->assertRedirect(route('home'));
    }
}
