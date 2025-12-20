<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('galley_proof_path')->nullable()->after('file_path');
        });
    }
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('galley_proof_path');
        });
    }
};
