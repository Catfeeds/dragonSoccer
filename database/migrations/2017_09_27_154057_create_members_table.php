<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon')->default('')->comment('头像');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('password')->default('')->comment('密码');
            $table->string('birthday')->default('')->comment('出生年月');
            $table->string('mobile',40)->default('')->comment('手机号');
            $table->string('sex',10)->default('')->comment('性别 f男 m女');
            $table->string('idnumber',120)->default('')->comment('身份证号');
            $table->string('province',120)->default('')->comment('省');
            $table->string('city',120)->default('')->comment('市');
            $table->string('country',120)->default('')->comment('区 县');
            $table->string('address')->default('')->comment('详细地址');
            $table->string('school',120)->default('')->comment('所在学校');
            $table->string('position',10)->default('')->comment('擅长位置');
            $table->string('foot',10)->default('')->comment('惯用脚');
            $table->string('status')->default('n')->comment('发布状态 n待发布 y发布');
            $table->string('isshow')->default('n')->comment('非好友被查看显示 n不显示全部 y显示全部');
            $table->string('instruction')->default('')->comment('简介');

            $table->integer('height')->default(0)->comment('身高');
            $table->integer('weight')->default(0)->comment('体重');

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
        Schema::dropIfExists('members');
    }
}
