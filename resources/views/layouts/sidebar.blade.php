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

            <li class="nav-item {{ Request::is('area') ? 'active open' : '' }} {{ Request::is('district') ? 'active open' : '' }} {{ Request::is('areaapp') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-map"></i>
                    <span class="title">Area</span>
                    <span class="arrow {{ Request::is('area') ? 'open' : '' }} {{ Request::is('district') ? 'active open' : '' }} {{ Request::is('areaapp') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('area') ? 'active' : '' }}">
                        <a href="{{ url('area') }}" class="nav-link ">
                            <span class="title">Area</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('district') ? 'active open' : '' }} ">
                        <a href="{{ url('district') }}" class="nav-link ">
                            <span class="title">District</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('channel') ? 'active open' : '' }} {{ Request::is('subchannel') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-share-alt"></i>
                    <span class="title">Channel</span>
                    <span class="arrow {{ Request::is('channel') ? 'open' : '' }} {{ Request::is('subchannel') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('channel') ? 'active open' : '' }}">
                        <a href="{{ url('channel') }}" class="nav-link ">
                            <span class="title">Channel</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('subchannel') ? 'active open' : '' }}">
                        <a href="{{ url('subchannel') }}" class="nav-link ">
                            <span class="title">Sub Channel</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('distributor') ? 'active open' : '' }}">
                <a href="{{ url('distributor') }}" class="nav-link nav-toggle">
                    <i class="fa fa-industry"></i>
                    <span class="title">Distributor</span>
                </a>
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
            <li class="nav-item 
            {{ Request::is('group') ? 'active open' : '' }} 
            {{ Request::is('category') ? 'active open' : '' }} 
            {{ Request::is('product') ? 'active open' : '' }}
            {{ Request::is('groupproduct') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">Product</span>
                    <span class="arrow {{ Request::is('group') ? 'open' : '' }} {{ Request::is('category') ? 'open' : '' }} {{ Request::is('product') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('groupproduct') ? 'active open' : '' }}">
                        <a href="{{ url('groupproduct') }}" class="nav-link ">
                            <span class="title">Group Product</span>
                        </a>
                    </li>
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

            <li class="nav-item 
                {{ Request::is('target') ? 'active open' : '' }}
                {{ Request::is('productfocus') ? 'active open' : '' }}
                {{ Request::is('price') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">Traget</span>
                    <span class="arrow 
                        {{ Request::is('target') ? 'active open' : '' }}
                        {{ Request::is('productfocus') ? 'active open' : '' }}
                        {{ Request::is('price') ? 'active open' : '' }}
                    "></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('target') ? 'active open' : '' }}">
                        <a href="{{ url('target') }}" class="nav-link nav-toggle">
                            <span class="title">Promoter Target</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('productfocus') ? 'active open' : '' }}">
                        <a href="{{ url('productfocus') }}" class="nav-link nav-toggle">
                            <span class="title">Product Focus</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('price') ? 'active open' : '' }}">
                        <a href="{{ url('price') }}" class="nav-link nav-toggle">
                            <span class="title">Price</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            
            <li class="nav-item {{ Request::is('posm') ? 'active open' : '' }}">
                <a href="{{ url('posm') }}" class="nav-link nav-toggle">
                    <i class="fa fa-tasks"></i>
                    <span class="title">POS Material</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('groupcompetitor') ? 'active open' : '' }}">
            <a href="{{ url('groupcompetitor') }}" class="nav-link nav-toggle">
                <i class="fa fa-street-view"></i>
                <span class="title">Group Competitor</span>
            </a>
            </li>

            <li class="nav-item {{ Request::is('fanspage') ? 'active open' : '' }}">
            <a href="{{ url('fanspage') }}" class="nav-link nav-toggle">
                <i class="fa fa-chain"></i>
                <span class="title">Fanspage</span>
            </a>
            </li>

            <li class="nav-item {{ Request::is('feedbackCategory*') ? 'active open' : '' }} {{ Request::is('feedbackAnswer*') ? 'active open' : '' }} {{ Request::is('product') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-commenting-o"></i>
                    <span class="title">Feedback</span>
                    <span class="arrow {{ Request::is('feedbackCategory*') ? 'open' : '' }} {{ Request::is('feedbackAnswer*') ? 'open' : '' }} {{ Request::is('product') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('feedbackCategory*') ? 'active open' : '' }}">
                        <a href="{{ url('feedbackCategory') }}" class="nav-link ">
                            <span class="title">Feedback Category</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('feedbackQuestion*') ? 'active open' : '' }}">
                        <a href="{{ url('feedbackQuestion') }}" class="nav-link ">
                            <span class="title">Feedback Question</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('feedbackAnswer*') ? 'active open' : '' }}">
                        <a href="{{ url('feedbackAnswer') }}" class="nav-link ">
                            <span class="title">Feedback Answer</span>
                        </a>
                    </li>
                </ul>
            </li>
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
            <li class="nav-item {{ Request::is('quiz*') ? 'active open' : '' }}">
                <a href="{{ url('quiz') }}" class="nav-link nav-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="title">Quiz</span>
                </a>
            </li>

            @endif

            @if(Auth::user()->role == 'Master' || Auth::user()->role == 'Admin')

            <li class="heading">
                <h3 class="uppercase">REPORTING</h3>
            </li>

            <li class="nav-item 
            {{ Request::is('sellinreport') ? 'active open' : '' }} 
            {{ Request::is('selloutreport') ? 'active open' : '' }}
            {{ Request::is('retconsumentreport') ? 'active open' : '' }}
            {{ Request::is('retdistributorreport') ? 'active open' : '' }}
            {{ Request::is('freeproductreport') ? 'active open' : '' }}
            {{ Request::is('tbatreport') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Sales</span>
                    <span class="arrow 
                    {{ Request::is('sellinreport') ? 'active open' : '' }} 
                    {{ Request::is('selloutreport') ? 'active open' : '' }}
                    {{ Request::is('retconsumentreport') ? 'active open' : '' }}
                    {{ Request::is('retdistributorreport') ? 'active open' : '' }}
                    {{ Request::is('freeproductreport') ? 'active open' : '' }}
                    {{ Request::is('tbatreport') ? 'active open' : '' }}
                    "></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('sellinreport') ? 'active open' : '' }}  ">
                        <a href="{{ url('sellinreport') }}" class="nav-link ">
                            <span class="title">Sell In</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('selloutreport') ? 'active open' : '' }}">
                        <a href="{{ url('selloutreport') }}" class="nav-link nav-toggle">
                            <span class="title">Sell Out</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('retconsumentreport') ? 'active open' : '' }}">
                        <a href="{{ url('retconsumentreport') }}" class="nav-link nav-toggle">
                            <span class="title">Return Consument</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('retdistributorreport') ? 'active open' : '' }}">
                        <a href="{{ url('retdistributorreport') }}" class="nav-link nav-toggle">
                            <span class="title">Return Distributor</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('freeproductreport') ? 'active open' : '' }}">
                        <a href="{{ url('freeproductreport') }}" class="nav-link nav-toggle">
                            <span class="title">Free Product</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('tbatreport') ? 'active open' : '' }}">
                        <a href="{{ url('tbatreport') }}" class="nav-link nav-toggle">
                            <span class="title">TBAT</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is('sohreport') ? 'active open' : '' }}">
                <a href="{{ url('sohreport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">SOH</span>                    
                </a>
            </li>


            <li class="nav-item {{ Request::is('maintenancerequest') ? 'active open' : '' }}">
                <a href="{{ url('maintenancerequest') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Maintenance Request</span>                    
                </a>
            </li>

            <li class="nav-item {{ Request::is('competitoractivityreport') ? 'active open' : '' }}">
                <a href="{{ url('competitoractivityreport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Competitor Activity</span>                    
                </a>
            </li>

            <li class="nav-item {{ Request::is('promoactivityreport') ? 'active open' : '' }}">
                <a href="{{ url('promoactivityreport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Promo Activity </span>                    
                </a>
            </li>

            <li class="nav-item 
            {{ Request::is('displaysharereport') ? 'active open' : '' }}
            {{ Request::is('posmactivityreport') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">ISE </span>   
                    <span class="arrow 
                    {{ Request::is('displaysharereport') ? 'active open' : '' }}
                    {{ Request::is('posmactivityreport') ? 'active open' : '' }}
                    "></span>                 
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('displaysharereport') ? 'active open' : '' }}  ">
                        <a href="{{ url('displaysharereport') }}" class="nav-link ">
                            <span class="title">Display Share</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('posmactivityreport') ? 'active open' : '' }}">
                        <a href="{{ url('posmactivityreport') }}" class="nav-link nav-toggle">
                            <span class="title">POSM Activity </span>                    
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ Request::is('attendancereport') ? 'active open' : '' }}">
                <a href="{{ url('attendancereport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title"> Attendance </span>                    
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
            <li class="nav-item {{ Request::is('messageToAdmin') ? 'active open' : '' }}">
                <a href="{{ url('messageToAdmin') }}" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">Message</span>                    
                </a>
            </li>

        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->