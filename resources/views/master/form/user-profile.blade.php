@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Profile
            <small>edit profile</small>
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
        <span class="active">My Profile</span>
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
					<i class="fa fa-cog font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">
						MY PROFILE
					</span>
				</div>

	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_user" class="form-horizontal" action="{{ url('profile') }}" method="POST" enctype="multipart/form-data" files="true">
			        {{ csrf_field() }}

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
				          <label class="col-sm-2 control-label">Username</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="name" name="name" class="form-control" value="{{ @$data->name }}" placeholder="Input Username" disabled />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Email</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="email" name="email" class="form-control" value="{{ @$data->email }}" placeholder="Input Email" disabled />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Role</label>
				          <div class="col-sm-9">

				          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role" id="role" required disabled>
                                	<option value="DM" {{ (@$data->role == 'DM') ? "selected" : "" }}>DM</option>
                                	<option value="RSM" {{ (@$data->role == 'RSM') ? "selected" : "" }}>RSM</option>
                                	<option value="Admin" {{ (@$data->role == 'Admin') ? "selected" : "" }}>ADMIN</option>
                                	@if(Auth::user()->role == 'Master')
                                	<option value="Master" {{ (@$data->role == 'Master') ? "selected" : "" }}>Master Admin</option>
                                	@endif
                                </select>
                               	
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
				        </div>
				        <div class="form-group">
				          <label class="col-sm-2 control-label">Join Date</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="join_date" name="join_date" class="form-control" value="{{ @$data->join_date }}" placeholder="Join Date" disabled/>
				            </div>
				          </div>
				        </div>

                		@if(Auth::user()->role == 'Promoter' || Auth::user()->role == 'Promoter Additional' || Auth::user()->role == 'Promoter Event' || Auth::user()->role == 'Demonstrator MCC' || Auth::user()->role == 'Demonstrator DA' || Auth::user()->role == 'ACT'  || Auth::user()->role == 'PPE' || Auth::user()->role == 'BDT' || Auth::user()->role == 'Salesman Explorer' || Auth::user()->role == 'SMD' || Auth::user()->role == 'SMD Coordinator' || Auth::user()->role == 'HIC' || Auth::user()->role == 'HIE' || Auth::user()->role == 'SMD Additional' || Auth::user()->role == 'ASC')
							<div class="form-group">
					          <label class="col-sm-2 control-label">Grading</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="grading" name="grading" class="form-control" value="{{ @$data->grading }}" placeholder="Grading" disabled/>
					            </div>
					          </div>
					        </div>
               			@endif				        

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Certificate</label>
                          <div class="col-sm-9">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <textarea id="certificate" name="certificate" class="form-control"  placeholder=" Does not have a certificate. " rows="10" disabled>{{ @$data->certificate }}</textarea>
                            </div>
        					<!-- <p class="help-block"> * Please add "<b> , </b>" to separate certificate &nbsp || &nbsp tolong tambahkan tanda "<b> , </b>" untuk memisahkan certificate </p> -->
                            
                          </div>
                        </div>   

				        <!-- View for old image * PHOTO * -->
				        @if (!empty($data))
				        	<div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">PHOTO</span>
	                        	<hr>
	                        </div>

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
                        	<span class="caption-subject font-dark bold uppercase">Change Password</span>
                        	<hr>
                        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">New Password</label>
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
	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/user-profile-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->
    
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

		});

	</script>
@endsection