@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">商品管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.goods.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.goods.add')}}">添加</a>  
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
                    <th width="10%">图片</th>
                    <th width="10%">名称</th>                    
                    <th width="10%">内购ID</th>
                    <th width="10%">价格</th>
                    <th width="5%">数量</th>
                    <th width="10%">详情链接</th>
                    <th width="10%">入库时间</th>
                    <th width="10%">状态</th>
                    <th width="10%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td><img src="{{$v->img}}" width="50" height="50"></td>
                                <td>{{$v->name}}</td>
                                <td>{{$v->appleid}}</td>
                                <td>{{$v->price}}</td>
                                <td>{{$v->number}}</td>
                                <td>{{$v->url}}</td>
                                <td>{{$v->created_at}}</td>
                                <td>{{$v->status=='n'?'未发布':'已发布'}}</td>
                                <td>                            
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.goods.edit'))
                                        <a href="{{route('admin.goods.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.goods.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.goods.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
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
<script type="text/javascript">
    $(document).ready(function (){
    });
</script> 
@endsection