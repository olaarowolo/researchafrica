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
        Schema::table('reviewer_accept_finals', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_id')->after('member_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviewer_accept_final', function (Blueprint $table) {
            $table->dropColumn('assigned_id');
        });
    }
};
