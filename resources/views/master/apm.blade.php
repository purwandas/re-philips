@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>APM Report & Management
                <small>report & management apm</small>
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
            <span class="active">APM Report & Management</span>
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
                        <i class="fa fa-cog font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER DATA</span>
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
                        <select id="filterProduct" class="select2select"></select>
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

                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-area-chart font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">APM</span>
                        </div>

                    </div>
                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="btn-group">
                            <a id="set-apmmonth" class="btn green" data-toggle="modal" href="#apmmonth"><i
                                class="fa fa-cog"></i> Set APM Month </a>

                        </div>
                        <div class="btn-group">
                            <a id="upload" class="btn btn-primary" data-toggle="modal" href="#upload-price"><i
                                class="fa fa-cloud-upload"></i> Update APM </a>

                        </div>
                        <div class="actions" style="text-align: left; padding-right: 10px;">
                            <a id="export" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                        </div>
                        <div class="actions" style="text-align: left; padding-right: 10px;">
                            <a id="exportAll" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="apmTable" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> Global Channel </th>
                                <th> Channel </th>
                                <th> Sub Channel </th>
                                <th> Region </th>
                                <th> Area </th>
                                <th> District </th>
                                <th> RE Store ID </th>
                                <th> Store Name </th>
                                <th> Product </th>
                                <th> 
                                    @if($apmMonth->first()->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[0] }}
                                </th>
                                <th>
                                    @if($apmMonth->get(1)->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[1] }}
                                </th>
                                <th>
                                    @if($apmMonth->get(2)->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[2] }}
                                </th>
                                <th>
                                    @if($apmMonth->get(3)->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[3] }}
                                </th>
                                <th> 
                                    @if($apmMonth->get(4)->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[4] }}
                                </th>
                                <th>
                                    @if($apmMonth->get(5)->selected == 1)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    @endif
                                    SO Value {{ $arMonth[5] }}
                                </th>
                            </tr>
                            </thead>
                        </table>

                    </div>

                <!-- END MAIN CONTENT -->

            </div>
            <!-- END EXAMPLE TABLE PORTLET-->

            @include('partial.modal.apm-modal')
            @include('partial.modal.upload-apm-modal')
        </div>
    </div>
@endsection

@section('additional-scripts')

    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/handler/datetimepicker-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
    <!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/apm-handler.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/upload-modal/upload-apm-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->

    <script>

        var dataAll = {};
        /*
         *
         *
         */
        var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterProduct'];
        var url = 'datatable/apm';
        var order = [ [0, 'desc'] ];
        var columnDefs = [
                            {"className": "dt-center", "targets": [0]},
                            {"className": "dt-center", "targets": [1]},
                            {"className": "dt-center", "targets": [2]},
                            {"className": "dt-center", "targets": [3]},
                            {"className": "dt-center", "targets": [10]},
                            {"className": "dt-center", "targets": [11]},
                            {"className": "dt-center", "targets": [12]},
                            {"className": "dt-center", "targets": [13]},
                            {"className": "dt-center", "targets": [14]},
                            {"className": "dt-center", "targets": [15]},
                         ];
        var tableColumns = [{data: 'id', name: 'id', orderable: false},
                            {data: 'global_channel', name: 'global_channel'},
                            {data: 'channel', name: 'channel'},
                            {data: 'sub_channel', name: 'sub_channel'},
                            {data: 'region', name: 'region'},
                            {data: 'area', name: 'area'},
                            {data: 'district', name: 'district'},
                            {data: 're_store_id', name: 're_store_id'},
                            {data: 'store_name', name: 'store_name'},
                            {data: 'product_name', name: 'product_name'},
                            {data: 'month_minus_1_value', name: 'month_minus_1_value'},
                            {data: 'month_minus_2_value', name: 'month_minus_2_value'},
                            {data: 'month_minus_3_value', name: 'month_minus_3_value'},
                            {data: 'month_minus_4_value', name: 'month_minus_4_value'},
                            {data: 'month_minus_5_value', name: 'month_minus_5_value'},
                            {data: 'month_minus_6_value', name: 'month_minus_6_value'},
                            ];

        var exportButton = '#export';

        var paramFilter = ['apmTable', $('#apmTable'), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, 'apmTable', $('#apmTable'), url, tableColumns, columnDefs, order, exportButton];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Get data district to var data
            $.ajax({
                type: 'POST',
                url: 'data/apmC',
                dataType: 'json',
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            if(count > 0){
                                $('#exportAll').removeAttr('disabled');
                            }else{
                                $('#exportAll').attr('disabled','disabled');
                            }

                    dataAll = results;
                }
            });

            initSelect2();

            // console.log(filters);

            // Set data for Data Table
            var table = $('#apmTable').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.apm') }}",
                    data: filters,
                    dataType: 'json',
                    type: 'POST',
                    dataSrc: function (res) {
                        var count = res.data.length;

                        if(count > 0){
                            $('#export').removeAttr('disabled');
                        }else{
                            $('#export').attr('disabled','disabled');
                        }

                        this.data = res.data;
                        return res.data;
                    },
                },
                "rowId": "id",
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });

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

            $('#filterProduct').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterProduct').on('select2:select', function () {
                self.selected('byProduct', $('#filterProduct').val());
            });

        }


        // On Change Search Date
        $(document).ready(function() {


        });


        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-apm',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                    }
                });

            }


        });

        $("#exportAll").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-apm-all',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                    }
                });

            }


        });

        $("#exportTemplate").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-apm-template',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                    }
                });

            }


        });

        // Init add form
        $(document).on("click", "#set-apmmonth", function () { 

            // $('#month1').attr('checked', 'checked'); 
            // $('#month1').removeAttr('checked'); 
            // document.getElementById("month1").checked = false;

            // $('#month1').prop("checked", true);

            return;

            var m1 = "{{ $apmMonth->first()->selected }}";
            var m2 = "{{ $apmMonth->get(1)->selected }}";
            var m3 = "{{ $apmMonth->get(2)->selected }}";
            var m4 = "{{ $apmMonth->get(3)->selected }}";
            var m5 = "{{ $apmMonth->get(4)->selected }}";
            var m6 = "{{ $apmMonth->get(5)->selected }}";   

            alert(document.getElementById("month1").innerHTML);  
        
            if(m1 == 1) document.getElementById("month1").checked = true;
            if(m2 == 1) document.getElementById("month2").checked = true;
            if(m3 == 1) document.getElementById("month3").checked = true;
            if(m4 == 1) document.getElementById("month4").checked = true;
            if(m5 == 1) document.getElementById("month5").checked = true;
            if(m6 == 1) document.getElementById("month6").checked = true;

        });


    </script>
@endsection
