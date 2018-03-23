@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">{{$type=='f'?'聊天群':'参赛队伍'}}管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.team.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.team.add')}}?type={{$type}}">添加</a>  
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
                    @if($type=='m')
                        <th width="15%">比赛</th>
                        <th width="5%">省</th>
                        <th width="5%">市</th>
                        <th width="5%">状态</th>
                    @endif
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
                                @if($type=='m')
                                    <td>{{$v->match->name}}</td>
                                    <td>{{$v->province}}</td>
                                    <td>{{$v->city}}</td>
                                    <td>{{$teamArr['stsArr'][$v->sts]}}</td>
                                @endif
                                <td>{{$v->created_at}}</td>
                                <td>                            
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.team.edit'))
                                        <a href="{{route('admin.team.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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