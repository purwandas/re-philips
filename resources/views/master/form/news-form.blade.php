@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>News
            <small>manage news</small>
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
        <a href="{{ url('news') }}">News Management</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
		<span>
			@if (empty($data))
				Add More News
			@else
				Update News
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
					<i class="fa fa-newspaper-o font-green"></i>
					<span class="caption-subject font-green sbold uppercase">
						@if (empty($data))
							ADD MORE NEWS
						@else
							UPDATE NEWS
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('news') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_news" class="form-horizontal" action="{{ url('news', @$data->id) }}" method="POST">	        	
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
				          <label class="col-sm-2 control-label">Sender</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="from" name="from" class="form-control" value="{{ @$data->from }}" placeholder="Input Sender" />
				            </div>
				          </div>
				        </div>

				         <div class="form-group">
				          <label class="col-sm-2 control-label">Subject</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="subject" name="subject" class="form-control" value="{{ @$data->subject }}" placeholder="Input Subject" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				        <label class="control-label col-md-2">Content
                                                </label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="content" name="content" rows="6" data-error-container="#ckeditor_error" >{{ @$data->content  }}</textarea>
                                                    <div id="ckeditor_error"> </div>
                                                </div>  
				        </div>

				        <!-- <div class="form-group">
				        	<label class="control-label col-md-2">WYSIHTML5 Editor
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-9">
                                                    <textarea class="wysihtml5 form-control" rows="6" name="content" data-error-container="#editor1_error"></textarea>
                                                    <div id="editor1_error"> </div>
                                                </div> -->
				        <!-- </div> -->

				        <div class="form-group">
                            <label class="control-label col-md-2">Target                               
                            </label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline" data-error-container="#form_news_target_error">
                                    <label class="mt-radio">
                                        <input type="radio" name="target_type" value="All" checked="checked" {{ (@$data->target_type == '1') ? "checked" : "" }}> All
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="target_type" value="Area" {{ (@$data->target_type == 'Area') ? "checked" : "" }}> Area
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="target_type" value="Store" {{ (@$data->target_type == 'Store') ? "checked" : "" }}> Store
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="target_type" value="Promoter" {{ (@$data->target_type == 'Promoter') ? "checked" : "" }}> Promoter
                                        <span></span>
                                    </label>
                                </div>
                                <div id="form_news_target_error"> </div>
                            </div>
                        </div>

	                    <div id="targetContent">
		                    <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">TARGET DETAILS</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group" id="areaGroup">
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

	                        <div class="form-group" id="storeGroup">
	                          <label class="col-sm-2 control-label">Store</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="store[]" id="store" multiple="multiple"></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>	                        

	                        <div class="form-group" id="promoterGroup">
	                          <label class="col-sm-2 control-label">Promoter</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="employee[]" id="employee" multiple="multiple"></select>
	                                
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
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/news-handler.js') }}" type="text/javascript"></script>
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
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
	                })
	            }
	        })); 

	        $('#area').select2(setOptions('{{ route("data.areaapp") }}', 'Area', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        })); 

	        $('#employee').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {            
	        	filters = {};
	        	filters['roleGroup'] = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];
	            return filterData('employee', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.nik + " - " + obj.name}
	                })
	            }
	        }));

	        // First load method
	        var target_type = $('input[type=radio][name=target_type]:checked').val();
	        resetForm();
			setForm(target_type);

		});

		// Reset form
		function resetForm(){

			$('#targetContent').children('.form-group').removeClass('has-error');
			$('#targetContent').addClass('display-hide');

			$('#areaGroup').addClass('display-hide');
			$('#storeGroup').addClass('display-hide');
			$('#promoterGroup').addClass('display-hide');

			$('#store').removeAttr('required');			
			select2Reset($('#store'));
			$('#area').removeAttr('required');
			select2Reset($('#area'));
			$('#employee').removeAttr('required');
			select2Reset($('#employee'));			

		}

		// Set form
		function setForm(value){

			var target_type = "{{ @$data->target_type }}";
			var target = "{{ @$data->target_detail }}";
			var data = target.split(", ");
			
			select2Reset($('#area'));
			select2Reset($('#store'));
			select2Reset($('#employee'));

			if(value == 'Area'){
				
				$('#area').attr('required', 'required');

				if(value == target_type){

					// Update Area Select2
					var getDataUrl = "{{ url('util/areaapp/') }}";
					data.forEach(function(id) {
					    $.get(getDataUrl + '/' + id, function (data) {
					    	setSelect2IfPatch($("#area"), data.id, data.name);
					    })
					});

				}

				$('#areaGroup').removeClass('display-hide');
				$('#targetContent').removeClass('display-hide');

			}else if(value == 'Store'){

				$('#store').attr('required', 'required');

				if(value == target_type){

					// Update Store Select2
					var getDataUrl = "{{ url('util/store/') }}";
					data.forEach(function(id) {
					    $.get(getDataUrl + '/' + id, function (data) {
					    	setSelect2IfPatch($("#store"), data.id, data.store_id + " - " + data.store_name_1 + " (" + data.store_name_2 + ")");
					    })
				    });

				}

				$('#storeGroup').removeClass('display-hide');
				$('#targetContent').removeClass('display-hide');

			}else if(value == 'Promoter'){

				$('#employee').attr('required', 'required');

				if(value == target_type){

					// Update Promoter Select2
					var getDataUrl = "{{ url('util/user/') }}";
					data.forEach(function(id) {
					    $.get(getDataUrl + '/' + id, function (data) {
					    	setSelect2IfPatch($("#employee"), data.id, data.name);
					    })
					});

				}

				$('#promoterGroup').removeClass('display-hide');
				$('#targetContent').removeClass('display-hide');

			}			
			
		}

		// On Change target
		$(document).ready(function() {
		    $('input[type=radio][name=target_type]').change(function() {
		        resetForm();
				setForm(this.value);		        
		    });
		});

	</script>
<script src="{{ URL::to('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
var editor_config = {
	path_absolute : "{{ URL::to('/') }}",
	selector : "textarea",
	plugins : [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern"
	],
	toolbar : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alighjustify | bullist numlist outdent indent | link ",
	relative_urls : false,
	setup : function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        },
	file_browser_callback : function(field_name, url, type, win){
		var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
		var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

		var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name='+ field_name;
		if(type == 'image'){
			cmsURL = cmsURL + "&type=Images";
		}else{
			cmsURL = cmsURL + "&type=Files";
		}

		tinyMCE.activeEditor.windowManager.open({
			file : cmsURL,
			title : 'Filemanager',
			width : x * 0.8,
			height : y * 0.8,
			resizeable : "yes",
			close_previous : "no"
		});
	}
};

tinymce.init(editor_config);
</script>	
@endsection
