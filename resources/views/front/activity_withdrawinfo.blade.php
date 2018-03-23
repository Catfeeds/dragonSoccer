@extends('front.common.activity_common')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
    <link href="/css/shareCash.css" rel="stylesheet">
@endsection

@section('content')
    @if(!empty($listArr))
        @foreach($listArr as $v)
            <div>
                <ul class="drawcell">
                    <li>{{$statusArr[$v->status]}}</li>
                    <li>{{$v->created_at}}</li>
                </ul>

                <p class="drawmoney">{{$v->total}}</p>
                <hr/>
            </div>
        @endforeach
    @endif


@endsection
