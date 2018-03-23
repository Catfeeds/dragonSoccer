@for($k=1;$k<=$number;$k++)
<div class="form-group">
    <div class="input-group">
        <div class="input-group-addon">
            {{$rulerkey.$k}}
            <input class="form-control" type="hidden" name="rulerinfo[{{$rulerkey}}][key][]"  style="width:60px;" value="{{$rulerkey.$k}}">      
        </div>
        <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
            <input class="form-control" size="16" type="text" name="rulerinfo[{{$rulerkey}}][starttime][]" value="{{empty($rulerinfoArr[$rulerkey.$k]['starttime'])?'':date('Y-m-d',$rulerinfoArr[$rulerkey.$k]['starttime'])}}"  style="width:100px;">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
        <input class="form-control" size="16" type="text" name="rulerinfo[{{$rulerkey}}][endtime][]"  value="{{empty($rulerinfoArr[$rulerkey.$k]['endtime'])?'':date('Y-m-d',$rulerinfoArr[$rulerkey.$k]['endtime'])}}" style="width:100px;">
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div> 
</div>
@endfor
@include('admin.common.datetime_js')