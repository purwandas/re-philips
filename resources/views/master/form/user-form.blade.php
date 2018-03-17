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
        <a href="{{ url('usernon') }}">Employee Management</a>
        <i class="fa fa-circle"></i>
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
							ADD NEW EMPLOYEE
						@else
							UPDATE EMPLOYEE
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('usernon') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_user" class="form-horizontal" action="{{ url('usernon', @$data->id) }}" method="POST">	        	
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
                        	<input type="hidden" name="penampungUserId" id="penampungUserId">
                        	<hr>
                        </div>

                        <div class="form-group">
				          <label class="col-sm-2 control-label">Role</label>
				          <div class="col-sm-9">

				          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role_id" id="role" required>
                                	<option></option>                                	
                                </select>
                               	<input type="hidden" id="selectedRole" name="selectedRole">
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
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
				            	<input type="text" id="name" name="name" class="form-control" value="{{ @$data->name }}" placeholder="Input Name" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Join Date</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="join_date" name="join_date" class="form-control" value="{{ @$data->join_date }}" placeholder="Join Date" />
				            </div>
				          </div>
				        </div>

						<div id="statusSpv" class="display-hide">
					        <div class="form-group">
	                            <label class="control-label col-md-2">Status
	                            </label>
	                            <div class="col-md-9">
	                                <div class="mt-radio-list" data-error-container="#form_employee_status_error">
	                                    <label class="mt-radio">
	                                        <input id="statusSpvCheck" type="radio" name="status_spv" value="Promoter" {{ (@$data->status == 'Promoter') ? "checked" : "" }}> Promoter
	                                        <span></span>
	                                    </label>
	                                    <label class="mt-radio">
	                                        <input id="statusSpvCheck2" type="radio" name="status_spv" value="Demonstrator" {{ (@$data->status == 'Demonstrator') ? "checked" : "" }}> Demonstrator
	                                        <span></span>
	                                    </label>
	                                </div>
	                                <div id="form_employee_status_error"> </div>
	                            </div>
	                        </div>
	                    </div>

	                    <div id="dedicateContent" class="display-hide form-group">
				          <label class="col-sm-2 control-label">Dedicate</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<select class="select2select" name="dedicate" id="dedicate">
				            		<option></option>
									<option value="DA" 
										{{ (@$spvDedicate->dedicate == 'DA') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'DA') ? "selected" : "" }}
										>
										DA
									</option>
									<option value="PC" 
										{{ (@$spvDedicate->dedicate == 'PC') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'PC') ? "selected" : "" }}
										>
										PC
									</option>
									<option value="MCC" 
										{{ (@$spvDedicate->dedicate == 'MCC') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'MCC') ? "selected" : "" }}
										>
										MCC
									</option>
									<option value="HYBRID" 
										{{ (@$spvDedicate->dedicate == 'HYBRID') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'HYBRID') ? "selected" : "" }}
										>
										HYBRID
									</option>
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
	                                        <input id="statusCheck" type="radio" name="status" value="stay" {{ (@$data->status == 'stay') ? "checked" : "" }}> Stay
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

	                    <!-- BEGIN STORE DETAILS -->
	                    <div id="storeContent" class="display-hide">
		                    <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">STORE</span>
	                        	<hr>
	                        </div>

	                        <div id="oneStoreContent" class="display-hide">
		                        <div class="form-group">
		                          <label class="col-sm-2 control-label">Employee's Store</label>
		                          <div class="col-sm-9">

		                          <div class="input-group" style="width: 100%;">
		     
		                                <select class="select2select" name="store_id" id="store" required="required"></select>
		                                
		                                <span class="input-group-addon display-hide">
		                                    <i class="fa"></i>
		                                </span>

		                            </div>
		                            
		                          </div>
		                        </div>
	                        </div>

	                        <div id="multipleStoreContent" class="display-hide">
		                        <div class="form-group">
		                          <label class="col-sm-2 control-label">Employee's Store</label>
		                          <div class="col-sm-9">

		                          <div class="input-group" style="width: 100%;">
		     
		                                <select class="select2select" name="store_ids[]" id="stores" multiple="multiple" required="required"></select>
		                                
		                                <span class="input-group-addon display-hide">
		                                    <i class="fa"></i>
		                                </span>

		                            </div>
		                            
		                          </div>
		                        </div>
	                        </div>
	                    </div>
	                    <!-- END STORE DETAILS -->


				        <!-- BEGIN DM DETAILS -->				       
				        <div id="dmContent" class="display-hide">
					        <div class="caption padding-caption">
	                        	<span id="areaTitle" class="caption-subject font-dark bold uppercase">DM AREA</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Area</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="area[]" id="area" multiple="multiple"></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>
	                    </div>

                        <!-- END DM DETAILS -->

                        <!-- BEGIN RSM DETAILS -->				       
				        <div id="rsmContent" class="display-hide">
					        <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">RSM REGION</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Region</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="region[]" id="region" multiple="multiple"></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>
	                    </div>
                        <!-- END RSM DETAILS -->

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
				          <label class="col-sm-2 control-label">Email</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="email" name="email" class="form-control" value="{{ @$data->email }}" placeholder="Input Email" />
				            </div>
				          </div>
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
    	var userId = "{{ collect(request()->segments())->last() }}";

		$(document).ready(function () {

			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

			$('#penampungUserId').val(userId);

	    	$('#area').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#region').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {            
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

	         $('#stores').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
	         	var selectedRolev = $('#selectedRole').val();
				selectedRolev = selectedRolev.split('`');
	         	if (selectedRolev[1] == 'Supervisor' || selectedRolev[1] == 'Supervisor Hybrid') {
		        	filters['bySpvNew'] = $('#penampungUserId').val();
		        	var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
		        	if (statusSpv == "Demonstrator") {
		        		filters['byDedicateSpv'] = "DA";
		        	}else{
		        		filters['byDedicateSpv'] = $('#dedicate').val();
		        		console.log("tes: "+$('#dedicate').val());
		        	}
		        }
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    if(obj.store_name_2 != null){
                            return {id: obj.id+'`'+obj.store_id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
	                    return {id: obj.id+'`'+obj.store_id, text: obj.store_id + " - " + obj.store_name_1}
	                })
	            }
	        }));

	       	$('#role').select2(setOptions('{{ route("data.role") }}', 'Role', function (params) {
	       		filters['nonPromoterGroup'] = '1';
	       		@if (Auth::user()->role->role_group != 'Master')
		          filters['nonMaster'] = '1';
		        @endif
	            return filterData('role', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {            
	                    return {id: obj.id+"`"+obj.role_group, text: obj.role}
	                })
	            }
	        }));
			setSelect2IfPatch($("#role"), "{{ @$data->role->id }}`{{ @$data->role->role_group }}", "{{ @$data->role->role }}");
			$('#selectedRole').val("{{ @$data->role->id }}`{{ @$data->role->role_group }}");

            $('#dedicate').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            setForm($('#selectedRole').val());

		});

		// Reset form
		function resetForm(){

			$('#area').removeAttr('required');			
			select2Reset($('#area'));
			$('#region').removeAttr('required');
			select2Reset($('#region'));

			$('#dmContent').children('.form-group').removeClass('has-error');
			$('#rsmContent').children('.form-group').removeClass('has-error');
			// $('#dedicateContent').children('.form-group').removeClass('has-error');

			$('#dmContent').addClass('display-hide');
			$('#rsmContent').addClass('display-hide');

			// NIK
			$('#nik').removeAttr('required');
			$('#nik').closest('.form-group').removeClass('has-error');
			var icon = $('#nik').parent('.input-icon').children('i');
            icon.removeClass("fa-warning").removeClass("fa-check");

            // STATUS
            $('#statusContent').addClass('display-hide');
            $('#statusCheck').removeAttr('required');
            $('#statusSpv').addClass('display-hide');
            $('#statusSpvCheck').removeAttr('required');

            //SPV
            $('#dedicate').removeAttr('required');
	       	$('#statusSpvCheck').removeAttr('required');
	       	$( "#statusSpvCheck" ).prop( "checked",{{ (@$spvDedicate->user_id != '') ? "true" : "false" }} );
	       	$( "#statusSpvCheck2" ).prop( "checked",{{ (@$spvDemo->user_id != '') ? "true" : "false" }} );

		}

		// Set and init dm and rsm
		function setForm(role){

			resetForm();
			resetStore();

			role = role.split('`');

			if(role[1] == 'DM'){
				$('#area').attr('required', 'required');
				updateArea();
				// setSelect2IfPatch($("#area"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				// setSelect2IfPatch($("#dedicate"), "{{ @$data->dmArea->dedicate }}", "{{ @$data->dmArea->dedicate }}");
				document.getElementById('areaTitle').innerHTML = "DM AREA";
				$('#dmContent').removeClass('display-hide');

				// $('#dedicate').attr('required', 'required');
				// setSelect2IfPatch($("#dedicate"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				// $('#dedicateContent').removeClass('display-hide');
			}else{
				$('#dedicateContent').addClass('display-hide');
			}

			if(role[1] == 'Trainer'){
				$('#area').attr('required', 'required');
				// setSelect2IfPatch($("#area"), "{{ @$data->trainerArea->area_id }}", "{{ @$data->trainerArea->area->name }}");
				updateAreaTrainer();
				document.getElementById('areaTitle').innerHTML = "TRAINER AREA";
				$('#dmContent').removeClass('display-hide');
			}

			if(role[1] == 'RSM'){
				$('#region').attr('required', 'required');
				// setSelect2IfPatch($("#region"), "{{ @$data->rsmRegion->region_id }}", "{{ @$data->rsmRegion->region->name }}");
				updateRegion();
				$('#rsmContent').removeClass('display-hide');
			}

			if (role[1] == 'Supervisor' || role[1] == 'Supervisor Hybrid') {

				$('#storeContent').removeClass('display-hide');				
				$('#multipleStoreContent').removeClass('display-hide');			
	            $('#stores').attr('required', 'required');
	            $('#dedicateContent').removeClass('display-hide');

	            if (role[1] == 'Supervisor') {
			       	$('#statusSpv').removeClass('display-hide');
			       	$('#dedicate').attr('required', 'required');
			       	$('#statusSpvCheck').attr('required', 'required');
			       	var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
			       	if (statusSpv) {
				       	if (statusSpv == "Demonstrator") 
				       	{
				       		$('#dedicateContent').addClass('display-hide');
				            $('#dedicate').removeAttr('required');
				       	}
				    }
                }
                
			}

			if(!checkAdmin()){
				$('#nik').attr('required', 'required');
			}

			if(!checkPromoter()){
				$('input[type=radio][name=status]').prop('checked', false);
			}

			if(checkPromoter()){
				$('#statusCheck').attr('required', 'required');

				if(role[1] == 'Salesman Explorer'){
					$('input[type=radio][name=status][value=mobile]').attr('checked', 'checked');
//				    $('#statusContent').removeClass('display-hide');
				}else{
					$('#statusContent').removeClass('display-hide');
				}


				//Set Store
			    var status = $('input[type=radio][name=status]:checked').val();

			    if(!(typeof(status) === 'undefined')){
			    	setStore(status);
		    	}

			}
		}

		/* STORE METHOD */

		// Reset store
		function resetStore(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');
			console.log("role"+selectedRolev[1]);

			$('#store').removeAttr('required');
			$('#stores').removeAttr('required');

			$('#storeContent').children('.form-group').removeClass('has-error');
			$('#storeContent').each(function(){
                $(this).children('.form-group').removeClass('has-error');
            });

   			$('#oneStoreContent').children('.form-group').removeClass('has-error');
   			$('#multipleStoreContent').children('.form-group').removeClass('has-error');
			$('#storeContent').addClass('display-hide');
			$('#oneStoreContent').addClass('display-hide');
			$('#multipleStoreContent').addClass('display-hide');

			// Reset selection
			if($('input[name=_method]').val() != "PATCH" || !checkPromoter()){
				select2Reset($('#store'));
				select2Reset($('#stores'));
			}			

			
			if($('input[name=_method]').val() == "PATCH" && (selectedRolev[1] == 'Supervisor' || selectedRolev[1] == 'Supervisor Hybrid')){
				select2Reset($('#stores'));
				updateStoreSpv();
				updateStoreSpvDemo();
			}			
		}

		function updateStoreSpv(){
			var getDataUrl = "{{ url('util/spvstore/') }}";

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#stores");
                    $.each(data, function() {
                    
						console.log('patch2#'+this.id);

						if(this.store_name_2 != null){
                        	setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
                    	}else{
						setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1);
						}

					});

            	}	

        	})
		}

		function updateStoreSpvDemo(){
			var getDataUrl = "{{ url('util/spvdemostore/') }}";

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#stores");
                    $.each(data, function() {

						if(this.store_name_2 != null){
                        	setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
                    	}else{
							setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1);
						}
					});

            	}	

        	})
		}
		

		// Set and init store select2
		function setStore(value){			

			$('#storeContent').removeClass('display-hide');				

			if(value == 'stay'){			
				$('#oneStoreContent').removeClass('display-hide');
	            $('#store').attr('required', 'required');
			}else if(value == 'mobile'){	
				$('#multipleStoreContent').removeClass('display-hide');			
	            $('#stores').attr('required', 'required');
			}			
		}		

		// Check admin
		function checkAdmin(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');

			if(selectedRolev[1] == 'DM' || selectedRolev[1] == 'RSM' || selectedRolev[1] == 'Admin' || selectedRolev[1] == 'Trainer' || selectedRolev[1] == 'Head Trainer'){
				return true;
			}

			return false;
		} 

		// Check promoter group
		function checkPromoter(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');

			if(selectedRolev[1] == 'Promoter' || selectedRolev[1] == 'Promoter Additional' || selectedRolev[1] == 'Promoter Event' || selectedRolev[1] == 'Demonstrator MCC' || selectedRolev[1] == 'Demonstrator DA' || selectedRolev[1] == 'ACT'  || selectedRolev[1] == 'PPE' || selectedRolev[1] == 'BDT' || selectedRolev[1] == 'Salesman Explorer' || selectedRolev[1] == 'SMD' || selectedRolev[1] == 'SMD Coordinator' || selectedRolev[1] == 'HIC' || selectedRolev[1] == 'HIE' || selectedRolev[1] == 'SMD Additional' || selectedRolev[1] == 'ASC'){
				return true;
			}

			return false;
		} 


		function initDateTimePicker(){

            // Filter Month
            $('#join_date').datetimepicker({
                format: "yyyy-mm-dd",
                startView: "2",
                minView: "2",
                autoclose: true,
            });
            // Set to Month now
            $('#join_date').val(
            	{{( @$data->join_date ) ? "" : "moment().format(" }}
            	'{{( @$data->join_date ) ? @$data->join_date : "YYYY-MM-DD" }}'
            	{{( @$data->join_date ) ? "" : ")" }}
            );

        }

        function updateRegion(){
			var getDataUrl = "{{ url('util/rsmregion/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#region"), this.id, this.name);
						});

            	}	

        	})
		}

		function updateArea(){
			var getDataUrl = "{{ url('util/dmarea/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#area"), this.id, this.name);
						});

            	}	

        	})
		}

		function updateAreaTrainer(){
			var getDataUrl = "{{ url('util/trainerarea/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#area"), this.id, this.name);
						});

            	}	

        	})
		}

		/*
		 * Select2 change
		 *
		 */ 
		$(document.body).on("change","#role",function(){

		    setForm($('#selectedRole').val());
		    
		});

		$(document.body).on("change","#statusSpvCheck2",function(){
			var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
	       	if (statusSpv) {
		       	if (statusSpv == "Demonstrator")
		       	{
		       		select2Reset($('#stores'));
		       		$('#dedicateContent').addClass('display-hide');
		            $('#dedicate').removeAttr('required');
		       	}
		    }
		});

		$(document.body).on("change","#statusSpvCheck",function(){
			var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
	       	if (statusSpv) {
		       	if (statusSpv == "Promoter") 
		       	{
		       		select2Reset($('#stores'));
		       		$('#dedicateContent').removeClass('display-hide');
		            $('#dedicate').attr('required','required');
		       	}
		    }
		});

		$(document).ready(function(){
			
			initDateTimePicker();

			// On Change status
		    // $('input[type=radio][name=status]').change(function() {
		    //     resetStore();
		    //     setStore(this.value);
		    // });

		    // On Change Role
		    $('#role').change(function() {
		        $("#selectedRole").val($("#role option:selected").val());
		    });

		    // $('div').removeClass('display-hide');
		});

	</script>	
@endsection