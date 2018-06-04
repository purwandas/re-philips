@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Display Share Report
                <small>report display share</small>
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
            <span class="active">Display Share Reporting</span>
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
                    <div class="col-md-4">
                        <input type="text" id="filterDate" class="form-control" placeholder="Date">
                    </div>
                </div>

                <br>

                <div class="btn-group">
                    <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerResetReport(paramReset)">
                        <i class="fa fa-refresh"></i> Reset </a>
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">Display Share</span>
                        </div>
                        <div class="actions" style="text-align: left">
                            <a id="export" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                        </div>
                        <div class="actions" style="text-align: left; padding-right: 10px;">
                            <a id="exportAll" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="displayShareReport" style="white-space: nowrap;">
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
                                <th> Customer Code </th>
                                <th> Store ID </th>
                                <th> NIK </th>
                                <th> Promoter Name </th>
                                <th> Date </th>
                                
                                <th> Category </th>
                                <th> Philips </th>
                                <th> All </th>
                                <th> Percentage </th>
                                
                                <th> Role </th>
                                <th> SPV/ARO Name </th>
                                <th> DM Name </th>
                                <th> Trainer </th>
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
        var dataAll = {};
        /*
         *
         *
         */
        var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterEmployee'];
        var url = 'datatable/displaysharereport';
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
                            
                            {data: 'category', name: 'category'},
                            {data: 'philips', name: 'philips'},
                            {data: 'all', name: 'all'},
                            {data: 'percentage', name: 'percentage'},
                            
                            {data: 'role', name: 'role'},
                            {data: 'spv_name', name: 'spv_name'},
                            {data: 'dm_name', name: 'dm_name'},
                            {data: 'trainer_name', name: 'trainer_name'},
                            ];

        var exportButton = '#export';

        var paramFilter = ['displayShareReport', $('#displayShareReport'), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, 'displayShareReport', $('#displayShareReport'), url, tableColumns, columnDefs, order, exportButton, '#filterMonth', '#filterDate'];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Get data district to var data
            $.ajax({
                type: 'POST',
                url: 'data/displaysharereport',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            // if(count > 0){
                            //     $('#exportAll').removeAttr('disabled');
                            // }else{
                            //     $('#exportAll').attr('disabled','disabled');
                            // }

                    dataAll = results;
                }
            });

            initSelect2();
            initDateTimePicker();

            // Set data for Data Table
            var table = $('#displayShareReport').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.displaysharereport') }}",
                    type: 'POST',
                    data: filters,
                    dataType: 'json',
                    dataSrc: function (res) {
                        var count = res.data.length;

                        // if(count > 0){
                        //     $('#export').removeAttr('disabled');
                        // }else{
                        //     $('#export').attr('disabled','disabled');
                        // }

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

        function initDateTimePicker (){

            // Filter Month
            $('#filterMonth').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            // $('#filterMonth').val(moment().format('MMMM YYYY'));
            // filters['searchMonth'] = $('#filterMonth').val();
            
            // Filter Date
            $('#filterDate').datetimepicker({
                format: "yyyy-mm-dd",
                startView: "2",
                minView: "2",
                autoclose: true,
            });
            
            // Set to Date now
            $('#filterDate').val(moment().format('YYYY-MM-DD'));
            filters['searchDate'] = $('#filterDate').val();

        }

        // On Change Search Date
        $(document).ready(function() {

            $('#filterMonth').change(function(){
                filters['searchMonth'] = this.value;
                console.log(filters);
            });
            
            $('#filterDate').change(function(){
                filters['searchDate'] = this.value;
                console.log(filters);
                $('#filterMonth').val('');
                delete filters['searchMonth'];
            });

        });

        $("#resetButton").click( function(){

            // Hide Table Content
            // $('#dataContent').addClass('display-hide');

            // Set to Month now
            // $('#filterMonth').val(moment().format('MMMM YYYY'));
            // filters['searchMonth'] = $('#filterMonth').val();
            
             $('#filterDate').val(moment().format('YYYY-MM-DD'));
            filters['searchDate'] = $('#filterDate').val();
            $('#filterMonth').val('');
            delete filters['searchMonth'];

        });

        $("#filterButton").click( function(){

            // Set Table Content
            // $('#dataContent').removeClass('display-hide');

            $.ajax({
                type: 'POST',
                url: 'data/displaysharereport',
                dataType: 'json',
                data: filters,
                global: false,
                async: false,
                success: function (results) {
                    var count = results.length;

                            // if(count > 0){
                            //     $('#exportAll').removeAttr('disabled');
                            // }else{
                            //     $('#exportAll').attr('disabled','disabled');
                            // }

                    dataAll = results;
                }
            });

        });

        $("#export").click( function(){
            
            var element = $("#export");
            var icon = $("#exportIcon");

            if ($('#export').attr('disabled') != 'disabled') {
                
                var thisClass = icon.attr('class');

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-displayshare',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

                        // console.log(data);

                        // window.location = data.url;
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                        // setTimeout(function () {
                        //     $.ajax({
                        //         type: 'POST',
                        //         url: 'util/export-delete',
                        //         dataType: 'json',
                        //         data: {data: data.url},
                        //         global: false,
                        //         async: false,
                        //         success: function (data) {
                        //             console.log(data);
                        //         }
                        //     });
                        // }, 1000);


                    }
                });

            }


        });

        $("#exportAll").click( function(){
            
            var element = $("#exportAll");
            var icon = $("#exportAllIcon");

            if ($('#export').attr('disabled') != 'disabled') {
                
                var thisClass = icon.attr('class');

                $.ajax({
                    type: 'POST',
                    url: 'data/displaysharereport',
                    dataType: 'json',
                    data: filters,
                    global: false,
                    async: false,
                    success: function (results) {
                        dataAll = results;
                    }
                });

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-displayshare-all',
                    dataType: 'json',
                    data: filters,
                    global: false,
                    async: false,
                    success: function (data) {

                        // console.log(data);

                        // window.location = data.url;
                        element.removeAttr('disabled');
                        icon.attr('class', thisClass);
                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                        // setTimeout(function () {
                        //     $.ajax({
                        //         type: 'POST',
                        //         url: 'util/export-delete',
                        //         dataType: 'json',
                        //         data: {data: data.url},
                        //         global: false,
                        //         async: false,
                        //         success: function (data) {
                        //             console.log(data);
                        //         }
                        //     });
                        // }, 1000);


                    }
                });

            }


        });


    </script>
@endsection
