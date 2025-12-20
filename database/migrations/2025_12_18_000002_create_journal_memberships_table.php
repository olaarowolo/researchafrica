<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('member_type_id'); // Author, Editor, Reviewer, etc.
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
            $table->unsignedBigInteger('assigned_by')->nullable(); // Admin who assigned
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');

            $table->foreign('journal_id')
                  ->references('id')
                  ->on('article_categories')
                  ->onDelete('cascade');

            $table->foreign('member_type_id')
                  ->references('id')
                  ->on('member_types')
                  ->onDelete('cascade');

            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('members')
                  ->onDelete('set null');

            // Indexes
            $table->index(['member_id', 'journal_id', 'status']);
            $table->index(['journal_id', 'member_type_id', 'status']);
            $table->index('status');

            // Unique constraint: One active membership per member per journal per type
            $table->unique(['member_id', 'journal_id', 'member_type_id', 'status'], 'unique_active_membership');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_memberships');
    }
};
