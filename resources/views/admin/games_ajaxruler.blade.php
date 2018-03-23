@for($k=1;$k<$ruler;$k++)
<div class="form-group form-inline col-sm-11 col-md-offset-1 ajaxbody">
    <div class="form-group col-sm-1">
        <input class="form-control" type="text" name="ruler[key][]"  style="width:60px;" value="{{$k}}">
    </div>    
    <div class="form-group col-sm-1">
        <input class="form-control ajaxrulerinfo"  dataid="{{empty($rulerArr[$k]['id'])?'':$rulerArr[$k]['id']}}" style="width:85px; " datakey="{{$k}}" name="ruler[teamnumber][]" placeholder="队伍数量" value="{{empty($rulerArr[$k]['teamnumber'])?'':$rulerArr[$k]['teamnumber']}}">
    </div>    
    <div class="form-group col-sm-1">
        <input class="form-control" style="width:85px; " name="ruler[risenumber][]" placeholder="晋级指数" value="{{empty($rulerArr[$k]['risenumber'])?'':$rulerArr[$k]['risenumber']}}">
    </div> 
    <div class="form-group col-sm-1">
        <input class="form-control" style="width:85px; " name="ruler[additionalnumber][]" placeholder="补充人数" value="{{empty($rulerArr[$k]['additionalnumber'])?'':$rulerArr[$k]['additionalnumber']}}">
    </div> 

    <div class="form-group col-sm-3">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="{{empty($rulerArr[$k]['starttime'])?'':date('Y-m-d',$rulerArr[$k]['starttime'])}}" name="ruler[starttime][]" style="width:100px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" size="16" type="text" value="{{empty($rulerArr[$k]['endtime'])?'':date('Y-m-d',$rulerArr[$k]['endtime'])}}" name="ruler[endtime][]" style="width:100px;">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div> 
        </div>
    </div>
    <div class="form-group col-sm-4 ajaxrulerinfocontent">
        @if(!empty($data[$k]['rulerinfoArr']))
        @foreach($data[$k]['rulerinfoArr'] as $vv)
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        {{$vv->key}}
                        <input class="form-control" type="hidden" name="rulerinfo[{{$k}}][key][]"  style="width:60px;" value="{{$vv->key}}">      
                    </div>
                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                        <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][starttime][]" value="{{empty($vv->starttime)?'':date('Y-m-d',$vv->starttime)}}"  style="width:100px;">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][endtime][]"  value="{{empty($vv->endtime)?'':date('Y-m-d',$vv->endtime)}}" style="width:100px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div> 
            </div>
        @endforeach    
        @endif    
    </div>
</div>
@endfor

<div class="form-group form-inline col-sm-11 col-md-offset-1">
    <div class="form-group col-sm-1">
        <input class="form-control" type="text" name="ruler[key][]"  style="width:60px;" value="{{$k}}">
    </div>    
    <div class="form-group col-sm-1">
        <input class="form-control" style="width:85px; " name="ruler[teamnumber][]" placeholder="队伍数量" value="{{empty($rulerArr[$k]['teamnumber'])?'1':$rulerArr[$k]['teamnumber']}}" readonly="readonly">
    </div>    
    <div class="form-group col-sm-1">
        <input class="form-control" style="width:85px; " name="ruler[risenumber][]" placeholder="晋级指数" value="{{empty($rulerArr[$k]['risenumber'])?'0':$rulerArr[$k]['risenumber']}}" readonly="readonly">
    </div> 
    <div class="form-group col-sm-1">
        <input class="form-control" style="width:85px; " name="ruler[additionalnumber][]" placeholder="补充人数" value="{{empty($rulerArr[$k]['additionalnumber'])?'':$rulerArr[$k]['additionalnumber']}}">
    </div> 

    <div class="form-group col-sm-3">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="{{empty($rulerArr[$k]['starttime'])?'':date('Y-m-d',$rulerArr[$k]['starttime'])}}" name="ruler[starttime][]" style="width:100px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" size="16" type="text" value="{{empty($rulerArr[$k]['endtime'])?'':date('Y-m-d',$rulerArr[$k]['endtime'])}}" name="ruler[endtime][]" style="width:100px;">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div> 
        </div>
    </div>
    <div class="form-group col-sm-4">
        @if(!empty($data[$k]['rulerinfoArr']))
        @foreach($data[$k]['rulerinfoArr'] as $vv)
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        {{$vv->key}}
                        <input class="form-control" type="hidden" name="rulerinfo[{{$k}}][key][]"  style="width:60px;" value="{{$vv->key}}">      
                    </div>
                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                        <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][starttime][]" value="{{empty($vv->starttime)?'':date('Y-m-d',$vv->starttime)}}"  style="width:100px;">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][endtime][]"  value="{{empty($vv->endtime)?'':date('Y-m-d',$vv->endtime)}}" style="width:100px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div> 
            </div>
        @endforeach 
        @else
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    {{$k}}1
                    <input class="form-control" type="hidden" name="rulerinfo[{{$k}}][key][]"  style="width:60px;" value="{{$k}}1">      
                </div>
                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][starttime][]" style="width:100px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" size="16" type="text" name="rulerinfo[{{$k}}][endtime][]" style="width:100px;">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div> 
        </div>           
        @endif

            
    </div>
</div>
@include('admin.common.datetime_js')