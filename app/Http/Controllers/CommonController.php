<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;
use App\Models\Matchinfo;
use App\Models\Members;
use Illuminate\Support\Facades\Redis;
use App\Models\Games;
use App\Models\Gamecontent;

use App\Models\Apply;
use App\Models\Group;
use App\Models\Groupmembers;

use Config;
use DB;
use Hash;
class CommonController extends Controller{
    
    private $easemobArr = '';
    private $systemArr = '';
    private $memberArr = '';
    public function __construct(Request $request){
        $this->easemobArr = Config::get('custom.member.easemobArr');
        $this->systemArr = Config::get('custom.system');
        $this->memberArr = Config::get('custom.member');
    }

    public function share(Request $request){
        $recommend = $request->get('recommend','');
        return view('front.share')->with('url',url('/invite?recommend='.$recommend));
    }


    public function invite(Request $request){
        $recommend = $request->get('recommend','');
        return view('front.invite')->with('recommend',$recommend);
    }

    //注册
    public function ajaxsignin(Request $request){
    	//检测验证码状态
    	$mobile = $request->get('mobile','');
    	$code = $request->get('code','');
    	$recommend = $request->get('recommend','reg');

    	if(empty($mobile) || empty($code) ){
    		return response()->json(array('error'=>1,'msg'=>'请填写完整资料'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
        }

        if($code == Redis::get($mobile.'reg')){
	        if(!Members::where('mobile','=',$mobile)->first()){
	            if($r = Members::create(array('mobile'=>$mobile,'password'=>Hash::make('123456'),'name'=>FunctionHelper::makemobilestar($mobile),'recommend'=>$recommend ))){
	            	
                    EasemobHelper::addUser($this->easemobArr['member'].$r->id,md5(substr($mobile,-6)),$mobile); //环信
	                return response()->json(array('error'=>0,'msg'=>'您的账号注册成功，默认登录密码为123456','url'=>url('/downloapak') ));                
	            }
	        }else{
	        	return response()->json(array('error'=>1,'msg'=>'手机号已存在'));
        		exit();		
	        }
        }else{
        	return response()->json(array('error'=>1,'msg'=>'验证码错误'));
        	exit();	
        }        
        return response()->json(array('error'=>1,'msg'=>'注册失败'));
    }

    public function schoolinfo(Request $request){
        $id = $request->get('id','');
        $listArr = [];
        if(!empty($id)){
            $listArr = Gamecontent::where('gamesid',$id)->get();
        }
        return view('front.schoolinfo')->with('listArr',$listArr);
    }


    /*public function test1(){
        ini_set('max_execution_time','0');
        for ($i=502550; $i < 535199; $i++) { 
            $mArr = Members::where('id',$i)->first();
            if(!empty($mArr)){
                $r = EasemobHelper::addUser('m'.$i,md5(substr($mArr->mobile,-6)));
                echo "---$i----$r---\r\n<br>";
            }else{
                echo "---$i----none---\r\n<br>";
            }
        }
    }*/
}
