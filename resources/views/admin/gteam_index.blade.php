@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">参赛队伍</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.gteam.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.gteam.add')}}">添加</a>  
        </h1>
    @endif
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="10%">ID</th>
                    <th width="10%">头像</th>
                    <th width="10%">名称</th>
                    <th width="10%">环信群id</th>
                    <th width="5%">省</th>
                    <th width="5%">市</th>
                    <th width="5%">状态</th>
                    <th width="10%">创建时间</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>                                
                                <td><img src="{{$v->icon}}" width="50" height="50"></td>
                                <td>{{$v->name}}</td>
                                <td>{{$v->gid}}</td>
                                <td>{{$v->province}}</td>
                                <td>{{$v->city}}</td>
                                <td>{{$teamArr[$v->status]}}</td>
                                <td>{{$v->created_at}}</td>
                                <td>                            
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.gteam.edit'))
                                        <a href="{{route('admin.gteam.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif

                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.gteam.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.gteam.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
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