<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_address')->unique();
            $table->string('password');
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number');
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('registration_via')->nullable();
            $table->string('email_verified')->nullable();
            $table->datetime('email_verified_at')->nullable();
            $table->string('verified')->nullable();
            $table->string('profile_completed')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
