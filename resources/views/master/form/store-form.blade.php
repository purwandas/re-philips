@extends('layouts.app')

@section('header')
<h1 class="page-title"> Store
	<small>Manage Store</small>
</h1>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="{{ url('/') }}">Home</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>			
			<a href="{{ url('store') }}">Store Management</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>
				@if (empty($data))
					Add New Store
				@else
					Update Store
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
					<i class="fa fa-shopping-cart font-green"></i>
					<span class="caption-subject font-green sbold uppercase">
						@if (empty($data))
							ADD NEW STORE
						@else
							UPDATE STORE
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('store') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_store" class="form-horizontal" action="{{ url('store', @$data->id) }}" method="POST">	        	
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
				          <label class="col-sm-2 control-label">Store ID</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_id" name="store_id" class="form-control" value="{{ @$data->store_id }}" placeholder="Input Store ID" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Store Name 1</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_name_1" name="store_name_1" class="form-control" value="{{ @$data->store_name_1 }}" placeholder="Input Store Name 1" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Store Name 2</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_name_2" name="store_name_2" class="form-control" value="{{ @$data->store_name_2 }}" placeholder="Input Store Name 2" />
				            </div>
				          </div>
				        </div>			

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">Longitude & Latitude</span>
                        	<hr>
                        </div>

                        <div class="form-group">
				          <label class="col-sm-2 control-label">Longitude</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="longitude" name="longitude" class="form-control" value="{{ @$data->longitude }}" disabled />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Latitude</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="latitude" name="latitude" class="form-control" value="{{ @$data->latitude }}" disabled />
				            </div>
				          </div>
				        </div>	        

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">Channel, Account, Area</span>
                        	<hr>
                        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Channel</label>
				          <div class="col-sm-9">

				          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="channel" id="channel" required>
                                	<option value="Modern Retail" {{ (@$data->role == 'Modern Retail') ? "selected" : "" }}>Modern Retail</option>
                                	<option value="Traditional Retail" {{ (@$data->role == 'Traditional Retail') ? "selected" : "" }}>Traditional Retail</option>
                                	<option value="Mother Child & Care" {{ (@$data->role == 'Mother Child & Care') ? "selected" : "" }}>Mother Child & Care</option>                                	
                                </select>
                               	
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
				        </div>	

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Account</label>
                          <div class="col-sm-9">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="account_id" id="account" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Area RE Apps</label>
                          <div class="col-sm-9">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="areaapp_id" id="areaapp" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>

                        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">Supervisor</span>
                        	<hr>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Supervisor</label>
                          <div class="col-sm-9">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="employee_id" id="employee" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

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
    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/store-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->

    <script>
		$(document).ready(function () {
			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

	    	$('#account').select2(setOptions('{{ route("data.account") }}', 'Account', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));   

	        $('#areaapp').select2(setOptions('{{ route("data.areaapp") }}', 'Area RE Apps', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#employee').select2(setOptions('{{ route("data.employee") }}', 'Supervisor', function (params) {            
	        	filters = {};
	        	filters['role'] = 'Supervisor';
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));	   

	       	$('#channel').select2({
                width: '100%',
                placeholder: 'Channel'
            })

            // Set select2 => 'country' if method PATCH
	       setSelect2IfPatch($("#account"), "{{ @$data->account_id }}", "{{ @$data->account->name }}");	
	       setSelect2IfPatch($("#areaapp"), "{{ @$data->areaapp_id }}", "{{ @$data->areaapp->name }}");
	       setSelect2IfPatch($("#employee"), "{{ @$data->employee_id }}", "{{ @$data->employee->name }}");


		});    

	</script>	
@endsection