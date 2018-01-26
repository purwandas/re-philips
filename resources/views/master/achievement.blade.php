@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Achievement Report
                <small>report achievement</small>
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
            <span class="active">Achievement Report Reporting</span>
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
                            <span class="caption-subject font-blue bold uppercase">Achievement Report</span>
                        </div>
                        <div class="actions" style="text-align: left">
                            <a id="export" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL ALT </a>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="achievementReport" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> Region </th>
                                <th> Area </th>
                                <th> District </th>
                                <th> NIK </th>
                                <th> Promoter Name </th>
                                <th> Account Type </th>
                                <th> Title Of Promoter </th>
                                <th> Classification Store </th>
                                <th> Account </th>
                                <th> Store ID </th>
                                <th> Store Name 1 </th>
                                <th> Customer Code </th>
                                <th> SPV Name </th>
                                <th> Trainer </th>
                                <th> Sell Type </th>

                                <th> Target DAPC </th>
                                <th> Actual DAPC </th>
                                <th> Target DA </th>
                                <th> Actual DA </th>
                                <th> Target PC </th>
                                <th> Actual PC </th>
                                <th> Target MCC </th>
                                <th> Actual MCC </th>
                                <th> Target PF DA </th>
                                <th> Actual PF DA </th>
                                <th> Target PF PC </th>
                                <th> Actual PF PC </th>
                                <th> Target PF MCC </th>
                                <th> Actual PF MCC </th>

                                <th> Target DA Week 1 </th>
                                <th> Actual DA Week 1</th>
                                <th> Target DA Week 2 </th>
                                <th> Actual DA Week 2 </th>
                                <th> Target DA Week 3 </th>
                                <th> Actual DA Week 3 </th>
                                <th> Target DA Week 4 </th>
                                <th> Actual DA Week 4 </th>
                                <th> Target DA Week 5 </th>
                                <th> Actual DA Week 5 </th>
                                <th> Target PC Week 1 </th>
                                <th> Actual PC Week 1 </th>
                                <th> Target PC Week 2 </th>
                                <th> Actual PC Week 2 </th>
                                <th> Target PC Week 3 </th>
                                <th> Actual PC Week 3 </th>
                                <th> Target PC Week 4 </th>
                                <th> Actual PC Week 4 </th>
                                <th> Target PC Week 5 </th>
                                <th> Actual PC Week 5 </th>
                                <th> Target MCC Week 1 </th>
                                <th> Actual MCC Week 1 </th>
                                <th> Target MCC Week 2 </th>
                                <th> Actual MCC Week 2 </th>
                                <th> Target MCC Week 3 </th>
                                <th> Actual MCC Week 3 </th>
                                <th> Target MCC Week 4 </th>
                                <th> Actual MCC Week 4 </th>
                                <th> Target MCC Week 5 </th>
                                <th> Actual MCC Week 5 </th>

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
    <script src="{{ asset('js/handler/datetimepicker-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->

    <script>
        /*
         *
         *
         */
        var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterEmployee'];
        var url = 'datatable/achievementreport';
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                            {data: 'region', name: 'region'},
                            {data: 'area', name: 'area'},
                            {data: 'district', name: 'district'}, 
                            {data: 'nik', name: 'nik'},
                            {data: 'promoter_name', name: 'promoter_name'},
                            {data: 'account_type', name: 'account_type'}, 
                            {data: 'title_of_promoter', name: 'title_of_promoter'}, 
                            {data: 'classification_store', name: 'classification_store'}, 
                            {data: 'account', name: 'account'}, 
                            {data: 'store_id', name: 'store_id'}, 
                            {data: 'store_name_1', name: 'store_name_1'}, 
                            {data: 'store_name_2', name: 'store_name_2'}, 
                            {data: 'spv_name', name: 'spv_name'}, 
                            {data: 'trainer', name: 'trainer'}, 
                            {data: 'sell_type', name: 'sell_type'}, 
                            {data: 'target_dapc', name: 'target_dapc'}, 
                            {data: 'actual_dapc', name: 'actual_dapc'}, 
                            {data: 'target_da', name: 'target_da'}, 
                            {data: 'actual_da', name: 'actual_da'}, 
                            {data: 'target_pc', name: 'target_pc'}, 
                            {data: 'actual_pc', name: 'actual_pc'}, 
                            {data: 'target_mcc', name: 'target_mcc'}, 
                            {data: 'actual_mcc', name: 'actual_mcc'}, 
                            {data: 'target_pf_da', name: 'target_pf_da'}, 
                            {data: 'actual_pf_da', name: 'actual_pf_da'}, 
                            {data: 'target_pf_pc', name: 'target_pf_pc'}, 
                            {data: 'actual_pf_pc', name: 'actual_pf_pc'}, 
                            {data: 'target_pf_mcc', name: 'target_pf_mcc'}, 
                            {data: 'actual_pf_mcc', name: 'actual_pf_mcc'}, 
                            {data: 'target_da_w1', name: 'target_da_w1'}, 
                            {data: 'actual_da_w1', name: 'actual_da_w1'}, 
                            {data: 'target_da_w2', name: 'target_da_w2'}, 
                            {data: 'actual_da_w2', name: 'actual_da_w2'}, 
                            {data: 'target_da_w3', name: 'target_da_w3'}, 
                            {data: 'actual_da_w3', name: 'actual_da_w3'}, 
                            {data: 'target_da_w4', name: 'target_da_w4'}, 
                            {data: 'actual_da_w4', name: 'actual_da_w4'}, 
                            {data: 'target_da_w5', name: 'target_da_w5'}, 
                            {data: 'actual_da_w5', name: 'actual_da_w5'}, 
                            {data: 'target_pc_w1', name: 'target_pc_w1'}, 
                            {data: 'actual_pc_w1', name: 'actual_pc_w1'}, 
                            {data: 'target_pc_w2', name: 'target_pc_w2'}, 
                            {data: 'actual_pc_w2', name: 'actual_pc_w2'}, 
                            {data: 'target_pc_w3', name: 'target_pc_w3'}, 
                            {data: 'actual_pc_w3', name: 'actual_pc_w3'}, 
                            {data: 'target_pc_w4', name: 'target_pc_w4'}, 
                            {data: 'actual_pc_w4', name: 'actual_pc_w4'}, 
                            {data: 'target_pc_w5', name: 'target_pc_w5'}, 
                            {data: 'actual_pc_w5', name: 'actual_pc_w5'}, 
                            {data: 'target_mcc_w1', name: 'target_mcc_w1'}, 
                            {data: 'actual_mcc_w1', name: 'actual_mcc_w1'}, 
                            {data: 'target_mcc_w2', name: 'target_mcc_w2'}, 
                            {data: 'actual_mcc_w2', name: 'actual_mcc_w2'}, 
                            {data: 'target_mcc_w3', name: 'target_mcc_w3'}, 
                            {data: 'actual_mcc_w3', name: 'actual_mcc_w3'}, 
                            {data: 'target_mcc_w4', name: 'target_mcc_w4'}, 
                            {data: 'actual_mcc_w4', name: 'actual_mcc_w4'}, 
                            {data: 'target_mcc_w5', name: 'target_mcc_w5'}, 
                            {data: 'actual_mcc_w5', name: 'actual_mcc_w5'},
                            ];


        var exportButton = '#export';

        var paramFilter = ['achievementReport', $('#achievementReport'), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, 'achievementReport', $('#achievementReport'), url, tableColumns, columnDefs, order];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Set data for Data Table
            {{--var table = $('#achievementReport').dataTable({--}}
                {{--"processing": true,--}}
                {{--"serverSide": true,--}}
                {{--"ajax": {--}}
                    {{--url: "{{ route('datatable.achievementreport') }}",--}}
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
                        return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"+ " - " + obj.dedicate}
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

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-achievement',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

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


                    }
                });

            }


        });


    </script>
@endsection
