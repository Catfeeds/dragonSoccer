<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Match;
use App\Models\Matchinfo;
use App\Models\Matchcollect;
use App\Models\Apply;
use App\Models\Teammember;
use App\Models\Team;
use App\Models\Matchlog;
use Config;
use DB;
class MatchController extends Controller{
    public $matchArr = array();

    public function __construct(){
        $this->matchArr = Config::get('custom.match');
    }

    public function alist() {
        $listArr = Match::where('status','=','y')->orderBy('sid', 'desc')->get();
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['matchid'] = $v->id;    
                $arr['name']    = $v->name;    
                $arr['rule']    = $this->matchArr['ruleArr'][$v->rule];
                $arr['starttime']  = date('Y年m月',$v->starttime);    
                $arr['endtime']    = date('Y年m月',$v->endtime);
                $dataArr[] = $arr;
            }
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //今日赛程-比赛类别
    public function levellist() {
        $listArr = Match::where('status','=','y')->orderBy('sid', 'desc')->get();
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['matchid'] = $v->id;    
                $arr['matchlevel']= $this->matchArr['levelArr'][$v->level];
                $dataArr[] = $arr;
            }
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }


    public function single(Request $request) {
        $id = $request->get('matchid','');
        $mid = $request->get('mid','');
        $listArr = Match::where('id','=',$id)->where('status','=','y')->first();

        $matchArr = Matchcollect::where('mid','=',$mid)->where('matchid','=',$id)->first();
        $num = Matchcollect::where('matchid','=',$id)->count();

        $applyArr = Apply::where(array('mid'=>$mid,'matchid'=>$id))->whereIn('status',array('1','5','6','7','8'))->first();
        $teamnumbernum = Team::where('matchid','=',$id)->count(); 

        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['matchid'] = $id; 
            $dataArr['name']    = $listArr->name;    
            $dataArr['rule']    = $this->matchArr['ruleArr'][$listArr->rule];    
            //$dataArr['region']  = $listArr->region;    
            //$dataArr['sex']     = $this->matchArr['sexArr'][$listArr->sex];    
            //$dataArr['level']   = $this->matchArr['levelArr'][$listArr->level];
            //$dataArr['applystarttime']  = date('Y年m月',$listArr->applystarttime);    
            $dataArr['applyendtime']    = date('Y年m月d日H时',$listArr->applyendtime).'前';    
            $dataArr['starttime']       = date('Y年m月',$listArr->starttime);    
            $dataArr['endtime']         = date('Y年m月',$listArr->endtime);    
            $dataArr['imgs']            = empty($listArr->imgs)?array():explode('#',$listArr->imgs);
            
            $dataArr['teamnumber'] = empty($teamnumbernum)?'0':(string)$teamnumbernum;
            $dataArr['favoritenumber'] = empty($num)?'0':(string)$num;
            $dataArr['infourl'] = url('/match/info/'.$id);
            $dataArr['iscollect'] = empty($matchArr)?'n':'y';
            $dataArr['isapply'] = empty($applyArr)?'n':'y';

            $dataArr['shareurl'] = $this->matchArr['shareurl'];

        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }
}
