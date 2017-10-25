@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>SOH
            <small>data soh</small>
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
        <span class="active">News Management</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-line-chart font-purple-plum"></i>
                        <span class="caption-subject bold font-blue-hoki uppercase"> Filter Report</span>
                        <!--  region, area, area re app, store, sma user(promoter) -->
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row filter">
                        <div class="col-md-4">
                            <select id="filterRegion" name="filterRegion" class="select2select" ></select>
                        </div>
                        <div class="col-md-4">
                            <select id="filterArea" name="filterArea" class="select2select" ></select>
                        </div>
                        <div class="col-md-4">
                            <select id="filterAreaReApp" name="filterAreaReApp" class="select2select" ></select>
                        </div>
                    </div>
                    <div class="row filter">
                        <div class="col-md-6">
                            <select id="filterStore" name="filterStore" class="select2select"></select>
                        </div>
                        <div class="col-md-6">
                            <select id="filterUser" name="filterUser" class="select2select"></select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="datetimepicker12" name="datetimepicker12">
                        </div>
                    </div>
                    <div class="btn-group">
                        <a href="javascript:;" class="btn red-pink" @click.prevent="resetFilter">
                            <i class="fa fa-refresh"></i> Reset </a>
                        <a href="javascript:;" class="btn blue-hoki" @click.prevent="filteringReport">
                            <i class="fa fa-filter"></i> Filter </a>                        
                    </div>

                </div>
            </div>
	    <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-newspaper-o font-green"></i>
					<span class="caption-subject font-green sbold uppercase">NEWS</span>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->            
	        	<div class="table-toolbar">
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="btn-group">
                             	<a class="btn green" href="{{ url('news/create') }}"><i
									class="fa fa-plus"></i> Add New </a>
                                
                            </div>
                    	</div>
                    </div>
                </div>

	        	<table class="table table-striped table-hover table-bordered" id="sohsTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                    		<th> No. </th>
                            <th> Area </th>
                            <th> Store Name </th>
                            <th> Store Name 2 </th>
                            <th> Store ID </th>
                            <th> NIK </th>
                            <th> Promoter Name </th>
                            <th> Date </th>
                            <th> Model </th>
                            <th> Group </th>
                            <th> Category </th>
                            <th> Product Name </th>
                            <th> Quantity </th>
                        </tr>
                    </thead>
				</table>

				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->

        @include('partial.util.read-who-modal')

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
<!-- BEGIN TEXT MODAL SCRIPTS -->
<script src="{{ asset('js/text-modal/popup.js') }}" type="text/javascript"></script>
<!-- END TEXT MODAL SCRIPTS -->
<style type="text/css">
    .filter {
        margin-bottom: 10px;
    }
</style>
<script>

    $(function () {
            $('#datetimepicker12').datetimepicker({
                startView: '3',
                
                minView: '3',
                format: 'MM yyyy',
                autoclose:true,
            });
        });

	$(document).ready(function () {    	

		$.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /* set select2 values */
        $('#filterRegion').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {            
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.name}
                })
            }
        }));  
        $('#filterArea').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {            
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.name}
                })
            }
        }));
        $('#filterAreaReApp').select2(setOptions('{{ route("data.areaapp") }}', 'Area Re-app', function (params) {            
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.name}
                })
            }
        }));
        $('#filterStore').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {            
            return filterData('store', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                })
            }
        }));
        $('#filterUser').select2(setOptions('{{ route("data.promoter") }}', 'Promotor', function (params) {            
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.name}
                })
            }
        }));

        
       

        // Set data for Data Table '#athletesTable'
        var table = $('#sohsTable').dataTable({
	        "processing": true,
	        "serverSide": true,	          
	        "ajax": {
                url: "{{ route('datatable.soh') }}",
                type: 'POST',
            },
	        "rowId": "id",
	        "columns": [
                {data: 'soh_detail_id', name: 'soh_detail_id'},    
                {data: 'area_name', name: 'area_name'},
                {data: 'store_name', name: 'store_name'},
                {data: 'store_name2', name: 'store_name2'},
                {data: 'store_id', name: 'store_id'},
                {data: 'nik', name: 'nik'},
                {data: 'user_name', name: 'user_name'},
                {data: 'date', name: 'date'},
                {data: 'model', name: 'model'},
                {data: 'group_name', name: 'group_name'},
                {data: 'category_name', name: 'category_name'},
                {data: 'product_name', name: 'product_name'},
                {data: 'quantity', name: 'quantity'},
	        ],
	        "columnDefs": [
        		// {"className": "dt-center", "targets": [0]},
          //       {"className": "dt-center", "targets": [6]},
          //       {"className": "dt-center", "targets": [8]},
          //       {"className": "dt-center", "targets": [9]},
      		],
            "order": [ [0, 'desc'] ],            
    	});


    	// Delete data with sweet alert
        $('#newsTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'news/' + id,
                            success: function (data) {
                                console.log(data);

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

</script>
@endsection
