<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->string('issn')->nullable();
            $table->string('online_issn')->nullable();
            $table->string('doi_link')->nullable();
            $table->string('journal_url')->nullable();
        });
    }
};
