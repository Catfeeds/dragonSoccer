<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;

use App\Models\Members;
use App\Models\Adminusers;
use Illuminate\Support\Facades\Redis;

use Config;
use DB;
use Hash;
class WebimController extends Controller{    
    public function __construct(){
       
    }

    public function login(){
        return view('webim.login');
    }

    public function logout(){
        $mobile = 'kf'.auth('adminusers')->user()->mobile;
        if(Redis::lRem('kfWaitList','0',$mobile)){ 
            auth()->guard('adminusers')->logout();
            request()->session()->flush();
            request()->session()->regenerate();           
            return redirect('/webim/login');
        }
    }

    public function ajaxlogin(Request $request){
        $mobile = $request->input('mobile');   
        $password = $request->input('password');

        if (empty($password)) {
            return response()->json(array('error' =>1, 'msg' => '密码不能为空'));
            exit(); 
        }
        if (!FunctionHelper::isMobile($mobile)) {
            return response()->json(array('error' =>1, 'msg' => '手机格式不对')); 
            exit();
        }         

        if (auth()->guard('adminusers')->attempt(array('mobile' =>$mobile, 'password' => $password))) {
            return response()->json(array('error'=>0,'msg'=>'成功','url'=>url('webim/chat')));
            exit();
        }

        return response()->json(array('error' =>1, 'msg' => '账号或密码不正确'));
        exit();
        
    }

    public function chat(Request $request){
        $mobile = 'kf'.auth('adminusers')->user()->mobile;
        if(Redis::get($mobile)){
            EasemobHelper::addUser($mobile,md5(substr($mobile,-6)),auth('adminusers')->user()->name);
            Redis::set($mobile,'1');
        }
        //设置空闲队列
        Redis::lRem('kfWaitList','0',$mobile);
        Redis::lpush('kfWaitList',$mobile);

        $mArr = Members::limit(5)->get();

        return view('webim.chat')->with('uname',$mobile)->with('pwd',md5(substr($mobile,-6)))->with('mArr',$mArr);
    }

    public function ajaxgetmemberbymobile(Request $request){
        $mobile = $request->get('mobile','');        
        $mArr = Members::where('mobile',$mobile)->first();

        $str = '';
        if(!empty($mArr)){
            $str .= '<li class="pull-left active" style="height: 28px; line-height: 28px; margin-bottom:5px; width:95%; " datename="m'.$mArr->id.'" >';
                $str .= '<img style="margin-right:5px;" src="'.$mArr->icon.'" width="28" class="img-circle pull-left">';
                $str .= '<span class="pull-left hidden-xs" >'.$mArr->name.'-'.$mArr->mobile.'</span>';
                $str .= '<span class="pull-left number" style="color:red; font-size: 28px; margin-left:5px;"></span>';
            $str .= '</li>'; 
        }
        return response()->json(array('str'=>$str,'mid'=>(empty($mArr)?'':'m'.$mArr->id) ));
    }

    public function ajaxsetownerchat(Request $request){
        $to = $request->get('to','');
        $from = 'kf'.auth('adminusers')->user()->mobile;
        $content = $request->get('content','');
        $type = $request->get('type','txt');

        $key = $from.$to;
        $time = time();
        if(Redis::hset($key,$from.'#'.$time,$content)){
            if(Redis::hset($key.'type',$time,$type)){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit();
            }   
        }
        return response()->json(array('error'=>0,'msg'=>'失败'));
        exit(); 
    }


    public function ajaxsetchat(Request $request){
        $from = $request->get('from','');
        $to = 'kf'.auth('adminusers')->user()->mobile;
        $content = $request->get('content','');
        $type = $request->get('type','txt');

        $key = $to.$from;
        $time = time();
        if(Redis::hset($key,$from.'#'.$time,$content)){
            if(Redis::hset($key.'type',$time,$type)){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit();
            }   
        }
        return response()->json(array('error'=>0,'msg'=>'失败'));
        exit(); 
    }

    public function ajaxgetchat(Request $request){
        $from = 'kf'.auth('adminusers')->user()->mobile;
        $to = $request->get('from','');//当前

        $toArr = array();
        if(substr($to,0,1)=='m'){
            $toArr = Members::where('id','=',substr($to,1))->first();
        }else{
            $toArr = Adminusers::where('mobile','=',$to)->first();
        }

        $key = $from.$to;
        //var_dump($key);
        $dataStr = array();
        if($content = Redis::hgetall($key)){
            foreach ($content as $k => $v) {
                $str = '';
                $from2 = substr($k,0,-strlen(time().'y'));
                $time = substr($k,-strlen(time()));
                //var_dump($from2);
                $pull = 'text-left';
                if($from==$from2){
                    $pull = 'text-right';
                }
                $str .= '<li style="margin-buttom:5px; height:30px; line-height: 30px;" class="col-xs-12 '.$pull.' " >';
                if($from!=$from2){
                    $str .= '<img style="margin-right:5px;" class="img-circle"  src="'.(empty($toArr->icon)?'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/cash_baomibg.png':$toArr->icon).'" width="28" height="28">';
                    $str .= '<span style="margin-right:5px;">'.(empty($toArr->name)?$toArr->mobile:$toArr->name).'</span>';
                }      
                $str .= (date('H:i:s',$time));

                if($from==$from2){
                    $str .= '<span style="margin-left:5px;">'.(empty(auth('adminusers')->user()->name)?'kf'.auth('adminusers')->user()->mobile:auth('adminusers')->user()->name).'</span>';
                    $str .= '<img style="margin-left:5px;" class="img-circle" src="'.(empty(auth('adminusers')->user()->icon)?'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/cash_baomibg.png':auth('adminusers')->user()->icon).'" width="28" height="28">';
                }       
                $str .= '</li>';
                if($from==$from2){
                    $str .= '<li style="margin-buttom:5px;" class="col-xs-12 '.$pull.' " >';
                }else{
                    $str .= '<li style="margin-buttom:5px; margin-left:30px;" class="col-xs-12 '.$pull.' " >';
                }

                $r = Redis::hget($key.'type',$time);
                if($r =='img'){
                    $str .= '<p style="width:96%;"><img src="'.$v.'"></p>'; 
                }else{
                    $str .= '<p style="width:96%;">'.$v.'</p>';  
                }
                
                $str .= '</li>';

                $dataStr[$time] = $str; 
            }   
        }
        empty($dataStr)?'':ksort($dataStr);
        echo empty($dataStr)?'':(implode('',$dataStr)); 
    }


    public function ajaxgetmember(Request $request){
        $to = $request->get('to','');

        $toArr = array();
        if(substr($to,0,1)=='m'){
            $toArr = Members::where('id','=',substr($to,1))->first();
        }else{
            $toArr = Adminusers::where('mobile','=',$to)->first();
        }        

        if(!empty($toArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','uname'=>$toArr->mobile,'icon'=>$toArr->icon));
            exit();    
        }
        return response()->json(array('error'=>0,'msg'=>'失败'));
        exit(); 
    }

}
