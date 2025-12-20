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
        Schema::table('article_categories', function (Blueprint $table) {
            // Add semantic clarity fields
            $table->string('name')->nullable()->after('id')->comment('Replaces confusing category_name with semantic clarity');
            $table->string('display_name')->nullable()->after('name')->comment('User-friendly name for UI presentation');
            $table->boolean('is_journal')->default(false)->after('display_name')->comment('Flag to distinguish journals from categories');
            $table->string('journal_slug')->nullable()->after('is_journal')->comment('URL-friendly identifier for journals');

            // Add indexes for performance
            $table->index(['is_journal', 'journal_slug']);
            $table->index('is_journal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['is_journal', 'journal_slug']);
            $table->dropIndex('is_journal');

            // Drop columns
            $table->dropColumn(['name', 'display_name', 'is_journal', 'journal_slug']);
        });
    }
};

