<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;

use App\Models\Area;
use Config;
use DB;
class AreaController extends Controller{
    
    public function __construct(){
    }

    public function ajaxgetlist($fid=0){
    	$dataArr = array();
    	if($fid==0){
    		$dataArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get(['code','name']);
    	}

    	if($fid > 0){
    		$dataArr = Area::where(array('parentid'=>$fid))->orderBy('id', 'asc')->get(['code','name']);
    	}
       
        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }

    
}