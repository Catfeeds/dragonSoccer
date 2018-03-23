<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;
use App\Helpers\PayHelper;
use Session;

use App\Models\Members;
use App\Models\Area;

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

use App\Models\Moneylog;
use App\Models\Orderwithdraw;
use App\Models\Company;
use App\Models\Orderslog;
use Auth;
use Config;
use Hash;
use DB;

class MemberController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'default/';
    public $memberArr = array();

    public function __construct(){
        $this->memberArr = Config::get('custom.member');
    }

    public function index(Request $request) {
        $id = $request->get('id','');
        $stime = $request->get('stime','');
        $etime = $request->get('etime','');
        $recommend = $request->get('recommend','');
        $mobile = $request->get('mobile','');
        $status = $request->get('status','');
        $name = $request->get('name','');
        $truename = $request->get('truename','');
        $school = $request->get('school','');
        
        $query = Members::orderBy('id', 'desc');
        if(!empty($id)){
            $query =  $query->where('id','=',$id);
        }

        if(!empty($stime) && !empty($etime)){
            $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
        }
        if(!empty($recommend)){
            $query =  $query->where('recommend','=',$recommend);
        }
        if(!empty($mobile)){
            $query =  $query->where('mobile','like','%'.$mobile.'%');
        }
        if(!empty($status)){
            $query =  $query->where('status','=',$status);
        }

        if(!empty($name)){
            $query =  $query->where('name','like','%'.$name.'%');
        }

        if(!empty($truename)){
            $query =  $query->where('truename','like','%'.$truename.'%');
        }

        if(!empty($school)){
            $query =  $query->where('school','like','%'.$school.'%');
        }

        $listArr = $query->paginate(20); 
        foreach ($listArr as $v){
            $fnum = 0;
            $listmidArr = Relation::where('mid','=',$v->id)->where('status','=','4')->count();
            $fnum += empty($listmidArr)?0:$listmidArr;
            $listfmidArr = Relation::where('friend_mid','=',$v->id)->where('status','=','4')->count();
            $fnum += empty($listmidArr)?0:$listmidArr;
            $v->fnum = $fnum;

            $mnum = 0;
            $listmatchArr = Apply::where('mid','=',$v->id)->where('status','=','8')->count();
            $mnum += empty($listmatchArr)?0:$listmatchArr;
            $v->mnum = $mnum;

            $tnum = 0;
            $listteamArr = Teammember::where('mid','=',$v->id)->count();
            $tnum += empty($listteamArr)?0:$listteamArr;
            $v->tnum = $tnum;

            $v->applynum = 0;
            $key = $v->mobile;
            $apply = Apply::where('status','>',5);
            $apply = $apply->with(array('member'=>function ($apply) use ($key) {
                    $apply->select('id','name','recommend');
                }));

            $apply = $apply->whereIn('mid', function ($apply) use ($key) {
                    $apply->where('recommend','=',$key)->select('id')->from('members');
                });

            $apply =  $apply->with(array('match'=>function($apply){
                    $apply->select('id','name');
                }));
            $v->applynum = $apply->count();

            $v->teamnum = 0;
            $team =  Teammember::with(array('member'=>function ($team) use ($key) {
                        $team->select('id','name');
                    }));
            $team = $team->whereIn('mid', function ($team) use ($key) {
                    $team->where('recommend','=',$key)->select('id')->from('members');
                });

            $team = $team->with(array('team'=>function($team){
                        $team->with(array('match'=>function($team){
                            $team->select('id','name');
                        }))->select('id','matchid','name');
                    }));
            $v->teamnum = $team->count();


        }       
        return view('admin.member_index')->with('listArr',$listArr)->with('stime',$stime)->with('etime',$etime)->with('recommend',$recommend)->with('mobile',$mobile)->with('status',$status)->with('name',$name)->with('truename',$truename)->with('school',$school)->with('id',$id);
    }

    //获取数据   
    public function add(){        
        $provinceArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
        return view('admin.member_add')->with('provinceArr',$provinceArr)->with('memberArr',$this->memberArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传头像'));
            exit();
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
            exit();
        }

        if(empty($input['password'])){
            return response()->json(array('error'=>1,'msg'=>'请填写密码'));
            exit();
        }

        if(empty($input['mobile']) || !FunctionHelper::isMobile($input['mobile'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }

        if(empty($input['idnumber']) || !FunctionHelper::isCreditNo($input['idnumber'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的身份证号'));
            exit();
        }
        $input['birthday'] = FunctionHelper::getBirthday($input['idnumber']);
        $input['sex'] = FunctionHelper::getSex($input['idnumber']);

        if(empty($input['province']) || empty($input['city']) || empty($input['country'])){
            return response()->json(array('error'=>1,'msg'=>'请选择省市区'));
            exit();
        }

        if(empty($input['address'])){
            return response()->json(array('error'=>1,'msg'=>'请填写详细地址'));
            exit();
        }

        if(empty($input['school'])){
            return response()->json(array('error'=>1,'msg'=>'请填写所在学校'));
            exit();
        }

        if(empty($input['height'])){
            return response()->json(array('error'=>1,'msg'=>'请填写身高'));
            exit();
        }

        if(empty($input['weight'])){
            return response()->json(array('error'=>1,'msg'=>'请填写体重'));
            exit();
        }

        if($input['password2'] != $input['password2'] ){
            return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
            exit();
        }
        unset($input['password2']);
        $input['password']=Hash::make($input['password']);       
               
        if(!Members::where('mobile','=',$input['mobile'])->first()){
            if($r = Members::create($input)){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.member.index') ));
                exit();                
            }
        }        
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
        exit();
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Members::where('id','=',$id)->first();
            $provinceArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
        }
        return view('admin.member_edit')->with('listArr',$listArr)->with('provinceArr',$provinceArr)->with('memberArr',$this->memberArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','icon','name','birthday','mobile','sex','idnumber','province','city','country','address','school','position','foot','instruction','height','weight','idcard_b','idcard_f','idcard_address','img','truename','nation','status'); 
        $id = $input['id'];
        unset($input['id']);
        
        if(empty($input['mobile']) || !FunctionHelper::isMobile($input['mobile'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
            exit();
        }

        if(!empty($input['idnumber']) && !FunctionHelper::isCreditNo($input['idnumber'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的身份证号'));
            exit();
        }

        $dataArr['icon'] = empty($input['icon'])?'':$input['icon']; 
        $dataArr['name'] = empty($input['name'])?'':$input['name']; 
        $dataArr['birthday'] = empty($input['birthday'])?'':$input['birthday']; 
        $dataArr['mobile'] = empty($input['mobile'])?'':$input['mobile']; 
        $dataArr['sex'] = empty($input['sex'])?'':$input['sex']; 
        $dataArr['idnumber'] = empty($input['idnumber'])?'':$input['idnumber']; 
        $dataArr['province'] = empty($input['province'])?'':$input['province']; 
        $dataArr['city'] = empty($input['city'])?'':$input['city']; 
        $dataArr['country'] = empty($input['country'])?'':$input['country']; 
        $dataArr['address'] = empty($input['address'])?'':$input['address']; 
        $dataArr['school'] = empty($input['school'])?'':$input['school']; 
        $dataArr['position'] = empty($input['position'])?'':$input['position'];
        $dataArr['foot'] = empty($input['foot'])?'':$input['foot']; 
        $dataArr['instruction'] = empty($input['instruction'])?'':$input['instruction']; 
        $dataArr['height'] = empty($input['height'])?'0':$input['height']; 
        $dataArr['weight'] = empty($input['weight'])?'0':$input['weight'];
        $dataArr['idcard_b'] = empty($input['idcard_b'])?'':$input['idcard_b']; 
        $dataArr['idcard_f'] = empty($input['idcard_f'])?'':$input['idcard_f']; 
        $dataArr['idcard_address'] = empty($input['idcard_address'])?'':$input['idcard_address']; 
        $dataArr['img'] = empty($input['img'])?'':$input['img']; 
        $dataArr['truename'] = empty($input['truename'])?'':$input['truename']; 
        $dataArr['nation'] = empty($input['nation'])?'':$input['nation']; 
        $dataArr['status'] = empty($input['status'])?'':$input['status']; 

        $r = Members::where('mobile','=',$input['mobile'])->first();
        if(empty($r) || $r->id==$id){   
            if(Members::where('id','=',$id)->update($dataArr)){ 
                return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.member.index') ));
                exit();
            }
        }else{
            return response()->json(array('error'=>1,'msg'=>'修改失败,手机号已存在'));
            exit();    
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
        exit();
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(Members::destroy((int)$id)){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
                exit();
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
        exit();
    }

    public function ajaxreset(Request $request){
        $id = $request->get('id','');
        $r = false;
        if(!empty($id)){
            $res = Members::where('id','=',$id)->first();
            if($res){
                $r = Members::where('id','=',$id)->update(array('password'=>Hash::make('Lzsn123456')),$id);
            }        
        }else{
            return response()->json(array('error'=>1,'msg'=>'数据不存在'));
            exit();
        }
        if(!empty($r)){
            return response()->json(array('error'=>0,'msg'=>'修改成功'));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
        exit();
    }

    public function money(Request $request) {
        $listArr = Moneylog::with('member')->orderBy('id', 'desc')->paginate(20);
        return view('admin.member_money')->with('listArr',$listArr)->with('typeArr',Config::get('custom.money.typeArr'));
    }

    //提现列表
    public function withdraw(Request $request) {
        $listArr = Orderwithdraw::with('member')->orderBy('id', 'desc')->paginate(20);
        return view('admin.member_withdraw')->with('listArr',$listArr)->with('statusArr',Config::get('custom.orderwithdraw.statusArr'));
    }

    //提现详情 
    public function withdrawview(Request $request) {
        $id = $request->get('id','');
        $listArr = Orderwithdraw::with('member')->where('id','=',$id)->orderBy('id', 'desc')->first();
        $mArr = array();
        $moneyArr = array(); //奖金列表
        $withdrawArr = array(); //提现列表
        $total = 0;
        $withdraw = 0;
        $company = '';
        if(!empty($listArr) && !empty($listArr->member)){
            $mArr = Members::where('recommend','=',$listArr->member->mobile)->get(['id','icon','name','mobile','status']);
            foreach ($mArr as &$v){
                $moneyArr6 = Moneylog::where('mid','=',$v->id)->where('type','=','apply6')->first();
                !empty($moneyArr6)?$total += $moneyArr6->money:'';
                !empty($moneyArr6)?$moneyArr[] = $moneyArr6:'';
                $moneyArr8 = Moneylog::where('mid','=',$v->id)->where('type','=','apply8')->first();
                !empty($moneyArr8)?$total += $moneyArr8->money:'';
                !empty($moneyArr8)?$moneyArr[] = $moneyArr8:'';
            }

            $withdraw = Moneylog::where('mid','=',$listArr->member->id)->where('type','=','withdraw')->sum('money');
            $withdrawArr = Moneylog::where('mid','=',$listArr->member->id)->where('type','=','withdraw')->get();

            $company = Company::where('key','=',$listArr->member->mobile)->first();
        }        

        return view('admin.member_withdrawview')->with('listArr',$listArr)->with('total',$total)->with('withdraw',$withdraw)->with('company',$company)->with('statusArr',Config::get('custom.orderwithdraw.statusArr'))->with('moneyArr',$moneyArr)->with('typeArr',Config::get('custom.money.typeArr'))->with('withdrawArr',$withdrawArr);
    }

    //提现
    public function ajaxwithdraw(Request $request) {
        $id = $request->get('id','');
        $payuser = $request->get('payuser','');
        $paytotal = $request->get('paytotal','');
        $status = $request->get('status','');
        $remark = $request->get('remark','');


        $listArr = Orderwithdraw::with('member')->where('id','=',$id)->orderBy('id', 'desc')->first();
        $total = 0;
        $withdraw = 0;
        $company = '';
        if(!empty($listArr) && !empty($listArr->member) && $listArr->status==1){
            if($company = Company::where('key','=',$listArr->member->mobile)->first()){ //失败
                if($status=='4' && !empty($remark)){
                    if(Orderwithdraw::where('id','=',$id)->update(['status'=>4,'remark'=>$remark]) ){
                        return response()->json(array('error'=>0,'msg'=>'修改成功'));
                        exit();
                    }
                }
                return response()->json(array('error'=>1,'msg'=>'参数错误1'));
                exit();
            }else{
                $payway=$listArr->payway;
                if(empty($payuser) || empty($paytotal) || $paytotal!=$listArr->total || $payuser!=$listArr->payuser || $payuser!=$listArr->member->$payway ){
                    return response()->json(array('error'=>1,'msg'=>'参数错误2'));
                    exit();    
                }else{
                    $mArr = Members::where('recommend','=',$listArr->member->mobile)->get(['id','icon','name','mobile','status']);
                    foreach ($mArr as &$v){
                        $moneyArr6 = Moneylog::where('mid','=',$v->id)->where('type','=','apply6')->first();
                        !empty($moneyArr6)?$total += $moneyArr6->money:'';
                        $moneyArr8 = Moneylog::where('mid','=',$v->id)->where('type','=','apply8')->first();
                        !empty($moneyArr8)?$total += $moneyArr8->money:'';
                    }

                    $withdraw = Moneylog::where('mid','=',$listArr->member->id)->where('type','=','withdraw')->sum('money');  

                    if($paytotal<=($total-$withdraw) ){ //判断提现余额
                        $data['txt'] = 'adminuser_name：'.auth()->guard('adminusers')->user()->name.'adminuser_mobile：'.auth()->guard('adminusers')->user()->mobile.' withdraw';
                        $data['sn'] = $listArr->sn;
                        $data['payuser'] = $listArr->payuser;
                        $data['amount'] = $listArr->total;
                        $data['remark'] = '提现单号：'.$listArr->sn;
                        //写日志
                        Orderslog::create(array('sn'=>$listArr->sn,'content'=>'提现提交参数:'.$payway.json_encode($data)));

                        $paySts = true;
                        $payArr = [];
                        $paytotal = '';
                        if($payway=='ali'){
                            try {
                                $payArr = PayHelper::alipay($data);
                                //写日志
                                Orderslog::create(array('sn'=>$listArr->sn,'content'=>'提现返回值:'.$payway.json_encode($payArr)));
                                Orderwithdraw::where('id','=',$id)->update(['status'=>2,'remark'=>'提现提交','checktime'=>time()]);
                            } catch (Exception $e) {
                                return response()->json(array('error'=>1,'msg'=>'提现存在错误'));
                                exit();   
                            }
                            //写日志
                            Orderslog::create(array('sn'=>$listArr->sn,'content'=>'提现返回值:'.$payway.json_encode($payArr)));
                            Orderwithdraw::where('id','=',$id)->update(['status'=>2,'remark'=>'提现提交','checktime'=>time()]);
                                                  
                            if(!empty($payArr) && $payArr['alipay_fund_trans_toaccount_transfer_response']['code']==10000){
                                $paySts = false;
                                $paytotal = $listArr->total;
                            }
                        }

                        if($payway=='wechat'){
                            try {
                                $payArr = PayHelper::wecahtpay($data);
                                //写日志
                                Orderslog::create(array('sn'=>$listArr->sn,'content'=>'提现返回值:'.$payway.json_encode($payArr)));
                                Orderwithdraw::where('id','=',$id)->update(['status'=>2,'remark'=>'提现提交']);
                            } catch (Exception $e) {
                                return response()->json(array('error'=>1,'msg'=>'提现存在错误'));
                                exit();   
                            }                           
                            if(!empty($payArr) && $payArr['return_code']=='SUCCESS'){
                                $paySts = false;
                                $paytotal = $listArr->total;
                            }
                        }

                        DB::beginTransaction();
                            if($paySts){
                                //修改状态
                                Orderwithdraw::where('id','=',$id)->update(['status'=>4,'remark'=>'支付失败']) ;
                            }else{
                                //修改状态
                                Orderwithdraw::where('id','=',$id)->update(['status'=>3,'remark'=>'支付成功','paytotal'=>$paytotal,'paytime'=>time()]); 
                                
                                Moneylog::create(['mid'=>$listArr->mid,'sn'=>$listArr->sn,'type'=>'withdraw','money'=>$paytotal]);  
                            }
                        $res = true;
                        DB::commit(); 
                        if($res){
                            return response()->json(array('error'=>0,'msg'=>'成功'));
                            exit(); 
                        }

                        return response()->json(array('error'=>1,'msg'=>'失败'));
                        exit();    
                    } 
                    return response()->json(array('error'=>1,'msg'=>'金额不足，提现存在错误'));
                    exit(); 
                }
                    
            }
        }
        return response()->json(array('error'=>1,'msg'=>'提现存在错误'));
        exit();         

    }  
  
}
