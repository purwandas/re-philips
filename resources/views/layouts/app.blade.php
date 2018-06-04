<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Philips Retail Apps</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Philips Retail Apps" name="Philips Retail Apps" />
        <meta content="" name="PT. SADA INDONESIA" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{ asset('assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css') }}" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->  
        <!-- BEGIN PAGE LEVEL PLUGINS -->        
        <link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet"
          type="text/css"/>

        <link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{ asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{ asset('assets/layouts/layout4/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/layouts/layout4/css/themes/default.min.css') }}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{ asset('assets/swal/sweetalert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/layouts/layout4/css/custom.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->    
        <link href="{{ asset('assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css') }}" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" /> 
    </head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
    <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/layouts/layout2/img/logo-default-new3.png') }}" alt="logo" class="logo-default" /> </a>
                    <div class="menu-toggler sidebar-toggler">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN PAGE ACTIONS -->
                <!-- DOC: Remove "hide" class to enable the page header actions -->
                    <!-- Not Used -->
                <!-- END PAGE ACTIONS -->
                <!-- BEGIN PAGE TOP -->
                <div class="page-top">
                    <!-- BEGIN HEADER SEARCH BOX -->
                    <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
                        <!-- Not Used -->
                    <!-- END HEADER SEARCH BOX -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            @if(Auth::user()->role->role_group == 'Admin' || Auth::user()->role->role_group == 'Master')
                            <li class="dropdown dropdown-extended dropdown-notification dropdown-dark"
                                id="header_notification_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                                   data-close-others="true">
                                    <i class="fa fa-edit"></i>
                                    <span class="badge badge-danger bedgecount2">
                                    </span>
                                </a>
                                <ul class="dropdown-menu" style="min-width: 335px;">
                                    <li class="external">
                                        <h3>
                                            <span class="bold">
                                                <span class="bedgecount2"></span>
                                                Edit Sales Activities 
                                            </span>  
                                        </h3>
                                    </li>
                                    <li>
                                        <ul class="dropdown-menu-list scroller" style="height: 250px;"
                                            data-handle-color="#637283" id="dataSalesNotif"></ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown dropdown-extended dropdown-notification dropdown-dark"
                                id="header_notification_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                                   data-close-others="true">
                                    <i class="icon-user"></i>
                                    <span class="badge badge-danger bedgecount">
                                    </span>
                                </a>
                                <ul class="dropdown-menu" style="min-width: 335px;">
                                    <li class="external">
                                        <h3>
                                            <span class="bold"><span class="bedgecount"></span>
                                                Other user(s) is online </span>  </h3>
                                    </li>
                                    <li>
                                        <ul class="dropdown-menu-list scroller" style="height: 250px;"
                                            data-handle-color="#637283" id="datanotif"></ul>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                    <img alt="" class="img-circle" src="{{ Auth::user()->photo }}" onError="this.onerror=null;this.src='{{ asset('image/missing.png') }}';" />
                                    <span class="username username-hide-on-mobile"> {{ @Auth::user()->name }} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                              onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                            <i class="icon-key"></i> Log Out </a>
                                            <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                                  style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                    </li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END PAGE TOP -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            @include('layouts.sidebar')

        <div class="page-content-wrapper">
            <div class="page-content">                
                @yield('header')

                @yield('content')

                @include('partial.util.sales-history-modal')
            </div>
            <!-- END QUICK SIDEBAR -->
        </div>
        </div>
    </div>
    <div class="page-footer">
        <div class="page-footer-inner"> {{ date(' Y ') }} &copy; Philips Retail Apps</div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!-- Scripts -->        
        <script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
{{--        <script src="{{ asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>--}}
        <script src="{{ asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
                type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/morris/raphael-min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/counterup/jquery.waypoints.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/counterup/jquery.counterup.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/amcharts.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/serial.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/pie.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/radar.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/themes/light.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/themes/patterns.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amcharts/themes/chalk.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/ammap/ammap.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/ammap/maps/js/worldLow.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/amcharts/amstockcharts/amstock.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/fullcalendar/fullcalendar.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/flot/jquery.flot.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/flot/jquery.flot.resize.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/flot/jquery.flot.categories.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ asset('assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ asset('assets/pages/scripts/ui-extended-modals.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ asset('assets/pages/scripts/dashboard.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/pages/scripts/ui-blockui.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="{{ asset('assets/layouts/layout4/scripts/layout.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/layouts/layout4/scripts/demo.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
        <!-- BEGIN PAGE LEVEL SCRIPTS -->    
        <script src="{{ asset('assets/swal/sweetalert.min.js') }}"></script>
        <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->        
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{ asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ asset('assets/global/plugins/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}" type="text/javascript"></script>

        <!-- END PAGE LEVEL SCRIPTS -->

        <script>

            $(document).ready(function () {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            
            });

            var url = "{{url('util/user-online')}}";
                $.get(url, function (data) {
//                    console.log(data);
                    $('.bedgecount').text(data.count);

                    var missing_image = "{{ asset('image/missing.png') }}";

                    $.each(data.users, function (index, item) {
                       // console.log(item);

                        var li = $(`<li><a><img width='30px' height='30px' src='${item.photo}' onError='this.onerror=null;this.src="${missing_image}";'> &nbsp;&nbsp; (${item.role.role}) &nbsp;${item.name}</a></li>`);
                        $('#datanotif').append(li);
                    });
                });

            var urlCount = "{{url('util/sales-history-count')}}";
                $.get(urlCount, function (data) {
                   // console.log(data);
                    $('.bedgecount2').text(data.count);

                });

            var urlPromo = "{{url('util/sales-history')}}";
                $.get(urlPromo, function (data) {
                   // console.log(data);
                    // $('.bedgecount2').text(data.count);

                    $.each(data.activity, function (index, item) {
                       // console.log(item);
                       var obj = jQuery.parseJSON(item.details);
                       $.each(obj, function(index2, item2){
                         // console.log(item2);
                         var li = $(`<li class='open-sales-history-modal activity-${item.id}'
                          data-target='#sales-history-modal' 
                          data-toggle='modal' 
                          data-title='Sales Activity Details' 
                          data-history='promoter'
                          data-id='${item.id}'
                          data-user='${item.name}'
                          data-role='${item.role}'
                          data-date='${item.date}'
                          data-count='${data.count}'

                          data-activity='${item2.activity}'
                          data-type='${item2.type}'
                          data-action_from='${item2.action_from}'
                          data-detail_id='${item2.detail_id}'
                          data-week='${item2.week}'
                          data-distributor_code='${item2.distributor_code}'
                          data-distributor_name='${item2.distributor_name}'
                          data-region='${item2.region}'
                          data-region_id='${item2.region_id}'
                          data-channel='${item2.channel}'
                          data-sub_channel='${item2.sub_channel}'
                          data-area='${item2.area}'
                          data-area_id='${item2.area_id}'
                          data-district='${item2.district}'
                          data-district_id='${item2.district_id}'
                          data-store_name_1='${item2.store_name_1}'
                          data-store_name_2='${item2.store_name_2}'
                          data-store_id='${item2.store_id}'
                          data-storeId='${item2.storeId}'
                          data-dedicate='${item2.dedicate}'
                          data-nik='${item2.nik}'
                          data-promoter_name='${item2.promoter_name}'
                          data-user_id='${item2.user_id}'
                          // data-date='${item2.date}'
                          // data-role='${item2.role}'
                          data-spv_name='${item2.spv_name}'
                          data-dm_name='${item2.dm_name}'
                          data-trainer_name='${item2.trainer_name}'
                          data-model='${item2.model}'
                          data-group='${item2.group}'
                          data-category='${item2.category}'
                          data-product_name='${item2.product_name}'
                          data-unit_price='${item2.unit_price}'
                          data-quantity='${item2.quantity}'
                          data-value='${item2.value}'
                          data-value_pf_mr='${item2.value_pf_mr}'
                          data-value_pf_tr='${item2.value_pf_tr}'
                          data-value_pf_ppe='${item2.value_pf_ppe}'
                          data-new_quantity='${item2.new_quantity}'
                          data-new_value='${item2.new_value}'
                          data-new_value_pf_mr='${item2.new_value_pf_mr}'
                          data-new_value_pf_tr='${item2.new_value_pf_tr}'
                          data-new_value_pf_ppe='${item2.new_value_pf_ppe}'
                          >
                          <a> ${item.name} (${item.role}) - ${item2.activity} &nbsp;${item2.type}</a>
                          </li>`);
                            $('#dataSalesNotif').append(li);
                       });
                    });
                });

            var urlSee = "{{url('util/salesman-sales-history')}}";
                $.get(urlSee, function (data) {
                   // console.log(data);
                    // $('.bedgecount2').text(data.count);

                    $.each(data.activity, function (index, item) {
                       // console.log(item);
                       var obj = jQuery.parseJSON(item.details);
                       $.each(obj, function(index2, item2){
                         // console.log(item2);
                         var li = $(`<li class='open-sales-history-modal activity-${item.id}'
                          data-target='#sales-history-modal' 
                          data-toggle='modal' 
                          data-title='Sales Activity Details'
                          data-history='see' 
                          data-id='${item.id}'
                          data-user='${item.name}'
                          data-role='${item.role}'
                          data-date='${item.date}'
                          data-count='${data.count}'

                          data-activity='${item2.activity}'
                          data-type='${item2.type}'
                          data-action_from='${item2.action_from}'
                          data-detail_id='${item2.detail_id}'
                          data-week='${item2.week}'
                          data-distributor_code='${item2.distributor_code}'
                          data-distributor_name='${item2.distributor_name}'
                          data-region='${item2.region}'
                          data-region_id='${item2.region_id}'
                          data-channel='${item2.channel}'
                          data-sub_channel='${item2.sub_channel}'
                          data-area='${item2.area}'
                          data-area_id='${item2.area_id}'
                          data-district='${item2.district}'
                          data-district_id='${item2.district_id}'
                          data-store_name_1='${item2.store_name_1}'
                          data-store_name_2='${item2.store_name_2}'
                          data-store_id='${item2.store_id}'
                          data-storeId='${item2.storeId}'
                          data-dedicate='${item2.dedicate}'
                          data-nik='${item2.nik}'
                          data-promoter_name='${item2.promoter_name}'
                          data-user_id='${item2.user_id}'
                          // data-date='${item2.date}'
                          // data-role='${item2.role}'
                          data-spv_name='${item2.spv_name}'
                          data-dm_name='${item2.dm_name}'
                          data-trainer_name='${item2.trainer_name}'
                          data-model='${item2.model}'
                          data-group='${item2.group}'
                          data-category='${item2.category}'
                          data-product_name='${item2.product_name}'
                          data-unit_price='${item2.unit_price}'
                          data-quantity='${item2.quantity}'
                          data-value='${item2.value}'
                          data-value_pf='${item2.value_pf}'
                          data-new_quantity='${item2.new_quantity}'
                          data-new_value='${item2.new_value}'
                          data-new_value_pf='${item2.new_value_pf}'
                          >
                          <a> ${item.name} (${item.role}) - ${item2.activity} &nbsp;${item2.type}</a>
                          </li>`);
                            $('#dataSalesNotif').append(li);
                       });
                    });
                });

            
        </script>

        <!-- BEGIN TEXT MODAL SCRIPTS -->
    <script src="{{ asset('js/text-modal/saleshistory-popup.js') }}" type="text/javascript"></script>
    <!-- END TEXT MODAL SCRIPTS -->

    @yield('additional-scripts')

    

    @yield('additional-styles')

</body>

<style type="text/css">
    .tooltip {
        position: fixed;
        z-index: 10151 !important;
    }
    .modal.fade.in {
        margin-bottom: 50px;
    }
</style>

</html>
