<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gamesid')->default(0)->comment('赛事id');
            $table->integer('sid')->default(1)->comment('排序');
            $table->string('img')->default('')->comment('图片');
            $table->text('txt')->comment('文字')->nullable();
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
        Schema::dropIfExists('game_content');
    }
}
