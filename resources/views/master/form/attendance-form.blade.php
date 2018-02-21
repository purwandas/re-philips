@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Attendance
            <small>manage attendance</small>
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
        <a href="{{ url('usernon') }}">Attendance Management</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
		<span>
			@if (empty($data))
				Add New Attendance
			@else
				Update Attendance
			@endif
		</span>
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
					<i class="fa fa-group font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">
						@if (empty($data))
							ADD NEW ATTENDANCE
						@else
							UPDATE ATTENDANCE
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('attendance') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_user" class="form-horizontal" action="{{ url('attendance', @$data->id) }}" method="POST">	        	
			        {{ csrf_field() }}
			        @if (!empty($data))
			          {{ method_field('PATCH') }}
			        @endif
			        <div class="form-body">
                    	<div class="alert alert-danger display-hide">
                        	<button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                        	<button class="close" data-close="alert"></button> Your form validation is successful! </div>

                        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">DETAILS</span>
                        	<hr>
                        </div>


                        <div class="form-group">
                          <label class="col-sm-2 control-label">Store</label>
                          <div class="col-sm-9">

                          	<div class="input-group" style="width: 100%;">

                                <select class="select2select" name="store_id" id="store" ></select>

                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>

                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Promoter</label>
                          <div class="col-sm-9">

                          	<div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="employee" id="employee" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Date</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="date" name="date" class="form-control" value="{{ @$data->date }}" placeholder="Date" />
				            </div>
				          </div>
				        </div>

                        <div class="form-group">
				          <label class="col-sm-2 control-label">Status</label>
				          <div class="col-sm-9">

				          	<div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="status" id="status" required>
                                	<option value="Alpha" {{ (@$data->status == 'Alpha') ? "selected" : "" }}>Alpha</option>  
                                	<option value="Masuk" {{ (@$data->status == 'Masuk') ? "selected" : "" }}>Masuk</option>
                                	<option value="Sakit" {{ (@$data->status == 'Sakit') ? "selected" : "" }}>Sakit</option>
                                	<option value="Izin" {{ (@$data->status == 'Izin') ? "selected" : "" }}>Izin</option>
                                	<option value="Off" {{ (@$data->status == 'Off') ? "selected" : "" }}>Off</option>                        	
                                </select>
                               	
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
				        </div>

	                    <!-- BEGIN MASUK DETAILS -->


	                    <div id="targetContent" class="display-hide">
		                    <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">TARGET DETAILS</span>
	                        	<hr>
	                        </div>

					        <div id="checkInContent" class="display-hide form-group">
					          <label class="col-sm-2 control-label">Check In</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="timeInformat" name="check_in" class="form-control" value="{{ @$data->check_in }}" placeholder="Check In" />
					            </div>
					          </div>
					        </div>

					        <div id="checkOutContent" class="display-hide form-group">
					          <label class="col-sm-2 control-label">Check In</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="timeOutformat" name="check_out" class="form-control" value="{{ @$data->check_out }}" placeholder="Check Out" />
					            </div>
					          </div>
					        </div>
					    </div>

	                    <!-- END MASUK DETAILS -->

				        <div class="form-group" style="padding-top: 15pt;">
				          <div class="col-sm-9 col-sm-offset-2">
				            <button type="submit" class="btn btn-primary green">Save</button>
				          </div>
				        </div>

			    	</div>
			    </form>
				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
@endsection

@section('additional-scripts')		
	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->	
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/user-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->

    <script>

		$(document).ready(function () {

			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

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


	        $('#employee').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {
	        	filters['promoterGroup'] = 1;
	            return filterData('employee', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.nik + " - " + obj.name}
	                })
	            }
	        }));

	       	$('#status').select2({
                width: '100%',
                placeholder: 'Role'
            });


            setForm($('#status').val());

		});

		// Reset form
		function resetForm(){

			$('#check_in').removeAttr('required');			
			$('#check_out').removeAttr('required');

			$('#targetContent').children('.form-group').removeClass('has-error');
			$('#targetContent').addClass('display-hide');

			$('#checkInContent').addClass('display-hide');
			$('#checkOutContent').addClass('display-hide');

		}

		// Set and init dm and rsm
		function setForm(status){

			resetForm();

			if(status == 'Masuk'){
				
				$('#check_in').attr('required', 'required');
				$('#check_out').attr('required', 'required');

				$('#checkInContent').removeClass('display-hide');
				$('#checkOutContent').removeClass('display-hide');
				$('#targetContent').removeClass('display-hide');

			}
		}


		function initDateTimePicker(){

            // Filter Month
            $('#date').datetimepicker({
                format: "yyyy-mm-dd",
                startView: "2",
                minView: "2",
                autoclose: true,
            });
            // Set to Month now
            $('#date').val(
            	{{( @$data->date ) ? "" : "moment().format(" }}
            	'{{( @$data->date ) ? @$data->date : "YYYY-MM-DD" }}'
            	{{( @$data->date ) ? "" : ")" }}
            );

        }
		function initTimePicker(){
			$('#timeInformat').timepicker({ 'timeFormat': 'H:i:s' });
			$('#timeOutformat').timepicker({ 'timeFormat': 'H:i:s' });

        }

		/*
		 * Select2 change
		 *
		 */ 
		$(document.body).on("change","#status",function(){

		    setForm($('#status').val());
		    
		});


		$(document).ready(function(){
			
			initDateTimePicker();
			initTimePicker()
			// On Change status
		    // $('input[type=radio][name=status]').change(function() {
		    //     resetStore();
		    //     setStore(this.value);
		    // });

		    // On Change Dedicate
		    // $('#dedicate').change(function() {
		    //     $("#store").val('').change();
		    //     $("#stores").val('').change();
		    // });

		    // $('div').removeClass('display-hide');
		});

	</script>	
@endsection