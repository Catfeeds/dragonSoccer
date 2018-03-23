@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">提现明细</h1>
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="5%">ID</th>
                    <th width="10%">名称</th>
                    <th width="10%">单号</th>
                    <th width="10%">账号</th>
                    <th width="10%">金额</th>
                    <th width="10%">审核时间</th>
                    <th width="10%">实付金额</th>
                    <th width="10%">支付时间</th>
                    <th width="10%">支付方式</th>
                    <th width="10%">状态</th>
                    <th width="10%">时间</th>
                    <th width="15%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{$v->member->name}}</td>
                                <td>{{$v->sn}}</td>
                                <td>{{$v->payuser}}</td>
                                <td>{{$v->total}}</td>
                                <td>{{empty($v->checktime)?'--':date('Y-m-d H:i:s',$v->checktime)}}</td>
                                <td>{{$v->paytotal}}</td>
                                <td>{{empty($v->paytime)?'--':date('Y-m-d H:i:s',$v->paytime)}}</td>
                                <td>{{$v->payway}}</td>
                                <td>{{$statusArr[$v->status]}}</td>
                                <td>{{$v->created_at}}</td>
                                <td>
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.member.withdrawview'))
                                        <a href="{{route('admin.member.withdrawview')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif

                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.orders.view'))
                                        <a href="{{route('admin.orders.view')}}?sn={{$v->sn}}" class='btn btn-default btn-xs'>日志</a>
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