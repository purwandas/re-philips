@extends('layouts.app')

@section('additional-styles')
<style>
/*.modal.fade.in {
    top:30%;
}*/
#employee-store-modal{
    top:30%;
}
#history-employee-store-modal{
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
                        <i class="fa fa-cog font-blue"></i>
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

            <!-- </div> -->
        <!-- END FILTER-->

        <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <!-- <div class="portlet light bordered"> -->
			<div class="portlet-title" >
				<div class="caption">
					<i class="fa fa-group font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">EMPLOYEE</span>
				</div>
	        </div>
            <div class="portlet-title display-hide">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a class="btn green" href="{{ url('userpromoter/create') }}"><i
                        class="fa fa-plus"></i> Add Employee </a>
                    
                </div>
                <div class="actions" style="text-align: left">
                    <a id="export" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                </div>

                <div class="actions" style="text-align: left; padding-right: 10px;">
                    <a onclick="event.preventDefault();document.getElementById('exportAll-form').submit();" class="btn green-dark">
                      <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL)
                    </a>
                    <form id="exportAll-form" action="{{ url('util/export-promoter-all') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                    </form>
                </div>
            </div>

            <div class="portlet-body" >
	        	<table class="table table-striped table-hover table-bordered" id="userTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                            <th> Resign </th> 
                    		<th> No. </th>
                            <th> NIK </th>
                            <th> Name </th>
                        	<th> Role </th>
                            <th> Status </th>
                            <th> Join Date </th>
                            <th> Grading </th>
                            <th> Supervisor </th>
                            <th> Area </th>
                            <th> Store </th>
                            <th> History </th>                                                  
                        </tr>
                    </thead>
				</table>

				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->

        @include('partial.util.employee-store-modal')
        @include('partial.util.history-employee-store-modal')
        @include('partial.modal.resign-modal')

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
<script src="{{ asset('js/handler/resign-handler.js') }}" type="text/javascript"></script>

<script>
    var dataAll = {};
        var filterId = ['#filterNik', '#filterName', '#filterRole'];
        var url = 'datatable/resign';
        var order = [ [1, 'desc'] ];
        var columnDefs = [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [1]},
                {"className": "dt-center", "targets": [4]},
                {"className": "dt-center", "targets": [5]},
                {"className": "dt-center", "targets": [6]},
            ];

        var tableColumns = [
                {data: 'action', name: 'action', searchable: false, sortable: false},  
                {data: 'id', name: 'id'}, 
                {data: 'nik', name: 'nik'},               
                {data: 'name', name: 'name'},
                {data: 'roles', name: 'roles'},
                {data: 'status', name: 'status'},
                {data: 'join_date', name: 'join_date'},
                {data: 'grading', name: 'grading'},
                {data: 'supervisor', name: 'supervisor'},
                {data: 'area', name: 'area'},
                {data: 'store', name: 'store'},
                {data: 'history', name: 'history'},                            
            ];

        var paramFilter = ['userTable', $('#userTable'), url, tableColumns, columnDefs, order, '#export'];
        var paramReset = [filterId, 'userTable', $('#userTable'), url, tableColumns, columnDefs, order, '#export'];

	$(document).ready(function () {    	

		$.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/groupPromoterC',
            dataType: 'json',
            global: false,
            async: false,
            success: function (results) {
                var count = results.length;

                        if(count > 0){
                            $('#exportAll').removeAttr('disabled');
                        }else{
                            $('#exportAll').attr('disabled','disabled');
                        }
                dataAll = results;
            }
        });

        // Set data for Data Table
            var table = $('#userTable').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.resign') }}",
                    type: 'POST',
                    dataSrc: function (res) {
                        var count = res.data.length;

                        if(count > 0){
                            $('#export').removeAttr('disabled');
                        }else{
                            $('#export').attr('disabled','disabled');
                        }

                        this.data = res.data;
                        return res.data;
                    },
                },
                "rowId": "id",
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });


    	// Delete data with sweet alert
     //    $('#userTable').on('click', 'tr td button.deleteButton', function () {
     //        var id = $(this).val();

     //            // if(userRelation(id)){
     //            //     swal("Warning", "This data still related to others! Please check the relation first.", "warning");
     //            //     return;
     //            // }

     //        	swal({
					// title: "Are you sure?",
     //                text: "User's Relation will be Deleted!",
     //                type: "warning",
     //                showCancelButton: true,
     //                confirmButtonClass: "btn-danger",
     //                confirmButtonText: "Yes, delete it",
     //                cancelButtonText: "No, cancel",
     //                closeOnConfirm: false,
     //                closeOnCancel: false
     //            },
     //            function (isConfirm) {
     //                if (isConfirm) {
     //                    $.ajaxSetup({
     //                        headers: {
     //                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     //                        }
     //                    })


     //                    $.ajax({

     //                        type: "DELETE",
     //                        url:  'userpromoter/' + id,
     //                        success: function (data) {
                                
     //                            console.log(data);

     //                            // $("#"+id).remove();
     //                            // // $('#sportsTable').DataTable().ajax.reload();

     //                            // $.ajax({
     //                            //     type: 'POST',
     //                            //     url: 'data/groupPromoterC',
     //                            //     dataType: 'json',
     //                            //     global: false,
     //                            //     async: false,
     //                            //     success: function (results) {
     //                            //         var count = results.length;

     //                            //                 if(count > 0){
     //                            //                     $('#exportAll').removeAttr('disabled');
     //                            //                 }else{
     //                            //                     $('#exportAll').attr('disabled','disabled');
     //                            //                 }
     //                            //         dataAll = results;
     //                            //     }
     //                            // });
     //                        },
     //                        error: function (data) {
     //                            console.log('Error:', data);
     //                        }
     //                    });                        

     //                    swal("Deleted!", "Data has been deleted.", "success");
     //                    window.location = 'userpromoter';
     //                } else {
     //                    swal("Cancelled", "Data is safe ", "success");
     //                }
     //            });
     //    });

        initSelect2();       

    });

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-promoter',
                    dataType: 'json',
                    data: {data: data},
                    global: false,
                    async: false,
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

                        setTimeout(function () {
                            $.ajax({
                                type: 'POST',
                                url: 'util/export-delete',
                                dataType: 'json',
                                data: {data: data.url},
                                global: false,
                                async: false,
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }, 1000);


                    }
                });

            }


        });

        $("#exportAll").click( function(){

            if ($('#exportAll').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-promoter-all',
                    dataType: 'json',
                    data: filters,
                    success: function (data) {

                        console.log(data);

                        window.location = data.url;

                    }
                });

            }


        });
     function initSelect2(){

            /*
             * Select 2 init
             *
             */
            $('#filterNik').select2(setOptions('{{ route("data.groupPromoter") }}', 'NIK', function (params) {
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

            $('#filterName').select2(setOptions('{{ route("data.groupPromoter") }}', 'Name', function (params) {
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

        // For editing data
    $(document).on("click", ".confirm-resign", function () {

        var id = $(this).data('id');
        var getDataUrl = "{{ url('userpromoter/get-data') }}";

        // // Set action url form for update
        // $("#form_leadtime").attr("action", postDataUrl);

        // // Set Patch Method
        // if(!$('input[name=_method]').length){
        //     $("#form_leadtime").append("<input type='hidden' name='_method' value='PATCH'>");
        // }

        document.getElementById('nik').innerHTML = '-';
        document.getElementById('name').innerHTML = '-';
        document.getElementById('grading').innerHTML = '-';
        document.getElementById('join_date').innerHTML = '-';
        document.getElementById('role').innerHTML = '-';
        document.getElementById('status').innerHTML = '-';
        $('#employeeId').val('');


        $.get(getDataUrl + '/' + id, function (data) {

            (data.nik == '' || data.nik == null) ? document.getElementById('nik').innerHTML = '-' : document.getElementById('nik').innerHTML = data.nik;
            (data.name == '' || data.name == null) ? document.getElementById('name').innerHTML = '-' : document.getElementById('name').innerHTML = data.name;
            (data.grading_id == '' || data.grading_id == null) ? document.getElementById('grading').innerHTML = '-' : document.getElementById('grading').innerHTML = data.grading.grading;
            (data.join_date == '' || data.join_date == null) ? document.getElementById('join_date').innerHTML = '-' : document.getElementById('join_date').innerHTML = data.join_date;
            (data.role_id == '' || data.role_id == null) ? document.getElementById('role').innerHTML = '-' : document.getElementById('role').innerHTML = data.role.role;
            (data.status == '' || data.status == null) ? document.getElementById('status').innerHTML = '-' : document.getElementById('status').innerHTML = data.status;

            $('#employeeId').val(data.id);

        })

    });

</script>
@endsection
