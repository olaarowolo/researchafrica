<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;

class AdminWorkflowTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
    }

    /** @test */
    public function admin_can_login_and_access_dashboard()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('Dashboard')
                    ->assertSee('Welcome back');
        });
    }

    /** @test */
    public function admin_can_create_new_article()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'slug' => 'computer-science'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $category) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to articles
            $browser->clickLink('Articles')
                    ->assertSee('Articles Management')
                    ->clickLink('Create New Article')
                    ->assertSee('Create Article');

            // Fill article form
            $browser->type('title', 'Test Article for Browser Testing')
                    ->type('abstract', 'This is a comprehensive test article for browser automation testing.')
                    ->select('category_id', $category->id)
                    ->type('keywords', 'testing, browser, automation')
                    ->type('author_name', 'Test Author')
                    ->type('author_email', 'author@test.com')
                    ->select('status', '1') // Draft
                    ->press('Save Article')
                    ->assertSee('Article created successfully')
                    ->assertSee('Test Article for Browser Testing');
        });
    }

    /** @test */
    public function admin_can_view_and_edit_articles()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        $article = Article::factory()->create([
            'title' => 'Editable Article for Testing',
            'abstract' => 'This article can be edited through the browser interface.',
            'article_status' => 1 // Draft
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $article) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to articles list
            $browser->clickLink('Articles')
                    ->assertSee('Articles Management');

            // Click on article to edit
            $browser->with("tr[data-id='{$article->id}']", function ($row) use ($article) {
                $row->clickLink('Edit');
            });

            // Verify edit form
            $browser->assertPathIs("/admin/articles/{$article->id}/edit")
                    ->assertSee('Edit Article')
                    ->assertInputValue('title', $article->title)
                    ->assertInputValue('abstract', $article->abstract);

            // Update article
            $browser->type('title', 'Updated Article Title')
                    ->press('Update Article')
                    ->assertSee('Article updated successfully')
                    ->assertSee('Updated Article Title');
        });
    }

    /** @test */
    public function admin_can_manage_users()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        $member = Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@test.com',
            'member_type_id' => 1 // Author
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $member) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to members
            $browser->clickLink('Members')
                    ->assertSee('Members Management');

            // Search for specific member
            $browser->type('search', 'john.doe@test.com')
                    ->waitFor('.table tbody tr')
                    ->assertSee('John Doe')
                    ->assertSee('john.doe@test.com');

            // View member details
            $browser->with("tr:contains('john.doe@test.com')", function ($row) {
                $row->clickLink('View');
            });

            $browser->assertSee('Member Details')
                    ->assertSee('John Doe')
                    ->assertSee('john.doe@test.com')
                    ->assertSee('Author');
        });
    }


    /** @test */
    public function admin_can_manage_journals()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Medical Research',
            'slug' => 'medical-research',
            'description' => 'Journal for medical research articles'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $category) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to journals
            $browser->clickLink('Journals')
                    ->assertSee('Journal Management');

            // Create new journal
            $browser->clickLink('Create New Journal')
                    ->assertSee('Create Journal');

            $browser->type('name', 'Advanced Medical Journal')
                    ->type('slug', 'advanced-medical-journal')
                    ->type('description', 'A comprehensive journal for advanced medical research')
                    ->select('status', '1') // Active
                    ->press('Create Journal')
                    ->assertSee('Journal created successfully')
                    ->assertSee('Advanced Medical Journal');
        });
    }

    /** @test */
    public function admin_can_view_system_statistics()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Create some test data
        Article::factory()->count(5)->create(['article_status' => 3]); // Published
        Member::factory()->count(3)->create();

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Check dashboard statistics
            $browser->assertSee('Dashboard')
                    ->assertSee('Total Articles')
                    ->assertSee('Published Articles')
                    ->assertSee('Total Members');

            // Verify statistics display
            $browser->assertSee('5') // Published articles
                    ->assertSee('3'); // Total members
        });
    }

    /** @test */
    public function admin_can_manage_settings()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to settings
            $browser->clickLink('Settings')
                    ->assertSee('System Settings');

            // Update general settings
            $browser->type('site_name', 'Research Africa Platform')
                    ->type('site_description', 'Leading Academic Journal Platform')
                    ->type('contact_email', 'contact@researchafrica.com')
                    ->press('Save Settings')
                    ->assertSee('Settings updated successfully');
        });
    }

    /** @test */
    public function admin_can_logout_successfully()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin/dashboard');

            // Logout
            $browser->clickLink('Logout')
                    ->assertPathIs('/admin/login')
                    ->assertSee('Login')
                    ->assertSee('Email');
        });
    }

    /** @test */
    public function admin_cannot_access_member_area()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            // Login as admin
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin/dashboard');

            // Try to access member area
            $browser->visit('/member/dashboard')
                    ->assertSee('403') // Forbidden
                    ->assertSee('Access Denied');
        });
    }

    /** @test */
    public function admin_can_filter_articles_by_status()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        // Create articles with different statuses
        $draftArticle = Article::factory()->create([
            'title' => 'Draft Article',
            'article_status' => 1 // Draft
        ]);

        $publishedArticle = Article::factory()->create([
            'title' => 'Published Article',
            'article_status' => 3 // Published
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $draftArticle, $publishedArticle) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to articles
            $browser->clickLink('Articles')
                    ->assertSee('Articles Management');

            // Filter by Draft status
            $browser->select('status_filter', '1')
                    ->waitFor('.table tbody tr')
                    ->assertSee('Draft Article')
                    ->assertDontSee('Published Article');

            // Filter by Published status
            $browser->select('status_filter', '3')
                    ->waitFor('.table tbody tr')
                    ->assertSee('Published Article')
                    ->assertDontSee('Draft Article');
        });
    }

    /** @test */
    public function admin_can_search_articles()
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@researchafrica.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);

        $article1 = Article::factory()->create([
            'title' => 'Machine Learning in Healthcare',
            'abstract' => 'This article discusses ML applications in medical fields.'
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Database Design Principles',
            'abstract' => 'Comprehensive guide to database architecture.'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin, $article1, $article2) {
            // Login
            $browser->visit('/admin')
                    ->type('email', $admin->email)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to articles
            $browser->clickLink('Articles')
                    ->assertSee('Articles Management');

            // Search for specific article
            $browser->type('search', 'Machine Learning')
                    ->waitFor('.table tbody tr')
                    ->assertSee('Machine Learning in Healthcare')
                    ->assertDontSee('Database Design Principles');

            // Clear search and verify both articles
            $browser->clear('search')
                    ->waitFor('.table tbody tr')
                    ->assertSee('Machine Learning in Healthcare')
                    ->assertSee('Database Design Principles');
        });
    }
}

