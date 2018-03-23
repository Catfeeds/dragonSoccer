@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">权限管理--二级权限</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.permission.add')}}?id={{$listArr->id}}">添加</a>  
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
                        <tr>
                            <td>
                                {{$listArr->label}}--{{$listArr->name}}                                    
                                @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.edit'))
                                    <a href="{{route('admin.permission.edit')}}?id={{$listArr->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                @endif
                                
                                @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.del'))
                                    <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.permission.ajaxdel')}}" dataid="{{$listArr->id}}"><i class="glyphicon glyphicon-trash"></i></a>
                                @endif
                            </td>
                        </tr> 
                        @if(!empty($listArr->sons))
                            @foreach($listArr->sons as $vv)
                                <tr>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp; |__ {{$vv->label}}--{{$vv->name}}

                                        @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.edit'))
                                            <a href="{{route('admin.permission.edit')}}?id={{$vv->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                        @endif

                                        @if(Gate::forUser(auth('adminusers')->user())->check('admin.permission.del'))
                                            <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.permission.ajaxdel')}}"  dataid="{{$vv->id}}"><i class="glyphicon glyphicon-trash"></i></a>
                                        @endif
                                    </td>
                                </tr> 
                            @endforeach
                        @endif
                    @endif       
                </tbody>        
            </table>            
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection