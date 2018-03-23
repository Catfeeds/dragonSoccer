@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛事管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.match.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">排序:</label>
                            <input class="form-control"  style="width:320px;" name="sid" placeholder="请输入排序">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">赛制:</label>
                            <select class="form-control"  style="width:320px;" name="rule">
                                @foreach($matchArr['ruleArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">地区:</label>
                            <select class="form-control"  style="width:320px;" name="region">
                                <option value="全国">全国</option>
                                @foreach($areaArr as $v)
                                    <option value="{{$v->name}}">{{$v->name}}</option>
                                @endforeach 
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">参赛性别:</label>
                            <select class="form-control"  style="width:320px;" name="sex">
                                @foreach($matchArr['sexArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">年龄层:</label>
                            <select class="form-control"  style="width:320px;" name="level">
                                @foreach($matchArr['levelArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group  form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">报名开始时间:</label>
                            <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                <input class="form-control"  type="text" value="" name="applystarttime">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>

                        <div class="form-group  form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">报名结束时间:</label>
                            <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                <input class="form-control"  type="text" value="" name="applyendtime">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>

                        <div class="form-group  form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛开始时间:</label>
                            <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                <input class="form-control"  type="text" value="" name="starttime">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>

                        <div class="form-group  form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛结束时间:</label>
                            <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                <input class="form-control"  type="text" value="" name="endtime">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="n">待发布</option>
                                <option value="y">已发布</option>
                            </select>
                        </div>

                        @for($i=0;$i<=5;$i++)
                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody{{$i}}">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">@if($i==0) 赛事图片: @endif</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn{{$i}}" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="imgs[]" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>
                        @endfor

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">规则:</label>
                            <script class="form-label pull-left col-sm-8" id="container" name="content" type="text/plain"></script>
                        </div>
                        

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.match.ajaxadd')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.upload_img_more_js')
@include('admin.common.ueditor_js')
@include('admin.common.datetime_js')
@endsection


