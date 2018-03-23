<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use App\Models\Match;
use App\Models\Matchcollect;
use App\Models\Matchwarning;
use App\Models\Relation;
use App\Models\Comment;
use App\Models\Apply;
use App\Models\Applyinvite;
use App\Helpers\EasemobHelper;
use App\Models\Team;
use App\Models\Teammember;

use Hash;
use DB;
use Config;
class ApplyController extends Controller
{
    private $mid = '';
    private $memberArr = '';
    private $applyArr = '';
    private $systemArr = '';
    private $matchArr = '';
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
        $this->memberArr = Config::get('custom.member');
        $this->applyArr = Config::get('custom.apply');
        $this->systemArr = Config::get('custom.system');
        $this->matchArr = Config::get('custom.match');
	}

    //比赛报名
    public function applymatch(Request $request){
        $matchid = $request->get('matchid','');

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        if($memArr->status!='y'){
            return response()->json(array('error'=>1,'msg'=>'请先认证再报名'));
            exit();    
        }

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if($matchArr->region!='全国' && $matchArr->region!=mb_substr($memArr->province,0,2)){
            return response()->json(array('error'=>1,'msg'=>'您不在参赛区域'));
            exit();    
        }

        if(empty($memArr->province) || empty($memArr->city)){
            return response()->json(array('error'=>1,'msg'=>'请填写所在省市'));
            exit();    
        }
        
        $levelnumArr = $this->matchArr['levelnumArr'][$matchArr->level];
        $age = FunctionHelper::computerAge($memArr->birthday);
        if(!($age>=$levelnumArr['min'] && $age<=$levelnumArr['max'])){
            return response()->json(array('error'=>1,'msg'=>'您年龄不适合该比赛,比赛年龄范围为：'.$levelnumArr['min'].'-'.$levelnumArr['max'].'周岁'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        if(Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->first()){
            return response()->json(array('error'=>0,'msg'=>'报名成功1'));
            exit();
        }
        //if(Apply::create(array('mid'=>$this->mid,'matchid'=>$matchid,'position'=>$memArr->position))){
        $city = $memArr->city;
        if(in_array($memArr->province,array('北京','天津','上海','重庆'))){
            $city = $memArr->country;
        }
        if(Apply::create(array('mid'=>$this->mid,'matchid'=>$matchid,'province'=>$memArr->province,'city'=>$city ))){
            return response()->json(array('error'=>0,'msg'=>'报名成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'报名失败'));
    }

    //报名列表
    public function listapplymatch(Request $request){
        $matchid = $request->get('matchid','');
        $mArr = Apply::with('member')->where(array('mid'=>$this->mid,'matchid'=>$matchid))->first();

        $dataArr  = array();
        if(!empty($mArr)){
            if($mArr->friend_mid == 0){ //自己是队长
                $fmArr = Apply::with('member')->where(array('friend_mid'=>$this->mid,'matchid'=>$matchid))->where('status','>=','5')->get();
                $leaderArr = $mArr;
            }else{
                $leaderArr = Apply::with('member')->where(array('mid'=>$mArr->friend_mid,'matchid'=>$matchid))->first();

                $fmArr = Apply::with('member')->where(array('friend_mid'=>$mArr->friend_mid,'matchid'=>$matchid))->where('status','>=','5')->get(); 
            }

            $dataArr[0]['mid'] = empty($leaderArr->member)?'':$leaderArr->member->id;
            $dataArr[0]['icon'] = empty($leaderArr->member)?'':$leaderArr->member->icon;
            $dataArr[0]['name'] = empty($leaderArr->member)?'':$leaderArr->member->name;
            $dataArr[0]['position'] = empty($leaderArr->position)?'':$this->memberArr['positionArr'][$leaderArr->position];
            $dataArr[0]['positiont'] = empty($leaderArr->positiont)?'':$this->memberArr['positionArr'][$leaderArr->positiont];
            $dataArr[0]['isleader'] = $leaderArr->friend_mid>0?'n':'y';
            $dataArr[0]['status'] = $this->applyArr['statusArr'][$leaderArr->status];

            if(!empty($fmArr)){
                foreach ($fmArr as $k => $v) {
                    $dataArr[$k+1]['mid'] = empty($v->member)?'':$v->member->id;
                    $dataArr[$k+1]['icon'] = empty($v->member)?'':$v->member->icon;
                    $dataArr[$k+1]['name'] = empty($v->member)?'':$v->member->name;
                    $dataArr[$k+1]['position'] = empty($v->position)?'':$this->memberArr['positionArr'][$v->position];
                    $dataArr[$k+1]['positiont'] = empty($v->positiont)?'':$this->memberArr['positionArr'][$v->positiont];
                    $dataArr[$k+1]['isleader'] = $v->friend_mid>0?'n':'y';
                    $dataArr[$k+1]['status'] = $this->applyArr['statusArr'][$v->status];
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array_values($dataArr)));
        exit();
    }

    //比赛--好友邀请
    public function addapplyinvite(Request $request){
        $matchid = $request->get('matchid','');
        $friend_mid = $request->get('friendmid','');

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $friend_memArr = Members::where(array('id'=>$friend_mid))->first();
        if(empty($friend_memArr)){
            return response()->json(array('error'=>1,'msg'=>'好友不存在'));
            exit();    
        }

        $relationArr = Relation::orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$this->mid,'friend_mid'=>$friend_mid,'status'=>'4'));
        })->orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'4'));
        })->first();

        if(empty($relationArr)){
            return response()->json(array('error'=>1,'msg'=>'不存在好友关系'));
            exit();    
        }

        $applyArr = Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->where('status','>=','5')->first();
        $fapplyArr = Apply::where(array('mid'=>$friend_mid,'matchid'=>$matchid))->where('status','>=','5')->first();
        if(!empty($fapplyArr)|| !empty($applyArr) ){
            return response()->json(array('error'=>1,'msg'=>'您或您的小伙伴已经组队成功！'));
            exit(); 
        }

        //年龄段  地区 不同 不能邀请
        $memArr = Members::where(array('id'=>$this->mid))->first();
        $city1 = $memArr->city;
        if(in_array($memArr->province,array('北京','天津','上海','重庆'))){
            $city1 = $memArr->country;
        }
        $city2 = $friend_memArr->city;
        if(in_array($friend_memArr->province,array('北京','天津','上海','重庆'))){
            $city2 = $friend_memArr->country;
        }

        if($memArr->province!=$friend_memArr->province || $city1!=$city2 ){
            return response()->json(array('error'=>1,'msg'=>'您小伙伴和你不在一个赛区'));
            exit();     
        }

        $levelnumArr = $this->matchArr['levelnumArr'][$matchArr->level];
        $age = FunctionHelper::computerAge($friend_memArr->birthday);
        if($age<$levelnumArr['min'] || $age>$levelnumArr['max']){
            return response()->json(array('error'=>1,'msg'=>'您小伙伴年龄不适合该比赛'));
            exit();    
        }

        if(Applyinvite::where(array('mid'=>$this->mid,'matchid'=>$matchid,'friend_mid'=>$friend_mid))->whereIn('status',array('1'))->first() ){
            return response()->json(array('error'=>1,'msg'=>'已经邀请过了，请查看您小伙伴的回复！'));
            exit();    
        }

        if(Applyinvite::create(array('mid'=>$this->mid,'matchid'=>$matchid,'friend_mid'=>$friend_mid,'status'=>'1'))){
            EasemobHelper::sendSystemMsgToMember('invitefriend',array($this->memberArr['easemobArr']['member'].$friend_mid),$this->systemArr['easemobmsgArr']['invitefriend'].$matchArr->name);

            return response()->json(array('error'=>0,'msg'=>'好友邀请成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'好友邀请失败'));
    }

    //好友邀请列表--临时 ---删除1016
    public function listapplyinvite(Request $request){
        $listArr = Applyinvite::with('member','match')->where('friend_mid','=',$this->mid)->where('status','=','1')->get();
        $dataArr  = array();
        if(!empty($listArr)){
            //$listArr = Members::whereIn('id',$idsArr)->get();
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['mid'] = empty($v->member)?'':$v->member->id;
                $dataArr[$k]['icon'] = empty($v->member)?'':$v->member->icon;
                $dataArr[$k]['name'] = empty($v->member)?'':$v->member->name;
                $dataArr[$k]['matchid'] = empty($v->match)?'':$v->match->id;
                $dataArr[$k]['matchname'] = empty($v->match)?'':$v->match->name;
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }    

    //比赛--邀请同意
    public function acceptapplyinvite(Request $request){
        $matchid = $request->get('matchid','');
        $friend_mid = $request->get('friendmid','');

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        $friend_memArr = Members::where(array('id'=>$friend_mid))->first();
        if(empty($friend_memArr)){
            return response()->json(array('error'=>1,'msg'=>'好友不存在'));
            exit();    
        }

        $relationArr = Relation::orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$this->mid,'friend_mid'=>$friend_mid,'status'=>'4'));
        })->orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'4'));
        })->first();

        if(empty($relationArr)){
            return response()->json(array('error'=>1,'msg'=>'不存在好友关系'));
            exit();    
        }

        $applyArr = Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->where('status','>=','5')->first();
        $fapplyArr = Apply::where(array('mid'=>$friend_mid,'matchid'=>$matchid))->where('status','>=','5')->get();

        $res = false;
        if( (!empty($fapplyArr) && count($fapplyArr)>Config::get('custom.applyinvite.groupnumber')) || !empty($applyArr) ){ //失效
            //请求已失效
            if(Applyinvite::where(array('mid'=>$friend_mid,'matchid'=>$matchid,'friend_mid'=>$this->mid,'status'=>'1'))->update(array('status'=>'3'))){
                return response()->json(array('error'=>1,'msg'=>'您的好友已经完成组队'));
                exit(); 
            }
        }else{
            DB::beginTransaction();
                //修改好友状态 12个人以下 12-14
                if(count($fapplyArr)>= Config::get('custom.applyinvite.groupnumber')){
                    Apply::where(array('mid'=>$friend_mid,'matchid'=>$matchid,'status'=>'1'))->update(array('status'=>'5'));
                }

                //添加自己报名
                if(Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid,'status'=>'1'))->first()){
                    Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid,'status'=>'1'))->update(array('status'=>'5','friend_mid'=>$friend_mid));
                }else{
                    $city = $friend_memArr->city;
                    if(in_array($friend_memArr->province,array('北京','天津','上海','重庆'))){
                        $city = $friend_memArr->country;
                    }
                    Apply::create(array('mid'=>$this->mid,'matchid'=>$matchid,'friend_mid'=>$friend_mid,'status'=>'5','province'=>$friend_memArr->province,'city'=>$city));
                }

                //修改当前mid邀请状态
                Applyinvite::where(array('mid'=>$friend_mid,'matchid'=>$matchid,'friend_mid'=>$this->mid,'status'=>'1'))->update(array('status'=>'4'));
                $res = true;  
            DB::commit();    
        }

        if($res){
            EasemobHelper::sendSystemMsgToMember('inviteacceptfriend',array($this->memberArr['easemobArr']['member'].$friend_mid),$this->systemArr['easemobmsgArr']['inviteacceptfriend'].$matchArr->name);

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        } 
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }


    //邀请拒绝
    public function loseapplyinvite(Request $request){
        $matchid = $request->get('matchid','');
        $friend_mid = $request->get('friendmid','');

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $relationArr = Relation::orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$this->mid,'friend_mid'=>$friend_mid,'status'=>'4'));
        })->orWhere(function ($query) use ($friend_mid) {
            $query->where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'4'));
        })->first();

        if(empty($relationArr)){
            return response()->json(array('error'=>1,'msg'=>'不存在好友关系'));
            exit();    
        }

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        $res = false;
        DB::beginTransaction();
            //修改当前mid邀请状态
            Applyinvite::where(array('mid'=>$friend_mid,'matchid'=>$matchid,'friend_mid'=>$this->mid,'status'=>'1'))->update(array('status'=>'2'));
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //退出小组  删除自己 好友--清空好友id
    public function loseapply(Request $request){
        $matchid = $request->get('matchid','');

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        $res = false;
        DB::beginTransaction();
            Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->delete();
            if($fapplyArr = Apply::where(array('friend_mid'=>$this->mid,'matchid'=>$matchid))->first()){
                Apply::where(array('mid'=>$fapplyArr->mid,'matchid'=>$matchid))->update(array('friend_mid'=>0,'status'=>'1'));
            }
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //移除好友  直接删除好友即可
    public function delapplymember(Request $request){
        $matchid = $request->get('matchid','');
        $friend_mid = $request->get('friendmid','');

        $matchArr = Match::where(array('id'=>$matchid))->first();
        if(empty($matchArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        if(time()<=$matchArr->applystarttime || time()>=$matchArr->applyendtime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        $fapplyArr = Apply::where(array('mid'=>$friend_mid,'matchid'=>$matchid))->first();
        if(empty($fapplyArr)){
            return response()->json(array('error'=>1,'msg'=>'好友不在队伍中'));
            exit();    
        }

        $res = false;
        DB::beginTransaction();
            Apply::where(array('mid'=>$friend_mid,'matchid'=>$matchid))->delete();
            Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->update(array('friend_mid'=>0,'status'=>'1'));
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //修改位置
    public function updateapplyposition(Request $request){
        $matchid = $request->get('matchid','');
        $position = $request->get('position','');
        $positiont = $request->get('positiont','');
        
        if( !array_key_exists($position ,$this->memberArr['positionArr']) ){
            return response()->json(array('error'=>2,'msg'=>'擅长位置参数错误'));
            exit();
        }
        if( !array_key_exists($positiont ,$this->memberArr['positionArr']) ){
            return response()->json(array('error'=>2,'msg'=>'擅长位置参数错误'));
            exit();
        }

        if(Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->update(array('position'=>$position,'positiont'=>$positiont))){
            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //开始匹配
    public function startapplymatch(Request $request){
        $matchid = $request->get('matchid','');

        $applyArr = Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid,'friend_mid'=>'0'))->first();
        if(empty($applyArr) ){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }
        if(empty($applyArr->position) || empty($applyArr->position)){
            return response()->json(array('error'=>1,'msg'=>'比赛位置不能为空'));
            exit();    
        }

        $fapplyArr = Apply::where(array('friend_mid'=>$this->mid,'matchid'=>$matchid))->first();
        if(!empty($fapplyArr) && (empty($fapplyArr->position) || empty($fapplyArr->position) ) ) {
            return response()->json(array('error'=>1,'msg'=>'比赛位置不能为空2'));
            exit();    
        }        

        if($applyArr->status < 6){
            $res = false;
            DB::beginTransaction();
                Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid))->update(array('status'=>'6'));
                if(!empty($fapplyArr)){
                    Apply::where(array('friend_mid'=>$this->mid,'matchid'=>$matchid))->update(array('status'=>'6'));
                }
                $res = true;  
            DB::commit();
            if($res){
                return response()->json(array('error'=>0,'msg'=>'成功'));     
            }
        }
        
        return response()->json(array('error'=>1,'msg'=>'失败'));

    }

    //中只匹配
    public function stopapplymatch(Request $request){
        $matchid = $request->get('matchid','');
        if(!Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid,'friend_mid'=>'0'))->where('status','=','6')->first() ){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        $res = false;
        DB::beginTransaction();
            Apply::where(array('mid'=>$this->mid,'matchid'=>$matchid,'status'=>'6'))->delete();
            if( Apply::where(array('friend_mid'=>$this->mid,'matchid'=>$matchid,'status'=>'6'))->first() ){
                Apply::where(array('friend_mid'=>$this->mid,'matchid'=>$matchid,'status'=>'6'))->delete();
            }
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));

    }


    //我的比赛
    public function listapply(Request $request){
        $mArr = Apply::with('member','match')->where(array('mid'=>$this->mid))->where('status','>','1')->get();

        $dataArr  = array();
        if(!empty($mArr)){
            foreach ($mArr as $k => $v) {
                $dataArr[$k]['matchid'] = empty($v->match)?'':$v->match->id;
                $dataArr[$k]['name'] = empty($v->match)?'':$v->match->name;
                $dataArr[$k]['status'] = $this->applyArr['statusArr'][$v->status];
                $dataArr[$k]['statusmsg'] = $this->applyArr['statusmsgArr'][$v->status];

                $teamArr = '';
                if($v->status=='8'){
                    $mid = $this->mid;
                    $teamArr = Team::with('teammember')->where('matchid','=',$v->matchid)->whereHas('teammember', function ($q) use ($mid){
                        $q->where('mid', '=',$mid);
                    })->first();
                }

                $dataArr[$k]['teamid'] = empty($teamArr)?'':$teamArr->gid;//环信id
            }
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //我的比赛-详情
    public function listapplyinfo(Request $request){
        $matchid = $request->get('matchid','');
        $mArr = Apply::with('member','match')->where(array('mid'=>$this->mid,'matchid'=>$matchid))->first();

        $dataArr  = array();
        if(!empty($mArr)){
            $dataArr['sn'] = date('YmdHis').'#'.$mArr->id;
            $dataArr['status'] = $this->applyArr['statusArr'][$mArr->status];
            $dataArr['endtime'] = empty($v->match)?'正在计算...':date('Y-m-d H:i',$v->match->endtime);

            if($mArr->status=='8'){
                $mid = $this->mid;
                $teamArr = Team::with('teammember')->where('matchid','=',$matchid)->whereHas('teammember', function ($q) use ($mid){
                    $q->where('mid', '=',$mid);
                })->first();
            }

            $dataArr['teamid'] = empty($teamArr)?'':$teamArr->gid;//环信id
            $dataArr['matchid'] = $matchid;//环信id


            $i = $mArr->friend_mid==0?0:1; //自己是队长
            $j = $i==0?1:0;

            if($mArr->friend_mid >0){
                $fmArr = Apply::with('member')->where(array('mid'=>$mArr->friend_mid,'matchid'=>$matchid))->where('status','>=','5')->first();
            }else{
                $fmArr = Apply::with('member')->where(array('friend_mid'=>$this->mid,'matchid'=>$matchid))->where('status','>=','5')->first(); 
            }

            $dataArr['member'][$i]['mid'] = empty($mArr->member)?'':$mArr->member->id;
            $dataArr['member'][$i]['icon'] = empty($mArr->member)?'':$mArr->member->icon;
            $dataArr['member'][$i]['name'] = empty($mArr->member)?'':$mArr->member->name;
            $dataArr['member'][$i]['position'] = empty($mArr->position)?'':$this->memberArr['positionArr'][$mArr->position];
            $dataArr['member'][$i]['positiont'] = empty($mArr->positiont)?'':$this->memberArr['positionArr'][$mArr->positiont];
            $dataArr['member'][$i]['isleader'] = $mArr->friend_mid>0?'n':'y';
            //$dataArr['member'][$i]['status'] = $this->applyArr['statusArr'][$mArr->status];

            if(!empty($fmArr)){
                $dataArr['member'][$j]['mid'] = empty($fmArr->member)?'':$fmArr->member->id;
                $dataArr['member'][$j]['icon'] = empty($fmArr->member)?'':$fmArr->member->icon;
                $dataArr['member'][$j]['name'] = empty($fmArr->member)?'':$fmArr->member->name;
                $dataArr['member'][$j]['position'] = empty($fmArr->position)?'':$this->memberArr['positionArr'][$fmArr->position];
                $dataArr['member'][$j]['positiont'] = empty($fmArr->positiont)?'':$this->memberArr['positionArr'][$fmArr->positiont];
                $dataArr['member'][$j]['isleader'] = $fmArr->friend_mid>0?'n':'y';
                //$dataArr['member'][$j]['status'] = $this->applyArr['statusArr'][$fmArr->status];
            }
            ksort($dataArr['member']);
            $dataArr['member'] = array_values($dataArr['member']);
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }
}