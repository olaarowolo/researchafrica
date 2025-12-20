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
        // Make category_name nullable to support semantic clarity migration
        // This ensures backward compatibility while allowing gradual migration
        if (Schema::hasTable('article_categories') && Schema::hasColumn('article_categories', 'category_name')) {
            Schema::table('article_categories', function (Blueprint $table) {
                // Change category_name to nullable for backward compatibility
                $table->string('category_name')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('article_categories') && Schema::hasColumn('article_categories', 'category_name')) {
            Schema::table('article_categories', function (Blueprint $table) {
                // Revert category_name to required (use with caution!)
                $table->string('category_name')->nullable(false)->change();
            });
        }
    }
};

