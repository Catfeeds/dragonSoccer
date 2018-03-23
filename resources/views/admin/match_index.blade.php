@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">赛事管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.match.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.match.add')}}">添加</a>  
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
                    <th width="5%">缩率图</th>
                    <th width="5%">排序</th>
                    <th width="10%">名称</th>
                    <th width="10%">赛制</th>
                    <th width="10%">开始时间</th>
                    <th width="10%">参赛性别</th>
                    <th width="10%">年龄层</th>
                    <th width="10%">状态</th>
                    <th width="10%">组队状态</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <?php $imgArr = empty($v->imgs)?array():explode('#',$v->imgs);?>
                            <tr>
                                <td>{{$v->id}}</td>
                                <td><img src="{{ (empty($imgArr)||empty($imgArr[0]))?'':$imgArr[0] }}" width="50" height="50"></td>
                                <td>{{$v->sid}}</td>
                                <td>{{$v->name}}</td>
                                <td>{{$matchArr['ruleArr'][$v->rule]}}</td>
                                <td>{{empty($v->starttime)?'--':date('Y-m-d',$v->starttime)}}</td>
                                <td>{{$matchArr['sexArr'][$v->sex]}}</td>
                                <td>{{$matchArr['levelArr'][$v->level]}}</td>
                                <td>{{$matchArr['statusArr'][$v->status]}}</td>
                                <td>{{$matchArr['teamstsArr'][$v->teamsts]}}</td>
                                <td>                                                                 
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.match.edit'))
                                        <a href="{{route('admin.match.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.match.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.match.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
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