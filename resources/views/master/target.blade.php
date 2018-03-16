@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Target
            <small>manage target</small>
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
        <span class="active">Target Management</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet light bordered">
            <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cog font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER TARGET</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterEmployee" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterStore" class="select2select"></select>
                    </div>                    
                    <div class="col-md-4">
                        <select id="filterSellType" class="select2select">
                            <option value=""></option>
                            <option value="Sell In">Sell In</option>
                            <option value="Sell Out">Sell Out</option>
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

            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">TARGET</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-target" class="btn green" data-toggle="modal" href="#target"><i
                        class="fa fa-plus"></i> Add Target </a>

                </div>
                <div class="btn-group">
                    <a id="upload" class="btn btn-primary" data-toggle="modal" href="#upload-target"><i
                        class="fa fa-cloud-upload"></i> Update Target </a>

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
                    <table class="table table-striped table-hover table-bordered" id="targetTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Promoter </th>
                                <th> Store </th>
                                <th> Sell Type </th>
                                <th> Target DA </th>
                                <th> Target PF DA </th>
                                <th> Target PC </th>
                                <th> Target PF PC </th>
                                <th> Target MCC </th>
                                <th> Target PF MCC </th>
                                <th> Options </th>
                            </tr>
                        </thead>
                    </table>

            </div>

            @include('partial.modal.target-modal')
            @include('partial.modal.upload-target-modal')

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
<script src="{{ asset('js/handler/target-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/upload-modal/upload-target-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    var dataAll = {};
    
    /*
     *
     *
     */
    var filterId = ['#filterEmployee', '#filterStore', '#filterSellType'];
    var url = 'datatable/target';
    var order = [ [0, 'desc'] ];
    var columnDefs = [{"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [6]},];
    var tableColumns = [{data: 'id', name: 'id'},                
                {data: 'promoter_name', name: 'promoter_name'},
                {data: 'store_name', name: 'store_name'},
                {data: 'sell_type', name: 'sell_type'},
                {data: 'target_da', name: 'target_da'},
                {data: 'target_pf_da', name: 'target_pf_da'},
                {data: 'target_pc', name: 'target_pc'},
                {data: 'target_pf_pc', name: 'target_pf_pc'},
                {data: 'target_mcc', name: 'target_mcc'},
                {data: 'target_pf_mcc', name: 'target_pf_mcc'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
                        ];

    var exportButton = '#export';

    var paramFilter = ['targetTable', $('#targetTable'), url, tableColumns, columnDefs, order, exportButton];

    var paramReset = [filterId, 'targetTable', $('#targetTable'), url, tableColumns, columnDefs, order, exportButton];

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/targetC',
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
        var table = $('#targetTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.target') }}",
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
                {data: 'promoter_name', name: 'promoter_name'},
                {data: 'store_name', name: 'store_name'},
                {data: 'sell_type', name: 'sell_type'},
                {data: 'target_da', name: 'target_da'},
                {data: 'target_pf_da', name: 'target_pf_da'},
                {data: 'target_pc', name: 'target_pc'},
                {data: 'target_pf_pc', name: 'target_pf_pc'},
                {data: 'target_mcc', name: 'target_mcc'},
                {data: 'target_pf_mcc', name: 'target_pf_mcc'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [6]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#targetTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'target/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/targetC',
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
                    url: 'util/export-target',
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
                    url: 'util/export-target-all',
                    dataType: 'json',
                    data: filters,
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
                    url: 'util/export-target-template',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

                    }
                });

            }


        });

        initSelect2();
        initSelect2B();

    });

    // Init add form
    $(document).on("click", "#add-target", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#target_da').val('0');
        $('#target_pf_da').val('0');
        $('#target_pc').val('0');
        $('#target_pf_pc').val('0');
        $('#target_mcc').val('0');
        $('#target_pf_mcc').val('0');
//        $('#sell_type').val('Sell In');
        select2Reset($("#promoter"));
        select2Reset($("#store"));
        select2Reset($("#sell_type"));

        // Set action url form for add
        var postDataUrl = "{{ url('target') }}";
        $("#form_target").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

        // Clear filters
        delete filters['byEmployee'];
        delete filters['byStore'];

        resetCheckbox();

    });


    // For editing data
    $(document).on("click", ".edit-target", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('target/edit/') }}";
        var postDataUrl = "{{ url('target') }}"+"/"+id;

        // Set action url form for update
        $("#form_target").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_target").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#target_da').val(data.target_da);
                    $('#target_pf_da').val(data.target_pf_da);
                    $('#target_pc').val(data.target_pc);
                    $('#target_pf_pc').val(data.target_pf_pc);
                    $('#target_mcc').val(data.target_mcc);
                    $('#target_pf_mcc').val(data.target_pf_mcc);
                    setSelect2IfPatchModal($("#sell_type"), data.sell_type, data.sell_type);
                    setSelect2IfPatchModal($("#promoter"), data.user_id, data.user.name);
                    if(data.store.store_name_2 != null){
                        setSelect2IfPatchModal($("#store"), data.store_id, data.store.store_id+ " - " + data.store.store_name_1 + " (" + data.store.store_name_2 + ")");
                    }else{
                        setSelect2IfPatchModal($("#store"), data.store_id, data.store.store_id+ " - " + data.store.store_name_1);
                    }
                    

                    // Set filters
                    self.selected('byEmployee', $('#promoter').val());
                    self.selected('byStore', $('#store').val());

                    if(data.partner == 1){
                        $("#partnerCheck").prop( "checked", true);
                        $('#partnerContent').removeClass('display-hide');
                    }else{
                        resetCheckbox();
                    }

        });

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

        $('#promoter').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {
	        	filters['promoterGroupNew'] = 1;
	            return filterData('employee', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {
	                    return {id: obj.id, text: obj.nik + " - " + obj.name}
	                })
	            }
	        }));
        $('#promoter').on('select2:select', function () {
                self.selected('byEmployee', $('#promoter').val());

                var getDataUrl = "{{ url('util/user/') }}";
                $.get(getDataUrl + '/' + $('#promoter').val(), function (data) {

                    // console.log(data);

                    if(data.role.role_group == 'Demonstrator DA'){
                        $('#partnerContent').removeClass('display-hide');
                    }else{
                        $('#partnerContent').addClass('display-hide');
                    }

                });

                $("#partnerCheck").prop( "checked", false);
        });

        $('#store').select2(setOptions('{{ route("data.stores") }}', 'Store', function (params) {
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {
                        // if (obj.dedicate != null ){
                        // return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")" + " - " + obj.dedicate }
                        // } else {
                        // return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")" }
                        // }
                        if(obj.store_name_2 != null){
                            return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
                        return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 }
	                })
	            }
	        }));
        $('#store').on('select2:select', function () {
                self.selected('byStore', $('#store').val());
        });

        $('#sell_type').select2({
            width: '100%',
            placeholder: 'Sell Type'
        });

    }

    function resetCheckbox(){

        $("#partnerCheck").prop( "checked", false);
        $('#partnerContent').addClass('display-hide');

    }

    function initSelect2B(){

            /*
             * Select 2 init
             *
             */

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

            $('#filterEmployee').select2(setOptions('{{ route("data.groupPromoter") }}', 'Promoter', function (params) {
                filters['roleGroup'] = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];
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

            $('#filterSellType').select2({
                width: '100%',
                placeholder: 'Sell Type'
            });
            $('#filterSellType').on('select2:select', function () {
                self.selected('bySellType', $('#filterSellType').val());
            });

        }


</script>
@endsection
