<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Goods;
use Config;
use DB;
class GoodsController extends Controller{
   
    public function __construct(){
    }

    public function goodslistforios(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$this->goodslist('ios')));
        exit();    
    }

    public function goodslistforandroid(){
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$this->goodslist('android')));
        exit();
    }

    private function goodslist($type) {
        if($type=='ios'){
            $listArr = Goods::where('status','=','y')->where('appleid','!=','android')->orderBy('id', 'desc')->get();
        }else{
            $listArr = Goods::where('status','=','y')->where('appleid','=','android')->orderBy('id', 'desc')->get();            
        }
        
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();
                $arr['gid']     = empty($v->id)?'':$v->id;    
                $arr['img']     = empty($v->img)?'':$v->img;    
                $arr['name']    = empty($v->name)?'':$v->name;    
                $arr['appleid'] = empty($v->appleid)?'':$v->appleid;   
                $arr['price']   = empty($v->price)?'':$v->price;   
                $arr['number']  = empty($v->number)?'':$v->number;    
                $arr['url']     = empty($v->url)?'':$v->url;   
                $dataArr[] = $arr;
            }
        }

        return $dataArr;
    }

    
}
