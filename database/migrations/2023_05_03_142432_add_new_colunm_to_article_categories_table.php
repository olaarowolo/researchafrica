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
            $table->longText('description')->nullable();
            $table->longText('aim_scope')->nullable();
            $table->longText('editorial_board')->nullable();
            $table->longText('submission')->nullable();
            $table->longText('subscribe')->nullable();
            $table->integer('parent_id')->nullable();
        });
    }
};