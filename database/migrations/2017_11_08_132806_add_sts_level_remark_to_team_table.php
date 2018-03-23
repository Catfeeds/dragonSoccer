<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStsLevelRemarkToTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team', function (Blueprint $table) {
            $table->string('sts',10)->default('')->comment('w匹配中 s成功 f淘汰 ');
            $table->string('level',10)->default('')->comment('赛事状态==match->teamsts');
            $table->string('remark')->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team', function (Blueprint $table) {
            $table->dropColumn(['sts','level','remark']);
        });
    }
}
