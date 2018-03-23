<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('groupid')->default(0)->comment('队伍id');
            $table->integer('mid')->default(0)->comment('成员id');
            $table->string('isleader',10)->default('n')->comment('是否是老大');
            $table->string('position',10)->default('')->comment('擅长位置');
            $table->string('positiont',10)->default('')->comment('第二擅长位置');
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
        Schema::dropIfExists('group_members');
    }
}
