@extends('front.common.activity_common',['title'=>'活动'])

@section('css')
    <link href="/css/indexCss.css" rel="stylesheet">
@endsection

@section('content')

@if(!empty($listArr))
    @foreach($listArr as $v)
        <a href="javascript:;" dataurl="{{$v['url']}}" class="ajaxurl">
            <div class="background">
                <ul  style="background:url({{empty($v['img'])?'/imgs/activity_bgimg.png':$v['img']}}) no-repeat; background-size: 100%  100%;">
                    <li>{{$v['title']}}</li>
                    <li>{{$v['time']}}</li>
                </ul>
            </div>
        </a>
    @endforeach
@endif
@endsection

@section('scripts')
@include('front.common.activity_login_js')
@endsection
