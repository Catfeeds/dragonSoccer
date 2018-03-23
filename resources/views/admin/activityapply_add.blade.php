@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">代言人--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.activityapply.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">会员id:</label>
                            <input class="form-control"  style="width:320px;" name="mid" placeholder="请输入id">
                        </div>                      
                        
                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">简介:</label>
                            <textarea class="form-control" style="width:320px;" name="txt"  placeholder="请输入简介"></textarea>
                        </div>

                        @for($i=0;$i<=8;$i++)
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

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="w">待审核</option>
                                <option value="n">失败</option>
                                <option value="y">成功</option>
                            </select>
                        </div>                       

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.activityapply.ajaxadd')}}" type="button" value="保存">
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
@endsection


