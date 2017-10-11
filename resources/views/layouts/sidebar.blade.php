<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
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
        <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="nav-item start {{ Request::is('/') ? 'active open' : '' }}">
                <a href="{{ url('/') }}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>                 
                </a>                
            </li>

            @if(Auth::user()->role == 'Master' || Auth::user()->role == 'Admin')

            <li class="heading">
                <h3 class="uppercase">MASTER DATA</h3>
            </li>

            @endif

            @if(Auth::user()->role == 'Master')

            <li class="nav-item {{ Request::is('area') ? 'active open' : '' }} {{ Request::is('areaapp') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-map"></i>
                    <span class="title">Area</span>
                    <span class="arrow {{ Request::is('area') ? 'open' : '' }} {{ Request::is('areaapp') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('area') ? 'active' : '' }}">
                        <a href="{{ url('area') }}" class="nav-link ">
                            <span class="title">Area</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('areaapp') ? 'active open' : '' }} ">
                        <a href="{{ url('areaapp') }}" class="nav-link ">
                            <span class="title">Area RE Apps</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('accounttype') ? 'active open' : '' }} {{ Request::is('account') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-share-alt"></i>
                    <span class="title">Account</span>
                    <span class="arrow {{ Request::is('accounttype') ? 'open' : '' }} {{ Request::is('account') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('accounttype') ? 'active open' : '' }}">
                        <a href="{{ url('accounttype') }}" class="nav-link ">
                            <span class="title">Account Type</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('account') ? 'active open' : '' }}">
                        <a href="{{ url('account') }}" class="nav-link ">
                            <span class="title">Account</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('user*') ? 'active open' : '' }}">
                <a href="{{ url('user') }}" class="nav-link nav-toggle">
                    <i class="fa fa-group"></i>
                    <span class="title">Employee</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('store*') ? 'active open' : '' }} {{ Request::is('place*') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="title">Store</span>
                    <span class="arrow {{ Request::is('store*') ? 'open' : '' }} {{ Request::is('place*') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('store*') ? 'active open' : '' }}">
                        <a href="{{ url('store') }}" class="nav-link ">
                            <span class="title">Store</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('place*') ? 'active open' : '' }}">
                        <a href="{{ url('place') }}" class="nav-link ">
                            <span class="title">Other Places</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('group') ? 'active open' : '' }} {{ Request::is('category') ? 'active open' : '' }} {{ Request::is('product') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">Product</span>
                    <span class="arrow {{ Request::is('group') ? 'open' : '' }} {{ Request::is('category') ? 'open' : '' }} {{ Request::is('product') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('group') ? 'active open' : '' }}">
                        <a href="{{ url('group') }}" class="nav-link ">
                            <span class="title">Group</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('category') ? 'active open' : '' }}">
                        <a href="{{ url('category') }}" class="nav-link ">
                            <span class="title">Category</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('product') ? 'active open' : '' }}">
                        <a href="{{ url('product') }}" class="nav-link ">
                            <span class="title">Product</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('posm') ? 'active open' : '' }}">
                <a href="{{ url('posm') }}" class="nav-link nav-toggle">
                    <i class="fa fa-user"></i>
                    <span class="title">POS Material</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('groupcompetitor') ? 'active open' : '' }}">
            <a href="{{ url('groupcompetitor') }}" class="nav-link nav-toggle">
                <i class="fa fa-street-view"></i>
                <span class="title">Group Competitor</span>
            </a>

            @endif

            @if(Auth::user()->role == 'Master' || Auth::user()->role == 'Admin')


            </li>
            <li class="nav-item {{ Request::is('news*') ? 'active open' : '' }}">
                <a href="{{ url('news') }}" class="nav-link nav-toggle">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="title">News</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('product-knowledge*') ? 'active open' : '' }}">
                <a href="{{ url('product-knowledge') }}" class="nav-link nav-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="title">Product Knowledge</span>
                </a>
            </li>

            @endif

            <li class="heading">
                <h3 class="uppercase">SETTINGS</h3>
            </li>

            <li class="nav-item {{ Request::is('profile') ? 'active open' : '' }}">
                <a href="{{ url('profile') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Profile</span>                    
                </a>
            </li>

        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->