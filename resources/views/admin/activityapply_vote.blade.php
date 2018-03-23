@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">代言人-投票</h1>
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="5%">ID</th>
                    <th width="10%">名称</th>
                    <th width="10%">最佳</th>
                    <th width="10%">票数</th>
                    <th width="10%">时间</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <?php $imgArr = empty($v->imgs)?array():explode('#',$v->imgs);?>
                            <tr>
                                <td>{{$v->id}}</td>
                                <td>{{$v->member->name}}</td>
                                <td>{{$v->bestmember->name}}</td>
                                <td>{{$v->number}}</td>
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
@endsection