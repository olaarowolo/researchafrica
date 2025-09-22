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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('stage')->nullable();
            $table->string('doi_link')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue_no')->nullable();
            $table->date('publish_date')->nullable();
            $table->boolean('is_recommended')->default(false);
        });
    }
};
