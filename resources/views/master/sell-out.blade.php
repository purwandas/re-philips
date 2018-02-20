@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Sell Out
            <small>manage sell out</small>
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
        <span class="active">Sell Out Management</span>
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
                    <span class="caption-subject font-blue bold uppercase">Sell Out</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group col-md-12">
                                    <a class="btn green" href="{{ url('sellout/create') }}"><i
                                    class="fa fa-plus"></i> Add New </a>
                                    @if(Session::has('status'))
                                    <div class="col-md-10 alert alert-info text-center" style="float: right;">
                                        {{ Session::get('status') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped table-hover table-bordered" id="sellOutTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>                            
                                <th> Store ID </th>
                                <th> Store Name </th>
                                <th> Customer Code </th>
                                <th> Product </th>
                                <th> Quantity </th>
                                <th> Input Time </th>
                                <th> Options </th>                        
                            </tr>
                        </thead>
                    </table>                 

                </div>

                @include('partial.modal.sell-in-modal')

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
<script src="{{ asset('js/handler/sellout-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<div id="additionalScripts">
    
</div>

<script>
    /*
     * Sell In
     *
     */
    $(document).ready(function () {                

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table
        var table = $('#sellOutTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.sellout') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'store_id', name: 'store_id'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'product', name: 'product'},
                {data: 'quantity', name: 'quantity'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', searchable: false, sortable: false},           
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
                {"className": "dt-center", "targets": [7]},
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
        $('#sellOutTable').on('click', 'tr td button.deleteButton', function () {
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


        initSelect2SellOut();

    });

    // Init add form
    $(document).on("click", "#add-sell-in", function () {       
        
        // resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#quantity').val('');        
        // select2Reset($("#store"));
        // select2Reset($("#product"));

        // Set action url form for add
        var postDataUrl = "{{ url('sellout') }}";    
        $("#form_sell-out").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    // $(document).on("click", ".edit-area", function () {

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

    function initSelect2SellOut(){

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

        $('#store').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {            
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
    }


</script>
@endsection
