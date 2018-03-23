@extends('front.common.content_common')
@section('content')
@if(!empty($listArr))
    @foreach($listArr as $v)		        
    	<div class="row">
		  	<div class="col-xs-12">
			  	@if(!empty($v['txt']))
			    	<p style="word-break:break-all;overflow:auto;">{{$v['txt']}}</p>
			    @endif
		  	</div>
		  	<div class="col-xs-12">
			    @if(!empty($v['img']))
			    	<img src="{{$v['img']}}" width="100%" height="100%">
			    @endif
		  	</div>
		</div>    
    @endforeach
@endif
@endsection
@section('scripts')
@endsection
