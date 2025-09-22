<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id', 'member_fk_8283013')->references('id')->on('members');
            $table->unsignedBigInteger('article_category_id')->nullable();
            $table->foreign('article_category_id', 'article_category_fk_8282822')->references('id')->on('article_categories');
        });
    }
}
