<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Members;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Facades\Auth;
use Session;
class Apiauth
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
                return response()->json(array('error'=>1,'msg'=>'token错误'));
                exit();    
            }
            
        }
        return response()->json(array('error'=>9,'msg'=>'您还未登陆，请登陆后操作。'));
        exit();
    }
}
