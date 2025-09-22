<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('member_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->datetime('expiry_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
