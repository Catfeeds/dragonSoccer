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
use App\Models\Systemmsg;

use App\Models\Memberwarning;
use App\Models\Orders;
use App\Models\Balancelog;

use Hash;
use Config;

use Log;
class MemberController extends Controller
{
    private $mid = '';
    private $memberArr = '';
    private $systemArr = '';
    private $applyArr = '';
    private $balancelogArr = '';
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
        $this->applyArr = Config::get('custom.apply');
        $this->balancelogArr = Config::get('custom.balancelog');
	}

    public function logout(Request $request){
        $deviceid = $request->get('deviceid','');

        auth()->guard('members')->logout();
        request()->session()->flush();
        request()->session()->regenerate();
        if(Redis::del($deviceid.'login')){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit(); 
    }

    public function updatepwd(Request $request){
        $oldpwd = $request->get('oldpwd','');
        $pwd = $request->get('password','');
        $confirmpwd = $request->get('confirmpwd','');

        if(empty($oldpwd) || empty($pwd)){
            return response()->json(array('error'=>2,'msg'=>'参数不能为空'));
            exit(); 
        }

        if($pwd != $confirmpwd ){
            return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
        }

        if($r = Members::where('id','=',$this->mid)->first()){
            if(Hash::check($oldpwd,$r->password)){
                if(Members::where('id','=',$this->mid)->update(array('password'=>Hash::make($pwd) ))){
                    return response()->json(array('error'=>0,'msg'=>'修改成功'));                
                }
            }
            return response()->json(array('error'=>1,'msg'=>'旧密码错误')); 
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }

	public function single(){
        $listArr = Members::where('id','=',$this->mid)->first();
        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['mid']         = $this->mid; 
            $dataArr['icon']        = empty($listArr->icon)?'':$listArr->icon; 
            $dataArr['name']        = empty($listArr->name)?'':$listArr->name; 
            $dataArr['birthday']    = empty($listArr->birthday)?'':$listArr->birthday; 
            $dataArr['mobile']      = empty($listArr->mobile)?'':$listArr->mobile; 
            $dataArr['sex']         = empty($listArr->sex)?'':$this->memberArr['sexArr'][$listArr->sex]; 
            $dataArr['idnumber']    = empty($listArr->idnumber)?'':$listArr->idnumber;
            $dataArr['address']     = (empty($listArr->province)?'':$listArr->province.'/').(empty($listArr->city)?'':$listArr->city.'/').(empty($listArr->country)?'':$listArr->country.'/').(empty($listArr->address)?'':$listArr->address); 
            $dataArr['school']      = empty($listArr->school)?'':$listArr->school; 
            $dataArr['position']    = empty($listArr->position)?'':$this->memberArr['positionArr'][$listArr->position]; 
            $dataArr['foot']        = empty($listArr->foot)?'':$this->memberArr['footArr'][$listArr->foot]; 
            $dataArr['status']      = $this->memberArr['statusArr'][$listArr->status]; 
            $dataArr['instruction'] = empty($listArr->instruction)?'':$listArr->instruction;
            $dataArr['isshow']      = empty($listArr->isshow)?'n':$listArr->isshow;
            $dataArr['msgstatus']      = empty($listArr->msgstatus)?'n':$listArr->msgstatus;

            $dataArr['heightweight']= (empty($listArr->height)?'身高（无）/':$listArr->height.'cm/').(empty($listArr->weight)?'体重（无）':$listArr->weight.'kg'); 

            $dataArr['txturl'] = url('/txt/infoauth');

            //新增字段 10-27 
            $dataArr['idcard_b']      = empty($listArr->idcard_b)?'':$listArr->idcard_b;
            $dataArr['idcard_f']      = empty($listArr->idcard_f)?'':$listArr->idcard_f;
            $dataArr['idcard_address']= empty($listArr->idcard_address)?'':$listArr->idcard_address;
            $dataArr['img']           = empty($listArr->img)?'':$listArr->img;
            $dataArr['truename']      = empty($listArr->truename)?'':$listArr->truename;
            $dataArr['nation']        = empty($listArr->nation)?'':$listArr->nation;
            $dataArr['isauth']        = empty($listArr->isauth)?0:$listArr->isauth;

            $dataArr['ali']        = empty($listArr->ali)?'n':'y';
            $dataArr['wechat']     = empty($listArr->wechat)?'n':'y';
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //查看资料
    public function viewsingle(Request $request){
        $viewid = $request->get('viewid','');
        $listArr = Members::where('id','=',$viewid)->first();

        $relationArr = Relation::orWhere(function ($query) use ($viewid) {
            $query->where(array('mid'=>$this->mid,'friend_mid'=>$viewid,'status'=>'4'));
        })->orWhere(function ($query) use ($viewid) {
            $query->where(array('mid'=>$viewid,'friend_mid'=>$this->mid,'status'=>'4'));
        })->first();

        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['mid']         = $viewid; 
            $dataArr['icon']        = empty($listArr->icon)?'':$listArr->icon; 
            $dataArr['name']        = empty($listArr->name)?'':$listArr->name; 
            $dataArr['birthday']    = empty($listArr->birthday)?'':$listArr->birthday; 
            $dataArr['mobile']      = empty($listArr->mobile)?'':$listArr->mobile; 
            $dataArr['sex']         = empty($listArr->sex)?'':$this->memberArr['sexArr'][$listArr->sex]; 
            $dataArr['idnumber']    = empty($listArr->idnumber)?'':$listArr->idnumber;
            $dataArr['address']     = (empty($listArr->province)?'':$listArr->province.'/').(empty($listArr->city)?'':$listArr->city.'/').(empty($listArr->country)?'':$listArr->country.'/').(empty($listArr->address)?'':$listArr->address); 
            $dataArr['school']      = empty($listArr->school)?'':$listArr->school; 
            $dataArr['position']    = empty($listArr->position)?'':$this->memberArr['positionArr'][$listArr->position]; 
            $dataArr['foot']        = empty($listArr->foot)?'':$this->memberArr['footArr'][$listArr->foot]; 
            $dataArr['status']      = $this->memberArr['statusArr'][$listArr->status]; 
            $dataArr['instruction'] = empty($listArr->instruction)?'':$listArr->instruction;
            $dataArr['isshow']      = empty($listArr->isshow)?'n':$listArr->isshow;
            $dataArr['msgstatus']      = empty($listArr->msgstatus)?'n':$listArr->msgstatus;

            $dataArr['heightweight']= (empty($listArr->height)?'身高（无）/':$listArr->height.'cm/').(empty($listArr->weight)?'体重（无）':$listArr->weight.'kg'); 

            $dataArr['isrelation']      = !empty($relationArr)?'y':'n';

            //新增字段 10-27 
            $dataArr['idcard_b']      = empty($listArr->idcard_b)?'':$listArr->idcard_b;
            $dataArr['idcard_f']      = empty($listArr->idcard_f)?'':$listArr->idcard_f;
            $dataArr['idcard_address']= empty($listArr->idcard_address)?'':$listArr->idcard_address;
            $dataArr['img']           = empty($listArr->img)?'':$listArr->img;
            $dataArr['truename']      = empty($listArr->truename)?'':$listArr->truename;
            $dataArr['nation']        = empty($listArr->nation)?'':$listArr->nation;
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //修改非好友显示状态 n基本信息 y所有信息 b信息
    public function updateshow(Request $request){
        $isshow = $request->get('isshow','n');
        if(Members::where(array('id'=>$this->mid))->update(array('isshow'=>$isshow)) ){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //通知栏是否显示消息详情  n否 y是
    public function savemsgtatus(Request $request){
        $msgstatus = $request->get('msgstatus','');
        if(empty($msgstatus) || !in_array($msgstatus,array('n','y')) ){
            return response()->json(array('error'=>2,'msg'=>'参数错误'));
        }

        if( Members::where('id','=',$this->mid)->update(array('msgstatus'=>$msgstatus)) ){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功'));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));

    }

    //保存个人信息
    public function saveinfo(Request $request){
        $listArr = Members::where('id','=',$this->mid)->first();

        $isauth = $request->get('isauth','');
        $icon = $request->get('icon','');
        $name = $request->get('name','');
        
        $idnumber = $request->get('idnumber','');
        if(!empty($idnumber)){
            if(!empty($idnumber) && !FunctionHelper::isCreditNo($idnumber)){
                return response()->json(array('error'=>1,'msg'=>'请填写正确的身份证号'));
                exit();
            }

            $mArr = Members::where('idnumber','=',$idnumber)->first();
            if(!empty($mArr) && $mArr->id!=$this->mid){   
                return response()->json(array('error'=>1,'msg'=>'请填写自己的身份证号'));
                exit();
            }

            $birthday = FunctionHelper::getBirthday($idnumber);
            $sex = FunctionHelper::getSex($idnumber);
            $idnumber = $idnumber;
        }else{
            $birthday = $request->get('birthday','');
            
            $sex = $request->get('sex','');
            if(!empty($sex)){
                if(!array_key_exists($sex ,$this->memberArr['sexArr'])){
                    return response()->json(array('error'=>2,'msg'=>'性别参数错误'));
                    exit();
                }
            }
        }


        $school = $request->get('school','');

        $position = $request->get('position','');
        if(!empty($position)){
            if( !array_key_exists($position ,$this->memberArr['positionArr']) ){
                return response()->json(array('error'=>2,'msg'=>'擅长位置参数错误'));
                exit();
            }
        }

        $foot = $request->get('foot','');
        if(!empty($foot)){
            if(!array_key_exists($foot ,$this->memberArr['footArr'])){
                return response()->json(array('error'=>2,'msg'=>'惯用脚参数错误'));
                exit();
            }
        }

        $instruction = $request->get('instruction','');

        $address = $request->get('address','');
        if(!empty($address)){
            $addressArr = explode('/',$address);
            if( count($addressArr) < 4 ){
                return response()->json(array('error'=>2,'msg'=>'地址参数错误'));
                exit();
            }

            $dataArr['province']    = (empty($addressArr[0])?'':$addressArr[0]); 
            $dataArr['city']        = (empty($addressArr[1])?'':$addressArr[1]); 
            $dataArr['country']     = (empty($addressArr[2])?'':$addressArr[2]); 
            $dataArr['address']     = (empty($addressArr[3])?'':$addressArr[3]);
        }

        $heightweight = $request->get('heightweight','');
        if(!empty($heightweight)){
            $heightweightArr = explode('/',$heightweight);
            if( count($heightweightArr) < 2 ){
                return response()->json(array('error'=>2,'msg'=>'身高体重参数错误'));
                exit();
            }
            $dataArr['height']    = (empty($heightweightArr[0])?'':intval($heightweightArr[0])); 
            $dataArr['weight']    = (empty($heightweightArr[1])?'':intval($heightweightArr[1])); 
        }
        
        $dataArr['icon']        = empty($icon)?'':$icon; 
        $dataArr['name']        = empty($name)?'':$name; 
        $dataArr['birthday']    = empty($birthday)?'':$birthday; 
        $dataArr['sex']         = empty($sex)?'':$sex; 
        $dataArr['idnumber']    = empty($idnumber)?'':$idnumber;
        $dataArr['school']      = empty($school)?'':$school; 
        $dataArr['position']    = empty($position)?'':$position; 
        $dataArr['foot']        = empty($foot)?'':$foot; 
        
        

        $str = '';
        if($listArr->status=='y'){
            unset($dataArr['idnumber']);
            unset($dataArr['name']);
            unset($dataArr['birthday']);
            unset($dataArr['sex']);
        }

        $str = '';
        if($listArr->status=='n' && $isauth=='y'){
            if($listArr->isauth < 2){
                if(FunctionHelper::checkidcardnum($dataArr['idnumber'],$dataArr['name'])){//认证
                    $dataArr['status'] = 'y'; 
                    $str = '认证成功';    
                }else{
                    $str = '您的还有'.(2-$listArr->isauth).'次认证机会，请检查完整信息';
                    $dataArr['isauth'] = $listArr->isauth+1; 
                }
            }else{
                $str = '您的认证机会已使用完,请联系客服010-57159820';    
            }
        }
        
        $dataArr['instruction'] = empty($instruction)?'':$instruction; 
        if(Members::where('id','=',$this->mid)->update($dataArr)){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功'.(empty($str)?'':';'.$str)));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));

    }
    
    //意见反馈
    public function addcomment(Request $request){
        $content = $request->get('content','');
        if(empty($content)){
            return response()->json(array('error'=>1,'msg'=>'请填写意见'));
            exit();
        }

        if(Comment::create(array('mid'=>$this->mid,'content'=>$content))){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //获取用户配置信息
    public function getconfig(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$this->memberArr));
        exit();
    }

    //我的收藏
    public function getcollectlist(){
        $listArr = Matchcollect::with('match')->where('mid','=',$this->mid)->get();
        $dataArr  = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                if(empty($v->match)){
                    continue;
                }
                //$dataArr[$k]['collectid'] = $v->id;
                $dataArr[$k]['matchid'] = $v->matchid;
                $dataArr[$k]['title'] = $v->match->name;
                $dataArr[$k]['creattime'] = substr($v->match->created_at,0,10);
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //添加收藏
    public function addcollect(Request $request){
        $matchid = $request->get('matchid','');
        if(empty($matchid)){
            return response()->json(array('error'=>1,'msg'=>'请选择收藏的赛事'));
            exit();
        }

        if(!Matchcollect::where('mid','=',$this->mid)->where('matchid','=',$matchid)->first()){
            if(Matchcollect::create(array('mid'=>$this->mid,'matchid'=>$matchid))){
                return response()->json(array('error'=>0,'msg'=>'收藏成功'));
                exit();
            }
        }
        return response()->json(array('error'=>1,'msg'=>'收藏失败'));
    }

    //取消收藏
    public function delcollect(Request $request){
        $matchid = $request->get('matchid','');
        if(Matchcollect::where('mid','=',$this->mid)->where('matchid','=',$matchid)->delete() ){
            return response()->json(array('error'=>0,'msg'=>'删除成功'));
            exit();
        }

        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }

    //获取举报理由
    public function getwarningreason(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>Config::get('custom.matchwarning.reasonArr')));
        exit();
    }

    //举报
    public function addwarning(Request $request){
        $matchid = $request->get('matchid','');
        $reason = $request->get('reason','');
        if(empty($matchid) ||empty($reason)){
            return response()->json(array('error'=>1,'msg'=>'请选择赛事和举报理由'));
            exit();
        }

        $reasonArr = Config::get('custom.matchwarning.reasonArr');
        if(!array_key_exists($reason ,$reasonArr)){
            return response()->json(array('error'=>1,'msg'=>'代码错误'));
            exit();
        }

        if(Matchwarning::create(array('mid'=>$this->mid,'matchid'=>$matchid,'reason'=>$reason))){
            return response()->json(array('error'=>0,'msg'=>'举报成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'举报失败'));
    }

    //获取个人举报理由
    public function getmemberwarningreason(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>Config::get('custom.memberwarning.reasonArr')));
        exit();
    }

    //个人举报
    public function addmemberwarning(Request $request){
        $memberid = $request->get('memberid','');
        $reason = $request->get('reason','');
        if(empty($memberid) ||empty($reason)){
            return response()->json(array('error'=>1,'msg'=>'请选择举报人ID和举报理由'));
            exit();
        }

        $reasonArr = Config::get('custom.memberwarning.reasonArr');
        if(!array_key_exists($reason ,$reasonArr)){
            return response()->json(array('error'=>1,'msg'=>'代码错误'));
            exit();
        }

        if(Memberwarning::create(array('mid'=>$this->mid,'memberid'=>$memberid,'reason'=>$reason))){
            return response()->json(array('error'=>0,'msg'=>'举报成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'举报失败'));
    }
    
    

    //好友列表
    public function listrelation(Request $request){
        $listmidArr = Relation::where('mid','=',$this->mid)->where('status','=','4')->pluck('friend_mid')->toArray();
        $listfmidArr = Relation::where('friend_mid','=',$this->mid)->where('status','=','4')->pluck('mid')->toArray();

        $idsArr  = array();
        if(!empty($listmidArr)){
            $idsArr  = $listmidArr;
        }

        if(!empty($idsArr)){
            if(!empty($listfmidArr)){
                $idsArr  = array_unique( array_merge($idsArr,$listfmidArr) );
            }
        }else{
            if(!empty($listfmidArr)){
                $idsArr  = $listfmidArr;
            }
        }

        $dataArr  = array();
        if(!empty($idsArr)){
            $listArr = Members::whereIn('id',$idsArr)->get();
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['mid'] = $v->id;
                $dataArr[$k]['icon'] = $v->icon;
                $dataArr[$k]['name'] = $v->name;
                $dataArr[$k]['mobile'] = FunctionHelper::makemobilestar($v->mobile);
                $dataArr[$k]['easemobtype'] = $this->memberArr['easemobtypeArr']['member'];
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //用户查找
    public function searchlist(Request $request){
        $keywd = $request->get('keywd','');
        $listArr  = array();
        if(!empty($keywd)){
            $listArr = Members::orWhere('name','like','%'.$keywd.'%')->orWhere('mobile','like','%'.$keywd.'%')->get();
            $teamArr = Team::where('type','=','f')->where('name','like','%'.$keywd.'%')->get();
        }

        $dataArr  = array('member'=>array(),'team'=>array());
        if(!empty($listArr)){
            $dataArr2  = array();
            $i = 0;
            foreach ($listArr as $k => $v) {
                if($v->id == $this->mid){
                    continue;
                }
                $dataArr2[$i]['mid'] = $v->id;
                $dataArr2[$i]['icon'] = $v->icon;
                $dataArr2[$i]['name'] = $v->name;
                $dataArr2[$i]['mobile'] = FunctionHelper::makemobilestar($v->mobile);
                $i++;
            }
            $dataArr['member'] = $dataArr2;
        }

        if(!empty($teamArr)){
            //var_dump($teamArr->toArray());
            $dataArr3  = array();
            foreach ($teamArr as $k => $v) {
                $dataArr3[$k]['teamid'] = $v->id;
                $dataArr3[$k]['icon'] = $v->icon;
                $dataArr3[$k]['name'] = $v->name;
            }
            $dataArr['team'] = $dataArr3;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //好友申请
    public function applyrelation(Request $request){
        $friend_mid = $request->get('friendmid','');
        //var_dump($this->systemArr['easemobArr']['addfriend']);
        //var_dump($this->memberArr['easemobArr']['member']);

        if(empty($friend_mid)){
            return response()->json(array('error'=>1,'msg'=>'请选择需要添加的好友'));
            exit();
        }

        if( !Members::where(array('id'=>$friend_mid))->first() ){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        if( Relation::where(array('mid'=>$this->mid,'friend_mid'=>$friend_mid))->whereIn('status',array('1','4'))->first() ){
            return response()->json(array('error'=>1,'msg'=>'好友申请/关系已存在'));
            exit();    
        }

        if( Relation::where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid))->whereIn('status',array('1','4'))->first() ){
            return response()->json(array('error'=>1,'msg'=>'好友申请/关系已存在'));
            exit();    
        }

        if(Relation::create(array('mid'=>$this->mid,'friend_mid'=>$friend_mid,'status'=>'1'))){
            EasemobHelper::sendSystemMsgToMember('addfriend',array($this->memberArr['easemobArr']['member'].$friend_mid),$this->systemArr['easemobmsgArr']['addfriend']);
            return response()->json(array('error'=>0,'msg'=>'好友申请发送成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'好友申请发送失败'));
    }

    //好友申请列表----临时
    public function listapplyrelation(Request $request){
        //$idsArr = Relation::where('friend_mid','=',$this->mid)->where('status','=','1')->pluck('mid')->toArray();
        $listArr = Relation::with('member')->where('friend_mid','=',$this->mid)->get();
        $dataArr  = array();
        if(!empty($listArr)){
            //$listArr = Members::whereIn('id',$idsArr)->get();
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['mid'] = $v->member->id;
                $dataArr[$k]['icon'] = $v->member->icon;
                $dataArr[$k]['name'] = $v->member->name;
                $dataArr[$k]['status'] = $v->status;
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }    

    //忽略好友添加请求
    public function loserelation(Request $request){
        $friend_mid = $request->get('friendmid','');

        if(empty($friend_mid)){
            return response()->json(array('error'=>2,'msg'=>'参数错误'));
            exit();
        }

        if( !Members::where(array('id'=>$friend_mid))->first() ){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }
        
        if(Relation::where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'1'))->update(array('status'=>'2')) ){
            return response()->json(array('error'=>0,'msg'=>'忽略成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'忽略失败'));
    }

    //同意添加好友
    public function acceptrelation(Request $request){
        $friend_mid = $request->get('friendmid','');

        if(empty($friend_mid)){
            return response()->json(array('error'=>2,'msg'=>'参数错误'));
            exit();
        }

        if( !Members::where(array('id'=>$friend_mid))->first() ){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }
        
        if(Relation::where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'1'))->update(array('status'=>'4')) ){

            EasemobHelper::addFriend($this->memberArr['easemobArr']['member'].$this->mid,$this->memberArr['easemobArr']['member'].$friend_mid); //环信
            EasemobHelper::addFriend($this->memberArr['easemobArr']['member'].$friend_mid,$this->memberArr['easemobArr']['member'].$this->mid); //环信

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //删除好友关系
    public function delrelation(Request $request){
        $friend_mid = $request->get('friendmid','');

        if(empty($friend_mid)){
            return response()->json(array('error'=>1,'msg'=>'请选择需要删除的好友'));
            exit();
        }

        if( !Members::where(array('id'=>$friend_mid))->first() ){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }
        
        if(Relation::where(array('mid'=>$this->mid,'friend_mid'=>$friend_mid,'status'=>'4'))->delete() || Relation::where(array('mid'=>$friend_mid,'friend_mid'=>$this->mid,'status'=>'4'))->delete() ){

            EasemobHelper::deleteFriend($this->memberArr['easemobArr']['member'].$this->mid,$this->memberArr['easemobArr']['member'].$friend_mid); //环信
            EasemobHelper::deleteFriend($this->memberArr['easemobArr']['member'].$friend_mid,$this->memberArr['easemobArr']['member'].$this->mid); //环信

            return response()->json(array('error'=>0,'msg'=>'好友删除成功'));
            exit();
        }
        
        return response()->json(array('error'=>1,'msg'=>'好友删除失败'));
    }


    //消息提醒
    public function allmsg(Request $request){
        $dataArr  = array('member'=>array(),'applyinvite'=>array(),'apply'=>array());

        //好友邀请
        $relationArr = Relation::with('member')->where('friend_mid','=',$this->mid)->get();       
        if(!empty($relationArr)){
            $dataArr2  = array();
            foreach ($relationArr as $k => $v) {
                $dataArr2[$k]['mid'] = $v->member->id;
                $dataArr2[$k]['icon'] = $v->member->icon;
                $dataArr2[$k]['name'] = $v->member->name;
                $dataArr2[$k]['status'] = $v->status;
                $dataArr2[$k]['title'] = '添加您为好友';
            }
            $dataArr['member'] = $dataArr2;
        }

        //比赛邀请
        $applyinviteArr = Applyinvite::with('member','match')->where('friend_mid','=',$this->mid)->get();
        if(!empty($applyinviteArr)){
            $dataArr3  = array();
            foreach ($applyinviteArr as $k => $v) {
                $dataArr3[$k]['mid'] = empty($v->member)?'':$v->member->id;
                $dataArr3[$k]['icon'] = empty($v->member)?'':$v->member->icon;
                $dataArr3[$k]['name'] = empty($v->member)?'':$v->member->name;
                $dataArr3[$k]['matchid'] = empty($v->match)?'':$v->match->id;
                $dataArr3[$k]['status'] = $v->status;
                $dataArr3[$k]['title'] = '邀请您为参加'.empty($v->match)?'':$v->match->name;
            }
            $dataArr['applyinvite'] = $dataArr3;
        }

        //匹配状态
        $applyArr = Apply::with('match')->where('mid','=',$this->mid)->get();
        if(!empty($applyArr)){
            $dataArr4  = array();
            foreach ($applyArr as $k => $v) {
                $img = '';
                if(!empty($v->match) && !empty($v->match->img)){
                    $imgArr = explode('#',$v->match->img);
                    $img = empty($imgArr)?'':$imgArr[0];
                }
                $dataArr4[$k]['icon'] = $img;

                $dataArr4[$k]['title'] = empty($v->match)?'':$v->match->name;
                $dataArr4[$k]['status'] = $this->applyArr['statusArr'][$v->status];
            }
            $dataArr['apply'] = $dataArr4;
        }

        //系统通知
        $sysArr = Systemmsg::where('mid','=',$this->mid)->get();
        if(!empty($sysArr)){
            $dataArr5  = array();
            foreach ($sysArr as $k => $v) {
                $dataArr5[$k]['title'] = $v->content;
                $dataArr5[$k]['type'] = $v->type;
            }
            $dataArr['systemmsg'] = $dataArr5;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }


    //12-05  个人数据
    public function infodata(){
        $listArr = Members::where('id','=',$this->mid)->first();
        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['mid']         = $this->mid; 
            $dataArr['icon']        = empty($listArr->icon)?'':$listArr->icon; 
            $dataArr['name']        = empty($listArr->name)?'':$listArr->name;
            $dataArr['status']      = $this->memberArr['statusArr'][$listArr->status]; 

            $i = 7;
            if(empty($listArr->province)||empty($listArr->city)|| empty($listArr->country) || empty($listArr->address)){
                $i -=1; 
            }
            if(empty($listArr->school)){
                $i -=1; 
            }
            if(empty($listArr->position)){
                $i -=1; 
            }
            if(empty($listArr->foot)){
                $i -=1; 
            }
            if(empty($listArr->weight)){
                $i -=1; 
            }
            if(empty($listArr->height)){
                $i -=1; 
            }
            if(empty($listArr->img)){
                $i -=1; 
            }
            $dataArr['footballsts'] = $i==0?0:ceil($i*100/7);

            $dataArr['balance'] = empty($listArr->balance)?0:$listArr->balance;

            $dataArr['order'] = Orders::where('mid','=',$this->mid)->count();

            $dataArr['apply'] = Apply::where('mid','=',$this->mid)->count();

            $dataArr['matchcollect'] = Matchcollect::where('mid','=',$this->mid)->count();
           
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //我的龙珠
    public function balanceloglist(){
        $listArr = Balancelog::where('mid','=',$this->mid)->get();
        $dataArr  = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['number'] = $v->number;
                $dataArr[$k]['symbol'] = $this->balancelogArr['symbolArr'][$v->type];
                $dataArr[$k]['type'] = $v->type;
                $dataArr[$k]['typemsg'] = $this->balancelogArr['typeArr'][$v->type];
                $dataArr[$k]['time'] = strtotime($v->created_at);
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //登录送龙珠
    public function balancelogadd(){
        if(!Balancelog::where('mid','=',$this->mid)->where('created_at','>=',date('Y-m-d'))->where('created_at','<=',date('Y-m-d').' 23:59:59')->first()){
            if(Balancelog::create(array('sn'=>'','mid'=>$this->mid,'type'=>'login','number'=>10 ))){
                if(Members::where('id','=',$this->mid)->increment('balance',10)){
                    return response()->json(array('error'=>0,'msg'=>'成功'));
                    exit();
                }
            }    
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();
    }

    //支付宝签名 12-13
    public function getalisign(Request $request){
        $dataArr['apiname'] = 'com.alipay.account.auth';
        $dataArr['method'] = 'alipay.open.auth.sdk.code.get';
        $dataArr['app_id'] = Config::get('pay.alipay.app_id');
        $dataArr['app_name'] = 'mc';
        $dataArr['biz_type'] = 'openservice';
        $dataArr['pid'] = '2088821717041744';
        $dataArr['product_id'] = 'APP_FAST_LOGIN';
        $dataArr['scope'] = 'kuaijie';
        $dataArr['target_id'] = md5(time());
        $dataArr['auth_type'] = 'AUTHACCOUNT';
        $dataArr['sign_type'] = 'RSA2';

        $data = FunctionHelper::getSignContent($dataArr);
        $res = "-----BEGIN RSA PRIVATE KEY-----\n".wordwrap(Config::get('pay.alipay.private_key'), 64, "\n", true)."\n-----END RSA PRIVATE KEY-----";
        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);

        $sign = base64_encode($sign);

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$data.'&sign='.$sign));
        exit();
    }


    //保存支付宝微信账号
    public function savealiwechat(Request $request){
        $ali = $request->get('ali','');
        $wechat = $request->get('wechat','');
        $dataArr = array();
        if(!empty($ali)){
            $dataArr['ali'] = $ali;
        }

        if(!empty($wechat)){
            $dataArr['wechat'] = $wechat;
        }

        if(empty($dataArr)){            
            return response()->json(array('error'=>1,'msg'=>'参数错误'));
            exit();
        }
       
        if(Members::where('id','=',$this->mid)->update($dataArr) ){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功' ));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));

    }


    public function loginbymid(){
        if($r = Members::where('id','=',$this->mid)->first()){
            if (auth()->guard('members')->loginUsingId($this->mid)) {

                Log::error(date('Y-m-d H:i:s').'--loginbymid--'.json_encode(auth()->guard('members')->user()));
                return response()->json(array('error'=>0,'msg'=>'登录成功','data'=>array('mid'=>$r->id,'mobile'=>$r->mobile))); 
                exit();
            }
        }
        return response()->json(array('error'=>9,'msg'=>'您还未登录，请登录后操作。'));
        exit();
    }


   
}