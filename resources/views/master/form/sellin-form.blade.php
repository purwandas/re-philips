@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Sell In
            <small>manage sell in</small>
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
        <a href="{{ url('sellin') }}">Sell In Management</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
		<span>
			@if (empty($data))
				Add More Sell In
			@else
				Update Sell In
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
					<i class="fa fa-newspaper-o font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">
						@if (empty($data))
							ADD MORE Sell In
						@else
							UPDATE Sell In
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('sellin') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_sellin" class="form-horizontal" action="{{ url('sellin', @$data->id) }}" method="POST">	        	
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
                          <label class="col-sm-3 control-label">Store</label>
                          <div class="col-sm-8">
                            <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="store_id" id="store" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                          </div>
                        </div>

                        <div id="itemList" style="margin:0;padding:0;">
                        	<div class="note" style="margin:0;padding:0;">

	                          <div class="form-group">
	                            <label class="col-sm-3 control-label">Product</label>
	                            <div class="col-sm-8">
	                            <div class="input-group" style="width: 100%;">
	                                  <select class="select2select" name="product_id[]" id="product" required></select>
	                                  <span class="input-group-addon display-hide">
	                                      <i class="fa"></i>
	                                  </span>
	                              </div>
	                              
	                            </div>
	                          </div> 
	                           <div class="form-group">
	                            <label class="col-sm-3 control-label">Quantity</label>
	                            <div class="col-sm-8">
	                              <div class="input-icon right">
	                                  <i class="fa"></i>
	                                  <input type="number" id="quantity" name="quantity[]" class="form-control" placeholder="Input Product Quantity" data-tooltip="true" />
	                              </div>
	                            </div>
	                          </div>
	                        
	                        </div>
                        </div>

                        

				         <div class="form-group" style="padding-top: 15pt;">
				          <div class="col-sm-9 col-sm-offset-2">
				            <button type="button" class="btn btn-primary green" id="addItem">More Product</button>
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
    <script src="{{ asset('js/handler/sellin-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->
    
    <script id="scriptId">
    	var quizId = "{{ collect(request()->segments())->last() }}";
    	var index = 0;
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
                        return {id: obj.id, text: obj.store_id + ' - ' + obj.store_name_1 + ' (' + obj.store_name_2 + ')'}
                    })
                }
            }));

	        initSelect2Product();

            $("#addItem").click(function(){
            	var myRoute = "data.store";
		        $("#itemList").append('<div class="note" style="margin:0;padding:0;"><div class="form-group"><label class="col-sm-3 control-label">Product</label><div class="col-sm-8"><div class="input-group" style="width: 100%;"><select class="select2select" name="product_id[]" id="product'+index+'" required></select><span class="input-group-addon display-hide"><i class="fa"></i></span></div></div></div> <div class="form-group"><label class="col-sm-3 control-label">Quantity</label><div class="col-sm-8"><div class="input-icon right"><i class="fa"></i><input type="number" id="quantity" name="quantity[]" class="form-control" placeholder="Input Product Quantity" data-tooltip="true" /></div></div></div></div>');
		        	
		        	var productSelect2 = "$(document).ready(function () { "+
		        	"$('#product"+index+"').";
		        	var productSelect22 = "select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {"+
			                "return filterData('product', params.term);"+
			            "}, function (data, params) {"+
			                "return {"+
			                    ".results: $.map(data, function (obj) {"+
			                        "return {id: obj.id, text: obj.name}"+
			                    "})"+
			                "}"+
			            "}));"+
		        	" });";
		        	var escape = productSelect22.replace(/[\"&<>]/g);
		        $('#scriptId').append(productSelect2 + escape );
		        // $('#scriptId').append("kampret");
		        	
		        index++;
		    });

		});

		function initSelect2Product(){
			$('#product').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product1').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product2').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product3').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product4').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product5').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product6').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product7').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product8').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#product9').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));
		}


	</script>

@endsection
