<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\OssUploadHelper;
use App\Models\Goods;
use App\Models\Orders;
use App\Models\Orderslog;
use App\Models\Balancelog;
use App\Models\Members;
use Config;

class OrdersController extends Controller{
    public $orderArr = array();

    public function __construct(){
        $this->orderArr = Config::get('custom.order');
    }

    public function index(Request $request) {
        $listArr = Orders::with('member','goods')->orderBy('id', 'desc')->paginate(20);
        return view('admin.orders_index')->with('listArr',$listArr)->with('orderArr',$this->orderArr);
    }

    //获取数据   
    public function view(Request $request){  
        $sn = $request->get('sn',''); 
        $listArr = Orderslog::where('sn','=',$sn)->orderBy('id', 'desc')->paginate(10);     
        return view('admin.orders_view')->with('listArr',$listArr);
    }

    
}
