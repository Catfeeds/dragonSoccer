<?php
namespace App\Http\Controllers\Api\v1d2;
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
use App\Models\Cash;
use Config;
use DB;
class MatchController extends Controller{
    public $matchArr = array();

    public function __construct(){
        $this->matchArr = Config::get('custom.match');
    }

    public function alist() {
        $allnum = Cash::sum('money');
        $allnum = empty($allnum)?0:(int)$allnum;

        $listArr = Match::where('status','=','y')->orderBy('sid', 'desc')->get();
        $dataArr = array('allnum'=>$allnum,'infourl'=>url('/txt/cash'));
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['matchid'] = $v->id;    
                $arr['name']    = $v->name;    
                $arr['starttime']  = date('Y年m月',$v->starttime);    
                $arr['endtime']    = date('Y年m月',$v->endtime);
                $arr['cash']    = floor($allnum/count($listArr));
                $arr['level']    = $this->matchArr['levelArr'][$v->level];
                $arr['applyendtime']  = $v->applyendtime; //报名结束时间
                $dataArr['match'][] = $arr;
            }
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }
    
}
