<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mid')->default(0)->comment('会员id');            
            $table->integer('bestmid')->default(0)->comment('最佳mid');            
            $table->integer('teamid')->default(0)->comment('队伍id');            
            $table->integer('matchlogid')->default(0)->comment('赛程id');
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
        Schema::dropIfExists('vote');
    }
}
