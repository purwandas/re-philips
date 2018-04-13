@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Attendance Report
                <small>report Attendance</small>
            </h1>
        </div>
        <!-- END PAGE TITLE -->
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span class="active">Attendance Reporting</span>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                
                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">Attendance</span>
                        </div>
                    </div>
                    <div class="tabbable-line boxless tabbable-reversed">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_0" data-toggle="tab"> Promoter</a>
                            </li>
                            <li>
                                <a href="#tab_1" data-toggle="tab"> Supervisor Promoter</a>
                            </li>
                            <li>
                                <a href="#tab_2" data-toggle="tab"> Supervisor Demonstrator</a>
                            </li>
                            <li>
                                <a href="#tab_3" data-toggle="tab"> Others</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="caption padding-caption">
                                            <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> FILTERS BY</span>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterRegion" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterArea" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterDistrict" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterStore" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterEmployee" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" id="monthContent" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <input type="text" id="filterMonth" class="form-control" placeholder="Month">
                                            </div>
                                        </div>

                                        <br>

                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerReset(paramReset)">
                                                <i class="fa fa-refresh"></i> Reset </a>
                                            <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringAttendanceReport(paramFilter)">
                                                <i class="fa fa-filter"></i> Filter </a>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-md-12" id="dataContent">
                                        <div class="portlet-title col-md-12" style="margin-bottom: 15px;padding-right: 0px;">
                                            <div class="actions" style="float: right;">
                                                <a id="export" class="btn green-dark" >
                                                    <i id="exportIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                                            </div>
                                            <div class="actions" style="float: right; padding-right: 10px;">
                                                <a id="exportAll" class="btn green-dark" disabled>
                                                    <i id="exportAllIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                                            </div>
                                        </div>

                                        <div class="portlet-body" id="attendanceTable">
                                            <table class="table table-striped table-hover table-bordered" id="AttendanceReport" style="white-space: nowrap;">
                                                <thead>
                                                <tr>
                                                    <th> No. </th>
                                                    <th> NIK </th>
                                                    <th> Name </th>
                                                    <th> Role </th>
                                                    <th> Attendance </th>
                                                    <th> Attendance Detail </th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="caption padding-caption">
                                            <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> FILTERS BY</span>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterRegionSpv" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterAreaSpv" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterDistrictSpv" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterStoreSpv" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterEmployeeSpv" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" id="monthContentSpv" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <input type="text" id="filterMonthSpv" class="form-control" placeholder="Month">
                                            </div>
                                        </div>

                                        <br>

                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn red-pink" id="resetButtonSpv" onclick="triggerReset(paramResetSpv)">
                                                <i class="fa fa-refresh"></i> Reset </a>
                                            <a href="javascript:;" class="btn blue-hoki"  id="filterButtonSpv" onclick="filteringAttendanceReport(paramFilterSpv)">
                                                <i class="fa fa-filter"></i> Filter </a>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-md-12 display-hide" id="dataContentSpv">
                                        <div class="portlet-title col-md-12" style="margin-bottom: 15px;padding-right: 0px;">
                                            <div class="actions" style="float: right;">
                                                <a id="exportSpv" class="btn green-dark" >
                                                    <i id="exportSpvIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                                            </div>
                                            <div class="actions" style="float: right; padding-right: 10px;">
                                                <a id="exportAllSpv" class="btn green-dark" >
                                                    <i id="exportAllSpvIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                                            </div>
                                        </div>

                                        <div class="portlet-body" id="attendanceTableSpv">
                                            <table class="table table-striped table-hover table-bordered" id="AttendanceReportSpv" style="white-space: nowrap;">
                                                <thead>
                                                <tr>
                                                    <th> No. </th>
                                                    <th> NIK </th>
                                                    <th> Name </th>
                                                    <th> Role </th>
                                                    <th> Attendance </th>
                                                    <th> Attendance Detail </th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="caption padding-caption">
                                            <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> FILTERS BY</span>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterRegionDemo" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterAreaDemo" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterDistrictDemo" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <select id="filterStoreDemo" class="select2select"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterEmployeeDemo" class="select2select"></select>
                                            </div>
                                        </div>

                                        <div class="row filter" id="monthContentDemo" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <input type="text" id="filterMonthDemo" class="form-control" placeholder="Month">
                                            </div>
                                        </div>

                                        <br>

                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn red-pink" id="resetButtonDemo" onclick="triggerReset(paramResetDemo)">
                                                <i class="fa fa-refresh"></i> Reset </a>
                                            <a href="javascript:;" class="btn blue-hoki"  id="filterButtonDemo" onclick="filteringAttendanceReport(paramFilterDemo)">
                                                <i class="fa fa-filter"></i> Filter </a>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-md-12 display-hide" id="dataContentDemo">
                                        <div class="portlet-title col-md-12" style="margin-bottom: 15px;padding-right: 0px;">
                                            <div class="actions" style="float: right;">
                                                <a id="exportDemo" class="btn green-dark" >
                                                    <i id="exportDemoIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                                            </div>
                                            <div class="actions" style="float: right; padding-right: 10px;">
                                                <a id="exportAllDemo" class="btn green-dark" >
                                                    <i id="exportAllDemoIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                                            </div>
                                        </div>

                                        <div class="portlet-body" id="attendanceTableDemo">
                                            <table class="table table-striped table-hover table-bordered" id="AttendanceReportDemo" style="white-space: nowrap;">
                                                <thead>
                                                <tr>
                                                    <th> No. </th>
                                                    <th> NIK </th>
                                                    <th> Name </th>
                                                    <th> Role </th>
                                                    <th> Attendance </th>
                                                    <th> Attendance Detail </th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="caption padding-caption">
                                            <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> FILTERS BY</span>
                                        </div>

                                        <div class="row filter" style="margin-top: 10px;">
                                            <div class="col-md-4">
                                                <input type="text" id="filterMonthOthers" class="form-control" placeholder="Month">
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filterEmployeeOthers" class="select2select"></select>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn red-pink" id="resetButtonOthers" onclick="triggerReset(paramResetOthers)">
                                                <i class="fa fa-refresh"></i> Reset </a>
                                            <a href="javascript:;" class="btn blue-hoki"  id="filterButtonOthers" onclick="filteringAttendanceReport(paramFilterOthers)">
                                                <i class="fa fa-filter"></i> Filter </a>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-md-12 display-hide" id="dataContentOthers">
                                        <div class="portlet-title col-md-12" style="margin-bottom: 15px;padding-right: 0px;">
                                            <div class="actions" style="float: right;">
                                                <a id="exportOthers" class="btn green-dark" >
                                                    <i id="exportOthersIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                                            </div>
                                            <div class="actions" style="float: right; padding-right: 10px;">
                                                <a id="exportAllOthers" class="btn green-dark" >
                                                    <i id="exportAllOthersIcon" class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                                            </div>
                                        </div>

                                        <div class="portlet-body" id="attendanceTableOthers">
                                            <table class="table table-striped table-hover table-bordered" id="AttendanceReportOthers" style="white-space: nowrap;">
                                                <thead>
                                                <tr>
                                                    <th> No. </th>
                                                    <th> NIK </th>
                                                    <th> Name </th>
                                                    <th> Role </th>
                                                    <th> Attendance </th>
                                                    <th> Attendance Detail </th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- END MAIN CONTENT -->

            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
            @include('partial.util.attendance-detail-modal')
        </div>
    </div>
@endsection

@section('additional-scripts')

    <style type="text/css">
        .cursor-pointer{
            cursor: pointer;
        }
    </style>

    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/handler/datetimepicker-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
    <!-- BEGIN TEXT MODAL SCRIPTS -->
    <script src="{{ asset('js/text-modal/popup.js') }}" type="text/javascript"></script>
    <!-- END TEXT MODAL SCRIPTS -->

    <script>
        /*
         *
         *
         */

        // Promoter group
            var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterEmployee'];
            var url = 'datatable/attendancereport';
            var order = [ [0, 'desc'] ];
            var columnDefs = [{"className": "dt-center", "targets": [0]},{ "width": "100%", "targets": 5 }];
            var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                                {data: 'user_nik', name: 'user_nik'},
                                {data: 'user_name', name: 'user_name'},
                                {data: 'user_role', name: 'user_role'},
                                {data: 'total_hk', name: 'total_hk'},
                                {data: 'attendance_details', name: 'attendance_details'},
                                ];

            var exportButton = '#export';

            var paramFilter = ['AttendanceReport', $("#AttendanceReport"), url, tableColumns, columnDefs, order, exportButton];

            var paramReset = [filterId, 'AttendanceReport', $('#AttendanceReport'), url, tableColumns, columnDefs, order, exportButton];

        // SPV promo + hybrid
            var filterId = ['#filterRegionSpv', '#filterAreaSpv', '#filterDistrictSpv', '#filterStoreSpv', '#filterEmployeeSpv'];
            var url = 'datatable/attendancereportspv';
            var order = [ [0, 'desc'] ];
            var columnDefs = [{"className": "dt-center", "targets": [0]},{ "width": "100%", "targets": 5 }];
            var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                                {data: 'user_nik', name: 'user_nik'},
                                {data: 'user_name', name: 'user_name'},
                                {data: 'user_role', name: 'user_role'},
                                {data: 'total_hk', name: 'total_hk'},
                                {data: 'attendance_details', name: 'attendance_details'},
                                ];

            var exportButton = '#exportSpv';

            var paramFilterSpv = ['AttendanceReportSpv', $("#AttendanceReportSpv"), url, tableColumns, columnDefs, order, exportButton];

            var paramResetSpv = [filterId, 'AttendanceReportSpv', $('#AttendanceReportSpv'), url, tableColumns, columnDefs, order, exportButton];

        // SPV demo
            var filterId = ['#filterRegionDemo', '#filterAreaDemo', '#filterDistrictDemo', '#filterStoreDemo', '#filterEmployeeDemo'];
            var url = 'datatable/attendancereportdemo';
            var order = [ [0, 'desc'] ];
            var columnDefs = [{"className": "dt-center", "targets": [0]},{ "width": "100%", "targets": 5 }];
            var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                                {data: 'user_nik', name: 'user_nik'},
                                {data: 'user_name', name: 'user_name'},
                                {data: 'user_role', name: 'user_role'},
                                {data: 'total_hk', name: 'total_hk'},
                                {data: 'attendance_details', name: 'attendance_details'},
                                ];

            var exportButton = '#exportDemo';

            var paramFilterDemo = ['AttendanceReportDemo', $("#AttendanceReportDemo"), url, tableColumns, columnDefs, order, exportButton];

            var paramResetDemo = [filterId, 'AttendanceReportDemo', $('#AttendanceReportDemo'), url, tableColumns, columnDefs, order, exportButton];

        // others
            var filterId = ['#filterRegionOthers', '#filterAreaOthers', '#filterDistrictOthers', '#filterStoreOthers', '#filterEmployeeOthers'];
            var url = 'datatable/attendancereportothers';
            var order = [ [0, 'desc'] ];
            var columnDefs = [{"className": "dt-center", "targets": [0]},{ "width": "100%", "targets": 5 }];
            var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                                {data: 'user_nik', name: 'user_nik'},
                                {data: 'user_name', name: 'user_name'},
                                {data: 'user_role', name: 'user_role'},
                                {data: 'total_hk', name: 'total_hk'},
                                {data: 'attendance_details', name: 'attendance_details'},
                                ];

            var exportButton = '#exportOthers';

            var paramFilterOthers = ['AttendanceReportOthers', $("#AttendanceReportOthers"), url, tableColumns, columnDefs, order, exportButton];

            var paramResetOthers = [filterId, 'AttendanceReportOthers', $('#AttendanceReportOthers'), url, tableColumns, columnDefs, order, exportButton];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAll').removeAttr('disabled');
                            }else{
                                $('#exportAll').attr('disabled','disabled');
                            }
                }
            });

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataSpvC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllSpv').removeAttr('disabled');
                            }else{
                                $('#exportAllSpv').attr('disabled','disabled');
                            }

                }
            });

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataDemoC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllDemo').removeAttr('disabled');
                            }else{
                                $('#exportAllDemo').attr('disabled','disabled');
                            }
                }
            });

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataOthersC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllOthers').removeAttr('disabled');
                            }else{
                                $('#exportAllOthers').attr('disabled','disabled');
                            }                    
                }
            });

            filteringAttendanceReport(paramFilter);
            initSelect2();
            initDateTimePicker();

            filteringAttendanceReport(paramFilterSpv);
            initSelect2Spv();
            initDateTimePickerSpv();

            filteringAttendanceReport(paramFilterDemo);
            initSelect2Demo();
            initDateTimePickerDemo();

            filteringAttendanceReport(paramFilterOthers);
            initSelect2Others();
            initDateTimePickerOthers();

            
        
        });

        function initSelect2(){

            /*
             * Select 2 init
             *
             */

            $('#filterRegion').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterRegion').on('select2:select', function () {
                self.selected('byRegion', $('#filterRegion').val());
            });

            $('#filterArea').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterArea').on('select2:select', function () {
                self.selected('byArea', $('#filterArea').val());
            });

            $('#filterDistrict').select2(setOptions('{{ route("data.district") }}', 'District', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterDistrict').on('select2:select', function () {
                self.selected('byDistrict', $('#filterDistrict').val());
            });

            $('#filterStore').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
                return filterData('store', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
	                    if(obj.store_name_2 != null){
                            return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
                        return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1}
	                })
                }
            }));
            $('#filterStore').on('select2:select', function () {
                self.selected('byStore', $('#filterStore').val());
            });

            $('#filterEmployee').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {
	        	filters['promoterGroup'] = 1;
	            return filterData('employee', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {
	                    return {id: obj.id, text: obj.nik + " - " + obj.name}
	                })
	            }
	        }));
            $('#filterEmployee').on('select2:select', function () {
                self.selected('byEmployee', $('#filterEmployee').val());
            });

        }

        function initDateTimePicker(){

            // Filter Month
            $('#filterMonth').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        }

        function initSelect2Spv(){

            /*
             * Select 2 init
             *
             */

            $('#filterRegionSpv').select2(setOptions('{{ route("data.regionspv") }}', 'Region', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterRegionSpv').on('select2:select', function () {
                self.selected('byRegionSpv', $('#filterRegionSpv').val());
            });

            $('#filterAreaSpv').select2(setOptions('{{ route("data.areaspv") }}', 'Area', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterAreaSpv').on('select2:select', function () {
                self.selected('byAreaSpv', $('#filterAreaSpv').val());
            });

            $('#filterDistrictSpv').select2(setOptions('{{ route("data.districtspv") }}', 'District', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterDistrictSpv').on('select2:select', function () {
                self.selected('byDistrictSpv', $('#filterDistrictSpv').val());
            });

            $('#filterStoreSpv').select2(setOptions('{{ route("data.storespv") }}', 'Store', function (params) {
                return filterData('storeSpv', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        if(obj.store_name_2 != null){
                            return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
                        return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1}
                    })
                }
            }));
            $('#filterStoreSpv').on('select2:select', function () {
                self.selected('byStoreSpv', $('#filterStoreSpv').val());
            });

            $('#filterEmployeeSpv').select2(setOptions('{{ route("data.spvpromo") }}', 'Supervisor Promoter / Hybrid', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        if (obj.nik != null) {
                            return {id: obj.id, text: obj.nik + " - " + obj.name}
                        }
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterEmployeeSpv').on('select2:select', function () {
                self.selected('byEmployeeSpv', $('#filterEmployeeSpv').val());
            });

        }

        function initDateTimePickerSpv(){

            // Filter Month
            $('#filterMonthSpv').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            $('#filterMonthSpv').val(moment().format('MMMM YYYY'));
            filters['searchMonthSpv'] = $('#filterMonthSpv').val();

        }

        function initSelect2Demo(){

            /*
             * Select 2 init
             *
             */

            $('#filterRegionDemo').select2(setOptions('{{ route("data.regiondemo") }}', 'Region', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterRegionDemo').on('select2:select', function () {
                self.selected('byRegionDemo', $('#filterRegionDemo').val());
            });

            $('#filterAreaDemo').select2(setOptions('{{ route("data.areademo") }}', 'Area', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterAreaDemo').on('select2:select', function () {
                self.selected('byAreaDemo', $('#filterAreaDemo').val());
            });

            $('#filterDistrictDemo').select2(setOptions('{{ route("data.districtdemo") }}', 'District', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterDistrictDemo').on('select2:select', function () {
                self.selected('byDistrictDemo', $('#filterDistrictDemo').val());
            });

            $('#filterStoreDemo').select2(setOptions('{{ route("data.storedemo") }}', 'Store', function (params) {
                return filterData('storeDemo', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        if(obj.store_name_2 != null){
                            return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
                        return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1}
                    })
                }
            }));
            $('#filterStoreDemo').on('select2:select', function () {
                self.selected('byStoreDemo', $('#filterStoreDemo').val());
            });

            $('#filterEmployeeDemo').select2(setOptions('{{ route("data.spvdemo") }}', 'Supervisor Demonstrator', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.nik + " - " + obj.name}
                    })
                }
            }));
            $('#filterEmployeeDemo').on('select2:select', function () {
                self.selected('byEmployeeDemo', $('#filterEmployeeDemo').val());
            });

        }

        function initDateTimePickerDemo(){

            // Filter Month
            $('#filterMonthDemo').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            $('#filterMonthDemo').val(moment().format('MMMM YYYY'));
            filters['searchMonthDemo'] = $('#filterMonthDemo').val();

        }

        function initSelect2Others(){

            /*
             * Select 2 init
             *
             */

            $('#filterEmployeeOthers').select2(setOptions('{{ route("data.userothers") }}', 'Employee', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name + " - " + obj.role}
                    })
                }
            }));
            $('#filterEmployeeOthers').on('select2:select', function () {
                self.selected('byEmployeeOthers', $('#filterEmployeeOthers').val());
            });

        }

        function initDateTimePickerOthers(){

            // Filter Month
            $('#filterMonthOthers').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            $('#filterMonthOthers').val(moment().format('MMMM YYYY'));
            filters['searchMonthOthers'] = $('#filterMonthOthers').val();

        }

        // On Change Search Date
		$(document).ready(function() {

            $('#filterMonth').change(function(){
                filters['searchMonth'] = this.value;
                console.log(filters);
            });

            $('#filterMonthSpv').change(function(){
                filters['searchMonthSpv'] = this.value;
            });

            $('#filterMonthDemo').change(function(){
                filters['searchMonthDemo'] = this.value;
                console.log(filters);
            });

            $('#filterMonthOthers').change(function(){
                filters['searchMonthOthers'] = this.value;
                console.log(filters);
            });

        });

        $("#resetButton").click( function(){
            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();
            
            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAll').removeAttr('disabled');
                            }else{
                                $('#exportAll').attr('disabled','disabled');
                            }

                }
            });
        });

        $("#resetButtonSpv").click( function(){
            // Set to Month now
            $('#filterMonthSpv').val(moment().format('MMMM YYYY'));
            filters['searchMonthSpv'] = $('#filterMonthSpv').val();
            
            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataSpvC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllSpv').removeAttr('disabled');
                            }else{
                                $('#exportAllSpv').attr('disabled','disabled');
                            }

                }
            });
        });

        $("#resetButtonDemo").click( function(){
            // Set to Month now
            $('#filterMonthDemo').val(moment().format('MMMM YYYY'));
            filters['searchMonthDemo'] = $('#filterMonthDemo').val();

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataDemoC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllDemo').removeAttr('disabled');
                            }else{
                                $('#exportAllDemo').attr('disabled','disabled');
                            }
                }
            });
        });

        $("#resetButtonOthers").click( function(){
            // Set to Month now
            $('#filterMonthOthers').val(moment().format('MMMM YYYY'));
            filters['searchMonthOthers'] = $('#filterMonthOthers').val();

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataOthersC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllOthers').removeAttr('disabled');
                            }else{
                                $('#exportAllOthers').attr('disabled','disabled');
                            }
                }
            });
        });

        $("#filterButton").click( function(){
            $('#dataContent').removeClass('display-hide');
            $('#dataContentSpv').addClass('display-hide');
            $('#dataContentDemo').addClass('display-hide');
            $('#dataContentOthers').addClass('display-hide');
            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAll').removeAttr('disabled');
                            }else{
                                $('#exportAll').attr('disabled','disabled');
                            }

                }
            });
        });

        $("#filterButtonSpv").click( function(){
            
            $('#dataContentSpv').removeClass('display-hide');
            $('#dataContent').addClass('display-hide');
            $('#dataContentDemo').addClass('display-hide');
            $('#dataContentOthers').addClass('display-hide');

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataSpvC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllSpv').removeAttr('disabled');
                            }else{
                                $('#exportAllSpv').attr('disabled','disabled');
                            }

                }
            });

        });
        $("#filterButtonDemo").click( function(){
            // Set Table Content
            $('#dataContentDemo').removeClass('display-hide');
            $('#dataContent').addClass('display-hide');
            $('#dataContentSpv').addClass('display-hide');
            $('#dataContentOthers').addClass('display-hide');

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataDemoC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllDemo').removeAttr('disabled');
                            }else{
                                $('#exportAllDemo').attr('disabled','disabled');
                            }
                }
            });
        });
        $("#filterButtonOthers").click( function(){
            // Set Table Content
            $('#dataContentOthers').removeClass('display-hide');
            $('#dataContent').addClass('display-hide');
            $('#dataContentSpv').addClass('display-hide');
            $('#dataContentDemo').addClass('display-hide');

            $.ajax({
                type: 'POST',
                url: 'data/attendanceDataOthersC',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAllOthers').removeAttr('disabled');
                            }else{
                                $('#exportAllOthers').attr('disabled','disabled');
                            }

                }
            });
        });

        $("#export").click( function(){

            var element = $("#export");
            var icon = $("#exportIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance',
                    dataType: 'json',
                    data: {data: JSON.stringify(data)},
                    global: false,
                    async: false,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;

                        setTimeout(function () {
                            $.ajax({
                                type: 'POST',
                                url: 'util/export-delete',
                                dataType: 'json',
                                data: {data: data.url},
                                global: false,
                                async: false,
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }, 1000);


                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }


        });

        $("#exportSpv").click( function(){

            var element = $("#exportSpv");
            var icon = $("#exportSpvIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance',
                    dataType: 'json',
                    data: {data: JSON.stringify(data)},
                    global: false,
                    async: false,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;

                        setTimeout(function () {
                            $.ajax({
                                type: 'POST',
                                url: 'util/export-delete',
                                dataType: 'json',
                                data: {data: data.url},
                                global: false,
                                async: false,
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }, 1000);


                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }


        });

        $("#exportDemo").click( function(){

            var element = $("#exportDemo");
            var icon = $("#exportDemoIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance',
                    dataType: 'json',
                    data: {data: JSON.stringify(data)},
                    global: false,
                    async: false,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;

                        setTimeout(function () {
                            $.ajax({
                                type: 'POST',
                                url: 'util/export-delete',
                                dataType: 'json',
                                data: {data: data.url},
                                global: false,
                                async: false,
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }, 1000);


                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }


        });

        $("#exportOthers").click( function(){

            var element = $("#exportOthers");
            var icon = $("#exportOthersIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance',
                    dataType: 'json',
                    data: {data: JSON.stringify(data)},
                    global: false,
                    async: false,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;

                        setTimeout(function () {
                            $.ajax({
                                type: 'POST',
                                url: 'util/export-delete',
                                dataType: 'json',
                                data: {data: data.url},
                                global: false,
                                async: false,
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }, 1000);


                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }


        });

        $("#exportAll").click( function(){
            var element = $("#exportAll");
            var icon = $("#exportAllIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';                

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance-all/1',
                    dataType: 'json',
                    data: filters,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;
                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }

            // console.log('belum dibuat pak');


        });

        $("#exportAllSpv").click( function(){
            var element = $("#exportAllSpv");
            var icon = $("#exportAllSpvIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';                

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance-all/2',
                    dataType: 'json',
                    data: filters,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;
                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }

            // console.log('belum dibuat pak');


        });

        $("#exportAllDemo").click( function(){
            var element = $("#exportAllDemo");
            var icon = $("#exportAllDemoIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';                

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance-all/3',
                    dataType: 'json',
                    data: filters,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;
                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }

            // console.log('belum dibuat pak');


        });

        $("#exportAllOthers").click( function(){
            var element = $("#exportAllOthers");
            var icon = $("#exportAllOthersIcon");
            if (element.attr('disabled') != 'disabled') {
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';                

                $.ajax({
                    type: 'POST',
                    url: 'util/export-attendance-all/4',
                    dataType: 'json',
                    data: filters,
                    beforeSend: function()
                    {   
                        element.attr('disabled', 'disabled');
                        icon.attr('class', 'fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(data);

                        window.location = data.url;
                    },
                    error: function(xhr, textStatus, errorThrown){
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        console.log(errorThrown);
                       alert('Export request failed');
                    }
                });

            }

            // console.log('belum dibuat pak');


        });
    </script>
@endsection
