<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Banner;
use App\Models\Cash;
use App\Models\Matchlog;
use Config;
use DB;
class CommonController extends Controller{
    public $matchArr = array();
    public $matchlogArr = array();

    public function __construct(){
        $this->matchArr = Config::get('custom.match');
        $this->matchlogArr = Config::get('custom.matchlog');
    }

    //首页banner
    public function bannerlist() {
        $listArr = Banner::where('status','=','y')->orderBy('sid', 'desc')->get();
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['bannerid'] = $v->id;    
                $arr['name']    = $v->name;    
                $arr['sharecontent']    = $v->sharecontent;    
                $arr['img']    = $v->img;    
                $arr['url']    = $v->url;    
                $dataArr[] = $arr;
            }
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //奖金池
    /*public function cashgetallnum() {
        $allnum = Cash::sum('money');
        $allnum = empty($allnum)?0:$allnum/100;
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('allnum'=>$allnum,'infourl'=>url('/txt/cash') )));
        exit();
    }*/

    //奖金池列表
    public function cashlist() {
        $allnum = 0;
        //$dataArr = array('allnum'=>$allnum,'infourl'=>url('/txt/cash'),'platform'=>array(),'apply'=>array(),'donation'=>array() );
        $dataArr = array('allnum'=>$allnum,'infourl'=>url('/txt/cash'),'content'=>array());

        $applynum = Cash::where('type','=','apply')->sum('money');
        $applynum = empty($applynum)?0:$applynum;
        $allnum += $applynum;

        $donationnum = Cash::where('type','=','donation')->sum('money');
        $donationnum = empty($donationnum)?0:$donationnum;
        $allnum += $donationnum;

        $platformnum = Cash::where('type','=','platform')->sum('money');
        $platformnum = empty($platformnum)?0:$platformnum;
        $allnum += $platformnum;

        $dataArr['allnum'] = empty($allnum)?0:$allnum;

        $platformArr = Cash::where('type','=','platform')->first();
        if(!empty($platformArr)){
            $platformData['icon'] = $platformArr->icon;
            $platformData['name'] = $platformArr->name;
            $platformData['percent'] = floor($platformnum/$allnum*10000)/100;
            $platformData['number'] = empty($platformnum)?0:(int)$platformnum;
            $dataArr['content'][] = $platformData;
        }

        if(!empty($applynum)){
            $applyData['icon'] = 'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/cash_baomibg.png';
            $applyData['name'] = '报名活动';
            $applyData['percent'] = empty($applynum)?0:floor($applynum/$allnum*10000)/100;
            $applyData['number'] = empty($applynum)?0:(int)$applynum;
            $dataArr['content'][] = $applyData;
        }

        $donationArr = Cash::where('type','=','donation')->get();
        if(!empty($donationArr)){
            foreach ($donationArr as $k => $v) {
                $arr = array();
                $arr['icon'] = $v->icon;
                $arr['name'] = $v->name;
                $arr['number'] = empty($v->money)?0:$v->money;
                $arr['percent'] = empty($v->money)?0:floor($v->money/$allnum*10000)/100;
                $dataArr['content'][] = $arr;
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }


    //首页今日赛程
    public function dayprocesslist() {
        $query = new Matchlog();        
        $time = strtotime(date('Y-m-d'));
        //$time = strtotime('2017-11-09');
        $query = $query->where('stime','>=',$time)->where('stime','<=',$time+3600*24);

        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name','level');
            }));
        $listArr = $query->select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->paginate(3);

        $dataArr = array('number'=>empty($listArr)?0:$listArr->total(),'info'=>array());
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $key = '';
                if(substr($v->matchlevel,0,1)=='c'){
                    $key = $v->city; 
                }

                if(substr($v->matchlevel,0,1)=='p'){
                    $key = $v->province;     
                }

                if(substr($v->matchlevel,0,1)=='t'){
                    $key = '全国';     
                }

                $arr = array();
                $status = '即将开始';
                if($v->status=='e'){
                    $status = $v->ateamscore.':'.$v->bteamscore;
                }else{
                    $status = $this->matchlogArr['statusArr'][$v->status];
                }

                $arr['processid'] = $v->id;    
                //$arr['type']      = substr($v->matchlevel,0,1);    
                $arr['region']    = $key;    
                $arr['homeimg']   = $v->ateam->icon; //主场 像
                $arr['homename']  = $v->ateam->name;//主场名称 

                $arr['awayimg']   = empty($v->bteamid)?'':$v->bteam->icon ; //主场 像
                $arr['awayname']  = empty($v->bteamid)?'':$v->bteam->name;//客场名称   
                $arr['matchtime'] = $status;//状态 
                $arr['matchlevel']= $this->matchArr['levelArr'][$v->match->level];

                $dataArr['info'][] = $arr;
            }
        }
        
        
        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    public function getandroidversion() {
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('version'=>'1.3.4','downloadurl'=>'http://download.dragonfb.com/public/lzsn.apk')));
        exit();
    }

    public function getiosversion() {
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('version'=>'1.3.1','downloadurl'=>'https://itunes.apple.com/cn/app/longshaozuqiu/id1286986769?mt=8')));
        exit();
    }
}
