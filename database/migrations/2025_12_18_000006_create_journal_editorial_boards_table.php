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
        if (!Schema::hasTable('journal_editorial_boards')) {
            Schema::create('journal_editorial_boards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('journal_id');
                $table->unsignedBigInteger('member_id');
                $table->string('position', 100); // e.g., "Editor-in-Chief", "Associate Editor"
                $table->string('department', 255)->nullable();
                $table->string('institution', 255)->nullable();
                $table->text('bio')->nullable();
                $table->string('orcid_id', 50)->nullable();
                $table->date('term_start')->nullable();
                $table->date('term_end')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('display_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

            // Foreign keys
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('article_categories')
                  ->onDelete('cascade');

            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['journal_id', 'is_active']);
            $table->index(['member_id', 'is_active']);
            $table->index('position');

            // Unique constraint: One active position per member per journal
            $table->unique(['journal_id', 'member_id', 'position', 'is_active'], 'unique_active_editor');
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_editorial_boards');
    }
};
