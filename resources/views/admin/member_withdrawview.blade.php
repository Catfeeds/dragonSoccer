@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">提现</h1>   
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
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        <tr>
                            <td>{{$listArr->id}}</td>
                            <td>{{$listArr->member->name}}</td>
                            <td>{{$listArr->sn}}</td>
                            <td>{{$listArr->payuser}}</td>
                            <td>{{$listArr->total}}</td>
                            <td>{{empty($listArr->checktime)?'--':date('Y-m-d H:i:s',$listArr->checktime)}}</td>
                            <td>{{$listArr->paytotal}}</td>
                            <td>{{empty($listArr->paytime)?'--':date('Y-m-d H:i:s',$listArr->paytime)}}</td>
                            <td>{{$listArr->payway}}</td>
                            <td>{{$statusArr[$listArr->status]}}</td>
                            <td>{{$listArr->created_at}}</td>
                        </tr>       
                    @endif       
                </tbody>        
            </table>
            <div class="form-group form-inline  col-sm-12">
                <hr>
            </div> 
            <div class="row" style="width: 98%; margin: 0px auto;">
                <div class="col-sm-12 bg-success" style="height: 40px; margin: 15px 0px 0px; line-height: 40px;">总累计金额 <span style="font-size: 16px; color: red;">￥ {{$total}}</span> </div>
                @if(!empty($moneyArr))
                    @foreach($moneyArr as $k=>$v)
                        <div class="col-sm-3 bg-info">
                            {{$v->id}}
                            {{$typeArr[$v->type]}}
                            <span class="bg-warning" style="color: red;">+{{$v->money}}</span>
                            {{$v->created_at}}
                            <span style="color: red;">￥<?php empty($allmoney)?$allmoney=$v->money:($allmoney+=$v->money); echo $allmoney; ?></td></span>
                        </div>                          
                    @endforeach
                @endif   
            </div> 

            <div class="row" style="width: 98%; margin: 0px auto;">
                <div class="col-sm-12 bg-warning" style="height: 40px; margin: 15px 0px 0px; line-height: 40px;">总提现金额 <span style="font-size: 16px; color: red;">￥ -{{$withdraw}}</span></div>
                @if(!empty($withdrawArr))
                    @foreach($withdrawArr as $k=>$v)
                        <div class="col-sm-4 bg-info">
                            {{$v->id}}
                            {{$v->sn}}
                            <span class="bg-warning" style="color: red;">-{{$v->money}}</span>
                            {{$v->created_at}}
                            <span style="color: red;">￥ -<?php empty($allmoney2)?$allmoney2=$v->money:($allmoney2+=$v->money); echo $allmoney2; ?></td></span>
                        </div>                          
                    @endforeach
                @endif   
            </div>  



            <div class="form-group form-inline  col-sm-12">
                <hr>
            </div>           
            
            <div class="row">
                @if($listArr->status==1)
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">                       
                        <input type="hidden" name="id" value="{{$listArr->id}}">
                        @if(empty($company))
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">账号:</label>
                                <input class="form-control"  style="width:320px;" name="payuser" placeholder="请输入账号" value="">
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">金额:</label>
                                <input class="form-control"  style="width:320px;" name="paytotal" placeholder="请输入金额" value="">
                            </div>
                        @else
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                                <span class="form-control" style="font-size: 16px; color: red;">该成员属于注册推广成员，提现走线下渠道</span>
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>
                                <select class="form-control"  style="width:320px;" name="status">
                                        <option value="4">失败</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">备注:</label>
                                <input class="form-control"  style="width:320px;" name="remark" placeholder="请输入金额" value="该成员属于注册推广成员，提现走线下渠道">
                            </div>  
                        @endif    

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxwithdraw" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
                @endif
           </div> 
             
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(function(){
    //排序 编辑
    $(".ajaxwithdraw").on("click",function(){ 
        if(confirm('确认？？？')){
            $.ajax({
                type: "POST",
                url: '{{route("admin.member.ajaxwithdraw")}}',
                data:$(this).parents('form').serialize(),
                dataType: "json",
                headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                success: function (da) {
                    alert(da.msg);
                    //window.location.reload(); 
                },
            });
        }
        return false;            
    })
})    
</script>
@endsection