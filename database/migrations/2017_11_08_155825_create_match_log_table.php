<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matchid')->default(0)->comment('赛事id');
            $table->string('groupsn',120)->default('')->comment('小组编号');
            $table->integer('ateamid')->default(0)->comment('A 队id');
            $table->integer('ateamscore')->default(0)->comment('A 队得分');
            $table->integer('bteamid')->default(0)->comment('B 队id');
            $table->integer('bteamscore')->default(0)->comment('B 队得分');
            $table->string('matchlevel',120)->default('')->comment('比赛轮次');
            $table->string('status',120)->default('w')->comment('w等待比赛 r即将开始 go开始 st暂停比赛 e比赛结束');
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->integer('stime')->default(0)->comment('比赛开始时间');
            $table->integer('successteamid')->default(0)->comment('胜者 队id');
            $table->integer('failedteamid')->default(0)->comment('败者 队id');
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
        Schema::dropIfExists('match_log');
    }
}
