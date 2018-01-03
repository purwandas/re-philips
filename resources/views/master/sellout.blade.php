@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Sell Out
            <small>manage sell out</small>
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
        <span class="active">Sell Out Management</span>
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
                    <i class="fa fa-share-alt font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">Sell Out</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <table class="table table-striped table-hover table-bordered" id="sellOutTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> User Name </th>
                                <th> User NIK </th>
                                <th> Store Name 1 </th>
                                <th> Store Name 2 </th>
                                <th> Store ID </th>
                                <th> Store Dedicate </th>
                                <th> Product </th>
                                <th> Quantity </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                    </table>

                </div>

                @include('partial.modal.editsellout-modal')

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
<script src="{{ asset('js/handler/editsellout-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>

    /*
     * ACCOUNT
     *
     */
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table
        var table = $('#sellOutTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.editsellout') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'user_name', name: 'user_name'},
                {data: 'user_nik', name: 'user_nik'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'store_id', name: 'store_id'},
                {data: 'dedicate', name: 'dedicate'},
                {data: 'product', name: 'product'},
                {data: 'quantity', name: 'quantity'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#sellOutTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'editsellout/' + id,
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


        initSelect2Account();

    });


    // For editing data
    $(document).on("click", ".edit-sellout", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('editsellout/edit/') }}";
        var postDataUrl = "{{ url('editsellout') }}"+"/"+id;

        // Set action url form for update
        $("#form_editsellout").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_editsellout").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#quantity').val(data.quantity);

                    // setSelect2IfPatchModal($("#globalchannel"), data.globalchannel_id, data.global_channel.name);

        })

    });

    function initSelect2Account(){

        /*
         * Select 2 init
         *
         */

         $('#globalchannel').select2(setOptions('{{ route("data.globalchannel") }}', 'Global Channel', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {
                    return {id: obj.id, text: obj.name}
                })
            }
        }));

    }


</script>
@endsection
