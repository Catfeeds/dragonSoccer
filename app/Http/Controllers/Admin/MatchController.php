<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\Match;
use App\Models\Matchinfo;
use App\Models\Area;
use Config;
use DB;
class MatchController extends Controller{
    public $matchArr = array();

    private $bucket = 'lzsn-icon';
    private $dir = 'default/';

    public function __construct(){
        $this->matchArr = Config::get('custom.match');
    }

    public function index() {
        $listArr = Match::with('info')->orderBy('id', 'desc')->paginate(20);        
        return view('admin.match_index')->with('listArr',$listArr)->with('matchArr',$this->matchArr);
    }

    //获取数据   
    public function add(){        
        $areaArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
        return view('admin.match_add')->with('areaArr',$areaArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('matchArr',$this->matchArr);
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['applystarttime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写报名开始时间'));
        }else{
            $applystarttime = $input['applystarttime'];
            unset($input['applystarttime']);
            $input['applystarttime'] = strtotime($applystarttime);    
        }

        if(empty($input['applyendtime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写报名结束时间'));
        }else{
            $applyendtime = $input['applyendtime'];
            unset($input['applyendtime']);
            $input['applyendtime'] = strtotime($applyendtime.' 23:59:59');    
        }

        if(empty($input['starttime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写比赛开始时间'));
        }else{
            $starttime = $input['starttime'];
            unset($input['starttime']);
            $input['starttime'] = strtotime($starttime);    
        }

        if(empty($input['endtime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写比赛结束时间'));
        }else{
            $endtime = $input['endtime'];
            unset($input['endtime']);
            $input['endtime'] = strtotime($endtime.' 23:59:59');    
        }

        if(empty($input['imgs'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }else{
            $imgs = array_filter($input['imgs']);
            unset($input['imgs']);
            $input['imgs'] = implode('#',$imgs);
        }

        if(empty($input['content'])){
            return response()->json(array('error'=>1,'msg'=>'请填写赛事规则'));
        }

        $res = false;
        DB::beginTransaction();
            $r = Match::create($input);
            $r2 = Matchinfo::create(array('matchid'=>$r->id,'content'=>$input['content']));  
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.match.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $areaArr = array();
        $listArr = array();
        if(!empty($id)){
            if($listArr = Match::with('info')->where('id','=',$id)->first()){ 
                $listArr->imgArr = empty($listArr->imgs)?array():explode('#',$listArr->imgs);            
                $areaArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
            }
        }
        return view('admin.match_edit')->with('listArr',$listArr)->with('areaArr',$areaArr)->with('matchArr',$this->matchArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','name', 'applystarttime', 'applyendtime', 'starttime','endtime','imgs','content','rule','region','sex','level','status','sid','teamsts'); 
        $id = $input['id'];
        unset($input['id']);
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['applystarttime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写报名开始时间'));
        }else{
            $applystarttime = $input['applystarttime'];
            unset($input['applystarttime']);
            $input['applystarttime'] = strtotime($applystarttime);    
        }

        if(empty($input['applyendtime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写报名结束时间'));
        }else{
            $applyendtime = $input['applyendtime'];
            unset($input['applyendtime']);
            $input['applyendtime'] = strtotime($applyendtime.' 23:59:59');    
        }

        if(empty($input['starttime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写比赛开始时间'));
        }else{
            $starttime = $input['starttime'];
            unset($input['starttime']);
            $input['starttime'] = strtotime($starttime);    
        }

        if(empty($input['endtime'])){
            return response()->json(array('error'=>1,'msg'=>'请填写比赛结束时间'));
        }else{
            $endtime = $input['endtime'];
            unset($input['endtime']);
            $input['endtime'] = strtotime($endtime.' 23:59:59');    
        }

        if(empty($input['imgs'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }else{
            $imgs = array_filter($input['imgs']);
            unset($input['imgs']);
            $input['imgs'] = implode('#',$imgs);
        }

        $content = '';
        if(empty($input['content'])){
            return response()->json(array('error'=>1,'msg'=>'请填写赛事规则'));
        }else{
            $content = $input['content'];
            unset($input['content']); 
        }

        $res = false;
        DB::beginTransaction();
            $r = Match::where('id','=',$id)->update($input);
            $r2 = Matchinfo::where('matchid','=',$id)->update(array('content'=>$content));  
            $res = true;  
        DB::commit();
        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.match.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            $res = false;
            DB::beginTransaction();
                $r = Match::destroy($id);
                $r2 = Matchinfo::where('matchid','=',$id)->delete();  
                $res = true;  
            DB::commit();
            if(!empty($res)){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 
  
}
