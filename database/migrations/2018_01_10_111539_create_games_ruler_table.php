<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesRulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_ruler', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key',20)->default('')->comment('级别');
            $table->integer('gamesid')->default(0)->comment('赛事id');
            $table->integer('teamnumber')->default(0)->comment('小组内队伍数量');
            $table->integer('risenumber')->default(1)->comment('晋级指数');
            $table->integer('additionalnumber')->default(0)->comment('补充人数');
            $table->integer('starttime')->default(0)->comment('补充开始时间');
            $table->integer('endtime')->default(0)->comment('补充结束时间');
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
        Schema::dropIfExists('games_ruler');
    }
}
