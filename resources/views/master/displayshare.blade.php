@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Display Share
            <small>manage display share</small>
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
        <span class="active">Display Share Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">DISPLAY SHARE</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <table class="table table-striped table-hover table-bordered" id="displayShareTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> User Name </th>
                                <th> User NIK </th>
                                <th> Store Name 1 </th>
                                <th> Store Name 2 </th>
                                <th> Store ID </th>
                                <th> Category Product </th>
                                <th> Philips </th>
                                <th> All </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                    </table>

                </div>

                @include('partial.modal.editdisplayshare-modal')

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
<script src="{{ asset('js/handler/editdisplayshare-handler.js') }}" type="text/javascript"></script>
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
        var table = $('#displayShareTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.editdisplayshare') }}",
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
                {data: 'category', name: 'category'},
                {data: 'philips', name: 'philips'},
                {data: 'all', name: 'all'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#displayShareTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'editdisplayshare/' + id,
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

    });


    // For editing data
    $(document).on("click", ".edit-displayshare", function () {
        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('editdisplayshare/edit/') }}";
        var postDataUrl = "{{ url('editdisplayshare') }}"+"/"+id;

        // Set action url form for update
        $("#form_editdisplayshare").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_editdisplayshare").append("<input type='hidden' name='_method' value='PATCH'>");
        }
        $.get(getDataUrl + '/' + id, function (data) {
                    $('#philips').val(data.philips);
                    $('#all').val(data.all);

        })

    });


</script>
@endsection
