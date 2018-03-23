<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sn')->default('')->comment('订单编号');
            $table->integer('mid')->default(0)->comment('用户id');   
            $table->integer('gid')->default(0)->comment('商品id');
            $table->string('type')->default('android')->comment('默认为安卓 苹果支付凭证');
            $table->decimal('total',10,2)->default('0.00')->comment('总金额');
            $table->decimal('paytotal',10,2)->default('0.00')->comment('实际支付金额');
            $table->integer('paytime')->default(0)->comment('支付时间');
            $table->integer('number')->default(1)->comment('数量');
            $table->string('payway',20)->default('')->comment('支付方式 apple ali wechat');
            $table->string('status',20)->default('2')->comment('状态 2待支付');
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
        Schema::dropIfExists('orders');
    }
}
