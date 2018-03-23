<aside class="main-sidebar" id="sidebar-wrapper">
    <section class="sidebar">
       <!--  <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/logo-white.png" class="img-circle"
                     alt="User Image"/>
            </div>
        </div> -->

       <?php $sidebarMenu = Request::get('sidebarMenu'); $curUrl = strtolower(Route::currentRouteName()); $urlArr = explode('.',$curUrl); $urlArr[1] = empty($urlArr[1])?'index':$urlArr[1]; ?>
        <ul class="sidebar-menu">
            <li class="header">栏目导航</li>
            @foreach($sidebarMenu as $v)
                <li class="treeview @if(in_array($urlArr[1],$v['urlArr'])) active @endif ">
                    <a href="#">
                        <i class="fa {{$v['icon']}}"></i>
                        <span>{{$v['title']}}</span> 
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if(!empty($v['sons']))
                            @foreach($v['sons'] as $vv)
                                <li class="treeview @if($urlArr[1]==$vv['urlstr']) active @endif">
                                    <a href="{{route($vv['url'])}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa {{ $vv['icon'] }} "></i>{{$vv['title']}}</a>
                                </li>  
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endforeach
        </ul>
        
    </section>
</aside>