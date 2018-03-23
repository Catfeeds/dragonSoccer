@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">代言人</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.activityapply.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.activityapply.add')}}">添加</a>  
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
                    <th width="10%">名称</th>
                    <th width="10%">手机</th>
                    <th width="10%">文字</th>
                    <th width="10%">状态</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <?php $imgArr = empty($v->imgs)?array():explode('#',$v->imgs);?>
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{empty($v->member)?'':$v->member->name}}</td>
                                <td>{{empty($v->member)?'':$v->member->mobile}}</td>
                                <td>{{$v->txt}}</td>
                                <td>{{$activityArr['statusArr'][$v->status]}}</td>
                                <td>                                                                 
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.activityapply.edit'))
                                        <a href="{{route('admin.activityapply.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
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