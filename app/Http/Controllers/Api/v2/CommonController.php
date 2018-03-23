<?php
namespace App\Http\Controllers\Api\v2;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Banner;
use App\Models\Webconfig;
use App\Models\Games;
use App\Models\Gamecontent;
use App\Models\School;
use App\Models\Cash;
use App\Models\Gamecollect;
use App\Models\Groupmembers;
use App\Models\Gteam;
use App\Models\Group;
use Config;
use DB;
class CommonController extends Controller{
    public function __construct(){
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

    public function gameinfo(){
        $gameinfo = array();
        $gid = Webconfig::where('key','=','indexgamesid')->first();
        $gids = explode('|',$gid->val);

        if(!empty($gids)){
            foreach ($gids as $v) {
                $gArr = Games::with('ages')->find($v);
                //var_dump($gArr->toArray());
                if(!empty($gArr)){
                    $gameinfo['gameid'] = $v;    
                    $gameinfo['name']    = $gArr->name;    
                    $gameinfo['info']    = $gArr->info;   

                    $gnum = Groupmembers::whereHas('group',function($q){ $q->select('id')->where('status','>=','2');} )->count();
                    $gameinfo['time']  = '参赛人数：'.$gnum;
                    $gameinfo['img'] = strpos($gArr->imgs,'#')===false?$gArr->imgs:substr($gArr->imgs,0,strpos($gArr->imgs,'#') );
                    $ageArr = array();
                    if(!empty($gArr->ages)){
                        $ageArr = FunctionHelper::arrayMinMax($gArr->ages->toArray(),'starttime','endtime');
                    }

                    $gtnum = Gteam::where('type','m')->count();
                    $gameinfo['age']  = '队伍数量：'.$gtnum;
                    $gameinfo['type'] = 'l';

                    $cash = Cash::sum('money');
                    $gameinfo['cash'] = number_format($cash).'元整';
                }
                $data[] = $gameinfo;
            }
        }

        $indexschoolimg = Webconfig::where('key','=','indexschoolimg')->first();

        
        //$data[] = array('type'=>'s','img' => empty($indexschoolimg->val)?'':$indexschoolimg->val);

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$data));
        exit();
    }

    //赛事详情
    public function gamecontent(Request $request){
        $gameid = $request->get('gameid','');
        $mid = $request->get('mid','');

        $dataArr = [];
        if(!empty($gameid)){
            if($gArr = Games::with('ages')->where('id',$gameid)->first() ){
                $dataArr['gameid'] = $gArr->id;    
                $dataArr['name']   = $gArr->name;     
                $dataArr['info']   = $gArr->info; 

                $gArrImg = empty($gArr->imgs)?[]:explode('#',$gArr->imgs);    
                $dataArr['img'] = empty($gArrImg[1])?(empty($gArrImg[0])?'':$gArrImg[0]):$gArrImg[1];
                $dataArr['applytime']  = '报名时间：'.date('Y年m月',$gArr->applystime).'-'.date('Y年m月',$gArr->applyetime);
                $dataArr['time']  = '参赛时间：'.date('Y年m月',$gArr->starttime).'-'.date('Y年m月',$gArr->endtime);
                $dataArr['method']  = '参赛方式：组队参赛、自由匹配';
                $dataArr['ages1']  = '参赛年龄：';

                $gaids = [];
                if(!empty($gArr->ages)){
                    foreach ($gArr->ages as $k => $v) { 
                        if($k==0){
                            $dataArr['ages1'] .= ($v->val.':'.FunctionHelper::computerAge(date('Y-m-d',$v->endtime)).'-'.FunctionHelper::computerAge(date('Y-m-d',$v->starttime)).'年龄组  ('.date('Y.m.d',$v->starttime).'-'.date('Y.m.d',$v->endtime).')'); 
                        }else{
                            $dataArr['ages'.($k+1)] = $v->val.':'.FunctionHelper::computerAge(date('Y-m-d',$v->endtime)).'-'.FunctionHelper::computerAge(date('Y-m-d',$v->starttime)).'年龄组  ('.date('Y.m.d',$v->starttime).'-'.date('Y.m.d',$v->endtime).')';
                        }
                        $gaids[] = $v->id;
                    }
                }
                $dataArr['member']  = '比赛赛制：七人制';
                $fimg = Webconfig::where('key','=','footballcashimg')->first();

                $dataArr['footballcashimg'] = empty($fimg)?'':$fimg->val;
                $cash = Cash::sum('money');
                $dataArr['cash'] = number_format($cash).'元整';

                $gArr = Gamecollect::where('mid','=',$mid)->where('gameid','=',$gameid)->first();
                $dataArr['iscollect'] = empty($gArr)?'n':'y';

                /*$groupArr = Group::whereIn('gamesagesid',$gaids)->whereHas('gmember',function($query) use ($mid){
                                $query->select('mid','groupid')->where(array('mid'=>$mid));
                            })->first();*/
                if(!empty($mid)){
                    $groupids = Groupmembers::where(['mid'=>$mid])->pluck('groupid');
                    if(!empty($groupids)){
                        $groupArr = Group::whereIn('gamesagesid',$gaids)->whereIn('id',$groupids->toArray())->first();
                    }
                }                
                $dataArr['groupid'] = empty($groupArr)?'':$groupArr->id;

                $dataArr['infourl'] = url('txt/lsruler');
            }
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    

    public function getandroidversion() {
        $android = Webconfig::where('key','=','android')->first();
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('version'=>empty($android->val)?'':$android->val,'downloadurl'=>'http://download.dragonfb.com/public/lzsn.apk')));
        exit();
    }

    public function getiosversion() {
        $ios = Webconfig::where('key','=','ios')->first();
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('version'=>empty($ios->val)?'':$ios->val,'downloadurl'=>'https://itunes.apple.com/cn/app/longshaozuqiu/id1286986769?mt=8')));
        exit();
    }

    //校园赛事
    public function gameschool(Request $request){
        $type = $request->get('type','s');
        $gArr = Games::with('ages')->whereIn('owner',function($query) use ($type){
            $query->select('id')->where('type',$type)->from('school');
        })->get();

        $dataArr = [];
        if(!empty($gArr)){
            foreach ($gArr as $k => $v) {
                $gameinfo['gameid'] = $v->id;    
                $gameinfo['name']    = $v->name;     
                $gameinfo['info']    = $v->info; 
                $gameinfo['img'] = strpos($v->imgs,'#')===false?$v->imgs:substr($v->imgs,0,strpos($v->imgs,'#') );
                $dataArr[] = $gameinfo;    
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    public function gameschoolcontent(Request $request){
        $gameid = $request->get('gameid','');
        $dataArr = [];
        if(!empty($gameid)){
            if($gArr = Games::with('ages')->where('id',$gameid)->first() ){
                $dataArr['gameid'] = $gArr->id;    
                $dataArr['name']   = $gArr->name;     
                $dataArr['info']   = $gArr->info;     
                $dataArr['infourl']   = url('/schoolinfo?id=').$gArr->id;     
                $dataArr['img'] = strpos($gArr->imgs,'#')===false?$gArr->imgs:substr($gArr->imgs,0,strpos($gArr->imgs,'#') );
            }  
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    public function getappimg() {
        $appindeximg = Webconfig::where('key','=','appindeximg')->first();
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array('img'=>empty($appindeximg->val)?'http://download.dragonfb.com/public/appindeximgdeafault.jpg':$appindeximg->val)));
        exit();
    }
    
}
