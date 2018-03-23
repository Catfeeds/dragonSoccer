<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\School;
use Config;
use DB;
use Hash;
class SchoolController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'school/';

    public function __construct(){
    }

    public function index() {
        $listArr = School::orderBy('id', 'desc')->paginate(20);        
        return view('admin.school_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.school_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxadd(Request $request){
        try{
            if($r = School::create($this->inputData($request->all()) )){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.school.index') ));
                exit();                
            }       
        }catch(\Exception $e){

        }
                
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
        exit();
    } 

    public function edit(Request $request){  
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = School::where('id','=',$id)->first();
        }

        return view('admin.school_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxedit(Request $request){
        $id = $request->get('id','');
        try{
            if($r = School::where('id',$id)->update($this->inputData($request->all()))){
                return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.school.index') ));
                exit();                
            }       
        }catch(\Exception $e){

        }
                
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
        exit();
    }

    private function inputData($input){
        $dataArr['icon'] = empty($input['icon'])?'':$input['icon']; 
        $dataArr['name'] = empty($input['name'])?'':$input['name']; 
        $dataArr['type'] = empty($input['type'])?'s':$input['type']; 
        $dataArr['loginname'] = empty($input['loginname'])?'':$input['loginname']; 
        if(!empty($input['pwd'])){
            $dataArr['pwd'] = Hash::make($input['pwd']);
        }

        return $dataArr;
    }

   
}
