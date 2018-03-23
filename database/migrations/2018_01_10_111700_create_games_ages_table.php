<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesAgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_ages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key',20)->default('')->comment('组名');
            $table->string('val')->default('')->comment('祖名');
            $table->integer('gamesid')->default(0)->comment('赛事id');
            $table->integer('starttime')->default(0)->comment('开始时间');
            $table->integer('endtime')->default(0)->comment('结束时间');
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
        Schema::dropIfExists('games_ages');
    }
}
