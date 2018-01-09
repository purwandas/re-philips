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
     
                                <select class="select2select" name="role" id="role" required>

                                	<!-- <option value="Promoter" {{ (@$data->role == 'Promoter') ? "selected" : "" }}>Promoter</option>
                                	<option value="Promoter Additional" {{ (@$data->role == 'Promoter Additional') ? 
                                	"selected" : "" }}>Promoter Additional</option>
                                	<option value="Promoter Event" {{ (@$data->role == 'Promoter Event') ? "selected" : "" }}>Promoter Event</option>
                                	<option value="Demonstrator MCC" {{ (@$data->role == 'Demonstrator MCC') ? "selected" : "" }}>Demonstrator MCC</option>
                                	<option value="Demonstrator DA" {{ (@$data->role == 'Demonstrator DA') ? "selected" : "" }}>Demonstrator DA</option>
                                	<option value="ACT" {{ (@$data->role == 'ACT') ? "selected" : "" }}>ACT</option>
                                	<option value="PPE" {{ (@$data->role == 'PPE') ? "selected" : "" }}>PPE</option>
                                	<option value="BDT" {{ (@$data->role == 'BDT') ? "selected" : "" }}>BDT</option>
                                	<option value="Salesman Explorer" {{ (@$data->role == 'Salesman Explorer') ? "selected" : "" }}>Salesman Explorer</option>
                                	<option value="SMD" {{ (@$data->role == 'SMD') ? "selected" : "" }}>SMD</option>
                                	<option value="SMD Coordinator" {{ (@$data->role == 'SMD Coordinator') ? "selected" : "" }}>SMD Coordinator</option>
                                	<option value="HIC" {{ (@$data->role == 'HIC') ? "selected" : "" }}>HIC</option>
                                	<option value="HIE" {{ (@$data->role == 'HIE') ? "selected" : "" }}>HIE</option>
                                	<option value="SMD Additional" {{ (@$data->role == 'SMD Additional') ? "selected" : "" }}>SMD Additional</option>
                                	<option value="ASC" {{ (@$data->role == 'ASC') ? "selected" : "" }}>ASC</option> -->

                                	<option value="Driver" {{ (@$data->role == 'Driver') ? "selected" : "" }}>Driver</option>
                                	<option value="Helper" {{ (@$data->role == 'Helper') ? "selected" : "" }}>Helper</option>
                                	<option value="PCE" {{ (@$data->role == 'PCE') ? "selected" : "" }}>PCE</option>
                                	<option value="RE Executive" {{ (@$data->role == 'RE Executive') ? "selected" : "" }}>RE Executive</option>
                                	<option value="RE Support" {{ (@$data->role == 'RE Support') ? "selected" : "" }}>RE Support</option>
                                	<option value="Supervisor" {{ (@$data->role == 'Supervisor') ? "selected" : "" }}>Supervisor</option>
                                	<option value="Trainer" {{ (@$data->role == 'Trainer') ? "selected" : "" }}>Trainer</option>
                                	<option value="Head Trainer" {{ (@$data->role == 'Head Trainer') ? "selected" : "" }}>Head Trainer</option>
                                	<option value="Supervisor Hybrid" {{ (@$data->role == 'Supervisor Hybrid') ? "selected" : "" }}>Supervisor Hybrid</option>
                                	<option value="DM" {{ (@$data->role == 'DM') ? "selected" : "" }}>DM</option>
                                	<option value="RSM" {{ (@$data->role == 'RSM') ? "selected" : "" }}>RSM</option>
                                	<option value="Admin" {{ (@$data->role == 'Admin') ? "selected" : "" }}>Admin</option>                                	
                                </select>
                               	
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

				        <div id="dedicateContent" class="display-hide form-group">
				          <label class="col-sm-2 control-label">Dedicate</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<select class="select2select" name="dedicate" id="dedicate" required>
				            		<option></option>
									<option value="DA" {{ (@$data->dedicate == 'DA') ? "selected" : "" }}>DA</option>
									<option value="PC" {{ (@$data->dedicate == 'PC') ? "selected" : "" }}>PC</option>
									<option value="MCC" {{ (@$data->dedicate == 'MCC') ? "selected" : "" }}>MCC</option>
									<option value="HYBRID" {{ (@$data->dedicate == 'HYBRID') ? "selected" : "" }}>HYBRID</option>
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
	     
	                                <select class="select2select" name="area" id="area"></select>
	                                
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
	     
	                                <select class="select2select" name="region" id="region"></select>
	                                
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
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
	                })
	            }
	        }));

	         $('#stores').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
	         	if ($('#role').val() == 'Supervisor' || $('#role').val() == 'Supervisor Hybrid') {
		        	filters['bySpv'] = $('#penampungUserId').val();
		        	filters['byDedicateSpv'] = $('#dedicate').val();
		        }
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
	                })
	            }
	        }));

	       	$('#role').select2({
                width: '100%',
                placeholder: 'Role'
            });

            $('#dedicate').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            setForm($('#role').val());

		});

		// Reset form
		function resetForm(){

			$('#area').removeAttr('required');			
			select2Reset($('#area'));
			$('#region').removeAttr('required');
			select2Reset($('#region'));

			$('#dmContent').children('.form-group').removeClass('has-error');
			$('#rsmContent').children('.form-group').removeClass('has-error');

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
		}

		// Set and init dm and rsm
		function setForm(role){

			resetForm();
			resetStore();

			if(role == 'DM'){
				$('#area').attr('required', 'required');
				setSelect2IfPatch($("#area"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				document.getElementById('areaTitle').innerHTML = "DM AREA";
				$('#dmContent').removeClass('display-hide');

				$('#dedicate').attr('required', 'required');
				// setSelect2IfPatch($("#dedicate"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				$('#dedicateContent').removeClass('display-hide');
			}else{
				$('#dedicateContent').addClass('display-hide');
			}

			if(role == 'Trainer'){
				$('#area').attr('required', 'required');
				setSelect2IfPatch($("#area"), "{{ @$data->trainerArea->area_id }}", "{{ @$data->trainerArea->area->name }}");
				document.getElementById('areaTitle').innerHTML = "TRAINER AREA";
				$('#dmContent').removeClass('display-hide');
			}

			if(role == 'RSM'){
				$('#region').attr('required', 'required');
				setSelect2IfPatch($("#region"), "{{ @$data->rsmRegion->region_id }}", "{{ @$data->rsmRegion->region->name }}");
				$('#rsmContent').removeClass('display-hide');
			}

			if (role == 'Supervisor' || role == 'Supervisor Hybrid') {
				$('#storeContent').removeClass('display-hide');				
				$('#multipleStoreContent').removeClass('display-hide');			
	            $('#stores').attr('required', 'required');
	            $('#dedicateContent').removeClass('display-hide');
			}

			if(!checkAdmin()){
				$('#nik').attr('required', 'required');
			}

			if(!checkPromoter()){
				$('input[type=radio][name=status]').prop('checked', false);
			}

			if(checkPromoter()){
				$('#statusCheck').attr('required', 'required');

				if(role == 'Salesman Explorer'){
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

			if($('input[name=_method]').val() == "PATCH" && checkPromoter()){
				updateStore();
			}

			var role = $('#role').val();
			if($('input[name=_method]').val() == "PATCH" && (role == 'Supervisor' || role == 'Supervisor Hybrid')){
				updateStoreSpv();
			}			
		}

		function updateStore(){
			var oldStatus = "{{ @$data->status }}";
			var getDataUrl = "{{ url('util/empstore/') }}";
			var status = $('input[type=radio][name=status]:checked').val();

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#store");
                    if(status == 'mobile'){
                    	element = $("#stores");
                    }

                    select2Reset($('#store'));
                    select2Reset($('#stores'));

                    if(oldStatus == status){                    	
	                    $.each(data, function() {
							setSelect2IfPatch(element, this.id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
						});
                	}                	

            	}	

        	})
		}

		function updateStoreSpv(){
			var oldStatus = "{{ @$data->status }}";
			var getDataUrl = "{{ url('util/spvstore/') }}";
			var status = $('input[type=radio][name=status]:checked').val();

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#stores");

                    select2Reset(element);

	                    $.each(data, function() {
							setSelect2IfPatch(element, this.id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
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
			var role = $('#role').val();

			if(role == 'DM' || role == 'RSM' || role == 'Admin'){
				return true;
			}

			return false;
		} 

		// Check promoter group
		function checkPromoter(){
			var role = $('#role').val();

			if(role == 'Promoter' || role == 'Promoter Additional' || role == 'Promoter Event' || role == 'Demonstrator MCC' || role == 'Demonstrator DA' || role == 'ACT'  || role == 'PPE' || role == 'BDT' || role == 'Salesman Explorer' || role == 'SMD' || role == 'SMD Coordinator' || role == 'HIC' || role == 'HIE' || role == 'SMD Additional' || role == 'ASC'){
				return true;
			}

			return false;
		} 

		/*
		 * Select2 change
		 *
		 */ 
		$(document.body).on("change","#role",function(){

		    setForm($('#role').val());
		    
		});

		// On Change status
		$(document).ready(function(){
		    $('input[type=radio][name=status]').change(function() {
		        resetStore();
		        setStore(this.value);
		    });
		});

	</script>	
@endsection