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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('platform_id');
            $table->foreign('platform_id')->on('platforms')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status' , ['active' , 'expired' , 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apps');
    }
};
