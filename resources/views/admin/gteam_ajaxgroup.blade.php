 @if(!empty($gArr))
    @foreach($gArr as $v)
        <tr>
            <td><input type="checkbox" name="gids[]" value="{{$v->id}}"></td>
            <td>{{$v->id}}</td>
            <td>{{$v->number}}</td>
            <td>
                @foreach($v->gmember as $vv)
                    {{$vv->members->name}} &nbsp;&nbsp;&nbsp;&nbsp;{{$vv->members->mobile}}&nbsp;&nbsp;&nbsp;&nbsp;{{$vv->members->birthday}}&nbsp;&nbsp;&nbsp;&nbsp;{{$vv->created_at}}<br>
                @endforeach
            </td>
        </tr>                            
    @endforeach
@endif    