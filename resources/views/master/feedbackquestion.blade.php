@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Feedback Questions
            <small>manage feedback Question</small>
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
        <span class="active">Feedback Question Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">Feedback Question</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group">
                                    <a id="add-feedbackQuestion" class="btn green" data-toggle="modal" href="#feedbackQuestion"><i
                                        class="fa fa-plus"></i> Add Feedback Question </a>

                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped table-hover table-bordered" id="feedbackQuestionTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Category </th>
                                <th> Question </th>   
                                <th> Options </th>                        
                            </tr>
                        </thead>
                    </table>                 

                </div>

                @include('partial.modal.feedbackQuestion-modal')

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
<script src="{{ asset('js/handler/feedbackQuestion-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
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

        // Set data for Data Table
        var table = $('#feedbackQuestionTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.feedbackQuestion') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'fbcategory_name', name: 'fbcategory_name'},
                {data: 'question', name: 'question'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [2]},
            ],
            "order": [ [0, 'asc'] ],
        });


        // Delete data with sweet alert
        $('#feedbackQuestionTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                // if(feedbackQuestionRelation(id)){
                //     swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                //     return;
                // }

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
                            url:  'feedbackQuestion/' + id,
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

    });

    // Init add form
    $(document).on("click", "#add-feedbackQuestion", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        select2Reset($("#feedbackCategory"));
        $('#question').val('');
        // $('#type').val('');

        // Set action url form for add
        var postDataUrl = "{{ url('feedbackQuestion') }}";
        $("#form_feedbackQuestion").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-feedbackQuestion", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('feedbackQuestion/edit/') }}";
        var postDataUrl = "{{ url('feedbackQuestion') }}"+"/"+id;

        // Set action url form for update
        $("#form_feedbackQuestion").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_feedbackQuestion").append("<input type='hidden' name='_method' value='PATCH'>");
        }
        $.get(getDataUrl + '/' + id, function (data) {
            // console.log(data.question)
                    setSelect2IfPatchModal($("#feedbackCategory"), data.feedbackCategory_id, data.feedback_category.name);
                    $('#question').val(data.question);


            })

        });

        function initSelect2(){
            // console.log('ok');
            /*
             * Select 2 init
             *
             */

            $('#feedbackCategory').select2(setOptions('{{ route("data.feedbackCategory") }}', 'Feedback Category', function (params) {
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
