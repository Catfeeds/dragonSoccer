<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_withdraw', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sn')->default('')->comment('订单编号');
            $table->integer('mid')->default(0)->comment('用户id');   
            $table->decimal('total',10,2)->default('0.00')->comment('总金额');
            $table->string('payuser',20)->default('')->comment('支付账户');
            $table->integer('checktime')->default(0)->comment('审核时间');
            $table->decimal('paytotal',10,2)->default('0.00')->comment('实际支付金额');
            $table->integer('paytime')->default(0)->comment('支付时间');            
            $table->string('payway',20)->default('')->comment('支付方式 ali wechat');
            $table->string('status',20)->default('1')->comment('状态1待审核');
            $table->string('remark')->default('')->comment('备注');
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
        Schema::dropIfExists('order_withdraw');
    }
}
