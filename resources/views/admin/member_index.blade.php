@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">会员管理</h1>
    @if(Gate::forUser(auth('adminusers')->user())->check('admin.member.add'))
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.member.add')}}">添加</a>  
        </h1>
    @endif
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            <form class="form-inline" role="form" method="get" action="{{route('admin.member.index')}}">
                <table class="table table-responsive" >
                    <tr>
                        <td class="col-sm-8"  style="border: none;">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">ID:</div>
                                    <div class="input-group date form_datetime form-date-box pull-left">
                                        <input class="form-control" size="16" type="text" value="{{empty($id)?'':$id}}" name="id">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">手机号:</div>
                                    <div class="input-group date form_datetime form-date-box pull-left">
                                        <input class="form-control" size="16" type="text" value="{{empty($mobile)?'':$mobile}}" name="mobile">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">推荐人:</div>
                                    <div class="input-group date form_datetime form-date-box pull-left" >
                                        <input class="form-control" size="16" type="text" value="{{empty($recommend)?'':$recommend}}" name="recommend">
                                    </div>  
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">认证状态:</div>
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                                        <select class="form-control" name="status">
                                            <option value="">全部</option>
                                            <option value="n">未认证</option>
                                            <option value="y">已认证</option>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                        </td>                        
                        <td class="form-group col-sm-4"  style="border: none;">
                            <input  type="submit" class="btn btn-primary ajaxSearch"  value="搜索">
                        </td>
                    </tr>

                    <tr>
                        <td class="col-sm-8"  style="border: none;">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">昵称:</div>
                                    <div class="input-group pull-left" >
                                        <input class="form-control" size="16" type="text" value="{{empty($name)?'':$name}}" name="name">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">姓名:</div>
                                    <div class="input-group pull-left">
                                        <input class="form-control" size="16" type="text" value="{{empty($truename)?'':$truename}}" name="truename">
                                    </div>  
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">学校:</div>
                                    <div class="input-group pull-left">
                                        <input class="form-control" size="16" type="text" value="{{empty($school)?'':$school}}" name="school">
                                    </div>  
                                </div>
                            </div>
                        </td>       
                    </tr>
                    <tr>
                        <td class="col-sm-8"  style="border: none;">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">注册时间:</div>
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
                        </td>
                    </tr>
                </table>
            </form>

            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="10%">头像</th>
                    <th width="10%">昵称</th>
                    <th width="10%">姓名</th>
                    <th width="5%">ID</th>
                    <th width="10%">手机号</th>
                    <th width="10%">学校</th>
                    <th width="10%">注册时间</th>
                    <th width="5%">好友</th>
                    <th width="5%">群</th>
                    <th width="5%">比赛</th>
                    <th width="10%">推荐人</th>
                    <th width="10%">匹配人数</th>
                    <th width="10%">参赛人数</th>
                    <th width="10%">认证状态</th>
                    <th width="20%">操作</th>
                </thead>
                <tbody>
                    @if(!empty($listArr))
                        @foreach($listArr as $v)
                            <tr>
                                <td><img src="{{$v->icon}}" width="50" height="50"></td>
                                <td>{{$v->name}}/{{empty($v->sex)?'--':($v->sex=='f'?'男':'女')}}</td>
                                <td>{{$v->truename}}</td>
                                <td>{{$v->id}}</td>
                                <td>{{$v->mobile}}</td>
                                <td>{{$v->school}}</td>
                                <td>{{$v->created_at}}</td>
                                <td>{{$v->fnum}}</td>
                                <td>{{$v->mnum}}</td>
                                <td>{{$v->tnum}}</td>
                                <td>{{$v->recommend}}</td>                                
                                <td>{{$v->applynum}}</td>
                                <td>{{$v->teamnum}}</td>
                                <td>{{$v->status=='n'?'未认证':'已认证'}}</td>
                                <td>                            
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.member.edit'))
                                        <a href="{{route('admin.member.edit')}}?id={{$v->id}}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    
                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.member.del'))
                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.member.ajaxdel')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
                                    @endif

                                    @if(Gate::forUser(auth('adminusers')->user())->check('admin.member.ajaxreset'))
                                        <a href="javascript:void(0);" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.member.ajaxreset')}}" dataid='{{$v->id}}' title="重置密码"><i class="glyphicon glyphicon-wrench  btn btn-danger btn-xs"></i></a>
                                    @endif
                                </td>
                            </tr>                            
                        @endforeach
                    @endif       
                </tbody>        
            </table>

            @if($listArr->lastPage() >1)
                <div class="form-group form-inline col-sm-12">
                    <span class="pull-left">{{$listArr->appends(['mobile'=>$mobile,'recommend'=>$recommend,'stime'=>$stime,'etime'=>$etime,'name'=>$name,'truename'=>$truename,'school'=>$school,'id'=>$id])->links()}}</span>
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
        $('select[name=status]').val("{{$status}}");
    });
</script> 
@endsection