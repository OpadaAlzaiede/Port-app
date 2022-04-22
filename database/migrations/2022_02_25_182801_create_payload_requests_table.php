<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayloadRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payload_requests', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->string('shipping_policy_number');
            $table->string('ship_number');
            $table->tinyInteger('status');
            $table->tinyInteger('way');
            $table->datetime('date');
            $table->unsignedBigInteger('payload_type_id');
            $table->unsignedBigInteger('process_type_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payload_requests');
    }
}
