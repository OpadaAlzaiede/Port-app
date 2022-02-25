<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterPortPierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enter_port_pier', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->dateTime('enter_date');
            $table->dateTime('leave_date');
            $table->unsignedBigInteger('enter_port_request_id');
            $table->unsignedBigInteger('pier_id');
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
        Schema::dropIfExists('enter_port_pier');
    }
}
