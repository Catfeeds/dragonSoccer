<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matchid')->default(0)->comment('赛事id');
            $table->integer('mid')->default(0)->comment('用户id');
            $table->string('position',10)->default('')->comment('擅长位置');
            $table->string('positiont',10)->default('')->comment('第二擅长位置');
            $table->string('friend_mid',10)->default(0)->comment('邀请人id 默认0');
            $table->string('status')->default('1')->comment('状态 默认1');
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
        Schema::dropIfExists('apply');
    }
}
