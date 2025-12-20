<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\Comment;
use App\Models\ContentPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SearchIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();
    }

    protected function seedBasicData()
    {
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    /** @test */
    public function it_searches_articles_by_title()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Computer Science']);
        $article1 = Article::factory()->create([
            'title' => 'Machine Learning in Healthcare',
            'article_category_id' => $category->id,
            'article_status' => 3 // Published
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Deep Learning Applications',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);
        $article3 = Article::factory()->create([
            'title' => 'Database Design Principles',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);

        // Act
        $results = Article::where('title', 'LIKE', '%Machine Learning%')
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
        $this->assertFalse($results->contains($article3));
    }

    /** @test */
    public function it_searches_articles_by_abstract()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Medicine']);
        $article1 = Article::factory()->create([
            'title' => 'Research Article 1',
            'abstract' => 'This study investigates cardiovascular disease prevention methods.',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Research Article 2',
            'abstract' => 'A comprehensive analysis of cancer treatment options.',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);

        // Act
        $results = Article::where('abstract', 'LIKE', '%cardiovascular%')
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
    }

    /** @test */
    public function it_searches_articles_by_keywords()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Engineering']);
        $article1 = Article::factory()->create([
            'title' => 'Renewable Energy Systems',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Solar Panel Efficiency',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);


        // Create keywords for articles
        $keyword1 = \App\Models\ArticleKeyword::factory()->create([
            'article_id' => $article1->id,
            'keyword' => 'solar energy'
        ]);
        $keyword2 = \App\Models\ArticleKeyword::factory()->create([
            'article_id' => $article2->id,
            'keyword' => 'photovoltaic'
        ]);

        // Act
        $results = Article::whereHas('keywords', function ($query) {
            $query->where('keyword', 'LIKE', '%solar%');
        })
        ->where('article_status', 3)
        ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
    }

    /** @test */
    public function it_searches_articles_by_category()
    {
        // Arrange
        $category1 = ArticleCategory::factory()->create(['name' => 'Computer Science']);
        $category2 = ArticleCategory::factory()->create(['name' => 'Medicine']);

        $article1 = Article::factory()->create([
            'title' => 'CS Research Article',
            'article_category_id' => $category1->id,
            'article_status' => 3
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Medical Research Article',
            'article_category_id' => $category2->id,
            'article_status' => 3
        ]);

        // Act
        $results = Article::where('article_category_id', $category1->id)
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
    }

    /** @test */
    public function it_performs_full_text_search_across_multiple_fields()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Biology']);
        $article1 = Article::factory()->create([
            'title' => 'Genetic Engineering Breakthrough',
            'abstract' => 'This research explores CRISPR technology applications.',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Climate Change Impact',
            'abstract' => 'Studying environmental effects on biodiversity.',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);

        // Act - Search across title and abstract
        $searchTerm = 'CRISPR';
        $results = Article::where(function ($query) use ($searchTerm) {
            $query->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('abstract', 'LIKE', "%{$searchTerm}%");
        })
        ->where('article_status', 3)
        ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
    }

    /** @test */
    public function it_searches_comments_by_content()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'Test Article for Comments',
            'article_status' => 3
        ]);
        $member = Member::factory()->create(['email_address' => 'commenter@test.com']);

        $comment1 = Comment::factory()->create([
            'article_id' => $article->id,
            'member_id' => $member->id,
            'content' => 'This is an excellent research paper on AI applications.'
        ]);
        $comment2 = Comment::factory()->create([
            'article_id' => $article->id,
            'member_id' => $member->id,
            'content' => 'I disagree with the methodology used in this study.'
        ]);

        // Act
        $results = Comment::where('content', 'LIKE', '%AI%')->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($comment1));
        $this->assertFalse($results->contains($comment2));
    }

    /** @test */
    public function it_searches_members_by_name_and_email()
    {
        // Arrange
        $member1 = Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email_address' => 'john.smith@university.edu'
        ]);
        $member2 = Member::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email_address' => 'jane.doe@research.org'
        ]);

        // Act - Search by name
        $nameResults = Member::where(function ($query) {
            $query->where('first_name', 'LIKE', '%John%')
                  ->orWhere('last_name', 'LIKE', '%Smith%');
        })->get();

        // Act - Search by email
        $emailResults = Member::where('email_address', 'LIKE', '%university.edu%')->get();

        // Assert
        $this->assertEquals(1, $nameResults->count());
        $this->assertTrue($nameResults->contains($member1));
        $this->assertFalse($nameResults->contains($member2));

        $this->assertEquals(1, $emailResults->count());
        $this->assertTrue($emailResults->contains($member1));
        $this->assertFalse($emailResults->contains($member2));
    }

    /** @test */
    public function it_searches_content_pages_by_title_and_body()
    {
        // Arrange
        $page1 = \App\Models\ContentPage::factory()->create([
            'title' => 'About Research Africa',
            'body' => 'Research Africa is a leading academic journal platform.',
            'slug' => 'about'
        ]);
        $page2 = \App\Models\ContentPage::factory()->create([
            'title' => 'Submission Guidelines',
            'body' => 'Authors should follow our submission guidelines carefully.',
            'slug' => 'submission-guidelines'
        ]);

        // Act - Search by title
        $titleResults = \App\Models\ContentPage::where('title', 'LIKE', '%Research%')->get();

        // Act - Search by body content
        $bodyResults = \App\Models\ContentPage::where('body', 'LIKE', '%guidelines%')->get();

        // Assert
        $this->assertEquals(1, $titleResults->count());
        $this->assertTrue($titleResults->contains($page1));
        $this->assertFalse($titleResults->contains($page2));

        $this->assertEquals(1, $bodyResults->count());
        $this->assertTrue($bodyResults->contains($page2));
        $this->assertFalse($bodyResults->contains($page1));
    }

    /** @test */
    public function it_handles_search_pagination()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'General Science']);

        // Create 15 articles
        for ($i = 1; $i <= 15; $i++) {
            Article::factory()->create([
                'title' => "Research Article Number {$i}",
                'article_category_id' => $category->id,
                'article_status' => 3
            ]);
        }

        // Act - Get first page (5 results per page)
        $page1 = Article::where('article_status', 3)
            ->orderBy('id')
            ->paginate(5);

        // Act - Get second page
        $page2 = Article::where('article_status', 3)
            ->orderBy('id')
            ->paginate(5, ['*'], 'page', 2);

        // Assert
        $this->assertEquals(5, $page1->count());
        $this->assertEquals(5, $page2->count());
        $this->assertEquals(15, $page1->total());
        $this->assertEquals(3, $page1->lastPage());
    }

    /** @test */
    public function it_searches_with_case_insensitive_matching()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Technology']);
        $article = Article::factory()->create([
            'title' => 'Artificial Intelligence in Modern Computing',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);

        // Act - Search with different cases
        $upperCaseResults = Article::where('title', 'LIKE', '%ARTIFICIAL INTELLIGENCE%')
            ->where('article_status', 3)
            ->get();

        $lowerCaseResults = Article::where('title', 'LIKE', '%artificial intelligence%')
            ->where('article_status', 3)
            ->get();

        $mixedCaseResults = Article::where('title', 'LIKE', '%Artificial%')
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(1, $upperCaseResults->count());
        $this->assertEquals(1, $lowerCaseResults->count());
        $this->assertEquals(1, $mixedCaseResults->count());
        $this->assertTrue($upperCaseResults->contains($article));
        $this->assertTrue($lowerCaseResults->contains($article));
        $this->assertTrue($mixedCaseResults->contains($article));
    }

    /** @test */
    public function it_handles_empty_search_results()
    {
        // Arrange
        $category = ArticleCategory::factory()->create(['name' => 'Empty Category']);
        Article::factory()->create([
            'title' => 'Different Topic Article',
            'article_category_id' => $category->id,
            'article_status' => 3
        ]);

        // Act
        $results = Article::where('title', 'LIKE', '%NonExistent Topic%')
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(0, $results->count());
        $this->assertTrue($results->isEmpty());
    }

    /** @test */
    public function it_searches_with_multiple_criteria()
    {
        // Arrange
        $category1 = ArticleCategory::factory()->create(['name' => 'AI Research']);
        $category2 = ArticleCategory::factory()->create(['name' => 'General Computing']);

        $article1 = Article::factory()->create([
            'title' => 'Machine Learning Applications',
            'abstract' => 'Deep learning in healthcare',
            'article_category_id' => $category1->id,
            'article_status' => 3
        ]);
        $article2 = Article::factory()->create([
            'title' => 'Database Systems',
            'abstract' => 'SQL optimization techniques',
            'article_category_id' => $category2->id,
            'article_status' => 3
        ]);

        // Act - Search with multiple criteria
        $results = Article::where('title', 'LIKE', '%Machine Learning%')
            ->orWhere('abstract', 'LIKE', '%healthcare%')
            ->where('article_status', 3)
            ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertTrue($results->contains($article1));
        $this->assertFalse($results->contains($article2));
    }
}

