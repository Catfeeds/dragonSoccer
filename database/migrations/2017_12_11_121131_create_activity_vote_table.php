<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_vote', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mid')->default(0)->comment('会员id');            
            $table->integer('bestmid')->default(0)->comment('最佳mid'); 
            $table->string('type',10)->default('')->comment('类型');  
            $table->integer('number')->default(0)->comment('数量');  
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
        Schema::dropIfExists('activity_vote');
    }
}
