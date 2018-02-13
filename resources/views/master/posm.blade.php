@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>POS Material
                <small>manage pos material</small>
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
            <span class="active">POS Material Management</span>
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
                        <i class="fa fa-tasks font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">POS Material</span>
                    </div>
                </div>
                <div class="portlet-title">
                <!-- MAIN CONTENT -->
                    <div class="btn-group">
                        <a id="add-posm" class="btn green" data-toggle="modal" href="#posm"><i
                                    class="fa fa-plus"></i> Add POS Material </a>

                    </div>
                    <div class="actions" style="text-align: left">
                        <a id="export" class="btn green-dark" >
                            <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL </a>
                    </div>
                </div>

                <div class="portlet-body" >
                        <table class="table table-striped table-hover table-bordered" id="posmTable" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> Name </th>
                                <th> Group  </th>
                                <th> Options </th>
                            </tr>
                            </thead>
                        </table>

                </div>

            @include('partial.modal.posm-modal')

            <!-- END MAIN CONTENT -->
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
    <script src="{{ asset('js/handler/posm-handler.js') }}" type="text/javascript"></script>
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
            url: 'data/posm',
            dataType: 'json',
            global: false,
            async: false,
            success: function (results) {
                data = results;
            }
        });

            // Set data for Data Table
            var table = $('#posmTable').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.posm') }}",
                    type: 'POST',
                },
                "rowId": "id",
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'group_name', name: 'group_name'},
                    {data: 'action', name: 'action', searchable: false, sortable: false},
                ],
                "columnDefs": [
                    {"className": "dt-center", "targets": [0]},
                    {"className": "dt-center", "targets": [3]},
                ],
                "order": [ [0, 'desc'] ],
            });


            // Delete data with sweet alert
            $('#posmTable').on('click', 'tr td button.deleteButton', function () {
                var id = $(this).val();

                if(posmRelation(id)){
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
                                url:  'posm/' + id,
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

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-posm',
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

            initSelect2();

        });

        // Init add form
        $(document).on("click", "#add-posm", function () {

            resetValidation();

            var modalTitle = document.getElementById('title');
            modalTitle.innerHTML = "ADD NEW ";

            $('#name').val('');
            select2Reset($("#group"));

            // Set action url form for add
            var postDataUrl = "{{ url('posm') }}";
            $("#form_posm").attr("action", postDataUrl);

            // Delete Patch Method if Exist
            if($('input[name=_method]').length){
                $('input[name=_method]').remove();
            }

        });


        // For editing data
        $(document).on("click", ".edit-posm", function () {

            resetValidation();

            var modalTitle = document.getElementById('title');
            modalTitle.innerHTML = "EDIT";

            var id = $(this).data('id');
            var getDataUrl = "{{ url('posm/edit/') }}";
            var postDataUrl = "{{ url('posm') }}"+"/"+id;

            // Set action url form for update
            $("#form_posm").attr("action", postDataUrl);

            // Set Patch Method
            if(!$('input[name=_method]').length){
                $("#form_posm").append("<input type='hidden' name='_method' value='PATCH'>");
            }

            $.get(getDataUrl + '/' + id, function (data) {

                $('#name').val(data.name);
                setSelect2IfPatchModal($("#group"), data.group_id, data.group.name);

            })

        });

        function initSelect2(){

            /*
             * Select 2 init
             *
             */

            $('#group').select2(setOptions('{{ route("data.group") }}', 'Group ', function (params) {
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
