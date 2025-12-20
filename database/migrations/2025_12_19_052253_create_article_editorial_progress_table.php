<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('article_editorial_progress')) {
            Schema::create('article_editorial_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained()->onDelete('cascade');
                $table->foreignId('editorial_workflow_id')->constrained()->onDelete('cascade');
                $table->foreignId('current_stage_id')->nullable()->constrained('editorial_workflow_stages')->onDelete('set null');
                $table->enum('status', ['draft', 'submitted', 'under_review', 'revision_requested', 'approved', 'rejected', 'published'])->default('draft');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('current_stage_started_at')->nullable();
                $table->dateTime('current_stage_deadline')->nullable();
                $table->integer('current_round')->default(1);
                $table->integer('max_rounds_reached')->default(0);
                $table->json('stage_history')->nullable(); // Track all stage transitions
                $table->json('review_assignments')->nullable(); // Track who is assigned to review each stage
                $table->text('current_comments')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['article_id', 'editorial_workflow_id'], 'article_workflow_unique');
                $table->index(['status', 'current_stage_id']);
                $table->index(['current_stage_deadline']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_editorial_progress');
    }
};
