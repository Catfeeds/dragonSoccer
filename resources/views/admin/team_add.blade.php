@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{{$type=='f'?'聊天群':'参赛队伍'}}--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.team.'.($type=='f'?'alist':'index') )}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">                       
                        <input type="hidden" name="type" value="{{$type}}">

                        <div class="form-group  form-inline  col-sm-12" id="selectbtnbody">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn" class="form-control" type="button" value="浏览">
                            <input class="form-control uploadbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly" value="">
                            <a href="" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>
                      
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称" value="">
                        </div>

                        @if($type=='m')
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                                <select class="form-control"  style="width:320px;" name="matchid">
                                    @foreach($matchArr as $v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group form-inline  col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                                <select class="form-control"  style="width:120px;" name="province">
                                    <option value="">请选择</option>
                                    @foreach($provinceArr as $v)
                                        <option value="{{$v->name}}" datacode='{{$v->code}}'>{{$v->name}}</option>
                                    @endforeach 
                                </select>

                                <select class="form-control"  style="width:120px;" name="city">
                                    <option value="">请选择</option>
                                </select>

                                <select class="form-control"  style="width:120px;" name="country">
                                    <option value="">请选择</option>
                                </select>
                            </div>
                        @endif
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.team.ajaxadd')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.upload_img_one_js') 
@include('admin.common.area_js')
@endsection
