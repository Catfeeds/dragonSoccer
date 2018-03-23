@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛程管理--查看</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.adminuser.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">赛事名称:</label>{{empty($listArr->match)?'--':$listArr->match->name}}
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">A名称:</label>{{empty($listArr->ateam)?'--':$listArr->ateam->name}}
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">B名称:</label>{{empty($listArr->bteam)?'--':$listArr->bteam->name}}
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">比分:</label>{{$listArr->ateamscore.':'.$listArr->bteamscore}}
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">层级:</label>{{$matchArr['teamstsArr'][$listArr->matchlevel]}}
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>{{$matchlogArr['statusArr'][$listArr->status]}}
                    </div>

                    <hr class="col-sm-12">
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">地址&&时间：</label>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                        <table class="table table-responsive table-striped"  style="width:820px;" id="videos-table">
                            <thead style="background-color:#F5F5F5;">
                                <th width="10%">队名</th>
                                <th width="10%">名称</th>
                                <th width="10%">日期</th>
                                <th width="10%">联系人</th>
                                <th width="10%">联系电话</th>
                                <th width="10%">地址</th>
                            </thead>
                            <tbody>
                                @if(!empty($setArr))
                                    @foreach($setArr as $v)
                                        <tr>
                                            <td>{{$v->team->name}}</td>
                                            <td>{{$v->member->name}}</td>
                                            <td>
                                                <?php $mtimeArr = empty($v->mtime)?array():json_decode($v->mtime,true); ?>
                                                @foreach($mtimeArr as $mv)
                                                    <p>{{$mv}}</p>
                                                @endforeach
                                            </td>
                                            <td>{{$v->rname}}</td>
                                            <td>{{$v->phone}}</td>
                                            <td>{{$v->address}}</td>
                                        </tr>                            
                                    @endforeach
                                @endif       
                            </tbody>        
                        </table>
                    </div>

                    @if($listArr->status=='mc')
                        <form id="subform" method="POST" action="" accept-charset="UTF-8">
                            <input type="hidden" name="type" value="1">
                            <input type="hidden" name="id" value="{{$listArr->id}}">
                            <input type="hidden" name="status" value="mwate">
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">时间</label>
                                    <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd" style="width:320px;">
                                    <input class="form-control"  type="text"  name="stime" value="">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                                <select class="form-control"  style="width:320px;" name="stimet">
                                    <option value="9">上午</option>
                                    <option value="13">下午</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">地址</label>
                                <input class="form-control"  style="width:320px;" name="address" placeholder="请输入比赛地址" value="">
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                                <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.matchlog.ajaxsave')}}" type="button" value="保存">
                                <a href="" class="btn btn-default">取消</a>
                            </div>
                        </form>
                    @else
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">日期:</label>
                            @if(!empty($listArr->stime)) {{date('Y-m-d',$listArr->stime)}}|{{date('H',$listArr->stime)=='9'?'am':'pm'}} @endif
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                            {{$listArr->address}}
                        </div>
                    @endif

                    <hr class="col-sm-12">
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛结果：</label>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                        <table class="table table-responsive table-striped"  style="width:820px;" id="videos-table">
                            <thead style="background-color:#F5F5F5;">
                                <th width="10%">队名</th>
                                <th width="10%">名称</th>
                                <th width="10%">比分</th>
                            </thead>
                            <tbody>
                                @if(!empty($contentArr))
                                    @foreach($contentArr as $v)
                                        <tr>
                                            <td>{{$v->team->name}}</td>
                                            <td>{{$v->member->name}}</td>
                                            <td>{{$v->txt1}}:{{$v->txt2}}</td>
                                        </tr>                            
                                    @endforeach
                                @endif       
                            </tbody>        
                        </table>
                    </div> 
                    @if($listArr->status=='eupc')
                        <form id="subform" method="POST" action="" accept-charset="UTF-8">
                            <input type="hidden" name="type" value="2">
                            <input type="hidden" name="id" value="{{$listArr->id}}">                            
                            <input type="hidden" name="status" value="eover">
                            <div class="form-group form-inline col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">获胜队伍:</label>
                                <select class="form-control"  style="width:320px;" name="successteamid">
                                        <option value="all">全部淘汰</option>
                                    @if(!empty($listArr->ateam))
                                        <option value="{{$listArr->ateamid}}">{{$listArr->ateam->name}}</option>
                                    @endif

                                    @if(!empty($listArr->bteam))
                                        <option value="{{$listArr->bteamid}}">{{$listArr->bteam->name}}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">胜</label>
                                <input class="form-control"  style="width:320px;" name="successscore" placeholder="请输入获胜成绩" value="">
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">负</label>
                                <input class="form-control"  style="width:320px;" name="failedscore" placeholder="请输入失败成绩" value="">
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                                <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.matchlog.ajaxsave')}}" type="button" value="保存">
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
@include('admin.common.datetime_js')
<script type="text/javascript">
    $(document).ready(function (){
        //$('select[name=status]').val("{{$listArr->status}}");
    });
</script>  
@endsection
