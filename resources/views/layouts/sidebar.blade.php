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

            @if(Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin')

            <li class="heading">
                <h3 class="uppercase">MASTER DATA</h3>
            </li>

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

            <li class="nav-item {{ Request::is('store*') ? 'active open' : '' }} {{ Request::is('place*') ? 'active open' : '' }} {{ Request::is('channel*') ? 'active open' : '' }} {{ Request::is('subchannel*') ? 'active open' : '' }} {{ Request::is('distributor*') ? 'active open' : '' }} {{ Request::is('classification') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="title">Store(s)</span>
                    <span class="arrow {{ Request::is('store*') ? 'active open' : '' }} {{ Request::is('place*') ? 'active open' : '' }} {{ Request::is('channel*') ? 'active open' : '' }} {{ Request::is('subchannel*') ? 'active open' : '' }} {{ Request::is('distributor*') ? 'active open' : '' }} {{ Request::is('classification') ? 'active open' : '' }}"></span>
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
                    <li class="nav-item {{ Request::is('distributor') ? 'active open' : '' }}">
                        <a href="{{ url('distributor') }}" class="nav-link nav-toggle">
                            <span class="title">Distributor</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('store*') ? 'active open' : '' }}">
                        <a href="{{ url('store') }}" class="nav-link ">
                            <span class="title">Store</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('place*') ? 'active open' : '' }}">
                        <a href="{{ url('place') }}" class="nav-link ">
                            <span class="title">Other Store</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('classification') ? 'active open' : '' }}">
                        <a href="{{ url('classification') }}" class="nav-link nav-toggle">
                            <span class="title">Store Classification</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item 
            {{ Request::is('user*') ? 'active open' : '' }} {{ Request::is('role') ? 'active open' : '' }} {{ Request::is('grading') ? 'active open' : '' }} {{ Request::is('resign') ? 'active open' : '' }}
            {{ Request::is('rejoin') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-group"></i>
                    <span class="title">Employee</span>
                    <span class="arrow 
                    {{ Request::is('user*') ? 'open' : '' }} {{ Request::is('role') ? 'active open' : '' }} {{ Request::is('grading') ? 'active open' : '' }}
                    "></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('userpromoter*') ? 'active open' : '' }}">
                        <a href="{{ url('userpromoter') }}" class="nav-link nav-toggle">
                            <span class="title">Promoter</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('usernon*') ? 'active open' : '' }}">
                        <a href="{{ url('usernon') }}" class="nav-link nav-toggle">
                            <span class="title">Non-Promoter</span>
                        </a>
                    </li>
                    @if(Auth::user()->role->role_group == 'Master')
                    <li class="nav-item {{ Request::is('role') ? 'active open' : '' }}">
                        <a href="{{ url('role') }}" class="nav-link nav-toggle">
                            <span class="title">Employee Role</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ Request::is('grading') ? 'active open' : '' }}">
                        <a href="{{ url('grading') }}" class="nav-link nav-toggle">                    
                            <span class="title">Employee Grading</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('resign') ? 'active open' : '' }}">
                        <a href="{{ url('resign') }}" class="nav-link nav-toggle">                    
                            <span class="title">Employee Resignation</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('rejoin') ? 'active open' : '' }}">
                        <a href="{{ url('rejoin') }}" class="nav-link nav-toggle">                    
                            <span class="title">Resign Summary</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item 
            {{ Request::is('groupproduct') ? 'active open' : '' }}
            {{ Request::is('group') ? 'active open' : '' }} 
            {{ Request::is('category') ? 'active open' : '' }} 
            {{ Request::is('product') ? 'active open' : '' }}
            {{ Request::is('price') ? 'active open' : '' }}
            {{ Request::is('leadtime') ? 'active open' : '' }}
            {{ Request::is('productfocus') ? 'active open' : '' }}
            {{ Request::is('productfocussalesman') ? 'active open' : '' }}
            {{ Request::is('productpromo') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">Product</span>
                    <span class="arrow {{ Request::is('group') ? 'open' : '' }} {{ Request::is('category') ? 'open' : '' }} {{ Request::is('product') ? 'open' : '' }} {{ Request::is('price') ? 'open' : '' }} {{ Request::is('leadtime') ? 'open' : '' }} {{ Request::is('leadtime') ? 'active open' : '' }} {{ Request::is('productfocus') ? 'active open' : '' }} {{ Request::is('productfocussalesman') ? 'active open' : '' }} {{ Request::is('productpromo') ? 'active open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <!-- <li class="nav-item {{ Request::is('groupproduct') ? 'active open' : '' }}">
                        <a href="{{ url('groupproduct') }}" class="nav-link ">
                            <span class="title">Group Product</span>
                        </a>
                    </li> -->
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
                    <li class="nav-item {{ Request::is('price') ? 'active open' : '' }}">
                        <a href="{{ url('price') }}" class="nav-link nav-toggle">
                            <span class="title">Price</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('productfocus') ? 'active open' : '' }}">
                        <a href="{{ url('productfocus') }}" class="nav-link nav-toggle">
                            <span class="title">Product Focus</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('productfocussalesman') ? 'active open' : '' }}">
                        <a href="{{ url('productfocussalesman') }}" class="nav-link nav-toggle">
                            <span class="title">Salesman Product Focus</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('productpromo') ? 'active open' : '' }}">
                        <a href="{{ url('productpromo') }}" class="nav-link nav-toggle">
                            <span class="title">Product Promo Tracking</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('leadtime') ? 'active open' : '' }}">
                        <a href="{{ url('leadtime') }}" class="nav-link nav-toggle">
                            <span class="title">Leadtime</span>
                        </a>
                    </li>
                    
                </ul>
            </li>

            <li class="nav-item 
                {{ Request::is('target') ? 'active open' : '' }}            
                {{ Request::is('targetsalesman') ? 'active open' : '' }}                
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-line-chart"></i>
                    <span class="title">Target</span>
                    <span class="arrow 
                        {{ Request::is('target') ? 'active open' : '' }}                        
                        {{ Request::is('targetsalesman') ? 'active open' : '' }}                        
                    "></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Request::is('target') ? 'active open' : '' }}">
                        <a href="{{ url('target') }}" class="nav-link nav-toggle">
                            <span class="title">Promoter Target</span>
                        </a>
                    </li>                                
                    <li class="nav-item {{ Request::is('targetsalesman') ? 'active open' : '' }}">
                        <a href="{{ url('targetsalesman') }}" class="nav-link nav-toggle">
                            <span class="title">Salesman Target</span>
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

            <li class="nav-item {{ Request::is('timegone') ? 'active open' : '' }}">
                <a href="{{ url('timegone') }}" class="nav-link nav-toggle">
                    <i class="fa fa-hourglass-half"></i>
                    <span class="title">Timegone</span>
                </a>
            </li>
            
            <li class="nav-item {{ Request::is('apm') ? 'active open' : '' }}">
                <a href="{{ url('apm') }}" class="nav-link nav-toggle">
                    <i class="fa fa-area-chart"></i>
                    <span class="title">APM</span>
                </a>
            </li>
            
                       

            @endif

            @if(Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin' || Auth::user()->role->role_group == 'Supervisor' || Auth::user()->role->role_group == 'Supervisor Hybrid')

            <li class="heading">
                <h3 class="uppercase">INPUT SALES DATA</h3>
            </li>

            <li class="nav-item {{ Request::is('sellin') ? 'active open' : '' }}">
                <a href="{{ url('sellin') }}" class="nav-link nav-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="title">Sell Thru</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('sellout') ? 'active open' : '' }}">
                <a href="{{ url('sellout') }}" class="nav-link nav-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="title">Sell Out</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">EDIT/DELETE SALES DATA</h3>
            </li>

            <li class="nav-item
            {{ Request::is('editsellin') ? 'active open' : '' }}
            {{ Request::is('editsellout') ? 'active open' : '' }}
            {{ Request::is('editretdistributor') ? 'active open' : '' }}
            {{ Request::is('editretconsument') ? 'active open' : '' }}
            {{ Request::is('editfreeproduct') ? 'active open' : '' }}
            {{ Request::is('edittbat') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="title">Update Sales</span>
                    <span class="arrow
                        {{ Request::is('editsellin') ? 'active open' : '' }}
                        {{ Request::is('editsellout') ? 'active open' : '' }}
                        {{ Request::is('editretdistributor') ? 'active open' : '' }}
                        {{ Request::is('editretconsument') ? 'active open' : '' }}
                        {{ Request::is('editfreeproduct') ? 'active open' : '' }}
                        {{ Request::is('edittbat') ? 'active open' : '' }}
                    "></span>
                </a>
                <ul class="sub-menu">

                    <li class="nav-item {{ Request::is('editsellin') ? 'active open' : '' }}">
                        <a href="{{ url('editsellin') }}" class="nav-link nav-toggle">
                            <span class="title">Sell Thru</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('editsellout') ? 'active open' : '' }}">
                        <a href="{{ url('editsellout') }}" class="nav-link nav-toggle">
                            <span class="title">Sell Out</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('editretdistributor') ? 'active open' : '' }}">
                        <a href="{{ url('editretdistributor') }}" class="nav-link nav-toggle">
                            <span class="title">Return Distributor</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('editretconsument') ? 'active open' : '' }}">
                        <a href="{{ url('editretconsument') }}" class="nav-link nav-toggle">
                            <span class="title">Return Consument</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('editfreeproduct') ? 'active open' : '' }}">
                        <a href="{{ url('editfreeproduct') }}" class="nav-link nav-toggle">
                            <span class="title">Free Product</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('edittbat') ? 'active open' : '' }}">
                        <a href="{{ url('edittbat') }}" class="nav-link nav-toggle">
                            <span class="title">TBAT</span>
                        </a>
                    </li>

                </ul>
            </li>



            <li class="nav-item {{ Request::is('editsoh') ? 'active open' : '' }}">
            <a href="{{ url('editsoh') }}" class="nav-link nav-toggle">
                <i class="fa fa-edit"></i>
                <span class="title">Update SOH</span>
            </a>
            </li>

            <li class="nav-item {{ Request::is('editdisplayshare') ? 'active open' : '' }}">
            <a href="{{ url('editdisplayshare') }}" class="nav-link nav-toggle">
                <i class="fa fa-edit"></i>
                <span class="title">Update Display Share</span>
            </a>
            </li>

            <li class="nav-item {{ Request::is('editposmactivity') ? 'active open' : '' }}">
            <a href="{{ url('editposmactivity') }}" class="nav-link nav-toggle">
                <i class="fa fa-edit"></i>
                <span class="title">Update POSM Activity</span>
            </a>
            </li>

            @endif

            @if (Auth::user()->role == 'Master' || Auth::user()->role == 'Admin')

            <li class="heading">
                <h3 class="uppercase">ENTRIES ATTENDANCE</h3>
            </li>

            <li class="nav-item {{ Request::is('attendance*') ? 'active open' : '' }}">
                <a href="{{ url('attendance') }}" class="nav-link nav-toggle">
                    <i class="fa fa-calendar"></i>
                    <span class="title">Entries Promotors</span>
                </a>
            </li>

            @endif

            @if(Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin' || Auth::user()->role->role_group == 'Supervisor' || Auth::user()->role->role_group == 'Supervisor Hybrid'
            || Auth::user()->role->role_group == 'DM' || Auth::user()->role->role_group == 'RSM')


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
                    <i class="fa fa-file-text-o"></i>
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
                            <span class="title">Sell Thru</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('selloutreport') ? 'active open' : '' }}">
                        <a href="{{ url('selloutreport') }}" class="nav-link nav-toggle">
                            <span class="title">Sell Out</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('retdistributorreport') ? 'active open' : '' }}">
                        <a href="{{ url('retdistributorreport') }}" class="nav-link nav-toggle">
                            <span class="title">Return Distributor</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('retconsumentreport') ? 'active open' : '' }}">
                        <a href="{{ url('retconsumentreport') }}" class="nav-link nav-toggle">
                            <span class="title">Return Consument</span>
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
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">SOH</span>                    
                </a>
            </li>

            <li class="nav-item {{ Request::is('competitoractivityreport') ? 'active open' : '' }}">
                <a href="{{ url('competitoractivityreport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">Competitor Activity</span>                    
                </a>
            </li>

            <li class="nav-item {{ Request::is('promoactivityreport') ? 'active open' : '' }}">
                <a href="{{ url('promoactivityreport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">Promo Activity </span>                    
                </a>
            </li>

            <li class="nav-item 
            {{ Request::is('displaysharereport') ? 'active open' : '' }}
            {{ Request::is('posmactivityreport') ? 'active open' : '' }}
            ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">ISE Reporting</span>   
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

            <li class="nav-item {{ Request::is('maintenancerequest') ? 'active open' : '' }}">
                <a href="{{ url('maintenancerequest') }}" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">Maintenance Request</span>
                </a>
            </li>


            <li class="nav-item {{ Request::is('attendancereport') ? 'active open' : '' }}">
                <a href="{{ url('attendancereport') }}" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title"> Attendance </span>                    
                </a>
            </li>

                @if (Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin')

                    <li class="nav-item {{ Request::is('visitplan') ? 'active open' : '' }}">
                        <a href="{{ url('visitplan') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title"> Visit Plan </span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('salesmanreport') ? 'active open' : '' }}">
                    <a href="{{ url('salesmanreport') }}" class="nav-link nav-toggle">
                        <i class="fa fa-file-text-o"></i>
                        <span class="title"> Salesman Report </span>
                    </a>
                    </li>

                    <li class="nav-item {{ Request::is('achievement') ? 'active open' : '' }}">
                        <a href="{{ url('achievement') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Achievement</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('salesmanachievement') ? 'active open' : '' }}">
                        <a href="{{ url('salesmanachievement') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Salesman Achievement</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('salesactivity') ? 'active open' : '' }}">
                        <a href="{{ url('salesactivity') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Sales Activity</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('locationactivitystore') ? 'active open' : '' }}">
                        <a href="{{ url('locationactivitystore') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Store Location Activity</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('createactivitystore') ? 'active open' : '' }}">
                        <a href="{{ url('createactivitystore') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Store Create Activity</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('konfig-promoter') ? 'active open' : '' }}">
                        <a href="{{ url('konfig-promoter') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Konfigurasi Promoter</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('konfig-store') ? 'active open' : '' }}">
                        <a href="{{ url('konfig-store') }}" class="nav-link nav-toggle">
                            <i class="fa fa-file-text-o"></i>
                            <span class="title">Konfigurasi Store</span>
                        </a>
                    </li>

                @endif

            @endif

            @if (Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin')

            <li class="heading">
                <h3 class="uppercase">UTILITIES</h3>
            </li>

            <li class="nav-item {{ Request::is('news*') ? 'active open' : '' }}">
                <a href="{{ url('news') }}" class="nav-link nav-toggle">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="title">News</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('product-knowledge*') ? 'active open' : '' }}">
                <a href="{{ url('product-knowledge') }}" class="nav-link nav-toggle">
                    <i class="fa fa-book"></i>
                    <span class="title">Guidelines</span>
                </a>
            </li>

            <li class="nav-item {{ Request::is('faq') ? 'active' : '' }}">
                <a href="{{ url('faq') }}" class="nav-link ">
                    <i class="fa fa-commenting-o"></i>
                    <span class="title">Frequently Asked Question (FAQ)</span>
                </a>
            </li>

            <li class="nav-item {{ Request::is('feedback*') ? 'active open' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-commenting-o"></i>
                    <span class="title">Feedback</span>
                    <span class="arrow 
                    {{ Request::is('feedbackCategory*') ? 'open' : '' }} 
                    {{ Request::is('feedbackQuestion*') ? 'open' : '' }} 
                    {{ Request::is('feedbackAnswer*') ? 'open' : '' }}"></span>
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

            </li>
            <li class="nav-item {{ Request::is('quiz*') ? 'active open' : '' }}">
                <a href="{{ url('quiz') }}" class="nav-link nav-toggle">
                    <i class="fa fa-list-alt"></i>
                    <span class="title">Quiz</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('fanspage') ? 'active open' : '' }}">
                <a href="{{ url('fanspage') }}" class="nav-link nav-toggle">
                    <i class="fa fa-chain"></i>
                    <span class="title">Fanspage</span>
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
                    <i class="fa fa-envelope"></i>
                    <span class="title">Message</span>                    
                </a>
            </li>

        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->