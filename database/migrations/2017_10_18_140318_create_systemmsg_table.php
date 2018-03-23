<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemmsgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systemmsg', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mid')->default(0)->comment('成员id');
            $table->string('content')->default('')->comment('群内名称');
            $table->string('type',60)->default('')->comment('类型');
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
        Schema::dropIfExists('systemmsg');
    }
}
