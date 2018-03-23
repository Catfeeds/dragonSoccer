<table class="table table-responsive table-striped" id="videos-table">
    <thead style="background-color:#F5F5F5;">
        <th width="5%"></th>
        <th width="5%">ID</th>
        <th width="10%">名称</th>
        <th width="10%">手机号</th>
        <th width="10%">注册时间</th>
        <th width="10%">推荐人</th>
        <th width="10%">认证状态</th>
    </thead>
    <tbody>
        @if(!empty($listArr))
            @foreach($listArr as $v)
                <tr>
                    <td><input type="checkbox" name="mids[]" value="{{$v->id}}"></td>
                    <td>{{$v->id}}</td>
                    <td>{{$v->name}}/{{empty($v->sex)?'--':($v->sex=='f'?'男':'女')}}</td>
                    <td>{{$v->mobile}}</td>
                    <td>{{$v->recommend}}</td>                                
                    <td>{{$v->created_at}}</td>
                    <td>{{$v->status=='n'?'未认证':'已认证'}}</td>
                </tr>                            
            @endforeach
        @endif       
    </tbody> 
    <tr>
        <td colspan="10">
            <ul class="pagination">
                @for($i=1;$i<=$listArr->lastPage();$i++)
                    <li  @if($curpage==$i) class="active" @endif><a href="javascript:;">{{$i}}</a></li>
                @endfor
                <li><a href="javascript:;">共计：{{$listArr->total()}}条</a></li>
            </ul>
        </td>
    </tr>       
</table>