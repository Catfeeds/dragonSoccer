@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">赛事报名</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.group.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.group.add')}}">添加</a>  
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
                    <th width="10%">比赛</th>
                    <th width="10%">年龄段</th>
                    <th width="10%">总人数</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{empty($v->gamesages->games)?'--':$v->gamesages->games->name}}</td>
                                <td>{{empty($v->gamesages->starttime)?'--':date('Y/m/d',$v->gamesages->starttime)}}—{{empty($v->gamesages->endtime)?'--':date('Y/m/d',$v->gamesages->endtime)}}</td>
                                <td>{{$v->number}}</td>
                                <td>                                                                 
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.group.edit'))
                                        <a href="{{route('admin.group.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif 
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.group.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.group.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
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