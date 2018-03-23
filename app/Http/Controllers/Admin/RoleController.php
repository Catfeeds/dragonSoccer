<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Helpers\FunctionHelper;
use Session;

use App\Models\Permission;
use App\Models\Role;
class RoleController extends Controller{

    public function index() {
        $listArr = Role::orderBy('id', 'desc')->paginate(20);        
        return view('admin.role_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        $listArr = Permission::where('cid','=','0')->get();  
        foreach ($listArr as $v) {
            $v->sons = Permission::where('cid','=',$v->id)->get();
            if(!empty($v->sons)){
                foreach ($v->sons as $vv) {
                    $vv->sons = Permission::where('cid','=',$vv->id)->get();
                }
            }      
        }
        //var_dump($listArr->toArray());
        return view('admin.role_add')->with('listArr',$listArr);
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();       
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限名'));
        }        
        if(!Role::where('name','=',$input['name'])->first()){          
            if($r = Role::create($input)){
                if (is_array($request->get('pids'))) {
                    if(Role::find($r->id)->permissions()->sync($request->get('pids',[]) )){
                        return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.role.index') ));
                    }
                }                
            }
        }        
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }

    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $permissionsArr = array();
        $cidArr = array();
        if(!empty($id)){
            if($listArr = Role::with('permissions')->where('id','=',$id)->first()){                
                if(!empty($listArr->permissions)){
                    foreach ($listArr->permissions as $v) {
                        $permissionsArr[] = $v->id;   
                    }
                }
                $cidArr = Permission::where('cid','=','0')->get();  
                foreach ($cidArr as $v) {
                    $v->sons = Permission::where('cid','=',$v->id)->get();
                    if(!empty($v->sons)){
                        foreach ($v->sons as $vv) {
                            $vv->sons = Permission::where('cid','=',$vv->id)->get();
                        }
                    }      
                }
            }
        }
        //var_dump($listArr->permissions->toArray());
        return view('admin.role_edit')->with('listArr',$listArr)->with('cidArr',$cidArr)->with('permissionsArr',$permissionsArr);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','pids','name'); 
        $id = $input['id'];
        unset($input['id']);

        $pids = $input['pids'];
        unset($input['pids']);

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写权限名'));
        }
        
        if(Role::where('id','=',$id)->update($input)){  
            if (is_array($pids)) {
                if(Role::find($id)->permissions()->sync($pids)){
                    return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.role.index') ));
                }
            }
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){  
            $r1 = Role::find($id)->users()->where('role_id','=',$id)->first();          
            $r2 = Role::find($id)->permissions()->where('role_id','=',$id)->first();          
            if(empty($r1) && empty($r2)){
                if(Role::destroy($id)){
                    return response()->json(array('error'=>0,'msg'=>'删除成功'));
                }                
            }
            return response()->json(array('error'=>1,'msg'=>'存在角色使用中，不可删除'));
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 
  
}
