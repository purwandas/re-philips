@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Place
            <small>manage place</small>
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
        <span class="active">Place Management</span>
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
                    <i class="fa fa-building-o font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">PLACE</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-place" class="btn green" data-toggle="modal" href="#place"><i
                        class="fa fa-plus"></i> Add Place </a>

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
                    <table class="table table-striped table-hover table-bordered" id="placeTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>        
                                <th> Place ID. </th>                          
                                <th> Place Name </th>
                                <th> Longitude </th>
                                <th> Latitude </th>
                                <th> Address </th>
                                <th> Description </th>
                                <th> Options </th>                        
                            </tr>
                        </thead>
                    </table>                 

            </div>

            @include('partial.modal.place-modal')

            <!-- END MAIN CONTENT -->
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/place-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>

    var dataAll = {};
    /*
     * PLACE
     *
     */
    $(document).ready(function () {                

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function test(){
            // alert('sssss');
        }

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/placeC',
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

        // Set data for Data Table
        var table = $('#placeTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.place') }}",
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
                {data: 'name', name: 'name'},
                {data: 'longitude', name: 'longitude'},
                {data: 'latitude', name: 'latitude'},
                {data: 'address', name: 'address'},
                {data: 'description', name: 'description'},
                {data: 'action', name: 'action', searchable: false, sortable: false},           
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [7]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#placeTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'place/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/placeC',
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
                                                    $('#export').attr('disabled','disabled');
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

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-place',
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

            if ($('#exportAll').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                if(dataAll.length > 0){

                    $.ajax({
                        type: 'POST',
                        url: 'util/export-place-all',
                        dataType: 'json',
                        success: function (data) {

                            console.log(data);

                            window.location = data.url;

                        }
                    });

                }

            }


        });

    });

    // Init add form
    $(document).on("click", "#add-place", function () {       
        
        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#store_id').val('');
        $('#name').val('');
        $('#longitude').val('');
        $('#latitude').val('');
        $('#address').val('');
        $('#description').val('');

        // Set action url form for add
        var postDataUrl = "{{ url('place') }}";    
        $("#form_place").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-place", function () {

        resetValidation();       
        
        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT ";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('place/edit/') }}";
        var postDataUrl = "{{ url('place') }}"+"/"+id;        

        // Set action url form for update        
        $("#form_place").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_place").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#store_id').val(data.store_id);
                    $('#name').val(data.name);
                    $('#longitude').val(data.longitude);
                    $('#latitude').val(data.latitude);
                    $('#address').val(data.address);
                    $('#description').val(data.description);

        })

    });

</script>
@endsection
