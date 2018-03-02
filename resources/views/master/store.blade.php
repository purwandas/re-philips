@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Store
            <small>manage store</small>
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
        <span class="active">Store Management</span>
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
                        <span class="caption-subject font-blue bold uppercase">FILTER STORE</span>
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
                        <select id="filterDistrict" class="select2select"></select>
                    </div>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterStore" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterGlobalChannel" class="select2select"></select>
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
                    <i class="fa fa-shopping-cart font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">STORE</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a class="btn green" href="{{ url('store/create') }}"><i
                        class="fa fa-plus"></i> Add Store </a>
                    
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

            <div class="portlet-body" >
                <table class="table table-striped table-hover table-bordered" id="storeTable" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th> No. </th>
                            <th> Store ID </th>
                            <th> Store Name 1 </th>
                            <th> Customer Code </th>
                            <th> Region </th>
                            <th> Area </th>
                            <th> District </th>
                            <th> Global Channel </th>
                            <th> Channel </th>
                            <th> Sub Channel</th>
                            <th> Distributor</th>
                            <th> Classification</th>
                            <th> Longitude</th>
                            <th> Latitude</th>
                            <th> Address</th>
                            <th> Phone Number</th>
                            <th> Owner Phone Number</th>
                            <th> Ownership Status</th>
                            <th> Location</th>
                            <th> Transaction Type</th>
                            <th> Transaction Type (2)</th>
                            <th> Condition</th>
                            <th> Supervisor </th>
                            <th> Options </th>                        
                        </tr>
                    </thead>
                </table>

                <!-- END MAIN CONTENT -->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    var dataAll = {};

    /*
     *
     *
     */
    var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterGlobalChannel'];
    var url = 'datatable/store';
    var order = [ [0, 'desc'] ];
    var columnDefs = [{"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [15]},];
    var tableColumns = [{data: 'id', name: 'id'},                
                {data: 'store_id', name: 'store_id'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'region_name', name: 'region_name'},
                {data: 'area_name', name: 'area_name'},
                {data: 'district_name', name: 'district_name'},
                {data: 'globalchannel_name', name: 'globalchannel_name'},
                {data: 'channel_name', name: 'channel_name'},
                {data: 'subchannel_name', name: 'subchannel_name'},
                {data: 'distributor', name: 'distributor'},
                {data: 'classification_id', name: 'classification_id'},
                {data: 'longitude', name: 'longitude'},
                {data: 'latitude', name: 'latitude'},
                {data: 'address', name: 'address'},
                {data: 'no_telp_toko', name: 'no_telp_toko'},
                {data: 'no_telp_pemilik_toko', name: 'no_telp_pemilik_toko'},
                {data: 'kepemilikan_toko', name: 'kepemilikan_toko'},
                {data: 'lokasi_toko', name: 'lokasi_toko'},
                {data: 'tipe_transaksi_2', name: 'tipe_transaksi_2'},
                {data: 'tipe_transaksi', name: 'tipe_transaksi'},
                {data: 'kondisi_toko', name: 'kondisi_toko'},
                {data: 'spv_name', name: 'spv_name'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
                        ];

    var exportButton = '#export';

    var paramFilter = ['storeTable', $('#storeTable'), url, tableColumns, columnDefs, order, exportButton];

    var paramReset = [filterId, 'storeTable', $('#storeTable'), url, tableColumns, columnDefs, order, exportButton];

    $(document).ready(function () {     

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/stores',
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

        // Set data for Data Table '#athletesTable'
        var table = $('#storeTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.store') }}",
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
            "columns": [
                {data: 'id', name: 'id'},                
                {data: 'store_id', name: 'store_id'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'region_name', name: 'region_name'},
                {data: 'area_name', name: 'area_name'},
                {data: 'district_name', name: 'district_name'},
                {data: 'globalchannel_name', name: 'globalchannel_name'},
                {data: 'channel_name', name: 'channel_name'},
                {data: 'subchannel_name', name: 'subchannel_name'},
                {data: 'distributor', name: 'distributor'},
                {data: 'classification_id', name: 'classification_id'},
                {data: 'longitude', name: 'longitude'},
                {data: 'latitude', name: 'latitude'},
                {data: 'address', name: 'address'},
                {data: 'no_telp_toko', name: 'no_telp_toko'},
                {data: 'no_telp_pemilik_toko', name: 'no_telp_pemilik_toko'},
                {data: 'kepemilikan_toko', name: 'kepemilikan_toko'},
                {data: 'lokasi_toko', name: 'lokasi_toko'},
                {data: 'tipe_transaksi_2', name: 'tipe_transaksi_2'},
                {data: 'tipe_transaksi', name: 'tipe_transaksi'},
                {data: 'kondisi_toko', name: 'kondisi_toko'},
                {data: 'spv_name', name: 'spv_name'},
                {data: 'action', name: 'action', searchable: false, sortable: false},                
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [15]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#storeTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                if(storeRelation(id)){
                    swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                    return;
                }
                
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it",
                    cancelButtonText: "No, cancel",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })


                        $.ajax({

                            type: "DELETE",
                            url:  'store/' + id,
                            success: function (data) {
                                console.log(data);

                                $("#"+id).remove();
                                // $('#sportsTable').DataTable().ajax.reload();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/stores',
                                    dataType: 'json',
                                    global: false,
                                    async: false,
                                    success: function (results) {
                                        var count = results.length;

                                                if(count > 0){
                                                    $('#exportAll').removeAttr('disabled');
                                                    $('#export').removeAttr('disabled');
                                                }else{
                                                    $('#exportAll').attr('disabled','disabled');
                                                    $('#export').removeAttr('disabled');
                                                }

                                        dataAll = results;
                                    }
                                });
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });                        

                        swal("Deleted!", "Data has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Data is safe ", "success");
                    }
                });
        });

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                $.ajax({
                    type: 'POST',
                    url: 'data/stores',
                    dataType: 'json',
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
                    url: 'util/export-store',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

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

            // console.log(dataAll);
            // console.log(data);
            // return;

            if ($('#exportAll').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-store-all',
                    dataType: 'json',
                    data: {data: dataAll},
                    global: false,
                    async: false,
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

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

        initSelect2();

    });

        function initSelect2(){

            /*
             * Select 2 init
             *
             */

             $('#filterGlobalChannel').select2(setOptions('{{ route("data.globalchannel") }}', 'Global Channel', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterGlobalChannel').on('select2:select', function () {
                self.selected('byGlobalChannel', $('#filterGlobalChannel').val());
            });

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



        }

</script>
@endsection
