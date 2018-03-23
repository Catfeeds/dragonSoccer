<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Permission;
class Adminsidebar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $dataArr = array(); 
        $listArr = Permission::where('cid','=','0')->get();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                if(\Gate::forUser(auth('adminusers')->user())->check($v->name)){
                    $dataArr[$k]['title'] = $v->label;
                    $dataArr[$k]['icon'] = $v->icon;

                    $urlArr = array();
                    $r = Permission::where('cid','=',$v->id)->get();
                    foreach ($r as $kk => $vv) {
                        if(\Gate::forUser(auth('adminusers')->user())->check($vv->name)){
                            $dataArr[$k]['sons'][$kk]['title'] = $vv->label;                    
                            $dataArr[$k]['sons'][$kk]['icon'] = $vv->icon;
                            $dataArr[$k]['sons'][$kk]['url'] = $vv->name;

                            $vv->name = strtolower($vv->name);
                            $nameArr2 = explode('.',$vv->name);
                            $urlArr[$kk] = empty($nameArr2[1])?'':$nameArr2[1];
                            $dataArr[$k]['sons'][$kk]['urlstr'] = $urlArr[$kk];
                        }                    
                    }
                    $dataArr[$k]['urlArr']= $urlArr;    
                }
            }
        }
        $request->attributes->set('sidebarMenu',$dataArr);
        return $next($request);
    }
}
