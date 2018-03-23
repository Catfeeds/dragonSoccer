@if(!empty($membersArr))
    @foreach($membersArr as $v)
        <tr>
            <td><input type="checkbox" name="mids[]" value="{{$v->id}}"></td>
            <td>{{$v->id}}</td>
            <td>{{$v->name}}</td>
            <td>{{$v->mobile}}</td>       
            <td>{{$v->birthday}}</td>       
            <td>{{$v->created_at}}</td>
            <td>{{$v->recommend}}</td> 
            <td>{{$v->status=='n'?'未认证':'已认证'}}</td>
        </tr>                            
    @endforeach

    <tr>
        <td colspan="10">
            <ul class="pagination">                         
                <li><a href="javascript:;"><i class="fa fa-caret-left"></i></a></li>
                @if($allpage <= 5)
                    @for($i=1;$i<=$allpage;$i++)
                        <li {{$page==$i?'class=active':''}}><a href="javascript:;">{{$i}}</a></li>
                    @endfor
                @endif

                @if($allpage > 5)
                    @if($page < 4)
                        @for($i=1;$i <= 5;$i++)
                            <li {{$page==$i?'class=active':''}}><a href="javascript:;">{{$i}}</a></li>
                        @endfor
                    @else
                        <li><a href="javascript:;">1</a></li>
                        <li class="disabled"><a href="javascript:;">...</a></li>
                    @endif

                    @if($page > 3 && ($allpage-$page) > 3)
                        <li><a href="javascript:;">{{$page-1}}</a></li>
                        <li class="active"><a href="{{$membersArr->url($page)}}">{{$page}}</a></li>
                        <li><a href="javascript:;">{{$page+1}}</a></li>
                    @endif

                    @if(($allpage-$page) < 4)
                        @for($i=4;$i>=0;$i--)
                            <li {{$page==($allpage-$i)?'class=active':''}}><a href="javascript:;">{{$allpage-$i}}</a></li>
                        @endfor
                    @else
                        <li class="disabled"><a href="javascript:;">...</a></li>
                        <li><a href="javascript:;">{{$allpage}}</a></li>
                    @endif
                @endif
                <li><a href="javascript:;"><i class="fa fa-caret-right"></i></a></li>

                <li><a href="javascript:;">共计：{{$membersArr->total()}}条</a></li>
            </ul>

            </ul>
        </td>
    </tr>    
@endif       