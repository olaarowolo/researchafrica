<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToMemberSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('member_email_id')->nullable();
            $table->foreign('member_email_id', 'member_email_fk_8260179')->references('id')->on('members');
            $table->unsignedBigInteger('subscription_name_id')->nullable();
            $table->foreign('subscription_name_id', 'subscription_name_fk_8260180')->references('id')->on('subscriptions');
        });
    }
}
