<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->on('apps')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('run_id');
            $table->foreign('run_id')->on('runs')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status' , ['active' , 'expired' , 'pending']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
