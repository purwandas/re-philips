@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Attendance
            <small>detail attendance</small>
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
        <a href="{{ url('quiz') }}">Attendance Detail</a>
        <i class="fa fa-circle"></i>
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
					<span class="caption-subject font-blue bold uppercase">
						Attendance Details
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('attendancereport') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	        	
			        {{ csrf_field() }}
			        
			        <div class="form-body">

                        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">DETAILS</span>
                        	<hr>
                        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Title</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="title" name="title" class="form-control" value="{{ @$data->user_name }}" placeholder="Input Title" />
				            </div>
				          </div>
				        </div>

			    	</div>
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
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/quiz-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->

    <script> 	
		$(document).ready(function () {
			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });   

	        $('#target').select2({
                width: '100%',
                placeholder: 'Select Target'
            }); 

            setSelect2IfPatch($("#target"), "{{ @$data->target }}", "{{ @$data->target }}");

		});

	</script>

@endsection
