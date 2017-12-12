@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Store
            <small>manage store</small>
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
        <span class="active">Store Management</span>
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
                    <i class="fa fa-shopping-cart font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">STORE</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->            
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a class="btn green" href="{{ url('store/create') }}"><i
                                    class="fa fa-plus"></i> Add Store </a>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-hover table-bordered" id="storeTable" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th> No. </th>
                            <th> Store ID </th>
                            <th> Store Name 1 </th>
                            <th> Store Name 2 </th>
                            <th> Dedicate </th>
                            <th> Region </th>
                            <th> Area </th>
                            <th> District </th>
                            <th> Global Channel </th>
                            <th> Channel </th>
                            <th> Sub Channel</th>
                            <th> Distributor</th>
                            <th> Classification</th>
                            <th> Longitude</th>
                            <th> Latitude</th>
                            <th> Address</th>
                            <th> Supervisor </th>
                            <th> Options </th>                        
                        </tr>
                    </thead>
                </table>

                <!-- END MAIN CONTENT -->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    $(document).ready(function () {     

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table '#athletesTable'
        var table = $('#storeTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('datatable.store') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},                
                {data: 'store_id', name: 'store_id'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'dedicate', name: 'dedicate'},
                {data: 'region_name', name: 'region_name'},
                {data: 'area_name', name: 'area_name'},
                {data: 'district_name', name: 'district_name'},
                {data: 'globalchannel_name', name: 'globalchannel_name'},
                {data: 'channel_name', name: 'channel_name'},
                {data: 'subchannel_name', name: 'subchannel_name'},
                {data: 'distributor', name: 'distributor'},
                {data: 'classification', name: 'classification'},
                {data: 'longitude', name: 'longitude'},
                {data: 'latitude', name: 'latitude'},
                {data: 'address', name: 'address'},
                {data: 'spv_name', name: 'spv_name'},
                {data: 'action', name: 'action', searchable: false, sortable: false},                
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [15]},
            ],
            "order": [ [0, 'desc'] ],            
        });


        // Delete data with sweet alert
        $('#storeTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                if(storeRelation(id)){
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
                            url:  'store/' + id,
                            success: function (data) {
                                console.log(data);

                                $("#"+id).remove();
                                // $('#sportsTable').DataTable().ajax.reload();
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

</script>
@endsection
