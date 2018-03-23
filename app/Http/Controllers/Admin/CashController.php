<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Models\Cash;
use App\Helpers\OssUploadHelper;
use Config;
use DB;
class CashController extends Controller{
    
    private $bucket = 'lzsn-icon';
    private $dir = 'cash/';

    public $cashArr = array();

    public function __construct(){
        $this->cashArr = Config::get('custom.cash');
    }

    public function index() {
        $listArr = Cash::with('member')->orderBy('id', 'desc')->paginate(20);        
        return view('admin.cash_index')->with('listArr',$listArr)->with('cashArr',$this->cashArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.cash_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('cashArr',$this->cashArr);
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }

        if(empty($input['money'])){
            return response()->json(array('error'=>1,'msg'=>'请填写金额'));
        }

        if(Cash::create($input)){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.cash.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Cash::where('id','=',$id)->first();
        }
        return view('admin.cash_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('cashArr',$this->cashArr);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','name','icon','type','money','remark'); 
        $id = $input['id'];
        unset($input['id']);
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }

        if(empty($input['money'])){
            return response()->json(array('error'=>1,'msg'=>'请填写金额'));
        }
        
        $res = Cash::where('id','=',$id)->update($input);
        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.cash.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(!empty(Cash::destroy($id))){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 


}
