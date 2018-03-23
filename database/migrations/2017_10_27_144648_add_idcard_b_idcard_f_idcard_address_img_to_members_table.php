<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdcardBIdcardFIdcardAddressImgToMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('idcard_b')->default('')->comment('身份证反面');
            $table->string('idcard_f')->default('')->comment('身份证正面');
            $table->string('idcard_address')->default('')->comment('身份证地址');
            $table->string('img')->default('')->comment('照片');
            $table->string('truename')->default('')->comment('真实姓名');
            $table->string('nation',120)->default('')->comment('民族');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['idcard_b','idcard_f','idcard_address','img','nickname']);
        });
    }
}
