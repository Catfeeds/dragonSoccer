@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">网站基本情况</h1>     
</section>
<div class="content">
    <div class="clearfix"></div>
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" style="word-break: break-all" id="videos-table">
            	<tr style="text-align: left;"><td >当前域名</td> <td>{{$_SERVER['SERVER_NAME']}}</td></tr>
		        <tr style="text-align: left;"><td >当前浏览器</td> <td>{{$_SERVER['HTTP_USER_AGENT']}}</td></tr>
		        <tr style="text-align: left;"><td >当前IP</td> <td>{{$_SERVER['REMOTE_ADDR']}}</td></tr>		       
		    </table>
        </div>
    </div>
</div>

@endsection
