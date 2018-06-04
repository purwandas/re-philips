@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Price
            <small>manage price</small>
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
        <span class="active">Price Management</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">

        <!-- BEGIN FILTER-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cog font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER PRICE</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>
                
                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterGlobalChannel" class="select2select">
                            <option value=""></option>
                        </select>
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

        <!-- END FILTER-->

        <!-- BEGIN EXAMPLE TABLE PORTLET-->

            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-dollar font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">PRICE</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-price" class="btn green" data-toggle="modal" href="#price"><i
                        class="fa fa-plus"></i> Add Price </a>

                </div>
                <div class="btn-group">
                    <a id="upload" class="btn btn-primary" data-toggle="modal" href="#upload-price"><i
                        class="fa fa-cloud-upload"></i> Update Price </a>

                </div>
                <div class="actions" style="text-align: left; padding-right: 10px;">
                    <a id="export" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                </div>
                <div class="actions" style="text-align: left; padding-right: 10px;">
                    <a id="exportAll" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                </div>
            </div>

            <div class="portlet-body" >
                    <table class="table table-striped table-hover table-bordered" id="priceTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Model </th>
                                <th> Product's </th>
                                <th> Global Channel </th>
                                <th> Sell Type</th>
                                <th> Price </th>
                                <th> Release Date </th>
                                <th> Options </th>
                            </tr>
                        </thead>
                    </table>

            </div>

            @include('partial.modal.price-modal')
            @include('partial.modal.upload-price-modal')

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
<script src="{{ asset('js/handler/price-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/upload-modal/upload-price-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    var dataAll = {};
    /*
     *
     *
     */

        var filterId = ['#filterGlobalChannel'];
        var url = 'datatable/price';
        var order = [ [0, 'desc'] ];
        var columnDefs = [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [5]},
            ];

        var tableColumns = [
                {data: 'id', name: 'id'},
                {data: 'product_model', name: 'product_model'},
                {data: 'product_name', name: 'product_name'},
                {data: 'globalchannel_name', name: 'globalchannel_name'},
                {data: 'sell_type', name: 'sell_type'},
                {data: 'price', name: 'price'},
                {data: 'release_date', name: 'release_date'},
                {data: 'action', name: 'action', searchable: false, sortable: false},            
            ];
        var paramFilter = ['priceTable', $('#priceTable'), url, tableColumns, columnDefs, order, '#export'];
        var paramReset = [filterId, 'priceTable', $('#priceTable'), url, tableColumns, columnDefs, order, '#export'];

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Filter Date
        $('#release_date').datetimepicker({
            format: "yyyy-mm-dd",
            startView: "2",
            minView: "2",
            autoclose: true,
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/priceC',
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

        var table = $('#priceTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.price') }}",
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
                {data: 'product_model', name: 'product_model'},
                {data: 'product_name', name: 'product_name'},
                {data: 'globalchannel_name', name: 'globalchannel_name'},
                {data: 'sell_type', name: 'sell_type'},
                {data: 'price', name: 'price'},
                {data: 'release_date', name: 'release_date'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [4]},
            ],
            "order": [ [0, 'desc'] ],
        });

        // Delete data with sweet alert
        $('#priceTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'price/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/priceC',
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
                    url: 'util/export-price',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

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
                    url: 'util/export-price-all',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

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
                    url: 'util/export-price-template',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        var a = document.createElement("a");
                        a.href = data.file; 
                        a.download = data.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                    }
                });

            }


        });

        initSelect2();

    });

    // Init add form
    $(document).on("click", "#add-price", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#prices').val('');
        select2Reset($("#product"));
        select2Reset($("#globalchannel"));
        select2Reset($("#sell_type"));
            
        // Set to Date now
        $('#release_date').val(moment().format('YYYY-MM-DD'));

        // Set action url form for add
        var postDataUrl = "{{ url('price') }}";
        $("#form_price").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-price", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('price/edit/') }}";
        var postDataUrl = "{{ url('price') }}"+"/"+id;

        // Set action url form for update
        $("#form_price").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_price").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#prices').val(data.price);
                    setSelect2IfPatchModal($("#product"), data.product_id, data.product.name);
                    setSelect2IfPatchModal($("#globalchannel"), data.globalchannel_id, data.global_channel.name);
                    setSelect2IfPatchModal($("#sell_type"), data.sell_type, data.sell_type);

                    // Set to Date according to value
                    $('#release_date').val(data.release_date);

        })

    });

    // Init add form
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

        $('#globalchannel').select2(setOptions('{{ route("data.globalchannel") }}', 'Global Channel', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {
                    return {id: obj.id, text: obj.name}
                })
            }
        }));


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
            self.selected('byGChannel', $('#filterGlobalChannel').val());
        });

        $('#sell_type').select2({
            width: '100%',
            placeholder: 'Sell Type'
        });

    }


</script>
@endsection
