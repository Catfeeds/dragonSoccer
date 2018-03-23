<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('name')->default('')->comment('名称');
            $table->integer('sid')->default(1)->comment('排序');
            $table->string('info')->default('')->comment('简介');
            $table->integer('applystime')->default(0)->comment('报名开始时间');
            $table->integer('applyetime')->default(0)->comment('报名结束时间');
            $table->integer('starttime')->default(0)->comment('比赛开始时间');
            $table->integer('endtime')->default(0)->comment('比赛结束时间');
            $table->string('ruler',10)->default('1')->comment('赛制');
            $table->integer('owner')->default(0)->comment('主办方');
            $table->text('imgs')->comment('赛事图片 #号隔开');
            $table->string('status')->default('n')->comment('状态 n待发布 y发布');            
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
        Schema::dropIfExists('games');
    }
}
