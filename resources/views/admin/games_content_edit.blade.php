@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{{$gameArr->name}}--详情编辑</h1>
    </section>
    <div class="clearfix"></div>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <input type="hidden" name="id" value="{{$listArr->id}}">
                        <input type="hidden" name="gamesid" value="{{$gameArr->id}}">
                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody0">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->img}}">
                            <input id="selectbtn0" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="img" readonly="readonly" value="{{$listArr->img}}">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">文字:</label>
                            <textarea class="form-control"  style="width:320px;" name="txt" placeholder="请输入内容">{{$listArr->txt}}</textarea>
                        </div>


                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.games.ajaxeditcontent')}}" type="button" value="保存">
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


