@extends('layouts.app')

@section('header')
<h1 class="page-title"> Employee
	<small>Manage Employee</small>
</h1>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="{{ url('/') }}">Home</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>			
			<a href="{{ url('employee') }}">Employee Management</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>
				@if (empty($data))
					Add New Employee
				@else
					Update Employee
				@endif
			</span>
		</li>
	</ul>                        
</div>
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
	    <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-group font-green"></i>
					<span class="caption-subject font-green sbold uppercase">
						@if (empty($data))
							ADD NEW EMPLOYEE
						@else
							UPDATE EMPLOYEE
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('employee') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_employee" class="form-horizontal" action="{{ url('employee', @$data->id) }}" method="POST">	        	
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
				          <label class="col-sm-2 control-label">NIK</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="nik" name="nik" class="form-control" value="{{ @$data->nik }}" placeholder="Input NIK" />
				            </div>
				          </div>
				        </div>

				         <div class="form-group">
				          <label class="col-sm-2 control-label">Name</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="name" name="name" class="form-control" value="{{ @$data->name }}" placeholder="Input Employee Name" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Email</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="email" name="email" class="form-control" value="{{ @$data->email }}" placeholder="Input Email" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Role</label>
				          <div class="col-sm-9">

				          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role" id="role" required>
                                	<!-- <option value="SPV" {{ (@$data->role == 'SPV') ? "selected" : "" }}>SPV</option> -->
                                	<option value="Promoter" {{ (@$data->role == 'Promoter') ? "selected" : "" }}>Promoter</option>
                                	<option value="Promoter Additional" {{ (@$data->role == 'Promoter Additional') ? "selected" : "" }}>Promoter Additional</option>
                                	<option value="Promoter Event" {{ (@$data->role == 'Promoter Event') ? "selected" : "" }}>Promoter Event</option>
                                	<option value="Demonstrator MCC" {{ (@$data->role == 'Demonstrator MCC') ? "selected" : "" }}>Demonstrator MCC</option>
                                	<option value="Demonstrator DA" {{ (@$data->role == 'Demonstrator DA') ? "selected" : "" }}>Demonstrator DA</option>
                                	<option value="Driver" {{ (@$data->role == 'Driver') ? "selected" : "" }}>Driver</option>
                                	<option value="Helper" {{ (@$data->role == 'Helper') ? "selected" : "" }}>Helper</option>
                                	<option value="ACT" {{ (@$data->role == 'ACT') ? "selected" : "" }}>ACT</option>
                                	<option value="PPE" {{ (@$data->role == 'PPE') ? "selected" : "" }}>PPE</option>
                                	<option value="BDT" {{ (@$data->role == 'BDT') ? "selected" : "" }}>BDT</option>
                                	<option value="Salesman Explorer" {{ (@$data->role == 'Salesman Explorer') ? "selected" : "" }}>Salesman Explorer</option>
                                	<option value="PCE" {{ (@$data->role == 'PCE') ? "selected" : "" }}>PCE</option>
                                	<option value="RE Executive" {{ (@$data->role == 'RE Executive') ? "selected" : "" }}>RE Executive</option>
                                	<option value="RE Support" {{ (@$data->role == 'RE Support') ? "selected" : "" }}>RE Support</option>
                                	<option value="Supervisor" {{ (@$data->role == 'Supervisor') ? "selected" : "" }}>Supervisor</option>
                                	<option value="Trainer" {{ (@$data->role == 'Trainer') ? "selected" : "" }}>Trainer</option>
                                	<option value="Head Trainer" {{ (@$data->role == 'Head Trainer') ? "selected" : "" }}>Head Trainer</option>
                                	<option value="SMD" {{ (@$data->role == 'SMD') ? "selected" : "" }}>SMD</option>
                                	<option value="SMD Coordinator" {{ (@$data->role == 'SMD Coordinator') ? "selected" : "" }}>SMD Coordinator</option>
                                	<option value="HIC" {{ (@$data->role == 'HIC') ? "selected" : "" }}>HIC</option>
                                	<option value="HIE" {{ (@$data->role == 'HIE') ? "selected" : "" }}>HIE</option>
                                	<option value="Supervisor Hybrid" {{ (@$data->role == 'Supervisor Hybrid') ? "selected" : "" }}>Supervisor Hybrid</option>
                                	<option value="SMD Additional" {{ (@$data->role == 'SMD Additional') ? "selected" : "" }}>SMD Additional</option>
                                	<option value="ASC" {{ (@$data->role == 'ASC') ? "selected" : "" }}>ASC</option>                                	                           
                                </select>
                               	
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
				        </div>

				        <div id="statusContent" class="display-hide">
					        <div class="form-group">
	                            <label class="control-label col-md-2">Status                               
	                            </label>
	                            <div class="col-md-9">
	                                <div class="mt-radio-list" data-error-container="#form_employee_status_error">
	                                    <label class="mt-radio">
	                                        <input type="radio" name="status" value="stay" {{ (@$data->status == 'stay') ? "checked" : "" }}> Stay
	                                        <span></span>
	                                    </label>
	                                    <label class="mt-radio">
	                                        <input type="radio" name="status" value="mobile" {{ (@$data->status == 'mobile') ? "checked" : "" }}> Mobile
	                                        <span></span>
	                                    </label>
	                                </div>
	                                <div id="form_employee_status_error"> </div>
	                            </div>
	                        </div>
	                    </div>
				        

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">PHOTO</span>
                        	<hr>
                        </div>		        

				        <!-- View for old image * PHOTO * -->
				        @if (!empty($data))				        	
				        	<div class="form-group">
				          		<label class="col-sm-2 control-label">Photo</label>
				         		<div class="col-sm-9">				          	
					    			<img width="90px" height="90px" src="{{ @$data->photo }}" onError="this.onerror=null;this.src='{{ asset('image/missing.png') }}';">
				         		</div>
				        	</div>

			          		<div class="form-group">
				          		<label class="col-sm-2 control-label">Photo URL</label>
				         		<div class="col-sm-9">				          	
					    			<input type="text" class="form-control" value="{{ @$data->photo }}" disabled="disabled" />
					    		</div>
				        	</div>
			        	@endif

				        <div class="form-group">
				          <label class="col-sm-2 control-label">{{ (!empty($data)) ? 'New ' : ' ' }}Photo</label>
				          <div class="col-sm-9">
				          	<div class="input-group" style="width: 100%;">
					          	<input type="file" accept=".jpg,.jpeg,.png,.gif,.svg" class="form-control" id="photo_file" name="photo_file">
					          	<p style="font-size: 10pt;" class="help-block"> (Type of file: jpg, jpeg, png, gif, svg - Max size : 2 MB) </p>
					          	<div class="file_error_message" style="display: none;"></div>
					      	</div>
				          </div>
				        </div>		 

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">Password</span>
                        	<hr>
                        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Password</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="password" id="password" name="password" class="form-control" placeholder="Input Password" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Confirm Password</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="password" id="password-confirm" name="password_confirmation" class="form-control" placeholder="Confirm Password" />
				            </div>
				          </div>
				        </div>       				        			        		  
				        
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
    <script> 	
		$(document).ready(function () {
			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });   

	       	$('#role').select2({
                width: '100%',
                placeholder: 'Role'
            })

            setForm($('#role').val());

		});

		// Reset dm and rsm
		function resetForm(){
			$('#statusContent').addClass('display-hide');
			$('#statusContent').children('.form-group').removeClass('has-error');
		}

		// Set and init dm and rsm
		function setForm(role){

			checkPromoter();

			resetForm();			

			if(isPromoter == 1){
				$('#statusContent').removeClass('display-hide');
			}
		}

		/*
		 * Select2 change
		 *
		 */ 

		$(document.body).on("change","#role",function(){
							
		    setForm($('#role').val());
		    
		});	 

		// Check role promoter
		function checkPromoter(){
			isPromoter = 0;
			var role = $('#role').val();

			if(role == 'Promoter' || role == 'Promoter Additional' || role == 'Promoter Event' || role == 'Demonstrator MCC' || role == 'Demonstrator DA' || role == 'ACT'  || role == 'PPE' || role == 'BDT' || role == 'Salesman Explorer' || role == 'SMD' || role == 'SMD Coordinator' || role == 'HIC' || role == 'HIE' || role == 'SMD Additional' || role == 'ASC'){
				isPromoter = 1;
			}
		}     

	</script>

	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->	
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/employee-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->
	
@endsection