<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamstsRemarkToMatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match', function (Blueprint $table) {
            $table->string('teamsts',10)->default('w')->comment('w匹配中 s自动匹配完成 f开启自动失败 o 匹配完成');
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
        Schema::table('match', function (Blueprint $table) {
            $table->dropColumn(['teamsts','remark']);
        });
    }
}
