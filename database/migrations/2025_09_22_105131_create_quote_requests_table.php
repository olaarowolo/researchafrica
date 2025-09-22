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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('ra_service')->default('afriscribe');
            $table->string('product');
            $table->string('location');
            $table->string('service_type');
            $table->integer('word_count')->nullable();
            $table->json('addons')->nullable(); // rush, plagiarism check, etc.
            $table->string('referral')->nullable();
            $table->text('message')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'completed'])->default('pending');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('estimated_turnaround')->nullable(); // e.g., "3-5 business days"
            $table->text('admin_notes')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
