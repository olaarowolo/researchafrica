<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\EditorialWorkflow;
use App\Models\EditorialWorkflowStage;
use App\Models\ArticleEditorialProgress;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Services\EditorialWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditorialWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected EditorialWorkflowService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflowService = app(EditorialWorkflowService::class);
    }

    /** @test */
    public function it_can_assign_workflow_to_article()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);

        $progress = $this->workflowService->assignWorkflowToArticle($article, $workflow);

        $this->assertInstanceOf(ArticleEditorialProgress::class, $progress);
        $this->assertEquals($article->id, $progress->article_id);
        $this->assertEquals($workflow->id, $progress->editorial_workflow_id);
        $this->assertEquals(ArticleEditorialProgress::STATUS_DRAFT, $progress->status);
    }

    /** @test */
    public function it_can_submit_article_for_review()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);
        $member = Member::factory()->create();

        $this->workflowService->assignWorkflowToArticle($article, $workflow);

        $success = $this->workflowService->submitArticleForReview($article, $member);

        $this->assertTrue($success);
        $article->refresh();
        $this->assertEquals(ArticleEditorialProgress::STATUS_SUBMITTED, $article->editorialProgress->status);
        $this->assertNotNull($article->editorialProgress->submitted_at);
    }

    /** @test */
    public function it_can_start_review_process()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);
        $stage = EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 1,
        ]);
        $member = Member::factory()->create();

        $this->workflowService->assignWorkflowToArticle($article, $workflow);
        $this->workflowService->submitArticleForReview($article, $member);

        $success = $this->workflowService->startReviewProcess($article, $member);

        $this->assertTrue($success);
        $article->refresh();
        $this->assertEquals(ArticleEditorialProgress::STATUS_UNDER_REVIEW, $article->editorialProgress->status);
        $this->assertEquals($stage->id, $article->editorialProgress->current_stage_id);
        $this->assertNotNull($article->editorialProgress->current_stage_started_at);
    }

    /** @test */
    public function it_can_approve_stage_and_move_to_next()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);

        // Create two stages
        $stage1 = EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 1,
        ]);
        $stage2 = EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 2,
        ]);

        $member = Member::factory()->create();

        // Set up article in first stage
        $this->workflowService->assignWorkflowToArticle($article, $workflow);
        $this->workflowService->submitArticleForReview($article, $member);
        $this->workflowService->startReviewProcess($article, $member);

        // Approve first stage
        $success = $this->workflowService->approveStage($article, $member, 'Looks good!');

        $this->assertTrue($success);
        $article->refresh();
        $this->assertEquals($stage2->id, $article->editorialProgress->current_stage_id);
        $this->assertEquals(ArticleEditorialProgress::STATUS_UNDER_REVIEW, $article->editorialProgress->status);

        // Check stage history
        $history = $article->editorialProgress->getStageHistory();
        $this->assertCount(1, $history);
        $this->assertEquals('approved', $history[0]['action']);
        $this->assertEquals('Looks good!', $history[0]['comments']);
    }

    /** @test */
    public function it_can_request_revision()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);
        $stage = EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 1,
        ]);
        $member = Member::factory()->create();

        // Set up article in review
        $this->workflowService->assignWorkflowToArticle($article, $workflow);
        $this->workflowService->submitArticleForReview($article, $member);
        $this->workflowService->startReviewProcess($article, $member);

        // Request revision
        $success = $this->workflowService->requestRevision($article, $member, 'Please revise the methodology section.');

        $this->assertTrue($success);
        $article->refresh();
        $this->assertEquals(ArticleEditorialProgress::STATUS_REVISION_REQUESTED, $article->editorialProgress->status);
        $this->assertEquals('Please revise the methodology section.', $article->editorialProgress->current_comments);
    }

    /** @test */
    public function it_can_publish_article()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id, 'article_status' => 2]); // Under review
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);
        $member = Member::factory()->create();

        // Set up approved article
        $this->workflowService->assignWorkflowToArticle($article, $workflow);
        $progress = $article->editorialProgress;
        $progress->status = ArticleEditorialProgress::STATUS_APPROVED;
        $progress->save();

        // Publish article
        $success = $this->workflowService->publishArticle($article, $member);

        $this->assertTrue($success);
        $article->refresh();
        $this->assertEquals(3, $article->article_status); // Published
        $this->assertEquals(ArticleEditorialProgress::STATUS_PUBLISHED, $article->editorialProgress->status);
    }

    /** @test */
    public function it_can_get_workflow_statistics()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);

        // Create articles in different states
        $draftArticle = Article::factory()->create(['journal_id' => $journal->id]);
        $submittedArticle = Article::factory()->create(['journal_id' => $journal->id]);
        $reviewArticle = Article::factory()->create(['journal_id' => $journal->id]);
        $approvedArticle = Article::factory()->create(['journal_id' => $journal->id]);

        $this->workflowService->assignWorkflowToArticle($draftArticle, $workflow);
        $this->workflowService->assignWorkflowToArticle($submittedArticle, $workflow);
        $this->workflowService->assignWorkflowToArticle($reviewArticle, $workflow);
        $this->workflowService->assignWorkflowToArticle($approvedArticle, $workflow);

        $submittedArticle->editorialProgress->update(['status' => ArticleEditorialProgress::STATUS_SUBMITTED]);
        $reviewArticle->editorialProgress->update(['status' => ArticleEditorialProgress::STATUS_UNDER_REVIEW]);
        $approvedArticle->editorialProgress->update(['status' => ArticleEditorialProgress::STATUS_APPROVED]);

        $stats = $this->workflowService->getJournalWorkflowStats($journal->id);

        $this->assertEquals(1, $stats['draft']);
        $this->assertEquals(1, $stats['submitted']);
        $this->assertEquals(1, $stats['under_review']);
        $this->assertEquals(1, $stats['approved']);
        $this->assertEquals(0, $stats['published']);
    }

    /** @test */
    public function it_can_check_if_member_can_perform_action()
    {
        // Create member types first
        \DB::table('member_types')->insert([
            ['id' => 2, 'name' => 'Editor', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Reviewer', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $journal = ArticleCategory::factory()->journal()->create();
        $article = Article::factory()->create(['journal_id' => $journal->id]);
        $workflow = EditorialWorkflow::factory()->create(['journal_id' => $journal->id]);
        $stage = EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'required_roles' => [2], // Editor role
            'allowed_actions' => ['approve', 'reject'],
        ]);

        $editor = Member::factory()->create();
        $reviewer = Member::factory()->create();

        // Create memberships - editor has access, reviewer doesn't
        \App\Models\JournalMembership::factory()->create([
            'journal_id' => $journal->id,
            'member_id' => $editor->id,
            'member_type_id' => 2, // Editor
            'status' => 'active',
        ]);

        $this->workflowService->assignWorkflowToArticle($article, $workflow);
        $progress = $article->editorialProgress;
        $progress->current_stage_id = $stage->id;
        $progress->save();

        $this->assertTrue($this->workflowService->canPerformAction($article, $editor, 'approve'));
        $this->assertFalse($this->workflowService->canPerformAction($article, $reviewer, 'approve'));
        $this->assertFalse($this->workflowService->canPerformAction($article, $editor, 'invalid_action'));
    }
}