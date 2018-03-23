<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',120)->default('')->comment('名称');
            $table->string('icon')->default('')->comment('头像');
            $table->integer('mid')->default(0)->comment('用户id');          
            $table->integer('money')->default(0)->comment('金额 分');
            $table->string('remark')->default('')->comment('备注');
            $table->string('type')->default('apply')->comment('类型 apply匹配  donation捐款  platform平台');
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
        Schema::dropIfExists('cash');
    }
}
