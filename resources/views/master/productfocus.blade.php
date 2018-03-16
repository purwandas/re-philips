@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Product Focus
            <small>manage product focus</small>
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
        <span class="active">Product Focus Management</span>
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
                    <i class="fa fa-search-plus font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">PRODUCT FOCUS</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-productfocus" class="btn green" data-toggle="modal" href="#productfocus"><i
                        class="fa fa-plus"></i> Add Product Focus </a>

                </div>
                <div class="btn-group">
                    <a id="upload" class="btn btn-primary" data-toggle="modal" href="#upload-product-focus"><i
                        class="fa fa-cloud-upload"></i> Update Product Focus </a>

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
                    <table class="table table-striped table-hover table-bordered" id="productfocusTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Product's </th>
                                <th> Type </th>
                                <th> Options </th>
                            </tr>
                        </thead>
                    </table>

            </div>

            @include('partial.modal.productfocus-modal')
            @include('partial.modal.upload-product-focus-modal')

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
<script src="{{ asset('js/handler/productfocus-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/upload-modal/upload-product-focus-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    var dataAll = {};
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
            url: 'data/productfocusC',
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
        var table = $('#productfocusTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.productfocus') }}",
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
                {data: 'product_name', name: 'product_name'},
                {data: 'type', name: 'type'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#productfocusTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

//                if(productRelation(id)){
//                    swal("Warning", "This data still related to others! Please check the relation first.", "warning");
//                    return;
//                }

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
                            url:  'productfocus/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                // Get data district to var data
                                $.ajax({
                                    type: 'POST',
                                    url: 'data/productfocusC',
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
                    url: 'util/export-productfocus',
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

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-productfocus-all',
                    dataType: 'json',
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

                    }
                });

            }


        });

        $("#exportTemplate").click( function(){

        if ($('#export').attr('disabled') != 'disabled') {

            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: 'util/export-productfocus-template',
                dataType: 'json',
                success: function (data) {

                    console.log(data);

                    window.location = data.url;

                }
            });

        }


    });


        initSelect2();

    });

    // Init add form
    $(document).on("click", "#add-productfocus", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        select2Reset($("#product"));
        select2Reset($("#type"));

        // Set action url form for add
        var postDataUrl = "{{ url('productfocus') }}";
        $("#form_productfocus").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-productfocus", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('productfocus/edit/') }}";
        var postDataUrl = "{{ url('productfocus') }}"+"/"+id;

        // Set action url form for update
        $("#form_productfocus").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_productfocus").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    setSelect2IfPatchModal($("#product"), data.product_id, data.product.name);
                    setSelect2IfPatchModal($("#type"), data.type, data.type);

        })

    });

    $(document).on("click", "#upload", function () {

        resetUploadValidation();

        $('#upload_file').val('');

    });

    function initSelect2(){

        /*
         * Select 2 init
         *
         */

        $('#product').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {
                    return {id: obj.id, text: obj.name}
                })
            }
        }));

        $('#type').select2({
            width: '100%',
            placeholder: 'Channel'
        })

    }


</script>
@endsection
