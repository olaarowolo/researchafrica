<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SemanticClarityDataSeeder extends Seeder
{
    /**
     * Run the database seeder for semantic clarity migration.
     *
     * This seeder will:
     * 1. Populate the 'name' field from existing 'category_name' data
     * 2. Set appropriate 'display_name' values
     * 3. Identify and flag journal entities
     * 4. Generate unique journal_slug values
     * 5. Maintain data integrity throughout the process
     */
    public function run(): void
    {
        $this->command->info('🔄 Starting semantic clarity data migration...');

        // Get all existing article categories
        $categories = DB::table('article_categories')->get();

        if ($categories->isEmpty()) {
            $this->command->info('ℹ️ No article categories found to migrate.');
            return;
        }

        $this->command->info("📊 Found {$categories->count()} article categories to process.");

        $updatedCount = 0;
        $journalCount = 0;
        $errors = [];

        foreach ($categories as $category) {
            try {
                $updates = [];

                // Step 1: Populate 'name' field from 'category_name'
                if (!empty($category->category_name)) {
                    $updates['name'] = $category->category_name;
                } elseif (!empty($category->description)) {
                    // Fallback to description if category_name is empty
                    $updates['name'] = Str::limit($category->description, 100);
                } else {
                    // Generate a default name
                    $updates['name'] = "Category #{$category->id}";
                }

                // Step 2: Set display_name (same as name initially)
                $updates['display_name'] = $updates['name'];

                // Step 3: Identify journals vs categories
                // Heuristic: If it has ISSN or journal-related fields, it's likely a journal
                $isJournal = $this->isLikelyJournal($category);
                $updates['is_journal'] = $isJournal;

                // Step 4: Generate journal slug for potential journals
                if ($isJournal) {
                    $updates['journal_slug'] = $this->generateUniqueJournalSlug($updates['name'], $category->id);
                    $journalCount++;
                }

                // Update the record
                DB::table('article_categories')
                    ->where('id', $category->id)
                    ->update($updates);

                $updatedCount++;

                // Progress indicator
                if ($updatedCount % 10 === 0) {
                    $this->command->info("   ✅ Processed {$updatedCount}/{$categories->count()} categories...");
                }

            } catch (\Exception $e) {
                $errors[] = "Category ID {$category->id}: " . $e->getMessage();
                $this->command->error("   ❌ Error processing category {$category->id}: " . $e->getMessage());
            }
        }

        // Report results
        $this->command->info("🎉 Data migration completed!");
        $this->command->info("   📊 Total categories processed: {$categories->count()}");
        $this->command->info("   ✅ Successfully updated: {$updatedCount}");
        $this->command->info("   📰 Identified as journals: {$journalCount}");
        $this->command->info("   📝 Identified as categories: " . ($categories->count() - $journalCount));

        if (!empty($errors)) {
            $this->command->warn("⚠️ Some errors occurred:");
            foreach ($errors as $error) {
                $this->command->warn("   - {$error}");
            }
        }

        // Verify data integrity
        $this->verifyDataIntegrity();
    }

    /**
     * Determine if a category is likely a journal based on its properties
     *
     * @param object $category
     * @return bool
     */
    private function isLikelyJournal($category): bool
    {
        // Journal indicators
        $journalIndicators = [
            !empty($category->issn),
            !empty($category->online_issn),
            !empty($category->doi_link),
            !empty($category->journal_url),
            !empty($category->aim_scope),
            !empty($category->editorial_board),
            !empty($category->submission),
        ];

        // Count positive indicators
        $score = array_sum($journalIndicators);

        // Category indicators (reverse scoring)
        $categoryIndicators = [
            stripos($category->category_name ?? '', 'category') !== false,
            stripos($category->category_name ?? '', 'section') !== false,
            stripos($category->description ?? '', 'subcategory') !== false,
        ];

        $categoryScore = array_sum($categoryIndicators);

        // Decision logic
        if ($score >= 2) return true;  // Strong journal indicators
        if ($categoryScore >= 1) return false;  // Strong category indicators
        if ($score > $categoryScore) return true;  // More journal than category indicators

        // Default to category for ambiguous cases
        return false;
    }

    /**
     * Generate a unique journal slug
     *
     * @param string $name
     * @param int $categoryId
     * @return string
     */
    private function generateUniqueJournalSlug(string $name, int $categoryId): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        // Ensure uniqueness
        while (DB::table('article_categories')
            ->where('journal_slug', $slug)
            ->where('id', '!=', $categoryId)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verify data integrity after migration
     */
    private function verifyDataIntegrity(): void
    {
        $this->command->info('🔍 Verifying data integrity...');

        // Check for null names
        $nullNames = DB::table('article_categories')
            ->whereNull('name')
            ->count();

        if ($nullNames > 0) {
            $this->command->error("❌ Found {$nullNames} categories with null 'name' field!");
        } else {
            $this->command->info('✅ All categories have valid names');
        }

        // Check for duplicate journal slugs
        $duplicateSlugs = DB::table('article_categories')
            ->select('journal_slug', DB::raw('COUNT(*) as count'))
            ->whereNotNull('journal_slug')
            ->groupBy('journal_slug')
            ->having('count', '>', 1)
            ->count();

        if ($duplicateSlugs > 0) {
            $this->command->error("❌ Found {$duplicateSlugs} duplicate journal slugs!");
        } else {
            $this->command->info('✅ All journal slugs are unique');
        }

        // Check journal/category distribution
        $journalCount = DB::table('article_categories')
            ->where('is_journal', true)
            ->count();

        $categoryCount = DB::table('article_categories')
            ->where(function ($query) {
                $query->where('is_journal', false)
                      ->orWhereNull('is_journal');
            })
            ->count();

        $this->command->info("📊 Final distribution:");
        $this->command->info("   📰 Journals: {$journalCount}");
        $this->command->info("   📝 Categories: {$categoryCount}");

        // Verify backward compatibility
        $backwardCompatIssues = DB::table('article_categories')
            ->where(function ($query) {
                $query->whereNull('category_name')
                      ->whereNotNull('name');
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('category_name')
                      ->whereNull('name')
                      ->whereNotNull('category_name');
            })
            ->count();

        if ($backwardCompatIssues === 0) {
            $this->command->info('✅ Backward compatibility maintained');
        } else {
            $this->command->warn("⚠️ {$backwardCompatIssues} potential backward compatibility issues found");
        }

        $this->command->info('🎯 Data integrity verification completed!');
    }
}

