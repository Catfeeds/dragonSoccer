<?php
namespace App\Http\Controllers\Shell;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Apply;
use App\Models\Moneylog;
use Illuminate\Support\Facades\Redis;
use DB;
use Config;
class MoneyController extends Controller
{
    public $moneyArr = array();

    public function __construct(){
        $this->moneyArr = Config::get('custom.money');
    }


    //从apply中查找符合条件的mid  插入money中
    public function applyToMoney(Request $request){
        $id = Redis::get('applyToMoneyid');
        $applyArr = Apply::where('status','>','5')->where('id','>',empty($id)?0:$id )->limit(20)->get(['mid','status','id']);
        if(!empty($applyArr->toArray())){
            foreach ($applyArr as $v) {
                if($v['status']==6 || $v['status']==7){ //匹配中
                    if(!Moneylog::where('mid','=',$v->mid)->where('type','=','apply6')->first()){
                        if(Moneylog::create(array('mid'=>$v->mid,'money'=>$this->moneyArr['moneyArr']['apply6'],'type'=>'apply6'))){
                            echo "insert apply6 mid:$v->mid id:$v->id\r\n";
                            Redis::set('applyToMoneyid',$v->id);
                        }
                    }
                }

                if($v['status']==8){ //参赛
                    if(!Moneylog::where('mid','=',$v->mid)->where('type','=','apply8')->first()){
                        if(Moneylog::create(array('mid'=>$v->mid,'money'=>$this->moneyArr['moneyArr']['apply8'],'type'=>'apply8'))){
                            echo "insert apply8 mid:$v->mid id:$v->id\r\n";
                            Redis::set('applyToMoneyid',$v->id);
                        }
                    }
                }
                    
            }
        }else{
            Redis::set('applyToMoneyid',0);
        }

        
    }
  
}