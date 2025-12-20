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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('journal_id')->nullable()->after('id');

            // Foreign key
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('article_categories')
                  ->onDelete('set null');

            // Indexes for performance
            $table->index(['journal_id', 'article_status']);
            $table->index(['journal_id', 'created_at']);
            $table->index(['journal_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropIndex(['journal_id', 'article_status']);
            $table->dropIndex(['journal_id', 'created_at']);
            $table->dropIndex(['journal_id', 'member_id']);
            $table->dropColumn('journal_id');
        });
    }
};
