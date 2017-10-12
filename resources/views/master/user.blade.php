@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Employee
            <small>manage employee</small>
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
        <span class="active">Employee Management</span>
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
					<i class="fa fa-group font-green"></i>
					<span class="caption-subject font-green sbold uppercase">EMPLOYEE</span>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->            
	        	<div class="table-toolbar">
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="btn-group">
                             	<a class="btn green" href="{{ url('user/create') }}"><i
									class="fa fa-plus"></i> Add Employee </a>
                                
                            </div>
                    	</div>
                    </div>
                </div>

	        	<table class="table table-striped table-hover table-bordered" id="userTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                    		<th> No. </th>
                            <th> NIK </th>
                            <th> Name </th>
                        	<th> Role </th>
                            <th> Status </th>
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
        var table = $('#userTable').dataTable({
	        "processing": true,
	        "serverSide": true,	          
	        "ajax": {
                url: "{{ route('datatable.user') }}",
                type: 'POST',
            },
	        "rowId": "id",
	        "columns": [
	            {data: 'id', name: 'id'}, 
                {data: 'nik', name: 'nik'},               
	            {data: 'name', name: 'name'},
                {data: 'role', name: 'role'},
                {data: 'status', name: 'status'},
	            {data: 'action', name: 'action', searchable: false, sortable: false},                
	        ],
	        "columnDefs": [
        		{"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [5]},
      		],
            "order": [ [0, 'desc'] ],            
    	});


    	// Delete data with sweet alert
        $('#userTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                if(userRelation(id)){
                    swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                    return;
                }

                if(productKnowledgeEmployeeRelation(id) > 0){
                    swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                    return;
                }

                if(productKnowledgeAdminRelation(id) > 0){
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
                            url:  'user/' + id,
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
