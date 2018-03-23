<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sid')->default(10)->comment('排序id');
            $table->string('name')->default('')->comment('图片名称');
            $table->string('img')->default('')->comment('图片链接');
            $table->string('url')->default('')->comment('链接');
            $table->string('status',60)->default('n')->comment('手否显示');
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
        Schema::dropIfExists('banner');
    }
}
