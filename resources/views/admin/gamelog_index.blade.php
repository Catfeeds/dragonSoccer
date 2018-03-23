@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">赛程</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.gamelog.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.gamelog.add')}}">添加</a>  
        </h1>
    @endif
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="5%">ID</th>
                    <th width="5%">编号</th>
                    <th width="15%">赛事名称</th>
                    <th width="15%">年龄段</th>
                    <th width="10%">A名称</th>
                    <th width="10%">B名称</th>
                    <th width="5%">比分</th>
                    <th width="10%">层级</th>
                    <th width="10%">状态</th>
                    <th width="10%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{$v->groupsn}}</td>
                                <td>{{$v->gamesages->games->name}}</td>
                                <td>{{empty($v->gamesages->starttime)?'--':date('Y/m/d',$v->gamesages->starttime)}}—{{empty($v->gamesages->endtime)?'--':date('Y/m/d',$v->gamesages->endtime)}}</td>
                                <td>{{empty($v->ateam)?'--':$v->ateam->name}}</td>
                                <td>{{empty($v->bteam)?'--':$v->bteam->name}}</td>
                                <td>{{$v->status=='eover'?'':($v->ateamscore.':'.$v->bteamscore)}}</td>
                                <td>{{FunctionHelper::gameLevel($v->matchlevel)}}</td>
                                <td>{{$statusArr[$v->status]}}</td>
                                <td>                                                                 
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.gamelog.edit'))
                                        <a href="{{route('admin.gamelog.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif                                  
                                </td>
                            </tr>                            
                        @endforeach
                    @endif       
                </tbody>        
            </table>

            @if($listArr->lastPage() >1)
                <div class="form-group form-inline col-sm-12">
                    <span class="pull-left">{{$listArr->links()}}</span>
                    <span  class="pull-left pagination" style="height: 30px; line-height: 34px;">&nbsp;&nbsp;&nbsp;&nbsp;共计：{{$listArr->total()}}条</span>
                </div>
            @endif            
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection