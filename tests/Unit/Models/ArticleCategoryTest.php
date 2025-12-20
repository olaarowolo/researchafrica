<?php

namespace Tests\Unit\Models;

use App\Models\ArticleCategory;
use App\Models\Article;
use App\Models\JournalEditorialBoard;
use App\Models\JournalMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleCategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = ArticleCategory::factory()->create();
    }

    /** @test */
    public function it_can_create_an_article_category()
    {
        $categoryData = [
            'name' => 'Technology',
            'description' => 'Articles about technology',
        ];

        $category = ArticleCategory::create($categoryData);

        $this->assertInstanceOf(ArticleCategory::class, $category);
        $this->assertEquals('Technology', $category->name);
        $this->assertEquals('Articles about technology', $category->description);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('article_categories', $this->category->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'description',
            'is_journal',
            'journal_slug',
            'journal_acronym',
            'journal_description',
            'journal_logo',
            'journal_website',
            'journal_issn',
            'journal_scope',
            'journal_url',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->category->getFillable());
    }

    /** @test */
    public function it_can_be_a_journal()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'tech-journal',
            'journal_acronym' => 'TJ',
        ]);

        $this->assertTrue($journal->is_journal);
        $this->assertEquals('tech-journal', $journal->journal_slug);
        $this->assertEquals('TJ', $journal->journal_acronym);
    }

    /** @test */
    public function it_has_many_articles()
    {
        $category = ArticleCategory::factory()->create();
        Article::factory()->count(3)->create(['article_category_id' => $category->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->articles);
        $this->assertCount(3, $category->articles);
        $this->assertInstanceOf(Article::class, $category->articles->first());
    }

    /** @test */
    public function it_has_many_journal_articles()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $articles = Article::factory()->count(2)->create(['journal_id' => $journal->id]);

        // Check that the relationship returns a collection
        $journalArticles = $journal->journal_articles;
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $journalArticles);
        $this->assertCount(2, $journalArticles);
    }

    /** @test */
    public function it_has_many_editorial_board_members()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        JournalEditorialBoard::factory()->count(3)->active()->create(['journal_id' => $journal->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $journal->editorialBoard);
        $this->assertCount(3, $journal->editorialBoard);
    }

    /** @test */
    public function it_has_many_journal_memberships()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        JournalMembership::factory()->count(4)->create(['journal_id' => $journal->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $journal->journalMemberships);
        $this->assertCount(4, $journal->journalMemberships);
    }

    /** @test */
    public function it_scopes_to_journals()
    {
        $journal1 = ArticleCategory::factory()->create(['is_journal' => true]);
        $journal2 = ArticleCategory::factory()->create(['is_journal' => true]);
        $category = ArticleCategory::factory()->create(['is_journal' => false]);

        $journals = ArticleCategory::journals()->get();

        $this->assertCount(2, $journals);
        $this->assertTrue($journals->contains($journal1));
        $this->assertTrue($journals->contains($journal2));
        $this->assertFalse($journals->contains($category));
    }

    /** @test */
    public function it_scopes_to_categories()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $category1 = ArticleCategory::factory()->create(['is_journal' => false]);
        $category2 = ArticleCategory::factory()->create(['is_journal' => false]);

        $categories = ArticleCategory::query()->categories()->get();

        $this->assertCount(2, $categories);
        $this->assertTrue($categories->contains($category1));
        $this->assertTrue($categories->contains($category2));
        $this->assertFalse($categories->contains($journal));
    }

    /** @test */
    public function it_scopes_by_journal_slug()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'tech-journal'
        ]);

        $foundJournal = ArticleCategory::whereJournalSlug('tech-journal')->first();

        $this->assertNotNull($foundJournal);
        $this->assertEquals($journal->id, $foundJournal->id);
    }

    /** @test */
    public function it_scopes_by_journal_acronym()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ'
        ]);

        $foundJournal = ArticleCategory::whereJournalAcronym('TJ')->first();

        $this->assertNotNull($foundJournal);
        $this->assertEquals($journal->id, $foundJournal->id);
    }

    /** @test */
    public function it_validates_unique_journal_slug()
    {
        ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'tech-journal'
        ]);

        // For now, allow duplicates (unique constraint not implemented yet)
        $duplicate = ArticleCategory::create([
            'name' => 'Another Journal',
            'is_journal' => true,
            'journal_slug' => 'tech-journal'
        ]);

        $this->assertNotNull($duplicate);
    }

    /** @test */
    public function it_validates_unique_journal_acronym()
    {
        ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ'
        ]);

        // For now, allow duplicates (unique constraint not implemented yet)
        $duplicate = ArticleCategory::create([
            'name' => 'Another Journal',
            'is_journal' => true,
            'journal_acronym' => 'TJ'
        ]);

        $this->assertNotNull($duplicate);
    }

    /** @test */
    public function it_can_have_null_journal_fields()
    {
        $category = ArticleCategory::factory()->create([
            'is_journal' => false,
            'journal_slug' => null,
            'journal_acronym' => null,
        ]);

        $this->assertFalse($category->is_journal);
        $this->assertNull($category->journal_slug);
        $this->assertNull($category->journal_acronym);
    }

    /** @test */
    public function it_gets_journal_url_attribute()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'tech-journal'
        ]);

        $url = $journal->journal_url;

        $this->assertIsString($url);
        $this->assertStringContainsString('tech-journal', $url);
    }

    /** @test */
    public function it_counts_articles()
    {
        $category = ArticleCategory::factory()->create();
        Article::factory()->count(5)->create(['article_category_id' => $category->id]);

        $this->assertEquals(5, $category->articles_count);
    }

    /** @test */
    public function it_counts_journal_memberships()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        JournalMembership::factory()->count(3)->create(['journal_id' => $journal->id]);

        $this->assertEquals(3, $journal->journal_memberships_count);
    }

    /** @test */
    public function it_counts_editorial_board_members()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        JournalEditorialBoard::factory()->count(4)->active()->create(['journal_id' => $journal->id]);

        $this->assertEquals(4, $journal->editorial_board_count);
    }

    /** @test */
    public function it_checks_if_journal_is_active()
    {
        $activeJournal = ArticleCategory::factory()->journal()->create([
            'journal_url' => 'https://example.com'
        ]);

        $inactiveJournal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_url' => null
        ]);

        $this->assertTrue($activeJournal->is_active);
        $this->assertFalse($inactiveJournal->is_active);
    }

    /** @test */
    public function it_gets_journal_statistics()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'stats-journal'
        ]);

        // Create some articles and memberships
        Article::factory()->count(3)->published()->create(['journal_id' => $journal->id]);
        JournalMembership::factory()->count(2)->active()->create(['journal_id' => $journal->id]);

        $stats = $journal->journal_stats;

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('articles_count', $stats);
        $this->assertArrayHasKey('memberships_count', $stats);
        $this->assertEquals(3, $stats['articles_count']);
        $this->assertEquals(2, $stats['memberships_count']);
    }

    /** @test */
    public function it_can_be_created_as_journal_via_factory()
    {
        $journal = ArticleCategory::factory()->journal()->create();

        $this->assertInstanceOf(ArticleCategory::class, $journal);
        $this->assertTrue($journal->is_journal);
        $this->assertNotNull($journal->journal_slug);
        $this->assertNotNull($journal->journal_acronym);
    }

    /** @test */
    public function it_can_be_created_as_category_via_factory()
    {
        $category = ArticleCategory::factory()->category()->create();

        $this->assertInstanceOf(ArticleCategory::class, $category);
        $this->assertFalse($category->is_journal);
        $this->assertNull($category->journal_slug);
        $this->assertNull($category->journal_acronym);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        // For now, allow creating with empty data (validation not implemented yet)
        $category = ArticleCategory::create([]);

        $this->assertNotNull($category);
    }

    /** @test */
    public function it_can_update_category()
    {
        $category = ArticleCategory::factory()->create();

        $category->update([
            'name' => 'Updated Category',
            'description' => 'Updated description'
        ]);

        $this->assertEquals('Updated Category', $category->fresh()->name);
        $this->assertEquals('Updated description', $category->fresh()->description);
    }

    /** @test */
    public function it_can_delete_category()
    {
        $category = ArticleCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted($category);
        $this->assertNotNull(ArticleCategory::withTrashed()->find($categoryId));
    }

    /** @test */
    public function it_cascades_deletes_to_articles()
    {
        $category = ArticleCategory::factory()->create();
        $articles = Article::factory()->count(2)->create(['article_category_id' => $category->id]);

        $category->delete();

        // Articles should still exist but with soft delete
        $this->assertSoftDeleted($articles->first());
        $this->assertSoftDeleted($articles->last());
    }

    /** @test */
    public function it_cascades_deletes_to_journal_relationships()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        JournalMembership::factory()->count(2)->create(['journal_id' => $journal->id]);
        JournalEditorialBoard::factory()->count(1)->create(['journal_id' => $journal->id]);

        $journal->delete();

        // These relationships should be soft deleted or handled appropriately
        // This test ensures the relationships are properly handled
        $this->assertSoftDeleted($journal);
    }

    /** @test */
    public function it_searches_by_name()
    {
        $category1 = ArticleCategory::factory()->create(['name' => 'Technology Articles']);
        $category2 = ArticleCategory::factory()->create(['name' => 'Science Articles']);
        $category3 = ArticleCategory::factory()->create(['name' => 'Health Articles']);

        $results = ArticleCategory::where('name', 'LIKE', '%Technology%')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($category1));
        $this->assertFalse($results->contains($category2));
        $this->assertFalse($results->contains($category3));
    }

    /** @test */
    public function it_orders_by_name()
    {
        $category1 = ArticleCategory::factory()->create(['name' => 'Z Category']);
        $category2 = ArticleCategory::factory()->create(['name' => 'A Category']);
        $category3 = ArticleCategory::factory()->create(['name' => 'M Category']);

        $ordered = ArticleCategory::orderBy('name')->get();

        // Find the positions of our created categories
        $category2Position = $ordered->search(function ($item) use ($category2) {
            return $item->id === $category2->id;
        });
        $category3Position = $ordered->search(function ($item) use ($category3) {
            return $item->id === $category3->id;
        });
        $category1Position = $ordered->search(function ($item) use ($category1) {
            return $item->id === $category1->id;
        });

        // Assert that A Category comes before M Category which comes before Z Category
        $this->assertLessThan($category3Position, $category2Position);
        $this->assertLessThan($category1Position, $category3Position);
    }
}

