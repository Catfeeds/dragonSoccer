<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('mid')->default(0)->comment('用户id');
            $table->string('friend_mid',10)->default(0)->comment('邀请人id 默认0');
            $table->string('status')->default('1')->comment('状态 默认1 拒绝2 失效3 同意4');
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
        Schema::dropIfExists('relation');
    }
}
