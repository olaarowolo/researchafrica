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
        Schema::table('article_categories', function (Blueprint $table) {
            // Journal identification
            if (!Schema::hasColumn('article_categories', 'journal_acronym')) {
                $table->string('journal_acronym', 10)->nullable()->after('journal_slug');
            }

            // Multi-domain support (for future sprints)
            if (!Schema::hasColumn('article_categories', 'subdomain')) {
                $table->string('subdomain', 100)->nullable()->after('journal_acronym');
            }
            if (!Schema::hasColumn('article_categories', 'custom_domain')) {
                $table->string('custom_domain', 255)->nullable()->after('subdomain');
            }

            // Journal configuration
            if (!Schema::hasColumn('article_categories', 'theme_config')) {
                $table->json('theme_config')->nullable()->after('custom_domain');
            }
            if (!Schema::hasColumn('article_categories', 'email_settings')) {
                $table->json('email_settings')->nullable()->after('theme_config');
            }
            if (!Schema::hasColumn('article_categories', 'submission_settings')) {
                $table->json('submission_settings')->nullable()->after('email_settings');
            }

            // Journal metadata
            if (!Schema::hasColumn('article_categories', 'publisher_name')) {
                $table->string('publisher_name', 255)->nullable()->after('submission_settings');
            }
            if (!Schema::hasColumn('article_categories', 'editor_in_chief')) {
                $table->string('editor_in_chief', 255)->nullable()->after('publisher_name');
            }
            if (!Schema::hasColumn('article_categories', 'contact_email')) {
                $table->text('contact_email')->nullable()->after('editor_in_chief');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn([
                'journal_acronym',
                'subdomain',
                'custom_domain',
                'theme_config',
                'email_settings',
                'submission_settings',
                'publisher_name',
                'editor_in_chief',
                'contact_email',
            ]);
        });
    }
};
