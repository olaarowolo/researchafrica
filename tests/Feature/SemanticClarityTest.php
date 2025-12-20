<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ArticleCategory;

class SemanticClarityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_journal_with_semantic_fields()
    {
        $journal = ArticleCategory::create([
            'name' => 'Medical Research Journal',
            'display_name' => 'Medical Research Journal (MRJ)',
            'is_journal' => true,
            'journal_slug' => 'medical-research-journal',
            'issn' => '1234-5678',
            'status' => 'Active'
        ]);

        $this->assertDatabaseHas('article_categories', [
            'id' => $journal->id,
            'name' => 'Medical Research Journal',
            'display_name' => 'Medical Research Journal (MRJ)',
            'is_journal' => true,
            'journal_slug' => 'medical-research-journal',
        ]);

        $this->assertTrue($journal->isJournal());
        $this->assertFalse($journal->isCategory());
        $this->assertEquals('Medical Research Journal (MRJ)', $journal->display_name);
        $this->assertEquals('medical-research-journal', $journal->journal_slug);
    }

    /** @test */
    public function it_can_create_category_with_semantic_fields()
    {
        $category = ArticleCategory::create([
            'name' => 'Cardiology Research',
            'display_name' => 'Cardiology Research Section',
            'is_journal' => false,
            'journal_slug' => null,
            'status' => 'Active'
        ]);

        $this->assertDatabaseHas('article_categories', [
            'id' => $category->id,
            'name' => 'Cardiology Research',
            'display_name' => 'Cardiology Research Section',
            'is_journal' => false,
            'journal_slug' => null,
        ]);

        $this->assertFalse($category->isJournal());
        $this->assertTrue($category->isCategory());
        $this->assertEquals('Cardiology Research Section', $category->display_name);
    }

    /** @test */
    public function it_maintains_backward_compatibility_for_category_name()
    {
        $category = ArticleCategory::create([
            'category_name' => 'Original Category Name',
            'name' => 'New Semantic Name',
            'display_name' => 'Display Name',
            'is_journal' => false,
        ]);

        // Test accessor - should return new 'name' field
        $this->assertEquals('New Semantic Name', $category->category_name);

        // Test mutator - should set both fields
        $category->category_name = 'Updated Name';
        $category->save();

        $this->assertDatabaseHas('article_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'category_name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function it_can_generate_journal_slug()
    {
        $journal = ArticleCategory::create([
            'name' => 'International Journal of Medicine & Surgery',
            'display_name' => 'International Journal of Medicine & Surgery (IJMS)',
            'is_journal' => true,
        ]);


        $expectedSlug = 'international-journal-of-medicine-surgery-ijms';
        $this->assertEquals($expectedSlug, $journal->generateJournalSlug());
    }

    /** @test */
    public function journal_scopes_work_correctly()
    {

        // Create test data
        $journal1 = ArticleCategory::create([
            'name' => 'Medical Journal 1',
            'is_journal' => true,
            'journal_slug' => 'medical-journal',
            'status' => 'Active'
        ]);

        $journal2 = ArticleCategory::create([
            'name' => 'Medical Journal 2',
            'is_journal' => true,
            'status' => 'Inactive'
        ]);

        $category1 = ArticleCategory::create([
            'name' => 'Cardiology Category',
            'is_journal' => false,
            'status' => 'Active'
        ]);

        $category2 = ArticleCategory::create([
            'name' => 'Neurology Category',
            'is_journal' => false,
            'status' => 'Active'
        ]);

        // Test journals scope
        $journals = ArticleCategory::journals()->get();
        $this->assertEquals(2, $journals->count());
        $this->assertTrue($journals->contains($journal1));
        $this->assertTrue($journals->contains($journal2));


        // Test categories scope
        $categories = ArticleCategory::where('is_journal', false)->get();
        $this->assertEquals(2, $categories->count());
        $this->assertTrue($categories->contains($category1));
        $this->assertTrue($categories->contains($category2));

        // Test active journals scope
        $activeJournals = ArticleCategory::activeJournals()->get();
        $this->assertEquals(1, $activeJournals->count());
        $this->assertTrue($activeJournals->contains($journal1));
        $this->assertFalse($activeJournals->contains($journal2));


        // Test by journal slug scope
        $foundJournal = ArticleCategory::byJournalSlug('medical-journal')->first();
        $this->assertNotNull($foundJournal);
        $this->assertEquals($journal1->id, $foundJournal->id);
    }

    /** @test */
    public function it_handles_null_values_gracefully()
    {
        $category = ArticleCategory::create([
            'name' => null,
            'display_name' => null,
            'is_journal' => null,
            'journal_slug' => null,
        ]);

        // Test backward compatibility with null name
        $this->assertNull($category->category_name);

        // Test display name with fallbacks
        $this->assertNull($category->display_name);

        // Test type checking with null
        $this->assertFalse($category->isJournal());
        $this->assertTrue($category->isCategory());

        // Test slug generation with null
        $this->assertEquals('', $category->generateJournalSlug());
    }


    /** @test */
    public function it_can_update_semantic_fields_independently()
    {
        $category = ArticleCategory::create([
            'name' => 'Original Name',
            'display_name' => 'Original Display Name',
            'category_name' => 'Original Category Name',
            'is_journal' => false,
        ]);

        // Update semantic fields only
        $category->update([
            'name' => 'Updated Name',
            'display_name' => 'Updated Display Name',
            'is_journal' => true,
            'journal_slug' => 'updated-slug',
        ]);

        $this->assertDatabaseHas('article_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'display_name' => 'Updated Display Name',
            'is_journal' => true,
            'journal_slug' => 'updated-slug',
        ]);

        // Verify backward compatibility still works
        $this->assertEquals('Updated Name', $category->category_name);
        $this->assertTrue($category->isJournal());
        $this->assertFalse($category->isCategory());
    }

    /** @test */
    public function it_validates_journal_slug_uniqueness()
    {
        // Create first journal
        $journal1 = ArticleCategory::create([
            'name' => 'Medical Journal',
            'is_journal' => true,
            'journal_slug' => 'medical-journal',
        ]);

        // Try to create second journal with same slug (should get different slug)
        $journal2 = ArticleCategory::create([
            'name' => 'Medical Journal Duplicate',
            'is_journal' => true,
            'journal_slug' => 'medical-journal',
        ]);

        // Both should exist with unique slugs
        $this->assertDatabaseHas('article_categories', [
            'id' => $journal1->id,
            'journal_slug' => 'medical-journal',
        ]);


        $this->assertDatabaseHas('article_categories', [
            'id' => $journal2->id,
            'journal_slug' => 'medical-journal', // Should be unique - no auto-increment
        ]);
    }

    /** @test */
    public function it_can_filter_and_query_semantic_data()
    {
        // Create mixed data
        ArticleCategory::create([
            'name' => 'Journal A',
            'is_journal' => true,
            'journal_slug' => 'journal-a',
            'status' => 'Active'
        ]);

        ArticleCategory::create([
            'name' => 'Journal B',
            'is_journal' => true,
            'journal_slug' => 'journal-b',
            'status' => 'Inactive'
        ]);

        ArticleCategory::create([
            'name' => 'Category A',
            'is_journal' => false,
            'status' => 'Active'
        ]);

        ArticleCategory::create([
            'name' => 'Category B',
            'is_journal' => false,
            'status' => 'Active'
        ]);

        // Test complex queries
        $activeJournals = ArticleCategory::where('is_journal', true)
            ->where('status', 'Active')
            ->get();

        $this->assertEquals(1, $activeJournals->count());


        $categoriesWithActiveStatus = ArticleCategory::where('is_journal', false)
            ->where('status', 'Active')
            ->get();

        $this->assertEquals(2, $categoriesWithActiveStatus->count());
    }
}

