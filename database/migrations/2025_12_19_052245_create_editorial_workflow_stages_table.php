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
        Schema::create('editorial_workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('editorial_workflow_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->json('required_roles'); // Roles that can perform actions in this stage
            $table->json('allowed_actions'); // Actions allowed in this stage (approve, reject, revise, etc.)
            $table->integer('deadline_days')->default(14); // Days allowed for this stage
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('requires_consensus')->default(false); // Whether all reviewers must agree
            $table->integer('min_reviewers')->default(1);
            $table->integer('max_reviewers')->nullable();
            $table->json('stage_config')->nullable(); // Additional configuration for the stage
            $table->timestamps();

            $table->unique(['editorial_workflow_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editorial_workflow_stages');
    }
};
