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
        // Make is_journal nullable to support gradual migration
        if (Schema::hasTable('article_categories') && Schema::hasColumn('article_categories', 'is_journal')) {
            Schema::table('article_categories', function (Blueprint $table) {
                $table->boolean('is_journal')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('article_categories') && Schema::hasColumn('article_categories', 'is_journal')) {
            Schema::table('article_categories', function (Blueprint $table) {
                // Set null values to false before making it not nullable
                $table->whereNull('is_journal')->update(['is_journal' => false]);
                $table->boolean('is_journal')->nullable(false)->change();
            });
        }
    }
};

