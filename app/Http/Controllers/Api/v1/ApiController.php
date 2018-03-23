<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;
use App\Models\Members;
use Hash;
use Config;
use Session;
/*
 * 
*/

class ApiController extends Controller
{
    private $easemobArr = '';
    private $systemArr = '';
    private $memberArr = '';
    public function __construct(Request $request){
        $this->easemobArr = Config::get('custom.member.easemobArr');
        $this->systemArr = Config::get('custom.system');
        $this->memberArr = Config::get('custom.member');
    }

	//注册
    public function signin(Request $request){
    	//检测验证码状态
    	$mobile = $request->get('mobile','');
    	$pwd = $request->get('password','');
    	$confirmpwd = $request->get('confirmpwd','');
    	$deviceid = $request->get('deviceid','');

    	if(empty($mobile) || empty($pwd) ||empty($deviceid)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
        }

        if($pwd != $confirmpwd ){
            return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
        }

        if('success' == Redis::get($mobile.'regstatus')){
	        if(!Members::where('mobile','=',$mobile)->first()){
	            if($r = Members::create(array('mobile'=>$mobile,'password'=>Hash::make($pwd),'name'=>FunctionHelper::makemobilestar($mobile) ))){
                    if (auth()->guard('members')->loginUsingId($r->id)) {
    	            	Redis::set($deviceid.'login',$r->id);
    	    			Redis::set($r->id.'login',$deviceid);
                        
                        EasemobHelper::addUser($this->easemobArr['member'].$r->id,md5(substr($mobile,-6)),$mobile); //环信

    	                return response()->json(array('error'=>0,'msg'=>'注册成功','data'=>array('mid'=>$r->id,'mobile'=>$mobile)));
                    } 
                    return response()->json(array('error'=>9,'msg'=>'重新登录')); 
                    exit();               
	            }
	        }
        }        
        return response()->json(array('error'=>1,'msg'=>'注册失败'));
    }

    //登录
    public function login(Request $request){
    	$mobile = $request->get('mobile','');
    	$pwd = $request->get('password','');
    	$deviceid = $request->get('deviceid','');

    	if(empty($mobile) || empty($pwd) ||empty($deviceid)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }
        
        $r = Members::where('mobile','=',$mobile)->first();
        if($r){
	    	//if(Hash::check($pwd,$r->password)){
            if (auth()->guard('members')->attempt(array('mobile' =>$mobile, 'password' => $pwd))) {
                    Redis::set($deviceid.'login',$r->id);
                    Redis::set($r->id.'login',$deviceid);

                    EasemobHelper::addUser($this->systemArr['easemobArr']['login'],md5($this->systemArr['easemobArr']['login']),$this->systemArr['easemobArr']['login']); //环信
                    EasemobHelper::sendMsg($this->systemArr['easemobArr']['login'] ,array($this->memberArr['easemobArr']['member'].$r->id),$this->systemArr['easemobmsgArr']['login']); //环信
	    		return response()->json(array('error'=>0,'msg'=>'登录成功','data'=>array('mid'=>$r->id,'mobile'=>$mobile)));
                exit(); 
	    	}
	    	return response()->json(array('error'=>1,'msg'=>'账号或者密码错误')); 
            exit();
	    }
	    return response()->json(array('error'=>8,'msg'=>'用户不存在')); 
        exit();
    }

    //短信登录
    public function logincode(Request $request){
    	$mobile = $request->get('mobile','');
    	$code = $request->get('code','');
    	$deviceid = $request->get('deviceid','');
    	if(empty($mobile) || empty($code) ||empty($deviceid)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }

        if($code == Redis::get($mobile.'logincode')){
    		if($r = Members::where('mobile','=',$mobile)->first()){
                if (auth()->guard('members')->loginUsingId($r->id)) {
    	    		Redis::set($deviceid.'login',$r->id);
    	    		Redis::set($r->id.'login',$deviceid);
    	    		return response()->json(array('error'=>0,'msg'=>'登录成功','data'=>array('mid'=>$r->id,'mobile'=>$mobile))); 
                }
                return response()->json(array('error'=>8,'msg'=>'用户不存在'));  
		    }

		    return response()->json(array('error'=>8,'msg'=>'用户不存在,请注册')); 	
        }

	    return response()->json(array('error'=>1,'msg'=>'短信验证码错误'));    
    }

    // 设置密码
    public function forgetpwd(Request $request){
    	//检测验证码状态
    	$mobile = $request->get('mobile','');
    	$pwd = $request->get('password','');
    	$confirmpwd = $request->get('confirmpwd','');

    	if(empty($mobile) || empty($pwd)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
        }

        if($pwd != $confirmpwd ){
            return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
        }

//        if('success' == Redis::get($mobile.'forgetpwdstatus')){  //hxy:暂时注释掉就可以正常使用
	        if($r = Members::where('mobile','=',$mobile)->first()){
	            if(Members::where('id','=',$r->id)->update(array('password'=>Hash::make($pwd) ))){
	                return response()->json(array('error'=>0,'msg'=>'修改成功'));                
	            }
	        }
//        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    
    //短信发送
    public function sendmessage(Request $request){
    	$mobile = $request->get('mobile','');
    	$type = $request->get('type','');

    	if(empty($mobile) || empty($type)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }

    	if(Redis::get($mobile.$type)){
    		return response()->json(array('error'=>1,'msg'=>'短信发送太频繁，请稍后再发'));
    		exit();	
    	}

    	if($type=='logincode' || $type=='forgetpwd'){
    		if(!Members::where('mobile','=',$mobile)->first()){
		        return response()->json(array('error'=>8,'msg'=>'用户不存在,请注册'));
		        exit();
		    }
    	}

    	if($type=='reg'){
    		if(Members::where('mobile','=',$mobile)->first()){
		        return response()->json(array('error'=>9,'msg'=>'用户已存在,请登录'));
		        exit();
		    }
    	}

    	$code = rand(100000,999999);
        if(FunctionHelper::sendregmsg($mobile,$code)){
            if(Redis::set($mobile.$type,$code)){
                Redis::expire($mobile.$type,120);
                return response()->json(array('error'=>0,'msg'=>'验证码已发送'));
                exit();
            }
        }
        return response()->json(array('error'=>3,'msg'=>'获取失败'));
    }

    //验证码检测
    public function checkmessage(Request $request){
    	$mobile = $request->get('mobile','');
    	$type = $request->get('type','');
    	$code = $request->get('code','');

    	if(empty($mobile) || empty($type) || empty($code)){
    		return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
    		exit();	
    	}

    	if(!FunctionHelper::isMobile($mobile)){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }

    	if($code == Redis::get($mobile.$type)){
    		Redis::set($mobile.$type.'status','success');
    		Redis::expire($mobile.$type.'status',120);
    		Redis::expire($mobile.$type,1);
            return response()->json(array('error'=>0,'msg'=>'验证通过'));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'验证码错误'));    
    }


    //获取用户配置信息
    public function getconfigposition(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$this->memberArr['positionArr']));
        exit();
    }
}
