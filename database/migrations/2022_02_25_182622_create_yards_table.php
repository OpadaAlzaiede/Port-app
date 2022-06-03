<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('size');
            $table->unsignedBigInteger('capacity');
            $table->unsignedBigInteger('payload_type_id');
            $table->tinyInteger('status'); //  0 => outOfService ; 1 otherwise
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
        Schema::dropIfExists('yards');
    }
}
