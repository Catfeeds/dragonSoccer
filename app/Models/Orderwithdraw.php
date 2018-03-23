<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Orderwithdraw extends Model{
	use SoftDeletes;
    protected $table='order_withdraw';

    protected $fillable=['sn','mid','total','payuser','checktime','paytotal','paytime','payway','status','remark'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
    
}