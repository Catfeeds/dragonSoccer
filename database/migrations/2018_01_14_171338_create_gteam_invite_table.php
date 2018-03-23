<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGteamInviteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gteam_invite', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gteamid')->default(0)->comment('队伍id');
            $table->integer('mid')->default(0)->comment('成员id');
            $table->integer('fmid')->default(0)->comment('成员id');
            $table->string('status',10)->default('1')->comment('1等待 2同意 3失效');
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
        Schema::dropIfExists('gteam_invite');
    }
}
