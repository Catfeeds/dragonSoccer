<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGteamMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gteam_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teamid')->default(0)->comment('队伍id');
            $table->integer('mid')->default(0)->comment('成员id');
            $table->string('name',120)->default('')->comment('群内名称');
            $table->string('isleader',10)->default('n')->comment('是否是老大');
            $table->string('position',10)->default('')->comment('擅长位置');
            $table->string('positiont',10)->default('')->comment('第二擅长位置');
            $table->string('isshowmsg',10)->default('n')->comment('消息免打扰');
            $table->string('isshowname',10)->default('n')->comment('是否显示名称');
            $table->string('number')->default('')->comment('球衣号');
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
        Schema::dropIfExists('gteam_members');
    }
}
