<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matchid')->default(0)->comment('赛事id');
            $table->longText('content')->comment('赛事详情');
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
        Schema::dropIfExists('match_info');
    }
}
