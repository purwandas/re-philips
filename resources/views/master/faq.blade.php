@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Frequently Asked Questions (FAQ)
            <small>manage FAQ</small>
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
        <span class="active">FAQ Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">Frequently Asked Questions</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group">
                                        <a class="btn green" href="{{ url('faq/create') }}"><i
                                    class="fa fa-plus"></i> Add New </a>

                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped table-hover table-bordered" id="faqTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>                            
                                <th> Question </th>                           
                                <th> Answer </th>
                                <th> Action </th>                        
                            </tr>
                        </thead>
                    </table>                 

                </div>

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
<script src="{{ asset('js/handler/faq-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    /*
     * FAQ
     *
     */
    $(document).ready(function () {                

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table
        var table = $('#faqTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.faq') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'question', name: 'question'},                
                {data: 'answer', name: 'answer'},
                {data: 'action', name: 'action', searchable: false, sortable: false},           
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#faqTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'faq/' + id,
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

    // Init add form
    $(document).on("click", "#add-faq", function () {       
        
        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#question').val('');
        $('#answer').val('');

        // Set action url form for add
        var postDataUrl = "{{ url('faq') }}";    
        $("#form_faq").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-faq", function () {

        resetValidation();       
        
        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('faq/edit/') }}";
        var postDataUrl = "{{ url('faq') }}"+"/"+id;        

        // Set action url form for update        
        $("#form_faq").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length) {
            $("#form_faq").append("<input type='hidden' name='_method' value='PATCH'>");
        }

    });

</script>
@endsection