@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Area
            <small>manage area</small>
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
        <span class="active">Area Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">AREA</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-area" class="btn green" data-toggle="modal" href="#area"><i
                        class="fa fa-plus"></i> Add Area </a>
                </div>
                <div class="actions" style="text-align: left">
                    <a id="export" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                </div>
                <div class="actions" style="text-align: left; padding-right: 10px;">
                    <a onclick="event.preventDefault();document.getElementById('exportAll-form').submit();" class="btn green-dark">
                      <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL)
                    </a>
                    <form id="exportAll-form" action="{{ url('util/export-area-all') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                    </form>
                </div>
            </div>

            <div class="portlet-body" >
                <table class="table table-striped table-hover table-bordered" id="areaTable" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th> No. </th>                            
                            <th> Area Name </th>                           
                            <th> Region </th>
                            <th> Options </th>                        
                        </tr>
                    </thead>
                </table>                 

            </div>

            @include('partial.modal.area-modal')

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
<script src="{{ asset('js/handler/area-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>

    var dataAll = {};
    /*
     * AREA
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
            url: 'data/areaC',
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
        var table = $('#areaTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.area') }}",
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
                {data: 'name', name: 'name'},                
                {data: 'region_name', name: 'region_name'},
                {data: 'action', name: 'action', searchable: false, sortable: false},           
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            // createdRow: function( row, data, dataIndex ) {
            //         // Set the data-status attribute, and add a class
            //         $( row )
            //             .attr('data-id',data.id)
            //             .attr('data-toggle','modal')
            //             .attr('data-target','#area')
            //             .attr('style','cursor:pointer')
            //             .addClass('clickable-row');
            //     },
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#areaTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

            if(areaRelation(id)){
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
                            url:  'area/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/areaC',
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
                    url: 'util/export-area',
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

                $.ajax({
                    type: 'POST',
                    url: 'util/export-area-all',
                    dataType: 'json',
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;
                    }
                });

            }


        });

        initSelect2Area();

    });

    // Init add form
    $(document).on("click", "#add-area", function () {       
        
        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#name').val('');        
        select2Reset($("#region"));

        // Set action url form for add
        var postDataUrl = "{{ url('area') }}";    
        $("#form_area").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-area", function () {

        resetValidation();       
        
        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('area/edit/') }}";
        var postDataUrl = "{{ url('area') }}"+"/"+id;        

        // Set action url form for update        
        $("#form_area").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_area").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#name').val(data.name);

                    setSelect2IfPatchModal($("#region"), data.region_id, data.region.name);

        })

    });

    // $(document).on("click", "#areaTable tbody tr", function () {
    //     // alert('kampter');
    //     resetValidation();       
        
    //     var modalTitle = document.getElementById('title');
    //     modalTitle.innerHTML = "EDIT";

    //     var id = $(this).data('id');
    //     var getDataUrl = "{{ url('area/edit/') }}";
    //     var postDataUrl = "{{ url('area') }}"+"/"+id;        

    //     // Set action url form for update        
    //     $("#form_area").attr("action", postDataUrl);

    //     // Set Patch Method
    //     if(!$('input[name=_method]').length){
    //         $("#form_area").append("<input type='hidden' name='_method' value='PATCH'>");
    //     }

    //     $.get(getDataUrl + '/' + id, function (data) {

    //                 $('#name').val(data.name);

    //                 setSelect2IfPatchModal($("#region"), data.region_id, data.region.name);

    //     })

    // });

    function initSelect2Area(){

        /*
         * Select 2 init
         *
         */

        $('#region').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {            
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
