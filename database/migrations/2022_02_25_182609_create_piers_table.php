<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('length');
            $table->double('draft');
            $table->string('code');
            $table->tinyInteger('type');
            $table->string('function');
            $table->tinyInteger('status');
            $table->timestamps();
            $table->timestamp('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('piers');
    }
}
