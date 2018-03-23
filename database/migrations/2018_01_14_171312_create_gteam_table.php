<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGteamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gteam', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon')->default('')->comment('头像');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('type',10)->default('f')->comment('组队类型 m比赛 f聊天');
            $table->integer('gamesagesid')->default(0)->comment('赛事年龄id');
            $table->integer('deletemid')->default(0)->comment('退出群得 成员id');
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->string('gid')->default('')->comment('环信id');
            $table->string('status')->default('w')->comment('状态 w等待');
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
        Schema::dropIfExists('gteam');
    }
}
