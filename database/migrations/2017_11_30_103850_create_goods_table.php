<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img')->default('')->comment('图片');
            $table->string('name')->default('')->comment('名称');
            $table->string('appleid')->default('android')->comment('苹果id 默认为安卓');
            $table->decimal('price',10,2)->default('0.00')->comment('价格');
            $table->integer('number')->default(1)->comment('数量');
            $table->string('url')->default('')->comment('商品详情 url');
            $table->string('status')->default('n')->comment('是否发布');
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
        Schema::dropIfExists('goods');
    }
}
