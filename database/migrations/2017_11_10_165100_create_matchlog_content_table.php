<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchlogContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchlog_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matchlogid')->default(0)->comment('赛程id');
            $table->integer('teamid')->default(0)->comment('队伍id');
            $table->integer('mid')->default(0)->comment('会员id');            
            $table->string('type',10)->default('')->comment('a分数 b动态 c举报 d结束比赛 e放弃比赛');
            $table->string('txt1')->default('')->comment('原因 比分');
            $table->string('txt2')->default('')->comment('原因 比分');
            $table->text('imgs')->nullable()->comment('json');
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
        Schema::dropIfExists('matchlog_content');
    }
}
