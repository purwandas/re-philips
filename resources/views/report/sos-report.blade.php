@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>SOS Report
                <small>report share on space</small>
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
            <span class="active">SOS Reporting</span>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-map-o font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER REPORT</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterRegion" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterArea" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterAreaApp" class="select2select"></select>
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

                <br>

                <div class="caption padding-caption">
                    <span class="caption-subject font-black bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DATE</span>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <div class="mt-radio-inline" >
                            <label class="mt-radio">
                                <input type="radio" name="date_type" id="radioDate" value="Date" checked="checked"> Date
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="date_type" value="DateRange"> Range of Date
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="date_type" value="Month"> Month
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="date_type" value="MonthRange"> Range of Month
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row filter" id="dateContent">
                    <div class="col-md-4">
                        <input type="text" id="filterDate" class="form-control" placeholder="Search by Date">
                    </div>
                </div>

                <div class="row filter display-hide" id="dateRangeContent">
                    <div class="col-md-4">
                        <input type="text" id="filterDateRange1" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="filterDateRange2" class="form-control" placeholder="End Date">
                    </div>
                </div>

                <div class="row filter display-hide" id="monthContent">
                    <div class="col-md-4">
                        <input type="text" id="filterMonth" class="form-control" placeholder="Search by Month">
                    </div>
                </div>

                <div class="row filter display-hide" id="monthRangeContent">
                    <div class="col-md-4">
                        <input type="text" id="filterMonthRange1" class="form-control" placeholder="Start Month">
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="filterMonthRange2" class="form-control" placeholder="End Month">
                    </div>
                </div>

                <br>

                <div class="btn-group">
                    <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerReset(paramReset)">
                        <i class="fa fa-refresh"></i> Reset </a>
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

                </div>

                <div class="portlet light bordered" id="dataContent">
                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">SOS</span>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="sosReport" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> Area </th>
                                <th> Store Name 1 </th>
                                <th> Store Name 2 </th>
                                <th> Store ID </th>
                                <th> NIK </th>
                                <th> Promoter Name </th>
                                <th> Date </th>
                                <th> Model </th>
                                <th> Group </th>
                                <th> Category </th>
                                <th> Product Name </th>
                                <th> Quantity </th>
                            </tr>
                            </thead>
                        </table>

                    </div>

                <!-- END MAIN CONTENT -->

            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@endsection

@section('additional-scripts')

    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->

    <script>
        /*
         *
         *
         */
        var filterId = ['#filterRegion', '#filterArea', '#filterAreaApp', '#filterStore', '#filterEmployee'];
        var url = 'datatable/sosreport';
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [{data: 'id', name: 'id'},
                            {data: 'area', name: 'area'},
                            {data: 'store_name_1', name: 'store_name_1'},
                            {data: 'store_name_2', name: 'store_name_2'},
                            {data: 'store_id', name: 'store_id'},
                            {data: 'nik', name: 'nik'},
                            {data: 'promoter_name', name: 'promoter_name'},
                            {data: 'date', name: 'date'},
                            {data: 'model', name: 'model'},
                            {data: 'group', name: 'group'},
                            {data: 'category', name: 'category'},
                            {data: 'product_name', name: 'product_name'},
                            {data: 'quantity', name: 'quantity'}];

        var paramFilter = ['sosReport', $('#sosReport'), url, tableColumns, columnDefs, order];
        var paramReset = [filterId, 'sosReport', $('#sosReport'), url, tableColumns, columnDefs, order];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Set data for Data Table
            var table = $('#sosReport').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.sosreport') }}",
                    type: 'POST',
                },
                "rowId": "id",
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });

            initSelect2();
            initDateTimePicker();

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

            $('#filterAreaApp').select2(setOptions('{{ route("data.areaapp") }}', 'Area RE App', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterAreaApp').on('select2:select', function () {
                self.selected('byAreaApp', $('#filterAreaApp').val());
            });

            $('#filterStore').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
                return filterData('store', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
	                })
                }
            }));
            $('#filterStore').on('select2:select', function () {
                self.selected('byStore', $('#filterStore').val());
            });

            $('#filterEmployee').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {
	        	filters['roleGroup'] = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];
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

        function initDateTimePicker (){

            // Filter Date
            $('#filterDate').datetimepicker({
                format: "dd MM yyyy",
                startView: "2",
                minView: "2",
                autoclose: true,
            });

            // Filter Date Range
            $('#filterDateRange1').datetimepicker({
                format: "dd MM yyyy",
                startView: "2",
                minView: "2",
                autoclose: true,
            });

            $('#filterDateRange2').datetimepicker({
                format: "dd MM yyyy",
                startView: "2",
                minView: "2",
                autoclose: true,
            });

            // Filter Month
            $('#filterMonth').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Filter Month Range
            $('#filterMonthRange1').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            $('#filterMonthRange2').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

        }

        function hideShowSearch(param){
            $('#dateContent').addClass('display-hide');
            $('#dateRangeContent').addClass('display-hide');
            $('#monthContent').addClass('display-hide');
            $('#monthRangeContent').addClass('display-hide');

            $('#filterDate').val("");
            $('#filterDateRange1').val("");
            $('#filterDateRange2').val("");
            $('#filterMonth').val("");
            $('#filterMonthRange1').val("");
            $('#filterMonthRange2').val("");

            var keys = ['searchDate', 'searchDateRange', 'searchMonth', 'searchMonthRange'];
            keys.forEach(function(key) {
                if (key in filters) {
                    delete filters[key];
                }
            });

            filters['searchDateRange'] = [];
            filters['searchMonthRange'] = [];

            if(param == 1){
                $('#dateContent').removeClass('display-hide');
            }else if(param == 2){
                $('#dateRangeContent').removeClass('display-hide');
            }else if(param == 3){
                $('#monthContent').removeClass('display-hide');
            }else if(param == 4){
                $('#monthRangeContent').removeClass('display-hide');
            }
        }

        // On Change Search Date
		$(document).ready(function() {
		    $('input[type=radio][name=date_type]').change(function() {
                if(this.value == 'Date'){
                    hideShowSearch(1);
                }else if(this.value == 'DateRange'){
                    hideShowSearch(2);
                }else if(this.value == 'Month'){
                    hideShowSearch(3);
                }else if(this.value == 'MonthRange'){
                    hideShowSearch(4);
                }
		    });

		    $('#filterDate').change(function(){
		        var keys = ['searchDateRange', 'searchMonth', 'searchMonthRange'];
                keys.forEach(function(key) {
                    if(key in filters){
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchDateRange'] = [];
                        filters['searchMonthRange'] = [];
                    }
                });
				filters['searchDate'] = this.value;
				console.log(filters);
            });

		    $('#filterDateRange1').change(function(){
		        var keys = ['searchDate', 'searchMonth', 'searchMonthRange'];
                keys.forEach(function(key) {
                    if (key in filters) {
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchMonthRange'] = [];
                    }
                });
				filters.searchDateRange[0] = this.value;
				console.log(filters);
            });

		    $('#filterDateRange2').change(function(){
		        var keys = ['searchDate', 'searchMonth', 'searchMonthRange'];
                keys.forEach(function(key) {
                    if (key in filters) {
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchMonthRange'] = [];
                    }
                });
				filters.searchDateRange[1] = this.value;
				console.log(filters);
            });

		    $('#filterMonth').change(function(){
		        var keys = ['searchDate', 'searchDateRange', 'searchMonthRange'];
                keys.forEach(function(key) {
                    if(key in filters){
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchDateRange'] = [];
                        filters['searchMonthRange'] = [];
                    }
                });
				filters['searchMonth'] = this.value;
				console.log(filters);
            });

		    $('#filterMonthRange1').change(function(){
		        var keys = ['searchDate', 'searchDateRange', 'searchMonth'];
                keys.forEach(function(key) {
                    if (key in filters) {
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchDateRange'] = [];
                    }
                });
				filters.searchMonthRange[0] = this.value;
				console.log(filters);
            });

		    $('#filterMonthRange2').change(function(){
		        var keys = ['searchDate', 'searchDateRange', 'searchMonth'];
                keys.forEach(function(key) {
                    if (key in filters) {
                        delete filters[key];
                    }

                    if(key == 'searchDateRange' || key == 'searchMonthRange'){
                        filters['searchDateRange'] = [];
                    }
                });
				filters.searchMonthRange[1] = this.value;
				console.log(filters);
            });
		});

        $("#resetButton").click( function(){

             // Set Date Search Content
            hideShowSearch(1);
            $('#radioDate').prop('checked', true);

        });

        $("#filterButton").click( function(){

             // Set Table Content
            $('#dataContent').removeClass('display-hide');

        });


    </script>
@endsection
