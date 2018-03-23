@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">商品管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.goods.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <div class="form-group  form-inline  col-sm-12" id="selectbtnbody">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">图片:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn" class="form-control" type="button" value="浏览">
                            <input class="form-control uploadbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="img">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">类型:</label>
                            <select class="form-control"  style="width:320px;" name="appleid">
                                <option value="android">android</option>
                                <option value="ios">ios</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">内购ID:</label>
                            <input class="form-control"  style="width:320px;" name="apple" placeholder="请输入内购ID">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">价格:</label>
                            <input class="form-control"  style="width:320px;" name="price" placeholder="请输入价格">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">数量:</label>
                            <input class="form-control"  style="width:320px;" name="number" placeholder="请输入数量,类型为android，该值为1" value="1" readonly="readonly">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">详情url:</label>
                            <input class="form-control"  style="width:320px;" name="url" placeholder="请输入详情url">
                        </div>                      

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="n">未发布</option>
                                <option value="y">已发布</option>
                            </select>
                         </div>

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.goods.ajaxadd')}}" type="button" value="保存">
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


