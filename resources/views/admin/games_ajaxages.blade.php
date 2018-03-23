@foreach($footballageArr as $v)
<input class="form-control" type="hidden" name="age[id][]" style="width:60px;" value="{{empty($ageArr[$v]['id'])?'':$ageArr[$v]['id']}}">
<div class="form-group form-inline col-sm-11 col-md-offset-1">
<div class="checkbox">
    <label>
        <input class="form-control" type="text" name="age[key][]" style="width:60px;" value="{{$v}}" readonly="readonly">
        <input class="form-control" type="text" name="age[val][]" style="width:60px;" value="{{empty($ageArr[$v]['val'])?'':$ageArr[$v]['val']}}">
    </label>
</div>
<div class="form-group">
    <div class="input-group">
        <div class="input-group-addon">年龄:</div>
        <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
            <input class="form-control" size="16" type="text" value="{{empty($ageArr[$v]['starttime'])?'':date('Y-m-d',$ageArr[$v]['starttime'])}}" name="age[starttime][]">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
        <input class="form-control" size="16" type="text" value="{{empty($ageArr[$v]['endtime'])?'':date('Y-m-d',$ageArr[$v]['endtime'])}}" name="age[endtime][]">
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div> 
</div>
</div>
@endforeach
@include('admin.common.datetime_js')