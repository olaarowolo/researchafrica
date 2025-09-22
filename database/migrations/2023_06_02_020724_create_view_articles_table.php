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
        Schema::create('view_articles', function (Blueprint $table) {
            $table->id();
            $table->string('article_id');
            $table->integer('view')->default(0);
            $table->timestamps();
        });
    }
};
