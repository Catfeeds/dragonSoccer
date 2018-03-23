<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Helpers\FunctionHelper;
use Session;
class LoginController extends Controller{

    protected $redirectTo = '/admin';
    protected $username;
   
    public function __construct()
    {
        $this->middleware('guest:adminusers', ['except' => 'logout']);
    }   

    public function login()
    {
        return view('admin.login_login');
    }

    public function ajaxlogin(Request $request) {
        $mobile = $request->input('mobile');   
        $password = $request->input('password');

        if (empty($password)) {
            return response()->json(array('error' =>1, 'msg' => '密码不能为空')); 
            exit();
        }
        if (!FunctionHelper::isMobile($mobile)) {
            return response()->json(array('error' =>1, 'msg' => '手机格式不对')); 
            exit();
        }         

        if (auth()->guard('adminusers')->attempt(array('mobile' =>$mobile, 'password' => $password))) {
            return response()->json(array('error' => 0, 'msg' => '登录成功','url'=>Session::get('url.intended', url('/admin')) ));
            exit();
        }
        return response()->json(array('error' =>1, 'msg' => '账号或密码不正确'));
        exit();

    }
   
    protected function guard()
    {
        return auth()->guard('adminusers');
    }
   
    public function logout()
    {
        auth()->guard('adminusers')->logout();
        request()->session()->flush();
        request()->session()->regenerate();
        return redirect('/admin/login');
    }


}
