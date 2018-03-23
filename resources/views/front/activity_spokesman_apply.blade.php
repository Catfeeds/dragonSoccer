@extends('front.common.activity_common')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
	<link href="/css/upExplainCss.css" rel="stylesheet">
@endsection

@section('content')
	<form>
		<input type="hidden" name="id" value="{{empty($listArr)?'':$listArr->id}}">
		<textarea  placeholder="请填写自我介绍" class="textView" name="txt">{{empty($listArr)?'':$listArr->txt}}</textarea>
		<hr/>
		<div class="container">
			<div class="row">
				@if(!empty($listArr->imgs))
					@foreach($listArr->imgs as $i=> $v)
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 placeImage selectbtnbody"  id="selectbtnbody{{$i}}">
							<img src="{{empty($v)?'/imgs/addBtn.png':$v}}" class="upimgs uploadimg selectbtn" style="width:100%; height:100%;" id="selectbtn{{$i}}" />
							<input class="uploadinput" type="hidden" name="imgs[]" value="{{empty($v)?'':$v}}">
						</div>
					@endforeach

					@for($i+=1;$i< 9;$i++)
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 placeImage selectbtnbody"  id="selectbtnbody{{$i}}">
							<img src="/imgs/addBtn.png" class="upimgs uploadimg selectbtn" style="width:100%; height:100%;" id="selectbtn{{$i}}" />
							<input class="uploadinput" type="hidden" name="imgs[]">
						</div>
					@endfor
				@else
					@for($i=0;$i< 9;$i++)
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 placeImage selectbtnbody"  id="selectbtnbody{{$i}}">
							<img src="/imgs/addBtn.png" class="upimgs uploadimg selectbtn" style="width:100%; height:100%;" id="selectbtn{{$i}}" />
							<input class="uploadinput" type="hidden" name="imgs[]">
						</div>
					@endfor
				@endif

				<!-- <div class="uploadspan"></div> -->
			</div>
		</div>

        
		<a><button class="navbar-fixed-bottom sureBtn ajaxformsubmit">完成</button></a>
	</form>
    

@endsection

@section('scripts')
@include('front.common.upload_img_more_js')
@include('front.common.activity_login_js')
<script type="text/javascript">
$(document).ready(function (){
	$(".ajaxformsubmit").on("click",function(){    
	    $.ajax({
	        type: "POST",
	        url: '{{url("activity/spokesman/ajaxapply?")}}'+parameter,
	        data:$(this).parents('form').serialize(),
	        dataType: "json",
	        headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
	        success: function (da) {
	            sendError(da.msg);
	            if (da.error == 0) {
	                window.location.href="{{url('/activity/spokesman/list?')}}"+parameter; 
	            }
	        },
	    });
	    return false;            
	})	
})	
</script>

@endsection