<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameWarningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_warning', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gameid')->default(0)->comment('赛事id');
            $table->integer('mid')->default(0)->comment('用户id');
            $table->string('reason',10)->default('')->comment('理由代码');
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
        Schema::dropIfExists('game_warning');
    }
}
