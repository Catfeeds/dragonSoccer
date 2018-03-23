<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Models\Company;
use App\Models\Members;

use App\Models\Apply;
use App\Models\Applyinvite;
use App\Models\Team;
use App\Models\Teammember;
use Illuminate\Support\Facades\Redis;
use DB;
use Config;
class CompanyController extends Controller{
    
    public function __construct(){
    }

    public function index() {
        $listArr = Company::orderBy('id', 'desc')->paginate(20);        
        return view('admin.company_index')->with('listArr',$listArr);
    }

    public function view(Request $request){
        $stime = $request->get('stime','');
        $etime = $request->get('etime','');
        $key = $request->get('key','');
        $type = $request->get('type','apply');

        $isauth = $request->get('isauth',''); //比赛状态
        $mobile = $request->get('mobile',''); //手机号
        $status = $request->get('status',''); //比赛状态

        $dataArr = array();
        if($type=='reg'){
            $query = Members::where('recommend','=',$key);
            if(!empty($isauth)){
                $query =  $query->where('status','=',$isauth);
            }
            if(!empty($mobile)){
                $query =  $query->where('mobile','=',$mobile);
            }

            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }

            if(empty($status)){ //只读取报名 
                $query = $query->with(['apply'=>function($query){ 
                    $query->with(['match'=>function($query){ 
                        $query->select('id','name');
                    }])->select('mid','matchid','status','province','city');
                }]);
            }else{
                if($status=='applyno'){
                    $query = $query->whereNotIn('id', function ($query){
                        $query->select('mid')->whereIn('status',['1','5','6','7','8'])->from('apply');
                    });
                }

                if($status=='applyyes'){
                    $query = $query->whereHas('apply',function($query){ 
                            $query->select('mid','matchid','status','province','city');}
                        )->whereHas('apply.match',function($query){ 
                            $query->select('id','name');}
                        )->whereIn('id', function ($query){
                            $query->select('mid')->whereIn('status',['1','5','6','7','8'])->from('apply');
                        });
                }

                if($status=='apply1'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['1'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply5'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['5'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply6'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['6','7'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply8'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['8'])->select('mid','matchid','status','province','city');
                    });
                }
            }
            //var_dump($query->toSql());
            $dataArr =  $query->select(['id','name','mobile','status','created_at'])->paginate(20);
        }


        if($type=='apply'){
            $query = Apply::where('status','>',5);
            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }

            $query = $query->with(array('member'=>function ($query) use ($key) {
                    $query->select('id','name','recommend');
                }));

            $query = $query->whereIn('mid', function ($query) use ($key) {
                    $query->where('recommend','=',$key)->select('id')->from('members');
                });

            $query =  $query->with(array('match'=>function($query){
                    $query->select('id','name');
                }));
            $dataArr = $query->select(['id','mid','matchid','created_at'])->paginate(20);
        }
        if($type=='team'){
            $query =  Teammember::with(array('member'=>function ($query) use ($key) {
                        $query->select('id','name');
                    }));
            $query = $query->whereIn('mid', function ($query) use ($key) {
                    $query->where('recommend','=',$key)->select('id')->from('members');
                });

            $query = $query->with(array('team'=>function($query){
                        $query->with(array('match'=>function($query){
                            $query->select('id','name');
                        }))->select('id','matchid','name');
                    }));
            
            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }
            $dataArr = $query->select(['id','mid','teamid','created_at'])->paginate(20);
        }
        //var_dump($dataArr->toArray());
        return view('admin.company_view')->with('listArr',$dataArr)->with('type',$type)->with('key',$key)->with('stime',$stime)->with('etime',$etime)->with('status',$status)->with('mobile',$mobile)->with('isauth',$isauth)->with('applyArr',Config::get('custom.apply'));
    }

    //获取数据   
    public function add(){        
        return view('admin.company_add');
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
            exit();
        }

        if(empty($input['key'])){
            return response()->json(array('error'=>1,'msg'=>'请填写关键字'));
            exit();
        }
        if(Company::where('key','=',$input['key'])->first()){
            return response()->json(array('error'=>1,'msg'=>'关键字已使用'));
            exit();    
        }
        $input['url'] = url('/invite?recommend='.$input['key']);
        if(Company::create($input)){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.company.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        $type = false;
        if(!empty($id)){
            $listArr = Company::where('id','=',$id)->first();
            $cArr = Members::where('recommend','=',$listArr->key)->first();
            $type = empty($cArr)?false:true;
        }
        return view('admin.company_edit')->with('listArr',$listArr)->with('type',$type);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','name','key'); 
        $id = $input['id'];
        unset($input['id']);
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
            exit();
        }

        if(empty($input['key'])){
            return response()->json(array('error'=>1,'msg'=>'请填写关键字'));
            exit();
        }

        $r = Company::where('key','=',$input['key'])->first();        
        if(empty($r) || $r->id==$id){
            $input['url'] = url('/invite?recommend='.$input['key']);
            $res = Company::where('id','=',$id)->update($input);
            if($res){
                return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.company.index')));     
            }
        }        
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }

    
    public function export(Request $request){
        $stime = $request->get('stime','');
        $etime = $request->get('etime','');
        $key = $request->get('key','');
        $type = $request->get('type','apply');

        $isauth = $request->get('isauth',''); //比赛状态
        $mobile = $request->get('mobile',''); //手机号
        $status = $request->get('status',''); //比赛状态

        $dataArr = array();
        if($type=='reg'){
            $query = Members::where('recommend','=',$key);
            if(!empty($isauth)){
                $query =  $query->where('status','=',$isauth);
            }
            if(!empty($mobile)){
                $query =  $query->where('mobile','=',$mobile);
            }

            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }

            if(empty($status)){ //只读取报名 
                $query = $query->with(['apply'=>function($query){ 
                    $query->with(['match'=>function($query){ 
                        $query->select('id','name');
                    }])->select('mid','matchid','status','province','city');
                }]);
            }else{
                if($status=='applyno'){
                    $query = $query->whereNotIn('id', function ($query){
                        $query->select('mid')->whereIn('status',['1','5','6','7','8'])->from('apply');
                    });
                }

                if($status=='applyyes'){
                    $query = $query->whereHas('apply',function($query){ 
                            $query->select('mid','matchid','status','province','city');}
                        )->whereHas('apply.match',function($query){ 
                            $query->select('id','name');}
                        )->whereIn('id', function ($query){
                            $query->select('mid')->whereIn('status',['1','5','6','7','8'])->from('apply');
                        });
                }

                if($status=='apply1'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['1'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply5'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['5'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply6'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['6','7'])->select('mid','matchid','status','province','city');
                    });
                }

                if($status=='apply8'){
                    $query = $query->whereHas('apply',function ($query) {
                        $query->whereHas('match',function($query){ $query->select('id','name');})->whereIn('status',['8'])->select('mid','matchid','status','province','city');
                    });
                }
            }
            //var_dump($query->toSql());
            $dataArr =  $query->select(['id','recommend','name','mobile','status','created_at','idnumber','sex'])->get();
        }

        if($type=='apply'){
            $query = Apply::where('status','>',5);
            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }

            $query = $query->with(array('member'=>function ($query) use ($key) {
                    $query->select('id','name','recommend','mobile','idnumber');
                }));

            $query = $query->whereIn('mid', function ($query) use ($key) {
                    $query->where('recommend','=',$key)->select('id')->from('members');
                });

            $query =  $query->with(array('match'=>function($query){
                    $query->select('id','name');
                }));
            $dataArr = $query->select(['id','mid','matchid','created_at'])->get();
        }

        if($type=='team'){
            $query =  Teammember::with(array('member'=>function ($query) use ($key) {
                        $query->select('id','name','recommend');
                    }));
            $query = $query->whereIn('mid', function ($query) use ($key) {
                    $query->where('recommend','=',$key)->select('id')->from('members');
                });

            $query = $query->with(array('team'=>function($query){
                        $query->with(array('match'=>function($query){
                            $query->select('id','name');
                        }))->select('id','matchid','name');
                    }));
            
            if(!empty($stime) && !empty($etime)){
                $query =  $query->where('created_at','>=',$stime)->where('created_at','<=',$etime.' 23:59:59');
            }
            $dataArr = $query->select(['id','mid','teamid','created_at'])->get();
        }
        

        if($type=='reg'){
            $str = 'ID,推荐人,性别,名称,手机号,身份证号,认证状态,加入时间,设备号,比赛-赛区-比赛状态'."\r\n";
            $applyArr = Config::get('custom.apply');

            foreach($dataArr as $v){
                $str .= (empty($v->id)?'':$v->id);    
                $str .= ','.(empty($v->recommend)?'':$v->recommend);    
                $str .= ','.(empty($v->sex)?'': ($v->sex=='f'?'男':'女') );    
                $str .= ','.(empty($v->name)?'':'**'.mb_substr($v->name,-1));    
                $str .= ','.(substr($v->mobile,0,3).'****'.substr($v->mobile,-4));    
                $str .= ','.(empty($v->idnumber)?'--':(substr($v->idnumber,0,3).'******'.substr($v->idnumber,-2)) );
                $str .= ','.($v->status=='n'?'未认证':'已认证');   
                $str .= ','.($v->created_at);

                $dstr = Redis::get($v->id.'login');
                $str .= ','.(empty($dstr)?'--':substr($dstr,0,-5) );  

                $str .= ',"';
                if(!empty($v->apply)){
                    foreach($v->apply as $vv){                        
                        $str .= ( (empty($vv->match)?'':$vv->match->name).'-'.(empty($vv)?'':$vv->province.'/'.$vv->city).'-'.(empty($vv)?'':$applyArr['statusArr'][$vv->status])."\r\n");
                    }
                }
                $str .= '"';

                $str .= "\r\n";    
            }
            
        }

        if($type=='apply'){
            $str = '会员ID,推荐人,名称,手机号,身份证号,比赛,加入时间,设备号'."\r\n";

            foreach($dataArr as $v){
                $str .= (empty($v->member->id)?'':$v->member->id); 
                $str .= ','.(empty($v->member)?'':$v->member->recommend); 
                $str .= ','.(empty($v->member->name)?'':'**'.mb_substr($v->member->name,-1));
                $str .= ','.(substr($v->member->mobile,0,3).'****'.substr($v->member->mobile,-4));    
                $str .= ','.(empty($v->member->idnumber)?'--':(substr($v->member->idnumber,0,3).'******'.substr($v->member->idnumber,-2)) );       
                $str .= ','.(empty($v->match)?'':$v->match->name);    
                $str .= ','.($v->created_at); 

                $dstr = Redis::get($v->member->id.'login');   
                $str .= ','.(empty($dstr)?'--':substr($dstr,0,-5) ); 
                   
                $str .= "\r\n";    
            }
            
        }

        if($type=='team'){
            $str = '推荐人,名称,比赛,队伍,加入时间'."\r\n";

            foreach($dataArr as $v){
                $str .= ','.(empty($v->member)?'':$v->member->recommend); 
                $str .= (empty($v->member)?'':$v->member->name);    
                $str .= ','.(empty($v->match)?'':$v->match->name);  
                $str .= ','.(empty($v->team->match)?'':$v->team->match->name);  
                $str .= ','.($v->created_at);    
                $str .= "\r\n";    
            }
        }

        if(empty($str)){
            echo '<script type="text/javascript">alert("无数据");history.go(-1);</script>';
            exit();  
        }

        $name = $type.date('ymdHi');
        header("Content-Type: application/force-download");  
        header("Content-type:text/csv;charset=utf-8");  
        header("Content-Disposition:filename=".$name.".csv"); 
        ob_end_flush();
        echo $str;
        flush();
        exit();

    }   
  
}
