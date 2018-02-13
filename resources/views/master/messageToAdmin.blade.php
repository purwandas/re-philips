@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Message to Admin
            <small>manage Message to Admin</small>
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
        <span class="active">Message to Admin Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">Message to Admin</span>
                </div>
            </div>
                <div class="portlet-title">
                <!-- MAIN CONTENT -->
                @if(Auth::user()->role == 'Master' || Auth::user()->role == 'Admin')
                    <div class="actions" style="text-align: left">
                        <a id="export" class="btn green-dark" >
                            <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL </a>
                    </div>
                @else
                    <div class="btn-group">
                        <a id="add-messageToAdmin" class="btn green" data-toggle="modal" href="#messageToAdmin" ><i
                            class="fa fa-plus"></i> Create Message </a>

                    </div>
                @endif
                </div>

                <div class="portlet-body" >
                    <table class="table table-striped table-hover table-bordered" id="messageToAdminTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> Date </th>  
                                <th> User Mail </th>  
                                <th> Subject </th>                           
                                <th> Body </th>
                                <th> Read By Admin </th>
                                <!-- <th> Options </th>                         -->
                            </tr>
                        </thead>
                    </table>                 

                </div>

                @include('partial.modal.messageToAdmin-modal')
                @include('partial.modal.messageToAdminShow-modal')

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
<script src="{{ asset('js/handler/messageToAdminNew-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/handler/messageToAdmin-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    var data = {};
    /*
     *
     *
     */
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/messagetoadmin',
            dataType: 'json',
            global: false,
            async: false,
            success: function (results) {
                data = results;
            }
        });

        // Set data for Data Table
        var table = $('#messageToAdminTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.messageToAdmin') }}",
                type: 'POST',
            },
            
            "rowId": "id",
            "columns": [
                {data: 'created_at', name: 'created_at', width: 80},
                {data: 'user', name: 'user', width: 100},
                {data: 'subject', name: 'subject', width: 100},
                {data: 'message', name: 'message', width: 500},
                {
                    data:   'status',
                    render: function ( data, type, row ) {
                        if ( type === 'display' ) {
                            return '<input type="checkbox" class="table-active">';
                        }
                        return data;
                    },
                    className: "dt-body-center", width: 10
                },
                // {data: 'action', name: 'action', searchable: false, sortable: false, width: 100},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [1]},
                {"className": "dt-center", "targets": [4]},

            ],
            "order": [ [0, 'desc'] ],
            "scroll": true,


            createdRow: function (row, data, dataIndex) {
                // set the data-status atribut and add a class
                $(row)
                    .attr('data-id',data.id)
                    .attr('data-toggle','modal')
                    .attr('data-target','#messageToAdminShow')
                    .attr('style','crusor:pointer')
                    .addClass('clickable-row');

            },

            rowCallback: function ( row, data ) {
                // Set the checked state of the checkbox in the table
                $('input.table-active', row).prop( 'checked', data.status == 'read' );
            },
        });

        // $('#messageToAdminTable tbody').on("click", 'tr', function () {

        // return response()->json(['url' => url('#messageToAdmin'), 'data-id'= data: 'id', .show-messageToAdmin]);        


        // });

 
        // Delete data with sweet alert
        // $('#messageToAdminTable').on('click', 'tr td button.deleteButton', function () {
        //     var id = $(this).val();

                // if(fanspageRelation(id)){
                //     swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                //     return;
                // }

        //         swal({
        //             title: "Are you sure?",
        //             text: "You will not be able to recover data!",
        //             type: "warning",
        //             showCancelButton: true,
        //             confirmButtonClass: "btn-danger",
        //             confirmButtonText: "Yes, delete it",
        //             cancelButtonText: "No, cancel",
        //             closeOnConfirm: false,
        //             closeOnCancel: false
        //         },
        //         function (isConfirm) {
        //             if (isConfirm) {
        //                 $.ajaxSetup({
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     }
        //                 })


        //                 $.ajax({

        //                     type: "DELETE",
        //                     url:  'messageToAdmin/' + id,
        //                     success: function (data) {
        //                         $("#"+id).remove();
        //                     },
        //                     error: function (data) {
        //                         console.log('Error:', data);
        //                     }
        //                 });

        //                 swal("Deleted!", "Data has been deleted.", "success");
        //             } else {
        //                 swal("Cancelled", "Data is safe ", "success");
        //             }
        //         });
        // });

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-messagetoadmin',
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


    });

    // Init add form
    $(document).on("click", "#add-messageToAdmin", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#user').val('');
        $('#subject').val('');
        $('#message').val('');

        // Set action url form for add
        var postDataUrl = "{{ url('messageToAdmin') }}";
        $("#form_MessageToAdmin").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    // $(document).on("click", ".edit-messageToAdmin", function () {

    //     resetValidation();

    //     var modalTitle = document.getElementById('title');
    //     modalTitle.innerHTML = "EDIT";

    //     var id = $(this).data('id');
    //     var getDataUrl = "{{ url('messageToAdmin/edit/') }}";
    //     var postDataUrl = "{{ url('messageToAdmin') }}"+"/"+id;

    //     // Set action url form for update
    //     $("#form_messageToAdmin").attr("action", postDataUrl);

    //     // Set Patch Method
    //     if(!$('input[name=_method]').length){
    //         $("#form_messageToAdmin").append("<input type='hidden' name='_method' value='PATCH'>");
    //     }

    //     $.get(getDataUrl + '/' + id, function (data) {

    //                 $('#subject').val(data.subject);
    //                 $('#message').val(data.message);

    //     })

    // });

    // For show data
    $(document).on("click", "#messageToAdminTable tbody tr", function () {
        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "SHOW";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('messageToAdmin/show/') }}";

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#usershow').val(data.user);
                    $('#subjectshow').val(data.subject);
                    $('#messageshow').val(data.message); 
        })

        // $('#messageToAdminTable').DataTable().search('').draw();
        $('#messageToAdminTable').DataTable().ajax.reload();
        $('#messageToAdminTable').DataTable().ajax.reload();

        $('#messageToAdminShow').modal('hide');
    });

</script>
@endsection
