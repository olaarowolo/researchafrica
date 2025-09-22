<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description');
            $table->longText('features');
            $table->string('plan_type');
            $table->string('cycle_type');
            $table->integer('cycle_number');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
