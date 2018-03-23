@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">推广列表</h1>
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body"> 
            <form class="form-inline" role="form" method="get" action="{{route('admin.company.view')}}">
                <input type="hidden" name="key" value="{{$key}}">
                <input type="hidden" name="type" value="{{$type}}">
                <table class="table table-responsive" >
                    <tr>
                        <td class="col-sm-8"  style="border: none;">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">创建时间:</div>
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="{{empty($stime)?'':$stime}}" name="stime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div> 
                                </div>
                            </div>至
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="{{empty($etime)?'':$etime}}" name="etime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>  
                                </div>
                            </div>
                            @if($type=='reg')
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">手机号:</div>
                                        <div class="input-group pull-left">
                                            <input class="form-control" size="16" type="text" value="{{empty($mobile)?'':$mobile}}" name="mobile">
                                        </div> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">认证状态:</div>
                                        <div class="input-group pull-left">
                                            <select class="form-control" name="isauth">
                                                <option value="">全部</option>
                                                <option value="n">未认证</option>
                                                <option value="y">已认证</option>
                                            </select>
                                        </div>  
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">比赛状态:</div>
                                        <div class="input-group pull-left">
                                            <select class="form-control" name="status">
                                                <option value="">全部</option>
                                                <option value="applyno">未报名</option>
                                                <option value="applyyes">已报名</option>
                                                <option value="apply1">报名成功</option>
                                                <option value="apply5">未匹配</option>
                                                <option value="apply6">匹配中</option>
                                                <option value="apply8">匹配成功</option>
                                            </select>
                                        </div>  
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="form-group col-sm-4"  style="border: none;">
                            <input  type="submit" class="btn btn-primary ajaxSearch" onclick="$(this).parents('form').attr('action','/admin/company/view')" value="搜索">
                            <input  type="submit" class="btn btn-primary ajaxSearch" onclick="$(this).parents('form').attr('action','/admin/company/export')" value="导出">
                        </td>
                    </tr>
                </table>
            </form>

            <table class="table table-responsive table-striped" id="videos-table">
                @if($type=='reg')
                    <thead style="background-color:#F5F5F5;">
                        <th width="5%">ID</th>
                        <th width="8%">名称</th>
                        <th width="10%">手机号</th>
                        <th width="8%">状态</th>
                        <th width="30%">比赛-赛区-比赛状态</th>
                        <th width="10%">加入时间</th>
                    </thead>
                    <tbody>
                        @if(!empty($listArr))
                            @foreach($listArr as $v)
                                <tr>
                                    <td>{{empty($v->id)?'':$v->id}}</td>
                                    <td>{{empty($v->name)?'':$v->name}}</td>
                                    <td>{{substr($v->mobile,0,3).'****'.substr($v->mobile,-4)}}</td>
                                    <td>{{$v->status=='n'?'未认证':'已认证'}}</td>
                                    <td>
                                        @if(!empty($v->apply))
                                            @foreach($v->apply as $vv)
                                                {{empty($vv->match)?'':$vv->match->name}} --{{empty($vv)?'':$vv->province.'/'.$vv->city}}--   {{empty($vv)?'':$applyArr['statusArr'][$vv->status]}} <br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{$v->created_at}}</td>
                                </tr>                            
                            @endforeach
                        @endif       
                    </tbody> 
                @endif

                @if($type=='apply')
                    <thead style="background-color:#F5F5F5;">
                        <th width="10%">名称</th>
                        <th width="10%">比赛</th>
                        <th width="10%">加入时间</th>
                    </thead>
                    <tbody>
                        @if(!empty($listArr))
                            @foreach($listArr as $v)
                                <tr>
                                    <td>{{empty($v->member)?'':$v->member->name}}</td>
                                    <td>{{empty($v->match)?'':$v->match->name}}</td>
                                    <td>{{$v->created_at}}</td>
                                </tr>                            
                            @endforeach
                        @endif       
                    </tbody> 
                @endif
                @if($type=='team')
                    <thead style="background-color:#F5F5F5;">
                        <th width="10%">名称</th>
                        <th width="10%">比赛</th>
                        <th width="10%">队伍</th>
                        <th width="10%">加入时间</th>
                    </thead>
                    <tbody>
                        @if(!empty($listArr))
                            @foreach($listArr as $v)
                                <tr>
                                    <td>{{empty($v->member)?'':$v->member->name}}</td>
                                    <td>{{empty($v->team)?'':$v->team->name}}</td>
                                    <td>{{empty($v->team->match)?'':$v->team->match->name}}</td>
                                    <td>{{$v->created_at}}</td>
                                </tr>                            
                            @endforeach
                        @endif       
                    </tbody> 
                @endif       
            </table>

            <div class="form-group form-inline col-sm-12">
                <span class="pull-left">{{$listArr->appends(['key'=>$key,'type'=>$type,'stime'=>$stime,'etime'=>$etime,'status'=>$status,'isauth'=>$isauth])->links()}}</span>
                <span  class="pull-left pagination" style="height: 30px; line-height: 34px;">&nbsp;&nbsp;&nbsp;&nbsp;共计：{{$listArr->total()}}条</span>
            </div>       
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.common.datetime_js')
<script type="text/javascript">
    $(document).ready(function (){
        $('select[name=status]').val("{{$status}}");
        $('select[name=isauth]').val("{{$isauth}}");
    });
</script> 
@endsection