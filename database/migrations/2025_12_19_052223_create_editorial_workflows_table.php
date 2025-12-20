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
        Schema::create('editorial_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('journal_id')->constrained('article_categories')->onDelete('cascade');
            $table->json('stages'); // Store workflow stages configuration
            $table->boolean('is_active')->default(true);
            $table->integer('review_deadline_days')->default(14); // Days for each review stage
            $table->integer('max_review_rounds')->default(3);
            $table->json('required_roles'); // Roles required for each stage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editorial_workflows');
    }
};
