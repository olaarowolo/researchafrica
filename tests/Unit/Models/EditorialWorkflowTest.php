<?php

namespace Tests\Unit\Models;

use App\Models\EditorialWorkflow;
use App\Models\EditorialWorkflowStage;
use App\Models\ArticleEditorialProgress;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditorialWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_create_an_editorial_workflow()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $workflow = EditorialWorkflow::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertInstanceOf(EditorialWorkflow::class, $workflow);
        $this->assertEquals($journal->id, $workflow->journal_id);
        $this->assertTrue($workflow->is_active);
    }

    /** @test */
    public function it_belongs_to_a_journal()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $workflow = EditorialWorkflow::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertInstanceOf(ArticleCategory::class, $workflow->journal);
        $this->assertEquals($journal->id, $workflow->journal->id);
    }

    /** @test */
    public function it_has_many_workflow_stages()
    {
        $workflow = EditorialWorkflow::factory()->create();

        // Create stages with unique orders
        EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 1,
        ]);
        EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 2,
        ]);
        EditorialWorkflowStage::factory()->create([
            'editorial_workflow_id' => $workflow->id,
            'order' => 3,
        ]);

        $workflow->refresh(); // Refresh to get the latest data

        $this->assertCount(3, $workflow->workflowStages);
        $this->assertInstanceOf(EditorialWorkflowStage::class, $workflow->workflowStages->first());
    }

    /** @test */
    public function it_has_many_article_progress_records()
    {
        $workflow = EditorialWorkflow::factory()->create();
        $progress = ArticleEditorialProgress::factory()->count(2)->create([
            'editorial_workflow_id' => $workflow->id,
        ]);

        $this->assertCount(2, $workflow->articleProgress);
        $this->assertInstanceOf(ArticleEditorialProgress::class, $workflow->articleProgress->first());
    }

    /** @test */
    public function it_can_scope_active_workflows()
    {
        $activeWorkflow = EditorialWorkflow::factory()->create(['is_active' => true]);
        $inactiveWorkflow = EditorialWorkflow::factory()->create(['is_active' => false]);

        $activeWorkflows = EditorialWorkflow::active()->get();

        $this->assertCount(1, $activeWorkflows);
        $this->assertEquals($activeWorkflow->id, $activeWorkflows->first()->id);
    }

    /** @test */
    public function it_can_get_default_workflow_for_journal()
    {
        $journal = ArticleCategory::factory()->journal()->create();
        $workflow = EditorialWorkflow::factory()->create([
            'journal_id' => $journal->id,
            'is_active' => true,
        ]);

        $defaultWorkflow = EditorialWorkflow::getDefaultForJournal($journal->id);

        $this->assertEquals($workflow->id, $defaultWorkflow->id);
    }

    /** @test */
    public function it_can_check_if_workflow_is_active()
    {
        $activeWorkflow = EditorialWorkflow::factory()->create(['is_active' => true]);
        $inactiveWorkflow = EditorialWorkflow::factory()->create(['is_active' => false]);

        $this->assertTrue($activeWorkflow->isActive());
        $this->assertFalse($inactiveWorkflow->isActive());
    }
}