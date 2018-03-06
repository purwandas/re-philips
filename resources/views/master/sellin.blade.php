@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Sell Thru
            <small>manage sell thru</small>
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
        <span class="active">Sell Thru Management</span>
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
                    <i class="fa fa-share-alt font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">Sell Thru</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <table class="table table-striped table-hover table-bordered" id="sellInTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Date </th>
                                <th> User Name </th>
                                <th> User NIK </th>
                                <th> Store Name 1 </th>
                                <th> Customer Code </th>
                                <th> Store ID </th>
                                <th> Product </th>
                                <th> Quantity </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                    </table>

                </div>

                @include('partial.modal.editsellin-modal')

                <!-- END MAIN CONTENT -->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN SELECT2 SCRIPTS -->
<script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
<!-- END SELECT2 SCRIPTS -->
<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/editsellin-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    /*
     * ACCOUNT
     *
     */

    var filterId = ['#filterRegion', '#filterArea', '#filterDistrict', '#filterStore', '#filterEmployee'];
    var url = 'datatable/editsellin';
    var order = [ [0, 'desc'] ];
    var columnDefs = [{"className": "dt-center", "targets": [0]}, {"className": "dt-center", "targets": [8]}];
    var tableColumns = [
                        {data: 'id', name: 'id'},
                        {data: 'date', name: 'date'},
                        {data: 'user_name', name: 'user_name'},
                        {data: 'user_nik', name: 'user_nik'},
                        {data: 'store_name_1', name: 'store_name_1'},
                        {data: 'store_name_2', name: 'store_name_2'},
                        {data: 'store_id', name: 'store_id'},
                        {data: 'product', name: 'product'},
                        {data: 'quantity', name: 'quantity'},
                        {data: 'action', name: 'action', searchable: false, sortable: false},
                        ];

    // var exportButton = '#export';

    var paramFilter = ['sellInTable', $('#sellInTable'), url, tableColumns, columnDefs, order];//, exportButton];

    var paramReset = [filterId, 'sellInTable', $('#sellInTable'), url, tableColumns, columnDefs, order];

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });        

        // Set data for Data Table
        var table = $('#sellInTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.editsellin') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                        {data: 'date', name: 'date'},
                        {data: 'user_name', name: 'user_name'},
                        {data: 'user_nik', name: 'user_nik'},
                        {data: 'store_name_1', name: 'store_name_1'},
                        {data: 'store_name_2', name: 'store_name_2'},
                        {data: 'store_id', name: 'store_id'},
                        {data: 'product', name: 'product'},
                        {data: 'quantity', name: 'quantity'},
                        {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [8]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#sellInTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

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
                            url:  'editsellin/' + id,
                            success: function (data) {
                                $("#"+id).remove();
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

        initSelect2();
        initDateTimePicker();

    });


    // For editing data
    $(document).on("click", ".edit-sellin", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('editsellin/edit/') }}";
        var postDataUrl = "{{ url('editsellin') }}"+"/"+id;

        // Set action url form for update
        $("#form_editsellin").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_editsellin").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#quantity').val(data.quantity);

        })

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
            // $('#dataContent').addClass('display-hide');

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        });

        $("#filterButton").click( function(){

            // Set Table Content
            // $('#dataContent').removeClass('display-hide');

        });


</script>
@endsection
