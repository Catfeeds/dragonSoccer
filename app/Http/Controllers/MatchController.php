<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Matchinfo;

use Config;
use DB;
class MatchController extends Controller{
    
    public function __construct(){
    }

    public function info($id) {
        $listArr = Matchinfo::where('matchid','=',$id)->first();
        return view('front.match_info')->with('listArr',$listArr);
    }

    
}
