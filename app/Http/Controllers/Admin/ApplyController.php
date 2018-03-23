<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Apply;
use App\Models\Applyinvite;

use Hash;
use DB;
use Config;
class ApplyController extends Controller
{
    private $ApplyArr = '';
    private $bucket = 'lzsn-icon';
    private $dir = 'default/';
	public function __construct(Request $request){
        $this->ApplyArr = Config::get('custom.apply');
	}

    public function index() {
        $listArr = Apply::with('member','match','friendmember')->orderBy('id', 'desc')->paginate(20);
        return view('admin.apply_index')->with('listArr',$listArr)->with('ApplyArr',$this->ApplyArr);
    }
    
}