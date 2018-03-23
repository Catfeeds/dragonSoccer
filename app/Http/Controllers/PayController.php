<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Goods;
use App\Models\Orders;
use App\Models\Orderslog;
use App\Models\Balancelog;
use App\Models\Members;

use Config;
use DB;
use Yansongda\Pay\Pay;
use Log;
class PayController extends Controller{
   
    public function __construct(Request $request){
    }

    //支付宝回掉
    public function alinotify(Request $request){
    	$pay = new Pay(Config::get('pay'));
        
    	$requestArr = $request->all();
    	//写日志
    	Orderslog::create(array('sn'=>$requestArr['out_trade_no'],'content'=>'支付回掉:'.json_encode($requestArr)));

        if ($pay->driver('alipay')->gateway('app')->verify($requestArr)) {
            if($this->payment($request->out_trade_no,$request->total_amount,'ali')){
            	echo 'SUCCESS';
            }
        }
    }

    //支付宝回掉
    public function wechatnotify(Request $request){
    	$pay = new Pay(Config::get('pay'));

    	$requestStr = $request->getContent();
    	$params = (array)simplexml_load_string($requestStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    	//写日志
    	Orderslog::create(array('sn'=>$params['out_trade_no'],'content'=>'支付回掉:'.json_encode($params)));

        $verify = $pay->driver('wechat')->gateway('app')->verify($requestStr);
        if ($verify) {
            if($this->payment($verify['out_trade_no'],$verify['total_fee']/100,'wechat')){
            	echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }
        } 
    }

    //支付回调出来
    private function payment($sn, $total, $payway)
    {
        $ordersArr = Orders::where('sn','=',$sn)->first();

        if (!empty($ordersArr) && $ordersArr->status == 2 && $total == $ordersArr->total){          
            $res = false;            
            DB::beginTransaction();
            	//修改状态
            	Orders::where('sn','=',$sn)->update(array('status'=>'3','payway'=>$payway,'paytotal'=>$total,'paytime'=>time()));
            	//写日志
            	Orderslog::create(array('sn'=>$sn,'content'=>'支付回掉成功:'.$payway));
            	//写龙珠日志
            	$number = $ordersArr->number*Config::get('custom.balancelog.ratio');
            	Balancelog::create(array('sn'=>$sn,'mid'=>$ordersArr->mid,'type'=>'mall','number'=>$number ));
            	//增加数量
            	Members::where('id','=',$ordersArr->mid)->increment('balance',$number); 

            $res = true;
            DB::commit();

            return $res;
        }
        return false;
    }
    
}
