            <div class="page-sidebar-wrapper">
                <!-- END SIDEBAR -->
                <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                <div class="page-sidebar navbar-collapse collapse">
                    <!-- BEGIN SIDEBAR MENU -->
                    <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                    <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                    <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                    <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                        <li class="nav-item start active open">
                            <a href="{{ url('/') }}" class="nav-link nav-toggle">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                                @if(Request::is('/'))
                                    <span class="selected"></span>
                                @endif
                            </a>
                        </li>
                        @if(Auth::user()->role == 'Master')
                        <li class="nav-item start active open">
                            <a class="nav-link nav-toggle">
                                <i class="icon-map"></i>
                                <span class="title">Area</span>
                                @if(Request::is('area') || Request::is('areaapp'))
                                    <span class="selected"></span>                 
                                @endif
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{ Request::is('area') ? 'open' : '' }}">
                                    <a href="{{ url('area') }}" class="nav-link ">                        
                                        <span class="title">Area</span>
                                    </a>                                    
                                </li>
                                <li class="nav-item start {{ Request::is('areaapp') ? 'open' : '' }}">
                                    <a href="{{ url('areaapp') }}" class="nav-link ">
                                        <span class="title">Area RE Apps</span>                                        
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item start active open">
                            <a class="nav-link nav-toggle">
                                <i class="fa fa-share-alt"></i>
                                <span class="title">Account</span>
                                @if(Request::is('accounttype') || Request::is('account'))
                                    <span class="selected"></span>                 
                                @endif
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{ Request::is('accounttype') ? 'open' : '' }}">
                                    <a href="{{ url('accounttype') }}" class="nav-link ">                        
                                        <span class="title">Account Type</span>
                                    </a>                                    
                                </li>
                                <li class="nav-item start {{ Request::is('account') ? 'open' : '' }}">
                                    <a href="{{ url('account') }}" class="nav-link ">
                                        <span class="title">Account</span>                                        
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item start active open">
                            <a href="{{ url('employee') }}" class="nav-link nav-toggle">
                                <i class="fa fa-group"></i>
                                <span class="title">Employee</span>
                                @if(Request::is('employee*'))
                                    <span class="selected"></span>                 
                                @endif                                
                            </a>                            
                        </li>
                        <li class="nav-item start active open">
                            <a class="nav-link nav-toggle">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="title">Store</span>
                                @if(Request::is('store*') || Request::is('place*'))
                                    <span class="selected"></span>                 
                                @endif
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{ Request::is('store*') ? 'open' : '' }}">
                                    <a href="{{ url('store') }}" class="nav-link ">                        
                                        <span class="title">Store</span>
                                    </a>                                    
                                </li>
                                <li class="nav-item start {{ Request::is('place*') ? 'open' : '' }}">
                                    <a href="{{ url('place') }}" class="nav-link ">
                                        <span class="title">Other Places</span>                                        
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item start active open">
                            <a href="{{ url('groupcompetitor') }}" class="nav-link nav-toggle">
                                <i class="fa fa-user"></i>
                                <span class="title">Group Competitor</span>
                                @if(Request::is('groupcompetitor*'))
                                    <span class="selected"></span>                 
                                @endif                                
                            </a>                            
                        </li>  
                        <li class="nav-item start active open">
                            <a href="{{ url('user') }}" class="nav-link nav-toggle">
                                <i class="fa fa-user"></i>
                                <span class="title">User</span>
                                @if(Request::is('user*'))
                                    <span class="selected"></span>                 
                                @endif                                
                            </a>                            
                        </li>                        
                        @endif
                        <li class="nav-item start active open">
                            <a href="{{ url('profile') }}" class="nav-link nav-toggle">
                                <i class="fa fa-cog"></i>
                                <span class="title">My Profile</span>
                                @if(Request::is('profile*'))
                                    <span class="selected"></span>                 
                                @endif                                
                            </a>                            
                        </li> 
                    </ul>
                    <!-- END SIDEBAR MENU -->
                </div>
                <!-- END SIDEBAR -->
            </div><!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->