<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesRulerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_ruler_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gamesrulerid')->default(0)->comment('赛制id');
            $table->string('key',20)->default('')->comment('轮次');            
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
        Schema::dropIfExists('games_ruler_info');
    }
}
