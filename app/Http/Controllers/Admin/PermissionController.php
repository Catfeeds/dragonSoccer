<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Helpers\FunctionHelper;
use Session;

use App\Models\Permission;
use Route;
class PermissionController extends Controller{

    public function index() {
        $listArr = Permission::where('cid','=','0')->get();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $v->sons = Permission::where('cid','=',$v->id)->get();    
            }
        }
        return view('admin.permission_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(Request $request){
        $id = $request->get('id','');
        $cid = 0;
        if(!empty($id)){
            $listArr = Permission::where('id','=',$id)->first();  
            $cid = $listArr->cid; 
        }
        $listArr = Permission::where('cid','=','0')->get();  
        return view('admin.permission_add')->with('listArr',$listArr)->with('cid',$cid)->with('cid2',$id);
    }

    
    public function ajaxgetcon(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Permission::where('cid','=',$id)->get();  
        }
        return response()->json(array('error'=>empty($listArr)?1:0,'msg'=>'','data'=>$listArr));
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();

        $cid = $input['cid'];
        $cid2 = $input['cid2'];
        unset($input['cid']);
        unset($input['cid2']);
        $input['cid'] = $cid2>0?$cid2:$cid;
        if($cid==0 || $cid2==0){
            if(empty($input['icon'])){
                return response()->json(array('error'=>1,'msg'=>'请选择图标'));
            }
        }
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限名'));
        }
        if(empty($input['label'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限解释名称'));
        }
        if(!Permission::where('name','=',$input['name'])->first()){
            if(Permission::create($input)){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.permission.index') ));
            }
        }        
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }

    public function lists(Request $request) {
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Permission::where('id','=',$id)->first();
            if(!empty($listArr)){
                $listArr->sons = Permission::where('cid','=',$id)->get();    
            }
        }
        return view('admin.permission_list')->with('listArr',$listArr);
    }  

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $cid = 0;
        $cid2 = 0;
        if(!empty($id)){
            $listArr = Permission::where('id','=',$id)->first();
            if($listArr->cid >0 ){
                $cid2Arr = Permission::where('id','=',$listArr->cid)->first();
                if($cid2Arr->cid==0){
                    $cid =  $cid2Arr->id;      
                    $cid2 =  0;      
                }else{
                    $cid =  $cid2Arr->cid; 
                    $cid2 =  $listArr->cid; 
                }
            }  
        }
        $cidArr = Permission::where('cid','=','0')->get(); 
        return view('admin.permission_edit')->with('listArr',$listArr)->with('cidArr',$cidArr)->with('cid',$cid)->with('cid2',$cid2);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','cid', 'cid2', 'icon', 'name','label'); 
        $id = $input['id'];
        unset($input['id']);
        
        $cid = $input['cid'];
        $cid2 = $input['cid2'];
        unset($input['cid']);
        unset($input['cid2']);
        $input['cid'] = $cid2>0?$cid2:$cid;
        if($cid==0 || $cid2==0){
            if(empty($input['icon'])){
                return response()->json(array('error'=>1,'msg'=>'请选择图标'));
            }
        }
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限名'));
        }
        if(empty($input['label'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限解释名称'));
        }
        
        if(Permission::where('id','=',$id)->update($input)){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.permission.index')));
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){            
            if(!Permission::where('cid','=',$id)->first()){
                if(Permission::destroy($id)){
                    return response()->json(array('error'=>0,'msg'=>'删除成功'));
                }                
            }
            return response()->json(array('error'=>1,'msg'=>'存在子权限，不可删除'));
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 
  
}
