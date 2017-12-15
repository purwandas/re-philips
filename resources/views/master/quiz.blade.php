@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Quiz
            <small>manage quiz</small>
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
        <span class="active">Quiz Management</span>
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
					<i class="fa fa-newspaper-o font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">QUIZ</span>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->            
	        	<div class="table-toolbar">
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="btn-group">
                             	<a class="btn green" href="{{ url('quiz/create') }}"><i
									class="fa fa-plus"></i> Add New </a>
                                
                            </div>
                    	</div>
                    </div>
                </div>

	        	<table class="table table-striped table-hover table-bordered" id="quizTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                    		<th> No. </th>
                            <th> Title </th>
                            <th> Description </th>
                            <th> Link </th>
                        	<th> Target </th>
                            <th> Total Read </th>
                            <th> Date </th>
                            <th> Action </th>
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

<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN TEXT MODAL SCRIPTS -->
<script src="{{ asset('js/text-modal/popup.js') }}" type="text/javascript"></script>
<!-- END TEXT MODAL SCRIPTS -->

<script>
	$(document).ready(function () {    	

		$.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table '#athletesTable'
        var table = $('#quizTable').dataTable({
	        "processing": true,
	        "serverSide": true,	          
	        "ajax": {
                url: "{{ route('datatable.quiz') }}",
                type: 'POST',
            },
	        "rowId": "id",
	        "columns": [
	            {data: 'id', name: 'id'},                
                {data: 'title', name: 'title'},
	            {data: 'description', name: 'description'},                
                {data: 'link', name: 'link'},
                {data: 'target', name: 'target'},
                {data: 'total_read', name: 'total_read'},
                {data: 'date', name: 'date'},
	            {data: 'action', name: 'action', searchable: false, sortable: false},                
	        ],
	        "columnDefs": [
        		{"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [1]},
                {"className": "dt-center", "targets": [2]},
                {"className": "dt-center", "targets": [4]},
      		],
            "order": [ [0, 'desc'] ],            
    	});


    	// Delete data with sweet alert
        $('#quizTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'quiz/' + id,
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
