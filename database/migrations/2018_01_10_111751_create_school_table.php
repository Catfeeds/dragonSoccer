<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon')->default('')->comment('头像');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('loginname')->default('')->comment('登陆名')->unique();
            $table->string('pwd')->default('')->comment('密码');
            $table->string('type','20')->default('s')->comment('类型 s校园 l龙少');
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
        Schema::dropIfExists('school');
    }
}
