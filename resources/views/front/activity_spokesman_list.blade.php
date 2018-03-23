@extends('front.common.activity_common')

@section('css')
@endsection

@section('content')
<!--两张图-->
<div class="row">
<a href="javascript:;" dataurl="{{url('activity/spokesman/detail/f?')}}" class="ajaxurl"><img src="http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/aboy.jpg" class="am-img-responsive col-xs-12" style="padding: 0px;" /></a>
<a href="javascript:;" dataurl="{{url('activity/spokesman/detail/m?')}}" class="ajaxurl"><img src="http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/agirl.jpg" class="am-img-responsive col-xs-12" style="padding: 0px;" /></a>


	<div class="navbar-fixed-bottom" style="margin-left:80%; "><a href="/txt/spokesman"><img src="/imgs/shuomkng.png" style="width: 60px;"></a></div>
</div>


@endsection
@section('scripts')
@include('front.common.activity_login_js')
@endsection