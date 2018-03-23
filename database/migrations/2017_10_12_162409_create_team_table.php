<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon')->default('')->comment('头像');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('type',10)->default('f')->comment('组队类型 m比赛 f聊天');
            $table->string('ischange',10)->default('n')->comment('是否转换为聊天');
            $table->integer('matchid')->default(0)->comment('赛事id');
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
        Schema::dropIfExists('team');
    }
}
