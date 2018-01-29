@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Quiz
            <small>manage quiz</small>
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
        <a href="{{ url('quiz') }}">Quiz Management</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
		<span>
			@if (empty($data))
				Add More Quiz
			@else
				Update Quiz
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
							ADD MORE QUIZ
						@else
							UPDATE QUIZ
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('quiz') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_quiz" class="form-horizontal" action="{{ url('quiz', @$data->id) }}" method="POST">	        	
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
				          <label class="col-sm-2 control-label">Title</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="title" name="title" class="form-control" value="{{ @$data->title }}" placeholder="Input Title" />
				            </div>
				          </div>
				        </div>

				         <div class="form-group">
				          <label class="col-sm-2 control-label">Description</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="description" name="description" class="form-control" value="{{ @$data->description }}" placeholder="Input Description" />
				            </div>
				          </div>
				        </div>

						<div class="form-group">
				          <label class="col-sm-2 control-label">Link</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="link" name="link" class="form-control" value="{{ @$data->link }}" placeholder="Input Link" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Target</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<select class="select2select" name="target[]" id="target" multiple="multiple">
				          			<option></option>
                                </select>
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
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/quiz-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->

    <script> 	
    	var quizId = "{{ collect(request()->segments())->last() }}";
		$(document).ready(function () {
			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });   

			updateTarget();

         	$('#target').select2(setOptions('{{ route("data.quiztarget") }}', 'Target', function (params) {

	            return filterData('target', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.role +' ('+obj.grading+')'}
	                })
	            }
	        }));

	        var newTarget = [];
	        console.log("{{@$data->target}}");
	        <?php 
	        if (isset($data->target))
	        {
	        	
	        			$bangs = explode(',',$data->target);
	        			// print_r($bangs);
	        	
		        // $target = explode(',', $data->target);
		        foreach ($bangs as $key => $value)
		        {
		        	if ($value != '') {
			        	echo "newTarget.push('$value');";
			        }
		        }
	        }
	        ?>

	        // var target = ({{ @$target }}).split(',');
	        newTarget.forEach(setSelect2);

		});

		function setSelect2(item, index){
        	console.log(item);
            setSelect2IfPatch($("#target"), item, item);
        }

        function updateTarget(){
			var getDataUrl = "{{ url('util/target/') }}";

			$.get(getDataUrl + '/' + quizId, function (data) {
				if(data){
                    var element = $("#target");
                    $.each(data, function() {
						setSelect2IfPatch(element, this.id,this.role+" ("+this.grading+ ")");
						// console.log('patch2#'+this.id+"*"+this.role+" ("+this.grading+ ")");
					});
            	}	

        	})
		}

	</script>

@endsection
