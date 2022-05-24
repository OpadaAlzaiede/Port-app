<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterPortRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enter_port_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ship_name');
            $table->double('ship_length');
            $table->double('ship_draft_length');
            $table->double('payload_weight');
            $table->double('ship_weight');
            $table->string('shipping_policy_number');
            $table->tinyInteger('way')->default('1');
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('process_type_id');
            $table->unsignedBigInteger('payload_type_id');
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
        Schema::dropIfExists('enter_port_requests');
    }
}
