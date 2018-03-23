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
use App\Models\Gamecollect;
use App\Models\Gamewarning;
use App\Models\School;
use App\Models\Cash;

use Config;
use DB;
class GamesController extends Controller{

    private $mid = '';
    public function __construct(Request $request){
        $this->mid = $request->get('mid','');
    }

    //添加收藏
    public function addcollect(Request $request){
        $gameid = $request->get('gameid','');
        if(empty($gameid)){
            return response()->json(array('error'=>1,'msg'=>'请选择收藏的赛事'));
            exit();
        }

        if(!Gamecollect::where('mid','=',$this->mid)->where('gameid','=',$gameid)->first()){
            if(Gamecollect::create(array('mid'=>$this->mid,'gameid'=>$gameid))){
                return response()->json(array('error'=>0,'msg'=>'收藏成功'));
                exit();
            }
        }
        return response()->json(array('error'=>1,'msg'=>'收藏失败'));
    }

    //取消收藏
    public function delcollect(Request $request){
        $gameid = $request->get('gameid','');
        if(Gamecollect::where('mid','=',$this->mid)->where('gameid','=',$gameid)->delete() ){
            return response()->json(array('error'=>0,'msg'=>'删除成功'));
            exit();
        }

        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }

    //获取举报理由
    public function getwarningreason(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>Config::get('custom.gamewarning.reasonArr')));
        exit();
    }

    //举报
    public function addwarning(Request $request){
        $gameid = $request->get('gameid','');
        $reason = $request->get('reason','');
        if(empty($gameid) ||empty($reason)){
            return response()->json(array('error'=>1,'msg'=>'请选择赛事和举报理由'));
            exit();
        }

        $reasonArr = Config::get('custom.gamewarning.reasonArr');
        if(!array_key_exists($reason ,$reasonArr)){
            return response()->json(array('error'=>1,'msg'=>'代码错误'));
            exit();
        }

        if(Gamewarning::create(array('mid'=>$this->mid,'gameid'=>$gameid,'reason'=>$reason))){
            return response()->json(array('error'=>0,'msg'=>'举报成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'举报失败'));
    }
}
