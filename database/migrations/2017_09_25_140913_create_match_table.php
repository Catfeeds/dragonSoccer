<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('rule',10)->default('')->comment('赛制');
            $table->string('region',60)->default('')->comment('地区 全国+省');
            $table->string('sex',10)->default('f')->comment('性别 f男 m女 fm混合');
            $table->string('level',10)->default('')->comment('年龄层');
            $table->integer('applystarttime')->default(0)->comment('报名开始时间');
            $table->integer('applyendtime')->default(0)->comment('报名结束时间');
            $table->integer('starttime')->default(0)->comment('比赛开始时间');
            $table->integer('endtime')->default(0)->comment('比赛结束时间');
            $table->text('imgs')->comment('赛事图片 #号隔开');
            $table->string('status')->default('n')->comment('发布状态 n待发布 y发布');
            $table->timestamps();
            $table->softDeletes(); //软删除
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match');
    }
}
