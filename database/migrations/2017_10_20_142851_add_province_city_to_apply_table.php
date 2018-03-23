<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProvinceCityToApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apply', function (Blueprint $table) {
            $table->string('province',120)->default('')->comment('省');
            $table->string('city',120)->default('')->comment('市');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apply', function (Blueprint $table) {
            $table->dropColumn(['province','city']);
        });
    }
}
