@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">订单日志</h1>
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">           
            <table class="table table-responsive table-striped" id="videos-table" style="word-break:break-all; word-wrap:break-all;">
                <thead style="background-color:#F5F5F5;">
                    <th width="5%">ID</th>
                    <th width="5%">编号</th>
                    <th width="70%">返回值</th>
                    <th width="15%">下单时间</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{$v->sn}}</td>
                                <td>{{$v->content}}</td>
                                <td>{{$v->created_at}}</td>
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