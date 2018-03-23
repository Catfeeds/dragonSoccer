<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;
use App\Models\Members;
use Illuminate\Support\Facades\Redis;
use App\Helpers\OssUploadHelper;
use App\Models\Activityapply;
use App\Models\Activityvote;
use App\Models\Balancelog;
use App\Models\Moneylog;
use App\Models\Apply;
use App\Models\Company;
use App\Models\Orderwithdraw;
use Config;
use DB;
use Hash;
class ActivityController extends Controller{
	private $bucket = 'lzsn-icon';
    private $dir = 'activity/';
	private $activityArr = array();    
    private $mid = '';
    private $memberArr = array();
    public function __construct(Request $request){
        $this->mid = $request->get('mid','');
        $this->memberArr = Members::where('id','=',$this->mid)->first();
    	$this->activityArr = Config::get('custom.activityArr');   
    }

    public function index(Request $request){
        return view('front.activity_index')->with('listArr',array($this->activityArr['spokesman'] ))->with('title','龙少活动');
        //return view('front.activity_index')->with('listArr',array($this->activityArr['regcash'],$this->activityArr['spokesman'] ))->with('title','龙少活动');
    }

    public function spokesmanlist(Request $request){
        return view('front.activity_spokesman_list')->with('title',$this->activityArr['spokesman']['title']);
    }

    public function spokesmandetail($sex){
        $listArr = Activityapply::with('member')->whereHas('member', function ($q) use ($sex) {
                $q->where('sex','=',$sex);
            })->where(['status'=>'y'])->get();
        $allnumber = Activityvote::where(['type'=>'spokesman'])->sum('number');
        foreach ($listArr as &$v) {
            $percent = 0;
            $curNumber = Activityvote::where(['type'=>'spokesman','bestmid'=>$v->mid])->sum('number');
            $v->allnumber = $curNumber;
            $v->percent = empty($allnumber)?0:ceil($curNumber*10000/$allnumber)/100;
        }

        $memArr = Activityapply::where(['mid'=>$this->mid,'status'=>'y'])->first();
        //var_dump($memArr->toArray());
        return view('front.activity_spokesman_detail')->with('title',$this->activityArr['spokesman']['title'])->with('listArr',$listArr)->with('memArr',$memArr)->with('spokesmanArr',$this->activityArr['spokesman'])->with('sex',$sex=='f'?'男':'女');
    }

    public function spokesmanapply(Request $request){
    	$id = $request->get('id','');
    	if(!empty($id)){
    		if($listArr = Activityapply::where(['id'=>$id,'mid'=>$this->mid])->first()){
    			$listArr->imgs = empty($listArr->imgs)?array():json_decode($listArr->imgs,true);
    			return view('front.activity_spokesman_apply')->with('title','报名活动')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('listArr',$listArr);
    		}
    	}
        return view('front.activity_spokesman_apply')->with('title','报名活动')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxspokesmanapply(Request $request){
    	$txt = $request->get('txt','');
    	$imgs = $request->get('imgs','');
        $id = $request->get('id','');
        if(!empty($imgs)){
        	foreach ($imgs as $k=>$v) {
        		if(empty($v)){
        			unset($imgs[$k]);
        		}
        	}
        }

        if(empty($imgs)){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
            exit();
        }

        if(empty($txt)){
            return response()->json(array('error'=>1,'msg'=>'请填写自我介绍'));
            exit();
        }

        if($listArr = Activityapply::where(['mid'=>$this->mid])->first()){
            if($listArr->id != $id){
                return response()->json(array('error'=>1,'msg'=>'已参加过，请勿重复参加'));
                exit();
            }
        	
        }

    	if(!empty($id)){
    		if($listArr = Activityapply::where(['id'=>$id,'mid'=>$this->mid,'status'=>'w'])->first()){
    			if(Activityapply::where(['id'=>$id,'mid'=>$this->mid,'status'=>'w'])->update(['imgs'=>json_encode(array_values($imgs)),'txt'=>$txt])){
    				return response()->json(array('error'=>0,'msg'=>'成功'));
	            	exit();	
    			}
    		}else{
    			return response()->json(array('error'=>1,'msg'=>'状态错误或者未报名'));
    			exit();	
    		}
    	}else{
    		if($r = Activityapply::create(['imgs'=>json_encode(array_values($imgs)),'txt'=>$txt,'mid'=>$this->mid,'status'=>'w'])){
	            return response()->json(array('error'=>0,'msg'=>'添加成功'));
	            exit();                
	        } 	
    	}         
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();
    }

    //支持
    public function ajaxspokesmansupport(Request $request){
        $bestmid = $request->get('bestmid','');
        $number = $request->get('number','');
       
        if(empty($bestmid)){
            return response()->json(array('error'=>1,'msg'=>'请选择支持的小伙伴'));
            exit();
        }

        if(empty($number)){
            return response()->json(array('error'=>1,'msg'=>'请填写支持数量'));
            exit();
        }

        if(!$listArr = Activityapply::where(['mid'=>$bestmid])->first()){
            return response()->json(array('error'=>1,'msg'=>'小伙伴还没有参加活动'));
            exit();
        }

        if($number > $this->memberArr->balance){
            return response()->json(array('error'=>1,'msg'=>'龙珠数量不足'));
            exit();
        }

        $res = false;            
        DB::beginTransaction();
            $r = Activityvote::create(['mid'=>$this->mid,'bestmid'=>$bestmid,'number'=>$number,'type'=>'spokesman']);            
            Balancelog::create(array('sn'=>$r->id,'mid'=>$this->mid,'type'=>'vote','number'=>$number ));
            //增加数量
            Members::where('id','=',$this->mid)->decrement('balance',$number); 

        $res = true;
        DB::commit();
        
        if($res){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();                
        }    
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();
    }


    

    public function spokesmandinfo($id){
        $listArr = Activityapply::with('member')->where(['id'=>$id])->first();
        if(!empty($listArr)){
            $listArr->imgs = empty($listArr->imgs)?array():json_decode($listArr->imgs,true);

            $allnumber = Activityvote::where(['type'=>'spokesman'])->sum('number');
            $curNumber = Activityvote::where(['type'=>'spokesman','bestmid'=>$listArr->mid])->sum('number');
            $listArr->allnumber = $curNumber;
            $listArr->percent = empty($allnumber)?0:ceil($curNumber*10000/$allnumber)/100;
        }
        
        return view('front.activity_spokesman_info')->with('title','查看详情')->with('listArr',$listArr);
    }

    //注册送现金
    public function sharecash(){
        $listArr = Members::where('recommend','=',$this->memberArr->mobile)->get(['id','icon','name','mobile','status']);

        $total = 0;
        $withdraw = 0;
        foreach ($listArr as &$v){
            $v->status=='n'?$v->statusstr = '已注册':'';
            $v->status=='n'?$v->percent = '25':'';
            $v->status=='y'?$v->statusstr = '已认证':'';
            $v->status=='y'?$v->percent = '50':'';
            if($applyArr = Apply::where('mid','=',$v->id)->first()){
                ($applyArr->status=='6'||$applyArr->status=='7')?$v->statusstr = '已报名':'';
                ($applyArr->status=='6'||$applyArr->status=='7')?$v->percent = '75':'';
                ($applyArr->status=='8')?$v->statusstr = '确认参赛':'';
                ($applyArr->status=='8')?$v->percent = '100':'';    
            }

            $moneyArr6 = Moneylog::where('mid','=',$v->id)->where('type','=','apply6')->first();
            !empty($moneyArr6)?$v->moneystr = '+'.$moneyArr6->money:'';
            !empty($moneyArr6)?$total += $moneyArr6->money:'';
            $moneyArr8 = Moneylog::where('mid','=',$v->id)->where('type','=','apply8')->first();
            !empty($moneyArr8)?$v->moneystr = '+'.$moneyArr8->money:'';
            !empty($moneyArr8)?$total += $moneyArr8->money:'';
        }
       
        $withdraw = Moneylog::where('mid','=',$this->mid)->where('type','=','withdraw')->sum('money');

        $company = Company::where('key','=',$this->memberArr->mobile)->first();

        return view('front.activity_sharecash')->with('title','分享，赢现金好礼')->with('listArr',$listArr)->with('total',$total)->with('withdraw',$withdraw)->with('company',$company);
    }

    public function sharecashinfo(){
        return view('front.activity_sharecashinfo')->with('title','分享，赢现金好礼');   
    }

    public function withdraw(){
        $listArr = Members::where('recommend','=',$this->memberArr->mobile)->get(['id','icon','name','mobile','status']);
        
        $total = 0;
        $withdraw = 0;
        foreach ($listArr as &$v){
            $moneyArr6 = Moneylog::where('mid','=',$v->id)->where('type','=','apply6')->first();
            !empty($moneyArr6)?$total += $moneyArr6->money:'';
            $moneyArr8 = Moneylog::where('mid','=',$v->id)->where('type','=','apply8')->first();
            !empty($moneyArr8)?$total += $moneyArr8->money:'';
        }
       
        $withdraw = Moneylog::where('mid','=',$this->mid)->where('type','=','withdraw')->sum('money');

        $company = Company::where('key','=',$this->memberArr->mobile)->first();

        return view('front.activity_withdraw')->with('title','分享，赢现金好礼')->with('total',$total-$withdraw)->with('company',$company);
    }

    public function ajaxwithdraw(Request $request){
        $payway = $request->get('payway','');
        if(empty($payway)){
            return response()->json(array('error'=>1,'msg'=>'请选择提现方式'));
            exit();
        }

        $listArr = Members::where('recommend','=',$this->memberArr->mobile)->get(['id','icon','name','mobile','status']);
        $mArr = Members::where('id','=',$this->mid)->first();
        
        $total = 0;
        $withdraw = 0;
        foreach ($listArr as &$v){
            $moneyArr6 = Moneylog::where('mid','=',$v->id)->where('type','=','apply6')->first();
            !empty($moneyArr6)?$total += $moneyArr6->money:'';
            $moneyArr8 = Moneylog::where('mid','=',$v->id)->where('type','=','apply8')->first();
            !empty($moneyArr8)?$total += $moneyArr8->money:'';
        }
       
        $withdraw = Moneylog::where('mid','=',$this->mid)->where('type','=','withdraw')->sum('money');

        if(!Orderwithdraw::where(['mid'=>$this->mid])->whereIn('status',['1','2'])->first() ){
            $insertArr['sn'] = FunctionHelper::makeSn();
            $insertArr['mid'] = $this->mid;
            $insertArr['payuser'] = $mArr->$payway;
            $insertArr['payway'] = $payway;
            $insertArr['total'] = $total;
            $insertArr['status'] = '1';
            if(Orderwithdraw::create($insertArr)){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit();
            }
        }

        return response()->json(array('error'=>1,'msg'=>'存在已申请得提现或者提现失败'));
        exit();
    }

    public function withdrawinfo(){
        $listArr = Orderwithdraw::where(['mid'=>$this->mid])->get(); 
        
        return view('front.activity_withdrawinfo')->with('title','提现记录')->with('listArr',$listArr)->with('statusArr',Config::get('custom.orderwithdraw.statusArr'));
    }

}
