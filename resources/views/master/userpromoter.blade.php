@extends('layouts.app')

@section('additional-styles')
<style>
.modal.fade.in {
    top:30%;
}
</style>
@endsection

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Promoter
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
        <span class="active">Promoter Management</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <!-- BEGIN FILTER-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-map-o font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER Promoter</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>
                
                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterNik" class="select2select">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterName" class="select2select">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterRole" class="select2select" >
                            <option value=""></option>
                            <option value="Promoter">Promoter</option>
                            <option value="Promoter Additional">Promoter Additional</option>
                            <option value="Promoter Event">Promoter Event</option>
                            <option value="Demonstrator MCC">Demonstrator MCC</option>
                            <option value="Demonstrator DA">Demonstrator DA</option>
                            <option value="ACT">ACT</option>
                            <option value="PPE">PPE</option>
                            <option value="BDT">BDT</option>
                            <option value="Salesman Explorer">Salesman Explorer</option>
                            <option value="SMD">SMD</option>
                            <option value="SMD Coordinator">SMD Coordinator</option>
                            <option value="HIC">HIC</option>
                            <option value="HIE">HIE</option>
                            <option value="SMD Additional">SMD Additional</option>
                            <option value="ASC">ASC</option>
                            
                            <!-- <option value="Driver">Driver</option>
                            <option value="Helper">Helper</option>
                            <option value="PCE">PCE</option>
                            <option value="RE Executive">RE Executive</option>
                            <option value="RE Support">RE Support</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Trainer">Trainer</option>
                            <option value="Head Trainer">Head Trainer</option>
                            <option value="Supervisor Hybrid">Supervisor Hybrid</option>
                            <option value="DM">DM</option>
                            <option value="RSM">RSM</option>
                            <option value="Admin">Admin</option>
                            <option value="Master">Master</option> -->
                        </select>
                    </div>
                </div>

                <br>

                <div class="btn-group">
                    <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerReset(paramReset)">
                        <i class="fa fa-refresh"></i> Reset </a>
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

            </div>
        <!-- END FILTER-->

        <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered">
			<div class="portlet-title" >
				<div class="caption">
					<i class="fa fa-group font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">EMPLOYEE</span>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->            
	        	<div class="table-toolbar">
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="btn-group">
                             	<a class="btn green" href="{{ url('userpromoter/create') }}"><i
									class="fa fa-plus"></i> Add Employee </a>
                                
                            </div>
                    	</div>
                    </div>
                </div>

	        	<table class="table table-striped table-hover table-bordered" id="userTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                    		<th> No. </th>
                            <th> NIK </th>
                            <th> Name </th>
                        	<th> Role </th>
                            <th> Status </th>
                            <th> Supervisor </th>
                            <th> Store </th>
                            <th> History </th>
                            <th> Options </th>                             
                        </tr>
                    </thead>
				</table>

				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->

        @include('partial.util.employee-store-modal')
        @include('partial.util.history-employee-store-modal')

	</div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->
    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/handler/datetimepicker-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
<!-- BEGIN TEXT MODAL SCRIPTS -->
<script src="{{ asset('js/text-modal/popup.js') }}" type="text/javascript"></script>
<!-- END TEXT MODAL SCRIPTS -->

<script>
        var filterId = ['#filterNik', '#filterName', '#filterRole'];
        var url = 'datatable/userpromoter';
        var order = [ [0, 'desc'] ];
        var columnDefs = [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [4]},
                {"className": "dt-center", "targets": [5]},
                {"className": "dt-center", "targets": [6]},
            ];

        var tableColumns = [
                {data: 'id', name: 'id'}, 
                {data: 'nik', name: 'nik'},               
                {data: 'name', name: 'name'},
                {data: 'role', name: 'role'},
                {data: 'status', name: 'status'},
                {data: 'supervisor', name: 'supervisor'},
                {data: 'store', name: 'store'},
                {data: 'history', name: 'history'},
                {data: 'action', name: 'action', searchable: false, sortable: false},                
            ];

        var paramFilter = ['userTable', $('#userTable'), url, tableColumns, columnDefs, order];
        var paramReset = [filterId, 'userTable', $('#userTable'), url, tableColumns, columnDefs, order];

	$(document).ready(function () {    	

		$.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

     //    // Set data for Data Table '#athletesTable'
     //    var table = $('#userTable').dataTable({
	    //     "processing": true,
	    //     "serverSide": true,	          
	    //     "ajax": {
     //            url: "{{ route('datatable.user') }}",
     //            type: 'POST',
     //        },
	    //     "rowId": "id",
	    //     "columns": [
	    //         {data: 'id', name: 'id'}, 
     //            {data: 'nik', name: 'nik'},               
	    //         {data: 'name', name: 'name'},
     //            {data: 'role', name: 'role'},
     //            {data: 'status', name: 'status'},
     //            {data: 'store', name: 'store'},
	    //         {data: 'action', name: 'action', searchable: false, sortable: false},                
	    //     ],
	    //     "columnDefs": ,
     //        "order": [ [0, 'desc'] ],            
    	// });

        // Set data for Data Table
            var table = $('#userTable').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.userpromoter') }}",
                    type: 'POST',
                },
                "rowId": "id",
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });


    	// Delete data with sweet alert
        $('#userTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                if(userRelation(id)){
                    swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                    return;
                }

            	swal({
					title: "Are you sure?",
                    text: "You will not be able to recover data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it",
                    cancelButtonText: "No, cancel",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })


                        $.ajax({

                            type: "DELETE",
                            url:  'userpromoter/' + id,
                            success: function (data) {
                                console.log(data);

                                $("#"+id).remove();
                                // $('#sportsTable').DataTable().ajax.reload();
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });                        

                        swal("Deleted!", "Data has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Data is safe ", "success");
                    }
                });
        });


        initSelect2();       

    });

     function initSelect2(){

            /*
             * Select 2 init
             *
             */
            $('#filterNik').select2(setOptions('{{ route("data.employee") }}', 'NIK', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.nik}
                    })
                }
            }));
            $('#filterNik').on('select2:select', function () {
                select2Reset($('#filterName'));
                self.selected('byNik', $('#filterNik').val());
            });

            $('#filterName').select2(setOptions('{{ route("data.employee") }}', 'Name', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterName').on('select2:select', function () {
                select2Reset($('#filterNik'));
                self.selected('byName', $('#filterName').val());
            });

            // $("#filterRole").attr("data-placeholder","bar");
            // $("#filterRole").select2();
            $('#filterRole').select2({
                width: '100%',
                placeholder: 'Role'
            });
            $('#filterRole').on('select2:select', function () {
                self.selected('byRole', $('#filterRole').val());
            });



        }

</script>
@endsection
