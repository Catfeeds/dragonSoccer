@extends('admin.common.common')

@section('content')
<section class="content-header">
    <h1 class="pull-left">网站配置</h1>     
</section>
<div class="clearfix"></div>
<div class="content">
    <div class="box box-primary">
        <div class="box-body">            
            <table class="table table-responsive table-striped" id="videos-table">
                <thead style="background-color:#F5F5F5;">
                    <th width="10%" style="text-align: center;">key</th>
                    <th width="10%" style="text-align: center;">标题</th>
                    <th width="60%" style="text-align: center;">val</th>
                </thead>
                <tbody>
                    <?php $i=0;?>
                    @foreach($webArr as $k=>$v)
                        @if($k=='indexschoolimg' || $k=='footballcashimg' || $k=='appindeximg')
                            <tr align="center" data="{{$k}}"   >
                                <td style="vertical-align: middle;">{{$k}}</td>
                                <td style="vertical-align: middle;">{{$v}}</td>
                                <td style="vertical-align: middle;" class="selectbtnbody" id="selectbtnbody{{$i}}">
                                    <div class="form-group form-inline">
                                        <img class="uploadimg selectbtn" id="selectbtn{{$i}}"  width="100" height="100"  rwidth="100" rheight="100" src="{{empty($$k)?'':$$k->val}}">
                                        <div class="input-group col-sm-10">
                                            <input  type="text" class="form-control uploadinput" value="{{empty($$k)?'':$$k->val}}" title="" readonly="readonly">
                                            <div class="input-group-addon  ajaxSaveVal" style="cursor: pointer;">保存</div>
                                        </div>
                                    </div> 
                                </td>                       
                            </tr> 
                            <?php $i++;?>
                        @else
                            <tr align="center" data="{{$k}}">
                                <td style="vertical-align: middle;">{{$k}}</td>
                                <td style="vertical-align: middle;">{{$v}}</td>
                                <td style="vertical-align: middle;">                              
                                    <div class="form-group ">
                                        <div class="input-group">
                                            <input class="form-control" value="{{empty($$k)?'':$$k->val}}" >
                                            <div class="input-group-addon  ajaxSaveVal" style="cursor: pointer;">保存</div>
                                        </div>
                                    </div> 
                                </td>                       
                            </tr>      
                        @endif
                    @endforeach

                                      
                </tbody>        
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@include('admin.common.upload_img_more_js')
<script type="text/javascript">
$(document).on('click','.ajaxSaveVal',function(){
    key = $(this).parents('tr').attr('data');
    val = $(this).prev().val();
    $.ajax({
        dataType: 'json',
        url: '{{route("admin.webconfig.ajaxsaveval") }}?key='+key+'&val='+val,
        success: function (da) {
            alert(da.msg);
            if (da.error==0) {
                window.location.reload();               
            }
            return false;
        },
    }); 
})
  
</script>
@endsection