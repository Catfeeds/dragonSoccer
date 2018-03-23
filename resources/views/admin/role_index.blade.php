@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">角色管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.role.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.role.add')}}">添加</a>  
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
                    <th width="20%">名称</th>
                    <th width="20%">描述</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{$v->name}}</td>
                                <td>{{$v->description}}</td>
                                <td>                              
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.role.edit'))
                                        <a href="{{route('admin.role.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.role.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.role.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
                                    @endif
                                </td>
                            </tr>                            
                        @endforeach
                    @endif       
                </tbody>        
            </table> 
            <table>
                <tr class="col-xs-12">
                    <td  style="text-align: right;">{{$listArr->links()}}</td>
                    
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;共计：{{$listArr->total()}}条</td>
                </tr>
            </table>           
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection