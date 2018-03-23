@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">赛事报名</h1>
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="10%">ID</th>
                    <th width="10%">名称</th>
                    <th width="10%">赛事</th>
                    <th width="10%">好友名称</th>
                    <th width="10%">报名时间</th>
                    <th width="10%">状态</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{empty($v->member)?'--':$v->member->name}}</td>
                                <td>{{empty($v->match)?'--':$v->match->name}}</td>
                                <td>{{empty($v->friendmember)?'无':$v->friendmember->name}}</td>
                                <td>{{$v->created_at}}</td>
                                <td>{{$ApplyArr['statusArr'][$v->status]}}</td>
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