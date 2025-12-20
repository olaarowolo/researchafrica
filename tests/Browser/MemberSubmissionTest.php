<?php

namespace Tests\Browser;


use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Member;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticleMail;

class MemberSubmissionTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
    }

    /** @test */
    public function member_can_register_successfully()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/member/register')
                    ->assertSee('Register')
                    ->assertSee('Create Account')
                    ->type('first_name', 'John')
                    ->type('last_name', 'Doe')
                    ->type('email_address', 'john.doe@example.com')
                    ->type('phone', '+234123456789')
                    ->select('country_id', 1)
                    ->select('member_type_id', 1) // Author
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->check('terms')
                    ->press('Register')
                    ->assertSee('Registration successful')
                    ->assertSee('Please verify your email address');
        });
    }

    /** @test */
    public function member_can_login_and_access_dashboard()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email_address' => 'jane.smith@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            $browser->visit('/member/login')
                    ->assertSee('Login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/member/dashboard')
                    ->assertSee('Dashboard')
                    ->assertSee('Welcome back, Jane Smith');
        });
    }

    /** @test */
    public function member_can_submit_new_article()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email_address' => 'alice.johnson@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'slug' => 'computer-science'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member, $category) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to submit article
            $browser->clickLink('Submit Article')
                    ->assertSee('Submit New Article');

            // Fill article submission form
            $browser->type('title', 'Advanced Machine Learning Techniques')
                    ->type('abstract', 'This research explores cutting-edge machine learning algorithms and their applications in healthcare diagnostics.')
                    ->select('category_id', $category->id)
                    ->type('keywords', 'machine learning, healthcare, algorithms, diagnostics')
                    ->type('author_name', 'Dr. Alice Johnson')
                    ->type('author_email', $member->email_address)
                    ->type('author_affiliation', 'University of Technology')
                    ->type('author_bio', 'Dr. Alice Johnson is a leading researcher in machine learning applications.')
                    ->attach('pdf_file', __DIR__ . '/../files/test-article.pdf')
                    ->press('Submit Article')
                    ->assertSee('Article submitted successfully')
                    ->assertSee('Thank you for your submission');
        });
    }

    /** @test */
    public function member_can_view_submitted_articles()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email_address' => 'bob.wilson@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $article = Article::factory()->create([
            'title' => 'My Research Article',
            'author_name' => 'Bob Wilson',
            'author_email' => $member->email_address,
            'article_status' => 2 // Under Review
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member, $article) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to my articles
            $browser->clickLink('My Articles')
                    ->assertSee('My Articles')
                    ->assertSee('My Research Article')
                    ->assertSee('Under Review')
                    ->assertSee('View Details');

            // View article details
            $browser->clickLink('View Details')
                    ->assertSee('Article Details')
                    ->assertSee('My Research Article')
                    ->assertSee('Status: Under Review')
                    ->assertSee('Bob Wilson');
        });
    }

    /** @test */
    public function member_can_edit_draft_article()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Carol',
            'last_name' => 'Brown',
            'email_address' => 'carol.brown@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $article = Article::factory()->create([
            'title' => 'Draft Article for Editing',
            'abstract' => 'This is a draft article that needs editing.',
            'author_name' => 'Carol Brown',
            'author_email' => $member->email_address,
            'article_status' => 1 // Draft
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member, $article) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to my articles
            $browser->clickLink('My Articles')
                    ->assertSee('Draft Article for Editing')
                    ->clickLink('Edit');

            // Edit article
            $browser->assertPathIs("/member/articles/{$article->id}/edit")
                    ->assertInputValue('title', 'Draft Article for Editing')
                    ->type('title', 'Updated Draft Article')
                    ->type('abstract', 'This article has been updated with new content.')
                    ->press('Update Article')
                    ->assertSee('Article updated successfully')
                    ->assertSee('Updated Draft Article');
        });
    }

    /** @test */
    public function member_can_view_article_status_history()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'David',
            'last_name' => 'Lee',
            'email_address' => 'david.lee@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $article = Article::factory()->create([
            'title' => 'Research Article with History',
            'author_name' => 'David Lee',
            'author_email' => $member->email_address,
            'article_status' => 3 // Published
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member, $article) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to my articles
            $browser->clickLink('My Articles')
                    ->clickLink('View Details')
                    ->assertSee('Status History')
                    ->assertSee('Published')
                    ->assertSee('View Comments');
        });
    }

    /** @test */
    public function member_can_view_and_manage_profile()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Emma',
            'last_name' => 'Davis',
            'email_address' => 'emma.davis@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1, // Author
            'phone' => '+234123456789',
            'institution' => 'Research University'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to profile
            $browser->clickLink('Profile')
                    ->assertSee('Profile Management')
                    ->assertInputValue('first_name', 'Emma')
                    ->assertInputValue('last_name', 'Davis')
                    ->assertInputValue('email_address', 'emma.davis@example.com')
                    ->assertInputValue('phone', '+234123456789')
                    ->assertInputValue('institution', 'Research University');

            // Update profile
            $browser->type('phone', '+234987654321')
                    ->type('institution', 'Advanced Research Institute')
                    ->press('Update Profile')
                    ->assertSee('Profile updated successfully');
        });
    }

    /** @test */
    public function member_can_change_password()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Frank',
            'last_name' => 'Miller',
            'email_address' => 'frank.miller@example.com',
            'password' => bcrypt('oldpassword123'),
            'member_type_id' => 1 // Author
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'oldpassword123')
                    ->press('Login');

            // Navigate to security settings
            $browser->clickLink('Security')
                    ->assertSee('Security Settings')
                    ->type('current_password', 'oldpassword123')
                    ->type('new_password', 'newpassword123')
                    ->type('new_password_confirmation', 'newpassword123')
                    ->press('Change Password')
                    ->assertSee('Password changed successfully');

            // Verify new password works
            $browser->clickLink('Logout')
                    ->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'newpassword123')
                    ->press('Login')
                    ->assertPathIs('/member/dashboard');
        });
    }

    /** @test */
    public function member_cannot_submit_article_without_required_fields()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Grace',
            'last_name' => 'Taylor',
            'email_address' => 'grace.taylor@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to submit article
            $browser->clickLink('Submit Article')
                    ->press('Submit Article')
                    ->assertSee('The title field is required')
                    ->assertSee('The abstract field is required')
                    ->assertSee('The category_id field is required');
        });
    }

    /** @test */
    public function member_can_view_published_articles()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Henry',
            'last_name' => 'Anderson',
            'email_address' => 'henry.anderson@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $category = ArticleCategory::factory()->create(['name' => 'Science']);


        // Create published articles
        $article1 = Article::factory()->create([
            'title' => 'Published Research 1',
            'abstract' => 'First published article.',
            'article_category_id' => $category->id,
            'article_status' => 3 // Published
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Published Research 2',
            'abstract' => 'Second published article.',
            'article_category_id' => $category->id,
            'article_status' => 3 // Published
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Navigate to published articles
            $browser->clickLink('Published Articles')
                    ->assertSee('Published Articles')
                    ->assertSee('Published Research 1')
                    ->assertSee('Published Research 2')
                    ->assertSee('View Article')
                    ->assertSee('Download PDF');
        });
    }

    /** @test */
    public function member_can_logout_successfully()
    {
        // Arrange
        $member = Member::factory()->create([
            'first_name' => 'Ivy',
            'last_name' => 'Thompson',
            'email_address' => 'ivy.thompson@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/member/dashboard');

            // Logout
            $browser->clickLink('Logout')
                    ->assertPathIs('/member/login')
                    ->assertSee('Login')
                    ->assertSee('Email Address');
        });
    }

    /** @test */
    public function member_receives_email_confirmation_on_submission()
    {
        // Arrange
        Mail::fake();

        $member = Member::factory()->create([
            'first_name' => 'Jack',
            'last_name' => 'White',
            'email_address' => 'jack.white@example.com',
            'password' => bcrypt('password123'),
            'member_type_id' => 1 // Author
        ]);

        $category = ArticleCategory::factory()->create(['name' => 'Technology']);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($member, $category) {
            // Login
            $browser->visit('/member/login')
                    ->type('email_address', $member->email_address)
                    ->type('password', 'password123')
                    ->press('Login');

            // Submit article
            $browser->clickLink('Submit Article')
                    ->type('title', 'Technology Research Article')
                    ->type('abstract', 'This is a technology research article.')
                    ->select('category_id', $category->id)
                    ->type('author_name', 'Jack White')
                    ->type('author_email', $member->email_address)
                    ->press('Submit Article')
                    ->assertSee('Article submitted successfully');

            // Verify email was sent
            Mail::assertSent(ArticleMail::class, function ($mail) use ($member) {
                return $mail->full_name === 'Jack White';
            });
        });
    }
}

