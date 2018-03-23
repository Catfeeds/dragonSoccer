<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\CurlHelper;
use App\Models\Goods;
use App\Models\Orders;
use App\Models\Orderslog;
use App\Models\Balancelog;
use App\Models\Members;
use Config;
use DB;
use Yansongda\Pay\Pay;

class OrdersController extends Controller{
   
    private $orderArr = array();
    private $mid = '';

    public function __construct(Request $request){
        $this->orderArr = Config::get('custom.order');
        $this->mid = $request->get('mid','');
    }

    //添加订单ios
    public function addforios(Request $request){
        $gid = $request->get('gid','');
        $number = $request->get('number','');
        if(!empty($gid) && !empty($number)){
            if($listArr = Goods::where('status','=','y')->where('id','=',$gid)->first()){
                if($listArr->appleid != '' && $listArr->number>0 && $number>0 && $listArr->price>0){
                    $arr['sn']      = FunctionHelper::makeSn();
                    $arr['mid']     = $this->mid;    
                    $arr['gid']     = $gid;  
                    $arr['type']    = 'ios';  
                    $arr['total']   = $listArr->price*$number;   
                    $arr['number']   = $listArr->number*$number; 
                    $arr['payway']   = 'apple';   
                    $arr['status']   = '2'; 

                    if($r = Orders::create($arr))
                    {
                        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('sn'=>$r->sn) ));
                        exit(); 
                    }   
                }                   
            }
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();    
    }

    //检测支付ios ---  
    public function paymentforios(Request $request){
        $sn = $request->get('sn','');
        $receipt_data = $request->get('receipt_data',''); 

        if(empty($sn) || empty($receipt_data)){
            return response()->json(array('error'=>1,'msg'=>'参数错误'));
            exit();
        }
        Orderslog::create(array('sn'=>$sn,'content'=>'ios凭证:'.$receipt_data));//ios 凭证

        if(!$ordersArr = Orders::where('sn','=',$sn)->where('mid','=',$this->mid)->where('status','=','2')->first()){
            return response()->json(array('error'=>1,'msg'=>'订单不存在'));
            exit();
        }

        if(!$goodsArr = Goods::where('status','=','y')->where('id','=',$ordersArr->gid)->first()){
            return response()->json(array('error'=>1,'msg'=>'商品不存在'));
            exit();    
        }

        $url = 'https://buy.itunes.apple.com/verifyReceipt';
        $responseArray = json_decode(CurlHelper::http_post_data($url,json_encode(['receipt-data'=> $receipt_data])),true);
        $str = 'sandbox';
        if($responseArray['status'] == 21007){
            $str = 'sandbox';
            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
            $responseArray = json_decode(CurlHelper::http_post_data($url,json_encode(['receipt-data'=> $receipt_data])),true);
        }
        Orderslog::create(array('sn'=>$sn,'content'=>'支付回掉成功-'.$str.':'.json_encode($responseArray)));//写日志

        if($responseArray['status'] == 0 && $responseArray['receipt']['quantity']==($ordersArr->number/$goodsArr->number) ){
            $res = false;
            DB::beginTransaction();
                //修改状态
                Orders::where('sn','=',$sn)->update(array('status'=>'3','payway'=>'apple','paytotal'=>$ordersArr->total,'paytime'=>time()));
                //写日志
                Orderslog::create(array('sn'=>$sn,'content'=>'支付回掉成功:apple'));
                //写龙珠日志
                $number = $ordersArr->number*Config::get('custom.balancelog.ratio');
                Balancelog::create(array('sn'=>$sn,'mid'=>$ordersArr->mid,'type'=>'mall','number'=>$number ));
                //增加数量
                Members::where('id','=',$ordersArr->mid)->increment('balance',$number); 

            $res = true;
            DB::commit();
            if($res){
                return response()->json(array('error'=>0,'msg'=>'验证成功'));
                exit();
            }
        }

        return response()->json(array('error'=>1,'msg'=>'验证失败'));
        exit();
    }

    



    //支付 安卓 
    public function paymentforandroid(Request $request){
        $gid = $request->get('gid','');
        $number = $request->get('number','');
        $payway = $request->get('payway','');
        $sn = $request->get('sn','');
        if(!empty($gid) && !empty($number) && in_array($payway,array('ali','wechat'))){
            if($listArr = Goods::where('status','=','y')->where('id','=',$gid)->first()){
                if($number>0 && $listArr->price>0){
                    $arr['type']    = 'android';  
                    $arr['total']   = $listArr->price*$number;   
                    $arr['number']  = $listArr->number*$number;   
                    $arr['payway']  = $payway; 

                    $r = false;
                    if(!empty($sn)){
                        $r = Orders::where('sn','=',$sn)->where('mid','=',$this->mid)->where('gid','=',$gid)->where('status','=','2')->update($arr);
                        if(empty($r)){
                            return response()->json(array('error'=>1,'msg'=>'订单已支付或不存在!' ));
                            exit();
                        }
                    }else{
                        if(Orders::where('status','=','2')->where('mid','=',$this->mid)->where('gid','=',$gid)->first()){
                            return response()->json(array('error'=>1,'msg'=>'存在未支付的订单，请先支付或取消后再下单' ));
                            exit(); 
                        }

                        $arr['sn']      = FunctionHelper::makeSn();
                        $arr['mid']     = $this->mid;  
                        $arr['gid']     = $gid; 
                        $arr['status']  = '2'; 
                        $r = Orders::create($arr);
                        $sn = $r->sn;
                    }
                    if($r){
                        //支付
                        if($rlt = $this->pay($payway,$sn)){
                            $data = array();
                            if($payway=='ali'){
                                $data['rlt'] = empty($rlt)?'':$rlt;
                            }

                            if($payway=='wechat'){
                                $data = $rlt;
                            }
                            $data['sn'] = $sn;
                        }

                        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$data ));
                        exit(); 
                    }   
                }                   
            }
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();    
    }

    private function pay($payway,$sn){
        $r = Orders::where('sn','=',$sn)->where('mid','=',$this->mid)->first();

        if($payway=='ali'){
             $config_biz = [
                'out_trade_no' => $sn,
                'total_amount' => $r->total,
                'subject'      => 'a'.$sn,
            ];

            $pay = new Pay(Config::get('pay'));

            return $pay->driver('alipay')->gateway('app')->pay($config_biz);
        }

        if($payway=='wechat'){
            $config_biz = [
                'out_trade_no' => $sn, // 订单号
                'total_fee' => $r->total*100, // 订单金额，**单位：分**
                'body' => 'w'.$sn, // 订单描述
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], // 支付人的 IP
            ];

            $pay = new Pay(Config::get('pay'));

            return $pay->driver('wechat')->gateway('app')->pay($config_biz);
        }
    }

    //我的订单  不显示安卓待支付
    public function mylistforios(Request $request){
        $data = $this->mylist('i');
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$data ));
        exit(); 
    }

    public function mylistforandroid(Request $request){
        $data = $this->mylist('a');
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$data ));
        exit();     
    }
    
    //我的订单
    private function mylist($type='a') {
        $query = Orders::with('goods')->where('mid','=',$this->mid);
        if($type == 'a'){
            $query = $query->where(function ($query) {
                $query->where('type','=','android')->orWhere(function ($query) {
                    $query->where('type','=','ios')->where('status','!=','2');
                });
            });
        }

        if($type == 'i'){
            $query = $query->where(function ($query) {
                $query->where('type','=','ios')->orWhere(function ($query) {
                    $query->where('type','=','android')->where('status','!=','2');
                });
            });
        }

        $listArr = $query->orderBy('id', 'desc')->get();
        //var_dump($listArr);
        
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['gid']     = empty($v->gid)?'':$v->gid;    
                $arr['sn']     = empty($v->sn)?'':$v->sn;    
                $arr['img']     = empty($v->goods->img)?'':$v->goods->img;    
                $arr['name']    = empty($v->goods->name)?'':$v->goods->name;    
                $arr['appleid'] = empty($v->goods->appleid)?'':$v->goods->appleid;   
                $arr['price']   = empty($v->goods->price)?'':$v->goods->price;

                $arr['number']  = empty($v->number)?'':$v->number;    
                $arr['total']     = empty($v->total)?'':$v->total;   
                $arr['paytotal']     = empty($v->paytotal)?'':$v->paytotal;   
                $arr['paytime']     = empty($v->paytime)?'':$v->paytime;   
                $arr['payway']     = empty($v->payway)?'':$this->orderArr['paywayArr'][$v->payway];   
                $arr['status']     = empty($v->status)?'':$v->status;   
                $arr['statusmsg']     = empty($v->status)?'':$this->orderArr['statusArr'][$v->status];
                $arr['time']     = empty($listArr->created_at)?'':strtotime($listArr->created_at);    
                $dataArr[] = $arr;
            }
        }

        return $dataArr;
    }

    //订单详情
    public function myinfo(Request $request){
        $sn = $request->get('sn','');
        $listArr = Orders::with('goods')->where('mid','=',$this->mid)->where('sn','=',$sn)->first();
        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['gid']     = empty($listArr->gid)?'':$listArr->gid;  
            $dataArr['sn']     = empty($listArr->sn)?'':$listArr->sn;    
            $dataArr['img']     = empty($listArr->goods->img)?'':$listArr->goods->img;    
            $dataArr['name']    = empty($listArr->goods->name)?'':$listArr->goods->name;    
            $dataArr['appleid'] = empty($listArr->goods->appleid)?'':$listArr->goods->appleid;   
            $dataArr['price']   = empty($listArr->goods->price)?'':$listArr->goods->price;

            $dataArr['number']  = empty($listArr->number)?'':$listArr->number;    
            $dataArr['total']     = empty($listArr->total)?'':$listArr->total;   
            $dataArr['paytotal']     = empty($listArr->paytotal)?'':$listArr->paytotal;   
            $dataArr['paytime']     = empty($listArr->paytime)?'':$listArr->paytime;   
            $dataArr['payway']     = empty($listArr->payway)?'':$this->orderArr['paywayArr'][$listArr->payway];   
            $dataArr['status']     = empty($listArr->status)?'':$listArr->status; 
            $dataArr['statusmsg']     = empty($listArr->status)?'':$this->orderArr['statusArr'][$listArr->status]; 
            $dataArr['time']     = empty($listArr->created_at)?'':strtotime($listArr->created_at); 
            
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr ));
        exit();    
    }
    
}
