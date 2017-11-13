@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Sell In Report
                <small>report sell in</small>
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
            <span class="active">Sell In Reporting</span>
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
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

                </div>

                <div class="portlet light bordered display-hide" id="dataContent">
                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">Sell In</span>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="sellInReport" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> Week </th>
                                <th> Distributor Code </th>
                                <th> Distributor Name </th>
                                <th> Region </th>
                                <th> Channel </th>
                                <th> Sub Channel </th>
                                <th> Area </th>
                                <th> District </th>
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
                                <th> Unit Price </th>
                                <th> Value </th>
                                <th> Value PF MR </th>
                                <th> Value PF TR </th>
                                <th> Value PF PPE </th>
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
        var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterEmployee'];
        var url = 'datatable/sellinreport';
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                            {data: 'week', name: 'week'},
                            {data: 'distributor_code', name: 'distributor_code'},
                            {data: 'distributor_name', name: 'distributor_name'},
                            {data: 'region', name: 'region'},
                            {data: 'channel', name: 'channel'},
                            {data: 'sub_channel', name: 'sub_channel'},
                            {data: 'area', name: 'area'},
                            {data: 'district', name: 'district'},
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
                            {data: 'quantity', name: 'quantity'},
                            {data: 'unit_price', name: 'unit_price'},
                            {data: 'value', name: 'value'},
                            {data: 'value_pf_mr', name: 'value_pf_mr'},
                            {data: 'value_pf_tr', name: 'value_pf_tr'},
                            {data: 'value_pf_ppe', name: 'value_pf_ppe'},
                            ];

        var paramFilter = ['sellInReport', $('#sellInReport'), url, tableColumns, columnDefs, order];
        var paramReset = [filterId, 'sellInReport', $('#sellInReport'), url, tableColumns, columnDefs, order];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Set data for Data Table
            {{--var table = $('#sellInReport').dataTable({--}}
                {{--"processing": true,--}}
                {{--"serverSide": true,--}}
                {{--"ajax": {--}}
                    {{--url: "{{ route('datatable.sellinreport') }}",--}}
                    {{--type: 'POST',--}}
                {{--},--}}
                {{--"rowId": "id",--}}
                {{--"columns": tableColumns,--}}
                {{--"columnDefs": columnDefs,--}}
                {{--"order": order,--}}
            {{--});--}}

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

        // On Change Search Date
		$(document).ready(function() {

            $('#filterMonth').change(function(){
				filters['searchMonth'] = this.value;
				console.log(filters);
            });

        });

        $("#resetButton").click( function(){

            // Hide Table Content
            $('#dataContent').addClass('display-hide');

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        });

        $("#filterButton").click( function(){

            // Set Table Content
            $('#dataContent').removeClass('display-hide');

        });


    </script>
@endsection
