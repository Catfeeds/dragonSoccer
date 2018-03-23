<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchlogSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchlog_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('matchlogid')->default(0)->comment('赛程id');
            $table->integer('teamid')->default(0)->comment('队伍id');
            $table->integer('mid')->default(0)->comment('会员id');
            $table->text('mtime')->comment('json 2017-11-10#am');
            $table->string('status',10)->default('n')->comment('n未采用 y已采用');
            $table->string('rname',10)->default('')->comment('联系人');
            $table->string('phone',120)->default('')->comment('手机');
            $table->string('address')->default('')->comment('场地地址');
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
        Schema::dropIfExists('matchlog_setting');
    }
}
