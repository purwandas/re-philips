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
			            	</div>	
				          <div class="col-sm-9">
				          	<div class="input-icon right" id="new_store">
				          		<i class="fa"></i>
				            	<input type="text" id="store_id" name="store_id" class="form-control" value="{{ @$data->store_id }}" placeholder="Input Store ID" readonly="readonly" />
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
				          <label class="col-sm-2 control-label">Customer Code</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="store_name_2" name="store_name_2" class="form-control" value="{{ @$data->store_name_2 }}" placeholder="Input Customer Code" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id="no_telp_toko_div">
				          <label class="col-sm-2 control-label">Store Phone Number</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="no_telp_toko" name="no_telp_toko" class="form-control" value="{{ @$data->no_telp_toko }}" placeholder="Input Store Phone Number" />
				            </div>
				          </div>
				        </div>
						
						<div class="form-group" id="no_telp_pemilik_toko_div">
				          <label class="col-sm-2 control-label">Store Owner Phone Number</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="no_telp_pemilik_toko" name="no_telp_pemilik_toko" class="form-control" value="{{ @$data->no_telp_pemilik_toko }}" placeholder="Input Store Owner Phone Number" />
				            </div>
				          </div>
				        </div>

						<div class="form-group" id="kepemilikan_toko_div">
				          <label class="col-sm-2 control-label">Store Ownership Status</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="kepemilikan_toko" name="kepemilikan_toko" class="form-control" value="{{ @$data->kepemilikan_toko }}" placeholder="Input Ownership Status" />
				            </div>
				          </div>
				        </div>

						<div class="form-group" id="lokasi_toko_div">
				          <label class="col-sm-2 control-label">Store Location</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
					            	<select class="select2select" name="lokasi_toko" id="lokasi_toko" required>
	                                	<option></option>
										<option value="Mall">Mall</option>
										<option value="ITC">ITC</option>
										<option value="Pasar">Pasar</option>
	                                </select>
				            </div>
				          </div>
				        </div>

						<div class="form-group" id="kondisi_toko_div">
				          <label class="col-sm-2 control-label">Store Condition</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            		<select class="select2select" name="kondisi_toko" id="kondisi_toko" required>
	                                	<option></option>
										<option value="Ada AC">Ada AC</option>
										<option value="Tidak Ada AC">Tidak Ada AC</option>
	                                </select>
				            </div>
				          </div>
				        </div>

						<div class="form-group" id="tipe_transaksi_div">
				          <label class="col-sm-2 control-label">Store Transaction Type</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            		<select class="select2select" name="tipe_transaksi" id="tipe_transaksi" required>
	                                	<option></option>
										<option value="Transaksi via Mesin/Kasir">Transaksi via Mesin/Kasir</option>
										<option value="Nota Manual">Nota Manual</option>
	                                </select>
				            </div>
				          </div>
				        </div>

				        <div class="form-group" id="tipe_transaksi_2_div">
				          <label class="col-sm-2 control-label">Store Transaction Type 2</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            		<select class="select2select" name="tipe_transaksi_2" id="tipe_transaksi_2" required>
	                                	<option></option>
										<option value="Konsumen Langsung">Konsumen Langsung</option>
										<option value="Online">Online</option>
										<option value="Grosir">Grosir</option>
	                                </select>
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
					            	<input type="text" id="longitude" name="longitude" class="form-control" value="{{ @$data->longitude }}"  />
					            </div>
					          </div>
					        </div>

					        <div class="form-group">
					          <label class="col-sm-2 control-label">Latitude</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="latitude" name="latitude" class="form-control" value="{{ @$data->latitude }}"  />
					            </div>
					          </div>
					        </div>

							<div class="form-group">
					          <label class="col-sm-2 control-label">Address</label>
					          <div class="col-sm-9">
					          	<div class="input-icon right">
					          		<i class="fa"></i>
					            	<input type="text" id="address" name="address" class="form-control" value="{{ @$data->address }}"  />
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
	                                	<option></option>
										<option value="New Store">New Store</option>
										<option value="Gold">Gold</option>
										<option value="Platinum">Platinum</option>
										<option value="Silver">Silver</option>
										<option value="Bronze">Bronze</option>
										<option value="Risna">Risna</option>
										<option value="Don`t have classification">Don`t have classification</option>
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

									<select class="select2select" name="distributor_ids[]" id="distributors" multiple="multiple"></select>

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
	     
	                                <select class="select2select" name="subchannel_id" id="subchannel"></select>
	                                
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

            $('#lokasi_toko').select2({
                width: '100%',
                placeholder: 'Store Location'
            });

            $('#tipe_transaksi_2').select2({
                width: '100%',
                placeholder: 'Transaction Type (2)'
            });

            $('#tipe_transaksi').select2({
                width: '100%',
                placeholder: 'Transaction Type'
            });

            $('#kondisi_toko').select2({
                width: '100%',
                placeholder: 'Store Condition'
            });

            // Set select2 if method PATCH            
	       setSelect2IfPatch($("#subchannel"), "{{ @$data->subchannel_id }}", "{{ @$data->subchannel->name }}");
	       setSelect2IfPatch($("#district"), "{{ @$data->district_id }}", "{{ @$data->district->name }}");
	       setSelect2IfPatch($("#classification"), "{{ @$data->classification }}", "{{ @$data->classification }}");

	       setSelect2IfPatch($("#lokasi_toko"), "{{ @$data->lokasi_toko }}", "{{ @$data->lokasi_toko }}");
	       setSelect2IfPatch($("#tipe_transaksi_2"), "{{ @$data->tipe_transaksi_2 }}", "{{ @$data->tipe_transaksi_2 }}");
	       setSelect2IfPatch($("#tipe_transaksi"), "{{ @$data->tipe_transaksi }}", "{{ @$data->tipe_transaksi }}");
	       setSelect2IfPatch($("#kondisi_toko"), "{{ @$data->kondisi_toko }}", "{{ @$data->kondisi_toko }}");
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

	</script>	

	
@endsection