<?php

namespace Tests\Helpers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\Admin;
use App\Models\EditorialWorkflow;
use App\Models\ArticleEditorialProgress;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class TestHelpers
{
    /**
     * Create a complete test article with all related data
     */
    public static function createCompleteArticle(array $overrides = []): Article
    {
        $category = ArticleCategory::factory()->create();

        $articleData = array_merge([
            'title' => 'Test Article ' . uniqid(),
            'abstract' => 'This is a test abstract for testing purposes.',
            'article_category_id' => $category->id,
            'article_status' => 3, // Published
            'author_name' => 'Test Author',
            'author_email' => 'author@test.com',
            'published_at' => now(),
        ], $overrides);

        return Article::factory()->create($articleData);
    }

    /**
     * Create a complete test member with all required relationships
     */
    public static function createCompleteMember(array $overrides = [])
    {
        $memberType = \App\Models\MemberType::factory()->create();
        $memberRole = \App\Models\MemberRole::factory()->create();
        $country = \App\Models\Country::factory()->create();

        $memberData = array_merge([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email_address' => 'member' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
            'country_id' => $country->id,
            'is_verified' => true,
        ], $overrides);

        return Member::factory()->create($memberData);
    }

    /**
     * Create a complete test admin user
     */
    public static function createCompleteAdmin(array $overrides = [])
    {
        $adminData = array_merge([
            'name' => 'Test Admin',
            'email' => 'admin' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ], $overrides);

        return Admin::factory()->create($adminData);
    }

    /**
     * Create a complete editorial workflow with progress tracking
     */
    public static function createCompleteEditorialWorkflow(array $overrides = [])
    {
        $workflow = EditorialWorkflow::factory()->create($overrides);

        // Create articles for this workflow
        $articles = Article::factory()->count(3)->create([
            'article_status' => 2, // Under Review
        ]);

        // Create editorial progress for each article
        foreach ($articles as $article) {
            ArticleEditorialProgress::factory()->create([
                'article_id' => $article->id,
                'workflow_id' => $workflow->id,
                'current_stage' => 'initial_review',
                'status' => 'in_progress',
            ]);
        }

        return $workflow;
    }

    /**
     * Create a test dataset with articles in different statuses
     */
    public static function createArticleStatusDataset()
    {
        $category = ArticleCategory::factory()->create(['name' => 'Test Category']);

        $articles = [
            'draft' => Article::factory()->create([
                'title' => 'Draft Article',
                'article_status' => 1,
                'article_category_id' => $category->id,
            ]),
            'under_review' => Article::factory()->create([
                'title' => 'Under Review Article',
                'article_status' => 2,
                'article_category_id' => $category->id,
            ]),
            'published' => Article::factory()->create([
                'title' => 'Published Article',
                'article_status' => 3,
                'article_category_id' => $category->id,
                'published_at' => now()->subDays(5),
            ]),
            'rejected' => Article::factory()->create([
                'title' => 'Rejected Article',
                'article_status' => 4,
                'article_category_id' => $category->id,
            ]),
        ];

        return $articles;
    }

    /**
     * Create a test dataset with members in different roles
     */
    public static function createMemberRoleDataset()
    {
        $memberTypeAuthor = \App\Models\MemberType::factory()->create(['name' => 'Author']);
        $memberTypeEditor = \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        $memberTypeReviewer = \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);

        $memberRoleAuthor = \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        $memberRoleEditor = \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        $memberRoleReviewer = \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);

        $members = [
            'author' => Member::factory()->create([
                'member_type_id' => $memberTypeAuthor->id,
                'member_role_id' => $memberRoleAuthor->id,
            ]),
            'editor' => Member::factory()->create([
                'member_type_id' => $memberTypeEditor->id,
                'member_role_id' => $memberRoleEditor->id,
            ]),
            'reviewer' => Member::factory()->create([
                'member_type_id' => $memberTypeReviewer->id,
                'member_role_id' => $memberRoleReviewer->id,
            ]),
        ];

        return $members;
    }

    /**
     * Mock file upload for testing
     */
    public static function createMockPdfFile($fileName = 'test-article.pdf', $size = 1024)
    {
        Storage::fake('articles');

        $file = \Illuminate\Http\UploadedFile::fake()->create($fileName, $size, 'application/pdf');
        $path = $file->store('articles', 'public');

        return [
            'file' => $file,
            'path' => $path,
            'fileName' => $fileName,
            'size' => $size,
        ];
    }

    /**
     * Mock email sending for testing
     */
    public static function mockEmailSending($expectedMailable = null)
    {
        Mail::fake();

        if ($expectedMailable) {
            Mail::assertNothingSent();
        }

        return Mail::getFacadeRoot();
    }

    /**
     * Verify email was sent with specific parameters
     */
    public static function verifyEmailSent($mailableClass, $callback = null)
    {
        if ($callback) {
            Mail::assertSent($mailableClass, $callback);
        } else {
            Mail::assertSent($mailableClass);
        }
    }

    /**
     * Create test data for search functionality
     */
    public static function createSearchTestData()
    {
        $categories = ArticleCategory::factory()->count(3)->create();

        $articles = collect();
        foreach ($categories as $category) {
            $categoryArticles = Article::factory()->count(5)->create([
                'article_category_id' => $category->id,
                'article_status' => 3, // Published
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
            $articles = $articles->merge($categoryArticles);
        }

        // Add articles with specific keywords for search testing
        $searchKeywords = ['machine learning', 'artificial intelligence', 'data science', 'blockchain', 'cloud computing'];

        foreach ($searchKeywords as $keyword) {
            Article::factory()->create([
                'title' => "Research on {$keyword}",
                'abstract' => "This article discusses {$keyword} applications and future prospects.",
                'keywords' => $keyword,
                'article_status' => 3,
                'article_category_id' => $categories->random()->id,
                'published_at' => now()->subDays(rand(1, 15)),
            ]);
        }

        return [
            'categories' => $categories,
            'articles' => $articles,
        ];
    }

    /**
     * Clean up test data after tests
     */
    public static function cleanUpTestData()
    {
        // Clean up files
        Storage::disk('articles')->deleteDirectory('test');
        Storage::disk('public')->deleteDirectory('test');

        // Clear email queue
        Mail::fake();

        // Clean database (handled by RefreshDatabase trait)
    }

    /**
     * Wait for specific condition in browser testing
     */
    public static function waitForCondition($condition, $timeout = 5, $interval = 0.1)
    {
        $start = microtime(true);

        while (microtime(true) - $start < $timeout) {
            if ($condition()) {
                return true;
            }
            usleep($interval * 1000000);
        }

        return false;
    }

    /**
     * Generate test credentials for different user types
     */
    public static function generateTestCredentials($type = 'member')
    {
        $credentials = [
            'email' => $type . uniqid() . '@test.com',
            'password' => 'password123',
        ];

        switch ($type) {
            case 'admin':
                $credentials['email'] = 'admin' . uniqid() . '@test.com';
                break;
            case 'editor':
                $credentials['email'] = 'editor' . uniqid() . '@test.com';
                break;
            case 'reviewer':
                $credentials['email'] = 'reviewer' . uniqid() . '@test.com';
                break;
        }

        return $credentials;
    }

    /**
     * Create bulk test data for performance testing
     */
    public static function createBulkTestData($articleCount = 100, $memberCount = 50)
    {
        $categories = ArticleCategory::factory()->count(5)->create();

        // Create bulk articles
        Article::factory()->count($articleCount)->create([
            'article_status' => 3,
            'published_at' => now()->subDays(rand(1, 365)),
        ]);

        // Create bulk members
        Member::factory()->count($memberCount)->create();

        // Create bulk comments
        $articles = Article::all();
        foreach ($articles as $article) {
            \App\Models\Comment::factory()->count(rand(0, 5))->create([
                'article_id' => $article->id,
            ]);
        }

        // Create bulk bookmarks
        $members = Member::all();
        foreach ($articles->take(20) as $article) {
            $member = $members->random();
            \App\Models\Bookmark::factory()->create([
                'member_id' => $member->id,
                'article_id' => $article->id,
            ]);
        }

        return [
            'categories' => $categories,
            'articles' => $articles,
            'members' => $members,
        ];
    }
}

