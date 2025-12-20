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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'price')) {
                $table->decimal('price', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'duration_months')) {
                $table->integer('duration_months')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('subscriptions', 'max_articles')) {
                $table->integer('max_articles')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'max_downloads')) {
                $table->integer('max_downloads')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('subscriptions', 'duration_months')) {
                $table->dropColumn('duration_months');
            }
            if (Schema::hasColumn('subscriptions', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('subscriptions', 'max_articles')) {
                $table->dropColumn('max_articles');
            }
            if (Schema::hasColumn('subscriptions', 'max_downloads')) {
                $table->dropColumn('max_downloads');
            }
        });
    }
};
