<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gamesagesid')->default(0)->comment('赛事年龄id');
            $table->string('groupsn',120)->default('')->comment('小组编号');
            $table->string('ateamid')->default(0)->comment('A 队id');
            $table->integer('ateamscore')->default(0)->comment('A 队得分');
            $table->string('bteamid')->default(0)->comment('B 队id');
            $table->integer('bteamscore')->default(0)->comment('B 队得分');
            $table->string('matchlevel',120)->default('')->comment('比赛轮次');
            $table->string('status',120)->default('mw')->comment('w等待比赛 r即将开始 go开始 st暂停比赛 e比赛结束');
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->integer('stime')->default(0)->comment('比赛开始时间');
            $table->integer('successteamid')->default(0)->comment('胜者 队id');
            $table->integer('failedteamid')->default(0)->comment('败者 队id');
            $table->string('address')->default('')->comment('地址');
            $table->softDeletes(); //软删除
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
        Schema::dropIfExists('game_log');
    }
}
