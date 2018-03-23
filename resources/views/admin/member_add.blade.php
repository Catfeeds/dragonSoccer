@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">会员管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.member.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody0">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn0" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">密码:</label>
                            <input class="form-control" type="password"   style="width:320px;" name="password" placeholder="请输入密码">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">重复密码:</label>
                            <input class="form-control" type="password"  style="width:320px;" name="password2" placeholder="请输入密码">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">手机:</label>
                            <input class="form-control"  style="width:320px;" name="mobile" placeholder="请输入mobile">
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

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">详细地址:</label>
                            <input class="form-control"  style="width:320px;" name="address" placeholder="请输入详细地址">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">真实姓名:</label>
                            <input class="form-control"  style="width:320px;" name="truename" placeholder="请输入真实姓名">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">民族:</label>
                            <input class="form-control"  style="width:320px;" name="nation" placeholder="请输入民族">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证号:</label>
                            <input class="form-control"  style="width:320px;" name="idnumber" placeholder="请输入身份证号">
                        </div>  

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody1">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证正面:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn1" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="idcard_f" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody2">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证反面:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn2" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="idcard_b" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证地址:</label>
                            <input class="form-control"  style="width:320px;" name="idcard_address" placeholder="请输入详细地址">
                        </div>

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody3">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">个人照片:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn3" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="img" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">所在学校:</label>
                            <input class="form-control"  style="width:320px;" name="school" placeholder="请输入所在学校">
                        </div>
                        
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">擅长位置:</label>
                            <select class="form-control"  style="width:320px;" name="position">
                                @foreach($memberArr['positionArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">管用脚:</label>
                            <select class="form-control"  style="width:320px;" name="foot">
                                @foreach($memberArr['footArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">简介:</label>
                            <textarea name="instruction" class="form-control"  style="width:320px; height:100px;" placeholder="请输入简介">
                                
                            </textarea>
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身高:</label>
                            <input class="form-control"  style="width:160px;" name="height" placeholder="请输入身高"> cm
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">体重:</label>
                            <input class="form-control"  style="width:160px;" name="weight" placeholder="请输入体重"> kg
                        </div>



                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.member.ajaxadd')}}" type="button" value="保存">
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
@include('admin.common.area_js')
@endsection


