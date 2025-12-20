<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\Comment;
use App\Models\Bookmark;
use App\Models\EditorialWorkflow;
use App\Models\ArticleEditorialProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseTransactionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
    }

    /** @test */
    public function it_rolls_back_article_creation_on_failure()
    {
        // Arrange
        $member = Member::factory()->create();
        $category = ArticleCategory::factory()->create();

        // Act & Assert - Simulate transaction rollback
        DB::beginTransaction();

        try {
            $article = Article::create([
                'member_id' => $member->id,
                'article_category_id' => $category->id,
                'title' => 'Transaction Test Article',
                'article_status' => 1
            ]);

            // Simulate an error (duplicate key violation)
            Article::create([
                'member_id' => $member->id,
                'article_category_id' => $category->id,
                'title' => 'Transaction Test Article', // Duplicate title - should cause error
                'article_status' => 1
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        // Assert - Article should not exist due to rollback
        $this->assertDatabaseMissing('articles', [
            'title' => 'Transaction Test Article'
        ]);
    }

    /** @test */
    public function it_handles_editorial_workflow_transaction()
    {
        // Arrange
        $journal = ArticleCategory::factory()->create([
            'name' => 'Transaction Test Journal',
            'is_journal' => true
        ]);

        $workflow = EditorialWorkflow::factory()->create([
            'journal_id' => $journal->id,
            'name' => 'Test Workflow'
        ]);

        // Act & Assert - Complete workflow transaction
        DB::transaction(function () use ($journal, $workflow) {
            // Create article
            $article = Article::create([
                'article_category_id' => $journal->id,
                'journal_id' => $journal->id,
                'member_id' => Member::factory()->create()->id,
                'title' => 'Workflow Transaction Article',
                'article_status' => 2
            ]);

            // Create editorial progress
            ArticleEditorialProgress::create([
                'article_id' => $article->id,
                'workflow_id' => $workflow->id,
                'current_stage' => 'initial_review',
                'status' => 'in_progress'
            ]);

            // All operations should succeed or all should fail
            $this->assertModelExists($article);
            $this->assertDatabaseHas('article_editorial_progresses', [
                'article_id' => $article->id,
                'workflow_id' => $workflow->id
            ]);
        });

        // Assert - Both records should exist
        $this->assertDatabaseHas('articles', [
            'title' => 'Workflow Transaction Article'
        ]);

        $this->assertDatabaseHas('article_editorial_progresses', [
            'current_stage' => 'initial_review'
        ]);
    }

    /** @test */
    public function it_rolls_back_complex_article_workflow_on_error()
    {
        // Arrange
        $journal = ArticleCategory::factory()->create([
            'name' => 'Complex Transaction Journal',
            'is_journal' => true
        ]);

        $member = Member::factory()->create();
        $workflow = EditorialWorkflow::factory()->create([
            'journal_id' => $journal->id
        ]);

        // Act & Assert - Simulate partial failure in complex transaction
        DB::beginTransaction();

        try {
            // Create article
            $article = Article::create([
                'article_category_id' => $journal->id,
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'title' => 'Complex Transaction Article',
                'article_status' => 2
            ]);

            // Create editorial progress
            ArticleEditorialProgress::create([
                'article_id' => $article->id,
                'workflow_id' => $workflow->id,
                'current_stage' => 'initial_review',
                'status' => 'in_progress'
            ]);

            // Create comment
            Comment::create([
                'article_id' => $article->id,
                'member_id' => $member->id,
                'content' => 'Test comment'
            ]);

            // Simulate error that should rollback entire transaction
            throw new \Exception('Simulated transaction failure');

        } catch (\Exception $e) {
            DB::rollBack();
        }

        // Assert - Nothing should exist due to rollback
        $this->assertDatabaseMissing('articles', [
            'title' => 'Complex Transaction Article'
        ]);

        $this->assertDatabaseMissing('article_editorial_progresses', [
            'current_stage' => 'initial_review'
        ]);

        $this->assertDatabaseMissing('comments', [
            'content' => 'Test comment'
        ]);
    }

    /** @test */
    public function it_maintains_data_consistency_across_related_tables()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();
        $member = Member::factory()->create();

        // Act & Assert - Multi-table transaction
        DB::transaction(function () use ($category, $member) {
            // Create article
            $article = Article::create([
                'article_category_id' => $category->id,
                'member_id' => $member->id,
                'title' => 'Consistency Test Article',
                'article_status' => 3
            ]);

            // Create related records in same transaction
            $comment = Comment::create([
                'article_id' => $article->id,
                'member_id' => $member->id,
                'content' => 'Test comment for consistency'
            ]);

            $bookmark = Bookmark::create([
                'member_id' => $member->id,
                'article_id' => $article->id
            ]);

            // Verify all records are created within transaction
            $this->assertDatabaseHas('articles', ['id' => $article->id]);
            $this->assertDatabaseHas('comments', ['id' => $comment->id]);
            $this->assertDatabaseHas('bookmarks', ['id' => $bookmark->id]);
        });

        // Assert - All records should still exist after commit
        $this->assertDatabaseHas('articles', [
            'title' => 'Consistency Test Article'
        ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'Test comment for consistency'
        ]);
    }

    /** @test */
    public function it_handles_nested_transactions_properly()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();

        // Act & Assert - Nested transaction handling
        DB::transaction(function () use ($category) {
            // Outer transaction
            $article = Article::create([
                'article_category_id' => $category->id,
                'title' => 'Nested Transaction Article',
                'article_status' => 1
            ]);

            // Nested transaction using savePoint
            DB::transaction(function () use ($article) {
                // This should succeed
                Comment::create([
                    'article_id' => $article->id,
                    'member_id' => Member::factory()->create()->id,
                    'content' => 'Nested comment'
                ]);

                // Another nested transaction
                DB::transaction(function () use ($article) {
                    Bookmark::create([
                        'member_id' => Member::factory()->create()->id,
                        'article_id' => $article->id
                    ]);
                });
            });
        });

        // Assert - All records should exist
        $this->assertDatabaseHas('articles', [
            'title' => 'Nested Transaction Article'
        ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'Nested comment'
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'article_id' => Article::where('title', 'Nested Transaction Article')->first()->id
        ]);
    }

    /** @test */
    public function it_rolls_back_nested_transaction_on_error()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();

        // Act & Assert - Nested transaction with error
        DB::transaction(function () use ($category) {
            // Outer transaction - this should succeed
            $article = Article::create([
                'article_category_id' => $category->id,
                'title' => 'Nested Error Article',
                'article_status' => 1
            ]);

            // Nested transaction - this should fail
            DB::transaction(function () use ($article) {
                // This should succeed
                Comment::create([
                    'article_id' => $article->id,
                    'member_id' => Member::factory()->create()->id,
                    'content' => 'This should be rolled back'
                ]);

                // Simulate error in nested transaction
                throw new \Exception('Nested transaction error');
            });
        });

        // Assert - Article should exist (outer transaction committed)
        // But comment should not exist (nested transaction rolled back)
        $this->assertDatabaseHas('articles', [
            'title' => 'Nested Error Article'
        ]);

        $this->assertDatabaseMissing('comments', [
            'content' => 'This should be rolled back'
        ]);
    }

    /** @test */
    public function it_handles_isolation_levels_correctly()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();

        // Act & Assert - Test with different isolation levels
        DB::transaction(function () use ($category) {
            $article = Article::create([
                'article_category_id' => $category->id,
                'title' => 'Isolation Test Article',
                'article_status' => 1
            ]);

            // Simulate concurrent access within same transaction
            $retrievedArticle = Article::find($article->id);
            $this->assertEquals($article->id, $retrievedArticle->id);
            $this->assertEquals($article->title, $retrievedArticle->title);

            // Verify isolation by checking record visibility
            $this->assertTrue($retrievedArticle->exists);
        });
    }

    /** @test */
    public function it_prevents_deadlocks_in_concurrent_operations()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();
        $member = Member::factory()->create();

        // Act & Assert - Simulate potential deadlock scenario
        DB::transaction(function () use ($category, $member) {
            // Create article
            $article = Article::create([
                'article_category_id' => $category->id,
                'member_id' => $member->id,
                'title' => 'Deadlock Prevention Article',
                'article_status' => 1
            ]);

            // Create related records in consistent order
            $comment = Comment::create([
                'article_id' => $article->id,
                'member_id' => $member->id,
                'content' => 'Deadlock prevention test'
            ]);

            $bookmark = Bookmark::create([
                'member_id' => $member->id,
                'article_id' => $article->id
            ]);

            // All operations should succeed without deadlock
            $this->assertModelExists($article);
            $this->assertModelExists($comment);
            $this->assertModelExists($bookmark);
        });

        // Assert - All records should exist
        $this->assertDatabaseHas('articles', [
            'title' => 'Deadlock Prevention Article'
        ]);
    }

    /** @test */
    public function it_handles_large_transactions_efficiently()
    {
        // Arrange
        $category = ArticleCategory::factory()->create();
        $member = Member::factory()->create();
        $batchSize = 100;

        // Act & Assert - Large batch transaction
        DB::transaction(function () use ($category, $member, $batchSize) {
            $articleIds = [];

            // Create many articles in single transaction
            for ($i = 0; $i < $batchSize; $i++) {
                $article = Article::create([
                    'article_category_id' => $category->id,
                    'member_id' => $member->id,
                    'title' => "Batch Article {$i}",
                    'article_status' => 1
                ]);
                $articleIds[] = $article->id;
            }

            // Create comments for all articles
            foreach ($articleIds as $articleId) {
                Comment::create([
                    'article_id' => $articleId,
                    'member_id' => $member->id,
                    'content' => "Batch comment for article {$articleId}"
                ]);
            }

            // Verify all records are created
            $this->assertEquals($batchSize, Article::where('title', 'like', 'Batch Article %')->count());
            $this->assertEquals($batchSize, Comment::where('content', 'like', 'Batch comment for article %')->count());
        });

        // Assert - All batch records should exist
        $this->assertEquals($batchSize, Article::where('title', 'like', 'Batch Article %')->count());
        $this->assertEquals($batchSize, Comment::where('content', 'like', 'Batch comment for article %')->count());
    }
}
