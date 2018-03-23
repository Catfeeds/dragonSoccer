<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Log;

use App\Models\Members;
use Illuminate\Support\Facades\Redis;


class Webauth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        

        Log::error(date('Y-m-d H:i:s').'--Webauth--');        
        Session::put('url.intended',url("activity/index"));
        $mid = $request->get('mid','');
        $token = $request->get('token','');
        $deviceid = $request->get('deviceid','');
        $time = $request->get('time','');

        $rtoken = md5('Lzsn2017'.substr($time,4).$mid);
        if(!empty($mid) && !empty($token) && !empty($deviceid)){
            if($token == $rtoken){
                $relMid = Redis::get($deviceid.'login');
                $relDeviceid = Redis::get($mid.'login');
                if(!empty($relMid) && !empty($relDeviceid)){
                    if($relMid==$mid && $deviceid==$relDeviceid){
                        return $next($request);
                    }    
                }
            }else{
                if($request->ajax()){
                    return response()->json(array('error'=>1,'msg'=>'请刷新页面'));
                    exit();
                }else{
                    echo "<script type='text/javascript'>alert('请刷新页面');</script>";
                    exit(); 
                }
            }
            
        }
        if($request->ajax()){
            return response()->json(array('error'=>9,'msg'=>'请重新登录'));
            exit();
        }else{
            echo "<script type='text/javascript'>alert('请重新登录');</script>";
            exit();
        }
    }

   
}
