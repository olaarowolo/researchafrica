<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migrate existing articles to their respective journals
        // Based on article_category_id or article_sub_category_id

        DB::statement('
            UPDATE articles a
            INNER JOIN article_categories ac ON a.article_sub_category_id = ac.id
            SET a.journal_id = ac.id
            WHERE ac.is_journal = 1
        ');

        // For articles without journal assignment, use parent category
        DB::statement('
            UPDATE articles a
            INNER JOIN article_categories ac ON a.article_category_id = ac.id
            SET a.journal_id = ac.id
            WHERE a.journal_id IS NULL AND ac.is_journal = 1
        ');

        // Log articles that couldn't be migrated
        $unmigrated = DB::table('articles')
            ->whereNull('journal_id')
            ->count();

        if ($unmigrated > 0) {
            Log::warning("Sprint 2 Migration: {$unmigrated} articles could not be assigned to journals");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('journal_id');
        });
    }
};
