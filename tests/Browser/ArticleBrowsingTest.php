<?php
namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Comment;
use App\Models\Member;

class ArticleBrowsingTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
    }

    /** @test */
    public function public_user_can_view_homepage()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Research Africa')
                    ->assertSee('Leading Academic Journal Platform')
                    ->assertSee('Browse Articles')
                    ->assertSee('Journals');
        });
    }

    /** @test */
    public function public_user_can_browse_published_articles()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'slug' => 'computer-science'
        ]);

        $article1 = Article::factory()->create([
            'title' => 'Machine Learning in Healthcare',
            'abstract' => 'This article explores ML applications in medical fields.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(5)
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Database Optimization Techniques',
            'abstract' => 'Advanced database performance optimization strategies.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(2)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                    ->assertSee('Published Articles')
                    ->assertSee('Machine Learning in Healthcare')
                    ->assertSee('Database Optimization Techniques')
                    ->assertSee('View Article')
                    ->assertSee('Download PDF');
        });
    }

    /** @test */
    public function public_user_can_view_article_details()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Medicine',
            'slug' => 'medicine'
        ]);

        $article = Article::factory()->create([
            'title' => 'Cardiovascular Disease Prevention',
            'abstract' => 'A comprehensive study on preventing heart disease.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'author_name' => 'Dr. Sarah Johnson',
            'author_email' => 'sarah.johnson@university.edu',
            'published_at' => now()->subDays(10)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/articles/{$article->slug}")
                    ->assertSee('Cardiovascular Disease Prevention')
                    ->assertSee('Dr. Sarah Johnson')
                    ->assertSee('A comprehensive study on preventing heart disease.')
                    ->assertSee('Download PDF')
                    ->assertSee('Related Articles');
        });
    }

    /** @test */
    public function public_user_can_search_articles()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Engineering',
            'slug' => 'engineering'
        ]);

        $article1 = Article::factory()->create([
            'title' => 'Solar Energy Systems',
            'abstract' => 'Renewable energy from solar panels.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(7)
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Wind Turbine Design',
            'abstract' => 'Efficient wind energy conversion systems.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(3)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                    ->type('search', 'Solar Energy')
                    ->press('Search')
                    ->assertSee('Solar Energy Systems')
                    ->assertDontSee('Wind Turbine Design');
        });
    }

    /** @test */
    public function public_user_can_filter_articles_by_category()
    {
        // Arrange
        $category1 = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'slug' => 'computer-science'
        ]);

        $category2 = ArticleCategory::factory()->create([
            'name' => 'Medicine',
            'slug' => 'medicine'
        ]);

        $article1 = Article::factory()->create([
            'title' => 'AI Algorithms',
            'article_category_id' => $category1->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(5)
        ]);

        $article2 = Article::factory()->create([
            'title' => 'Medical Imaging',
            'article_category_id' => $category2->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(2)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($category1, $category2) {
            $browser->visit('/articles')
                    ->select('category', $category1->id)
                    ->waitFor('.article-card')
                    ->assertSee('AI Algorithms')
                    ->assertDontSee('Medical Imaging');

            // Switch to Medicine category
            $browser->select('category', $category2->id)
                    ->waitFor('.article-card')
                    ->assertSee('Medical Imaging')
                    ->assertDontSee('AI Algorithms');
        });
    }

    /** @test */
    public function public_user_can_comment_on_articles()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'Research Article for Comments',
            'article_status' => 3, // Published
            'allow_comments' => true
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/articles/{$article->slug}")
                    ->scrollTo('.comments-section')
                    ->assertSee('Comments')
                    ->assertSee('Leave a Comment');

            // Fill comment form
            $browser->type('name', 'John Commenter')
                    ->type('email', 'john@example.com')
                    ->type('comment', 'This is an excellent research article. Very informative!')
                    ->press('Submit Comment')
                    ->assertSee('Comment submitted successfully')
                    ->assertSee('John Commenter')
                    ->assertSee('This is an excellent research article. Very informative!');
        });
    }

    /** @test */
    public function public_user_can_browse_journals()
    {
        // Arrange
        $category1 = ArticleCategory::factory()->create([
            'name' => 'Journal of Computer Science',
            'slug' => 'journal-computer-science',
            'description' => 'Leading publication in computer science research'
        ]);

        $category2 = ArticleCategory::factory()->create([
            'name' => 'Medical Research Quarterly',
            'slug' => 'medical-research-quarterly',
            'description' => 'Comprehensive medical research publication'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/journals')
                    ->assertSee('Academic Journals')
                    ->assertSee('Journal of Computer Science')
                    ->assertSee('Leading publication in computer science research')
                    ->assertSee('Medical Research Quarterly')
                    ->assertSee('Comprehensive medical research publication')
                    ->assertSee('View Journal')
                    ->assertSee('Browse Articles');
        });
    }

    /** @test */
    public function public_user_can_view_specific_journal()
    {
        // Arrange
        $journal = ArticleCategory::factory()->create([
            'name' => 'Environmental Science Journal',
            'slug' => 'environmental-science-journal',
            'description' => 'Peer-reviewed environmental research',
            'issn' => '1234-5678'
        ]);

        // Create articles for this journal
        Article::factory()->count(3)->create([
            'article_category_id' => $journal->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(10)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($journal) {
            $browser->visit("/journals/{$journal->slug}")
                    ->assertSee('Environmental Science Journal')
                    ->assertSee('Peer-reviewed environmental research')
                    ->assertSee('ISSN: 1234-5678')
                    ->assertSee('Published Articles')
                    ->assertSee('View Article')
                    ->waitFor('.article-card', 2);
        });
    }

    /** @test */
    public function public_user_can_download_articles()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'Downloadable Research Article',
            'article_status' => 3, // Published
            'pdf_file_path' => 'test-article.pdf'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/articles/{$article->slug}")
                    ->assertSee('Download PDF')
                    ->clickLink('Download PDF')
                    ->assertSee('Download started')
                    ->assertSee('Thank you for downloading');
        });
    }

    /** @test */
    public function public_user_can_view_about_page()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/about')
                    ->assertSee('About Research Africa')
                    ->assertSee('Our Mission')
                    ->assertSee('Our Team')
                    ->assertSee('Contact Information')
                    ->assertSee('Leading Academic Journal Platform');
        });
    }

    /** @test */
    public function public_user_can_view_contact_page()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->assertSee('Contact Us')
                    ->assertSee('Get in Touch')
                    ->assertSee('Name')
                    ->assertSee('Email')
                    ->assertSee('Message')
                    ->assertSee('Send Message');
        });
    }

    /** @test */
    public function public_user_can_submit_contact_form()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->type('name', 'Jane Researcher')
                    ->type('email', 'jane.researcher@university.edu')
                    ->type('subject', 'Submission Inquiry')
                    ->type('message', 'I would like to inquire about article submission guidelines.')
                    ->press('Send Message')
                    ->assertSee('Message sent successfully')
                    ->assertSee('Thank you for contacting us');
        });
    }

    /** @test */
    public function public_user_can_browse_articles_with_pagination()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'General Science']);

        // Create 15 articles
        for ($i = 1; $i <= 15; $i++) {
            Article::factory()->create([
                'title' => "Science Article {$i}",
                'article_category_id' => $category->id,
                'article_status' => 3, // Published
                'published_at' => now()->subDays($i)
            ]);
        }

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                    ->waitFor('.article-card')
                    ->assertSee('Science Article 1')
                    ->assertSee('Science Article 2')
                    ->assertSee('Science Article 3');

            // Check pagination
            $browser->scrollTo('.pagination')
                    ->assertSee('2') // Page 2
                    ->clickLink('2')
                    ->waitFor('.article-card')
                    ->assertSee('Science Article 11')
                    ->assertSee('Science Article 12')
                    ->assertSee('Science Article 13');
        });
    }

    /** @test */
    public function public_user_can_view_related_articles()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Technology',
            'slug' => 'technology'
        ]);

        $mainArticle = Article::factory()->create([
            'title' => 'Main Technology Article',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(5)
        ]);

        $relatedArticle1 = Article::factory()->create([
            'title' => 'Related Technology Article 1',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(3)
        ]);

        $relatedArticle2 = Article::factory()->create([
            'title' => 'Related Technology Article 2',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'published_at' => now()->subDays(1)
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($mainArticle) {
            $browser->visit("/articles/{$mainArticle->slug}")
                    ->scrollTo('.related-articles')
                    ->assertSee('Related Articles')
                    ->assertSee('Related Technology Article 1')
                    ->assertSee('Related Technology Article 2')
                    ->assertSee('Read More');
        });
    }

    /** @test */
    public function public_user_can_view_footer_links()
    {
        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->scrollTo('.footer')
                    ->assertSee('Quick Links')
                    ->assertSee('Browse Articles')
                    ->assertSee('Journals')
                    ->assertSee('About')
                    ->assertSee('Contact')
                    ->assertSee('Privacy Policy')
                    ->assertSee('Terms of Service')
                    ->assertSee('Copyright');
        });
    }

    /** @test */
    public function public_user_sees_correct_meta_tags()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'SEO Optimized Article',
            'abstract' => 'This article has optimized meta tags for search engines.',
            'article_status' => 3, // Published
            'keywords' => 'SEO, meta tags, search engines'
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/articles/{$article->slug}")
                    ->assertTitleContains('SEO Optimized Article')
                    ->assertSeeIn('meta[name="description"]', 'This article has optimized meta tags');
        });
    }

    /** @test */
    public function public_user_can_navigate_breadcrumbs()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Research Categories',
            'slug' => 'research-categories'
        ]);

        $article = Article::factory()->create([
            'title' => 'Breadcrumb Navigation Test',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/articles/{$article->slug}")
                    ->scrollTo('.breadcrumb')
                    ->assertSee('Home')
                    ->assertSee('Articles')
                    ->assertSee('Research Categories')
                    ->assertSee('Breadcrumb Navigation Test')
                    ->clickLink('Articles')
                    ->assertPathIs('/articles');
        });
    }
}

