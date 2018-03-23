<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_apply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mid')->default(0)->comment('会员id');  
            $table->string('txt')->default('')->comment('宣言');
            $table->string('status',10)->default('w')->comment('默认待审核');
            $table->string('remark')->default('')->comment('备注');
            $table->text('imgs')->comment('图片');
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
        Schema::dropIfExists('activity_apply');
    }
}
