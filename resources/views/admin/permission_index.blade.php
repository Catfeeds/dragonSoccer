@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">权限管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.permission.add')}}">添加</a>  
        </h1>
    @endif
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                <th width="100%">权限名--权限值</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>
                                    {{$v->label}}--{{$v->name}}                                   
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.edit'))
                                        <a href="{{route('admin.permission.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.permission.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
                                    @endif
                                </td>
                            </tr> 
                            @if($v->sons)
                                @foreach($v->sons as $vv)
                                    <tr>
                                        <td>
                                            &nbsp;&nbsp;&nbsp;&nbsp; |__ {{$vv->label}}--{{$vv->name}}

                                            @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.list'))
                                                <a href="{{route('admin.permission.list')}}?id={{$vv->id}}" class='btn btn-default btn-xs'>子权限</a>
                                            @endif

                                            @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.edit'))
                                                <a href="{{route('admin.permission.edit')}}?id={{$vv->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                            @endif

                                            @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.del'))
                                                <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.permission.ajaxdel')}}" dataid="{{$vv->id}}"><i class="glyphicon glyphicon-trash"></i></a>
                                            @endif
                                        </td>
                                    </tr> 
                                @endforeach
                            @endif
                        @endforeach
                    @endif       
                </tbody>        
            </table>            
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection