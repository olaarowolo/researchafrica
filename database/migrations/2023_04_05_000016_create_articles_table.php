<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('author_name')->nullable();
            $table->string('other_authors')->nullable();
            $table->string('corresponding_authors')->nullable();
            $table->string('institute_organization')->nullable();
            $table->string('keywords')->nullable();
            $table->string('article_status')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
