@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Store
            <small>manage store</small>
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
        <a href="{{ url('store') }}">Store Management</a>
        <i class="fa fa-circle"></i>
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
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
	    <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-shopping-cart font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">
						@if (empty($data))
							ADD NEW STORE
						@else
							UPDATE STORE
						@endif
					</span>
				</div>

				<div id="backButton" class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
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
					        <div 
								@if (\Request::is('*store/edit*'))  
								  class="display-hide" 
								@endif
					        >
					          	<label class="col-sm-2">
					          			<input type="radio" name="store_status" value="new" id="rNew" checked> NEW 
				          			</label>
					          	<label class="col-sm-2">
					            	<input type="radio" name="store_status" value="old" id="rOld"> OLD
				            	</label>
			            	</div>	
				          <div class="col-sm-9">
				          	<div class="input-icon right" id="new_store">
				          		<i class="fa"></i>
				            	<input type="text" id="store_id" name="store_id" class="form-control" value="{{ @$data->store_id }}" placeholder="Input Store ID" readonly="readonly" />
				            </div>

				            <div class="input-icon right display-hide" style="margin-top: 10px" id="old_store">
				          		<i class="fa"></i>
				            	<select id="old_store_id" name="old_store_id"  class="select2select">
				            	</select>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id="store_name_1_div">
				          <label class="col-sm-2 control-label">Store Name 1</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_name_1" name="store_name_1" class="form-control" value="{{ @$data->store_name_1 }}" placeholder="Input Store Name 1" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id="store_name_2_div">
				          <label class="col-sm-2 control-label">Store Name 2</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_name_2" name="store_name_2" class="form-control" value="{{ @$data->store_name_2 }}" placeholder="Input Store Name 2" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Dedicate</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<select class="select2select" name="dedicate" id="dedicate" required>
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

				        <div id="latitude_longitude_div">
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

							<div class="form-group">
					          <label class="col-sm-2 control-label">Address</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="address" name="address" class="form-control" value="{{ @$data->address }}" disabled />
					            </div>
					          </div>
					        </div>
				        </div>

				        <div id="area_div">
					        <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">Distributor, Channel, District (Area)</span>
	                        	<hr>
	                        </div>

							<div class="form-group">
					          <label class="col-sm-2 control-label">Classification</label>
					          <div class="col-sm-9">

					          <div class="input-group" style="width: 100%;">

	                                <select class="select2select" name="classification" id="classification" required>
										<option value="New Store" {{ (@$data->role == 'New Store') ? "selected" : "" }}>New Store</option>
										<option value="Gold" {{ (@$data->role == 'Gold') ? "selected" : "" }}>Gold</option>
										<option value="Platinum" {{ (@$data->role == 'Platinum') ? "selected" : "" }}>Platinum</option>
										<option value="Silver" {{ (@$data->role == 'Silver') ? "selected" : "" }}>Silver</option>
										<option value="Don`t have any classification" {{ (@$data->role == 'Don`t have any classification') ? "selected" : "" }}>Don`t have any classification</option>
	                                </select>

	                                <span class="input-group-addon display-hide">
	                                	<i class="fa"></i>
	                                </span>

	              				</div>

					          </div>
					        </div>

							<div class="form-group">
							  <label class="col-sm-2 control-label">Distributor</label>
							  <div class="col-sm-9">

							  <div class="input-group" style="width: 100%;">

									<select class="select2select" name="distributor_ids[]" id="distributors" multiple="multiple" required></select>

									<span class="input-group-addon display-hide">
										<i class="fa"></i>
									</span>

								</div>

							  </div>
							</div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Sub Channel</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="subchannel_id" id="subchannel" required></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">District</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="district_id" id="district" required></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
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
		if(!($('input[name=_method]').val() == "PATCH")) {
            var url = "{{url('util/get-store-id')}}";
            $.get(url, function (data) {
                $('#store_id').val(data);
            });
        }
	</script>

    <script>
        var storeId = "{{ collect(request()->segments())->last() }}";

		$(document).ready(function () {
			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

	        $('#old_store_id').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")" + " - " + obj.dedicate}
	                })
	            }
	        }));

	    	$('#subchannel').select2(setOptions('{{ route("data.subchannel") }}', 'Sub Channel', function (params) {
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));   

	        $('#district').select2(setOptions('{{ route("data.district") }}', 'District', function (params) {
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#user').select2(setOptions('{{ route("data.employee") }}', 'Supervisor', function (params) {
	        	filters['role'] = 'Supervisor';
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#distributors').select2(setOptions('{{ route("data.distributor") }}', 'Distributor', function (params) {
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {
	                    return {id: obj.id, text: obj.code + " - " + obj.name }
	                })
	            }
	        }));

	        $('#classification').select2({
                width: '100%',
                placeholder: 'Classification'
            });

            $('#dedicate').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            // Set select2 if method PATCH            
	       setSelect2IfPatch($("#subchannel"), "{{ @$data->subchannel_id }}", "{{ @$data->subchannel->name }}");
	       setSelect2IfPatch($("#district"), "{{ @$data->district_id }}", "{{ @$data->district->name }}");
	       // setSelect2IfPatch($("#user"), "{{ @$data->user_id }}", "{{ @$data->user->name }}");

	       updateDistributor();


		});

		function updateDistributor(){
			var getDataUrl = "{{ url('util/storedist/') }}";

			$.get(getDataUrl + '/' + storeId, function (data) {
				if(data){

				    var element = $("#distributors");

                    select2Reset($('#distributors'));

                    $.each(data, function() {
                        setSelect2IfPatch(element, this.id, this.code + " - " + this.name);
                    });


            	}

        	})
		}

		$('input[type=radio][name=store_status]').change(function() {
	        if (this.value == 'old') {
	            $('#new_store').addClass('display-hide');
	            $('#store_name_1_div').addClass('display-hide');
	            $('#store_name_2_div').addClass('display-hide');
	            $('#latitude_longitude_div').addClass('display-hide');
	            $('#area_div').addClass('display-hide');
	            
	            $('#store_name_1').val('null');
	            $('#dedicate').prop('required',false);
	            $('#classification').prop('required',false);
	            $('#distributors').prop('required',false);
	            $("#subchannel").empty().append('<option value="id">- select Sub Channel -</option>').val('id').trigger('change');
	            $("#district").empty().append('<option value="id">- select District -</option>').val('id').trigger('change');


	            $('#old_store').removeClass('display-hide');
	        }
	        else if (this.value == 'new') {
	            $('#old_store').addClass('display-hide');

	            $('#store_name_1_div').removeClass('display-hide');
	            $('#store_name_2_div').removeClass('display-hide');
	            $('#latitude_longitude_div').removeClass('display-hide');
	            $('#area_div').removeClass('display-hide');
	            
	            $('#store_name_1').prop('aria-required',true);
	            $('#dedicate').prop('required',true);
	            $('#classification').prop('required',true);
	            $('#distributors').prop('required',true);

	            $('#new_store').removeClass('display-hide');
	        }
	    });

	</script>	

	
@endsection