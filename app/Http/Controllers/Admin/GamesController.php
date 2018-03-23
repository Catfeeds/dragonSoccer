<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\Area;
use App\Models\School;
use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Gamesruler;
use App\Models\Gamesrulerinfo;
use App\Models\Gamecontent;
use App\Models\Webconfig;
use Config;
use DB;
class GamesController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'default/';

    public function __construct(){
    }

    public function index() {
        $listArr = Games::with('school')->orderBy('id', 'desc')->paginate(20);        
        return view('admin.games_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){  
        $schoolArr = School::orderBy('id', 'desc')->get();       
        return view('admin.games_add')->with('schoolArr',$schoolArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxadd(Request $request){
        try {
            $dataArr = $this->inputData($request->all());            
            $res = false;            
            DB::beginTransaction();
                $r1 = Games::create($dataArr['data']);
                foreach ($dataArr['ruler'] as $k => &$v) {
                    $v['gamesid'] = $r1->id;
                    $r2 = Gamesruler::create($v);
                    foreach ($dataArr['rulerinfo'][$v['key']] as $kk => &$vv) {
                        $vv['gamesrulerid'] = $r2->id;
                        $r3 = Gamesrulerinfo::create($vv);
                    }
                }

                foreach ($dataArr['age'] as $ka => &$va) {
                    $va['gamesid'] = $r1->id;
                    unset($va['id']);
                    $r4 = Gamesages::create($va);
                }
            $res = true;
            DB::commit();

            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.games.index') ));
                exit();                
            }       
        }catch(Exception $e) {   

        }                
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
        exit();
    } 


    public function edit(Request $request){  
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Games::where('id','=',$id)->first();
            $listArr->imgArr = empty($listArr->imgs)?array():explode('#',$listArr->imgs);  
        }
        $schoolArr = School::orderBy('id', 'desc')->get(); 
        return view('admin.games_edit')->with('listArr',$listArr)->with('schoolArr',$schoolArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxedit(Request $request){
        $id = $request->get('id','');
        try{
            $dataArr = $this->inputData($request->all());
            $res = false;            
            DB::beginTransaction();
                $r1 = Games::where('id',$id)->update($dataArr['data']);
                //删除
                $r2 = Gamesrulerinfo::whereIn('gamesrulerid', function ($query) use ($id) {
                    $query->where('gamesid','=',$id)->select('id')->from('games_ruler');
                })->delete();
                $r3 = Gamesruler::where('gamesid','=',$id)->delete();
                //$r4 = Gamesages::where('gamesid','=',$id)->delete();

                foreach ($dataArr['ruler'] as $k => &$v) {
                    $v['gamesid'] = $id;
                    $r5 = Gamesruler::create($v);
                    foreach ($dataArr['rulerinfo'][$v['key']] as $kk => &$vv) {
                        $vv['gamesrulerid'] = $r5->id;
                        $r6 = Gamesrulerinfo::create($vv);
                    }
                }

                foreach ($dataArr['age'] as $ka => &$va) {
                    $va['gamesid'] = $id;
                    if(empty($va['id'])){
                        $r7 = Gamesages::create($va);
                    }else{
                        $r7 = Gamesages::where('id',$va['id'])->update($va);
                    }
                }
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.games.index')));
                exit();     
            }     
        }catch(Exception $e){

        }                
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
        exit();
    }

    private function inputData($input){
        $dataArr = ['data'=>[],'ruler'=>[],'rulerinfo'=>[],'age'=>[] ];

        $data['name'] = empty($input['name'])?'':$input['name']; 
        $data['sid'] = empty($input['sid'])?'1':$input['sid']; 
        $data['info'] = empty($input['info'])?'':$input['info']; 
        $data['applystime'] = empty($input['applystime'])?0:strtotime($input['applystime']); 
        $data['applyetime'] = empty($input['applyetime'])?0:strtotime($input['applyetime']); 
        $data['starttime'] = empty($input['starttime'])?0:strtotime($input['starttime']); 
        $data['endtime'] = empty($input['endtime'])?0:strtotime($input['endtime'].' 23:59:59'); 
        $data['owner'] = empty($input['owner'])?0:$input['owner']; 
        $data['ruler'] = empty($input['rulernum'])?'':$input['rulernum']; 
        $data['status'] = empty($input['status'])?'':$input['status'];
        $imgs = '';
        if(!empty($input['imgs'])){
            foreach ($input['imgs'] as $iv) {
                empty($iv)?'':($imgs = empty($imgs)?$iv:$imgs.'#'.$iv);    
            }    
        }
        $data['imgs'] = $imgs;
        $dataArr['data'] = $data;
        
        $rulerArr = [];
        $rulerinfoArr = [];
        if(!empty($input['ruler'])){
            if(!empty($input['ruler']['key'])){
                foreach ($input['ruler']['key'] as $k => $v) {
                    $rulerArr[$k]['key'] = $v;        
                    $rulerArr[$k]['teamnumber'] = empty($input['ruler']['teamnumber'][$k])?0:$input['ruler']['teamnumber'][$k];
                    $rulerArr[$k]['risenumber'] = empty($input['ruler']['risenumber'][$k])?0:$input['ruler']['risenumber'][$k];
                    $rulerArr[$k]['additionalnumber'] = empty($input['ruler']['additionalnumber'][$k])?0:$input['ruler']['additionalnumber'][$k];                
                    $rulerArr[$k]['starttime'] = empty($input['ruler']['starttime'][$k])?'0':strtotime($input['ruler']['starttime'][$k]);
                    $rulerArr[$k]['endtime'] = empty($input['ruler']['endtime'][$k])?'0':strtotime($input['ruler']['endtime'][$k].' 23:59:59');

                    if(!empty($input['rulerinfo'][$v])){
                        if(!empty($input['rulerinfo'][$v]['key'])){
                            foreach ($input['rulerinfo'][$v]['key'] as $kk => $vv) {
                                $rulerinfoArr[$v][$kk]['key'] = $vv;
                                $rulerinfoArr[$v][$kk]['starttime'] = empty($input['rulerinfo'][$v]['starttime'][$kk])?'0':strtotime($input['rulerinfo'][$v]['starttime'][$kk]);
                                $rulerinfoArr[$v][$kk]['endtime'] = empty($input['rulerinfo'][$v]['endtime'][$kk])?'0':strtotime($input['rulerinfo'][$v]['endtime'][$kk].' 23:59:59');
                            }
                        }
                    }

                }    
            }
        }
        $dataArr['ruler'] = $rulerArr;
        $dataArr['rulerinfo'] = $rulerinfoArr;
        
        $ageArr = [];
        if(!empty($input['age'])){
            if(!empty($input['age']['key'])){
                foreach ($input['age']['key'] as $k => $v) {
                    $ageArr[$k]['key'] = $v;        
                    $ageArr[$k]['val'] = empty($input['age']['val'][$k])?0:$input['age']['val'][$k];
                    $ageArr[$k]['id'] = empty($input['age']['id'][$k])?0:$input['age']['id'][$k];
                    $ageArr[$k]['starttime'] = empty($input['age']['starttime'][$k])?'0':strtotime($input['age']['starttime'][$k]);
                    $ageArr[$k]['endtime'] = empty($input['age']['endtime'][$k])?'0':strtotime($input['age']['endtime'][$k].' 23:59:59');
                }
            }
        }
        $dataArr['age'] = $ageArr;

        return $dataArr;
    }

    public function ajaxages(Request $request){  
        $gid = $request->get('gid','');
        $ownerid = $request->get('ownerid','');
        $footballageArr = [];
        $listArr = School::where('id',$ownerid)->where('type','l')->first();
        if(!empty($listArr)){
            $footballage = Webconfig::where('key','=','footballage')->first(); 
            $footballageArr = empty($footballage)?['ages']:explode('|', $footballage->val);   
        }else{
            $footballageArr = ['ages'];
        }

        $ageArr = Gamesages::where('gamesid','=',$gid)->get();
        $ageArr2 = [];
        if(!empty($ageArr)){
            foreach ($ageArr as $v) {
                $ageArr2[$v->key] = $v;    
            }
        }
        return view('admin.games_ajaxages')->with('footballageArr',$footballageArr)->with('ageArr',$ageArr2);
    }


    public function ajaxruler(Request $request){
        $gid = $request->get('gid','');
        $ruler = $request->get('ruler','');
        
        $rulerArr = Gamesruler::where('gamesid','=',$gid)->get();
        $rulerArr2 = [];
        $data = [];
        if(!empty($rulerArr)){
            foreach ($rulerArr as $v) {
                $rulerArr2[$v->key] = $v; 
                if($rulerinfoArr = Gamesrulerinfo::where('gamesrulerid','=',$v->id)->get()){
                    $data[$v->key]['rulerinfoArr'] = $rulerinfoArr;
                }   
            }
        }
        return view('admin.games_ajaxruler')->with('ruler',$ruler)->with('rulerArr',$rulerArr2)->with('data',$data);    
    }

    public function ajaxrulerinfo(Request $request){
        $grid = $request->get('grid','');
        $number = $request->get('number','');
        $rulerkey = $request->get('rulerkey','');
        
        $rulerinfoArr = Gamesrulerinfo::where('gamesrulerid','=',$grid)->get();
        $rulerinfoArr2 = [];
        if(!empty($rulerinfoArr)){
            foreach ($rulerinfoArr as $v) {
                $rulerinfoArr2[$v->key] = $v;    
            }
        }
        return view('admin.games_ajaxrulerinfo')->with('number',ceil(log($number,2)) )->with('rulerinfoArr',$rulerinfoArr2)->with('rulerkey',$rulerkey);    
    }


    public function content(Request $request) {
        $gamesid = $request->get('gamesid','');
        $listArr = Gamecontent::with('games')->where('gamesid',$gamesid)->orderBy('id', 'desc')->paginate(20);        
        return view('admin.games_content')->with('listArr',$listArr)->with('gamesid',$gamesid);
    }

    //获取数据   
    public function addcontent(Request $request){  
        $gamesid = $request->get('gamesid','');
        $gameArr = Games::find($gamesid);       
        return view('admin.games_content_add')->with('gameArr',$gameArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxaddcontent(Request $request){
        $i['gamesid'] = $request->get('gamesid','');
        $i['txt'] = $request->get('txt',' ');
        $img = $request->get('img','');        
        if(!empty($img)){
            $i['img'] = $img;    
        }
        try {
            if(Gamecontent::create($i)){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.games.content').'?gamesid='.$i['gamesid'] ));
                exit();                
            }       
        }catch(Exception $e) {   

        }                
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
        exit();
    } 


    public function editcontent(Request $request){  
        $id = $request->get('id','');
        $listArr = array();
        $gameArr = array();
        if(!empty($id)){
            $listArr = Gamecontent::where('id','=',$id)->first();            
            $gameArr = Games::find($listArr->gamesid); 
        } 
        return view('admin.games_content_edit')->with('listArr',$listArr)->with('gameArr',$gameArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxeditcontent(Request $request){
        $id = $request->get('id','');
        $i['gamesid'] = $request->get('gamesid','');
        $i['txt'] = $request->get('txt','');
        $img = $request->get('img','');
        if(!empty($img)){
            $i['img'] = $img;    
        }
        try {
            if(Gamecontent::where('id',$id)->update($i)){
                return response()->json(array('error'=>0,'msg'=>'成功','url'=>route('admin.games.content').'?gamesid='.$i['gamesid'] ));
                exit();                
            }       
        }catch(\Exception $e) {   

        }                
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();
    }

   
}
