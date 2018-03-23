 @if(!empty($gArr))
    @foreach($gArr as $v)
        <tr>
            <td><input type="checkbox" name="teamids[]" value="{{$v->id}}"></td>
            <td>{{$v->id}}</td>
            <td>
                @foreach($v->teammember as $vv)
                    {{$vv->member->name}} &nbsp;&nbsp;&nbsp;&nbsp;{{$vv->member->mobile}}&nbsp;&nbsp;&nbsp;&nbsp;{{$vv->member->birthday}}&nbsp;&nbsp;&nbsp;&nbsp;{{$vv->created_at}}<br>
                @endforeach
            </td>
        </tr>                            
    @endforeach
@endif    