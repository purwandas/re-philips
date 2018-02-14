@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Admin Dashboard
            <small>statistics, charts, recent events and reports</small>
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
        <span class="active">Dashboard</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered col-md-12">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-settings font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">DASHBOARD</span>
				</div>
	        </div>
	        <div class="portlet-body">

                    <div>

						<div class="row">
							<div class="col-md-12" style="text-align: center;padding-bottom: 20px;">
								<h3>Total Achievement</h3>
							</div>
						</div>

						<div id="notSellIn" class="display-hide">
							<div class="row">
								<div class="col-md-12" style="text-align: center;padding-bottom: 20px;padding-top: 120px;">
									<h4>Tidak Ada Achievement - Sell Thru</h4>
								</div>
							</div>
						</div>

						<div id="sellIn">

							<div class="row">
								<div id="content" class="col-md-6">
									<h4 id="titleSellinTotal" style="text-align: center;margin-right:50px;margin-bottom: 20px;"></h4>
									<div class="demo-container col-md-12 align-middle">
										<div id="chartSellinTotal" class="align-middle"></div>
									</div>
								</div>

								<div id="content2" class="col-md-6">
										<!-- BEGIN SAMPLE TABLE PORTLET-->
										<div id="table2" class="portlet box blue" style="width: 75%">
											<div class="portlet-title" style="text-align: center">
												<div style="font-size: 11pt;padding-top: 10px;">
													<i class="fa fa-bar-chart"></i>
													Total Achievement - Sell Thru </div>
											</div>
											<div id="portlet1" class="portlet-body" style="display: block;">
													<div class="table-scrollable">
													<table class="table table-striped">
														<tbody>
															<tr style="height: 45px;">
																<td width="40%" style="padding-left: 15px;"> Target </td>
																<td id="sellin1" width="60%"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> Actual </td>
																<td id="sellin2"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> A/T % </td>
																<td id="sellin3"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> GAP </td>
																<td id="sellin4"> - </td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<!-- END SAMPLE TABLE PORTLET-->
								</div>
							</div>

						</div>

						<div id="notSellOut" class="display-hide">
							<div class="row">
								<div class="col-md-12" style="text-align: center;padding-bottom: 20px;padding-top: 120px;">
									<h4>Tidak Ada Achievement - Sell Out</h4>
								</div>
							</div>
						</div>

						<div id="sellOut">

							<div class="row" style="margin-top: 30px;">
								<div id="content3" class="col-md-6">
									<h4 id="titleSelloutTotal" style="text-align: center;margin-right:50px;margin-bottom: 20px;"></h4>
									<div class="demo-container col-md-12 align-middle">
										<div id="chartSelloutTotal" class="align-middle"></div>
									</div>
								</div>

								<div id="content4" class="col-md-6">
									<!-- BEGIN SAMPLE TABLE PORTLET-->
										<div id="table2" class="portlet box blue" style="width: 75%">
											<div class="portlet-title" style="text-align: center">
												<div style="font-size: 11pt;padding-top: 10px;">
													<i class="fa fa-bar-chart"></i>
													Total Achievement - Sell Out </div>
											</div>
											<div id="portlet2" class="portlet-body" style="display: block;">
													<div class="table-scrollable">
													<table class="table table-striped">
														<tbody>
															<tr style="height: 45px;">
																<td width="40%" style="padding-left: 15px;"> Target </td>
																<td id="sellout1" width="60%"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> Actual </td>
																<td id="sellout2"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> A/T % </td>
																<td id="sellout3"> - </td>
															</tr>
															<tr style="height: 45px;">
																<td style="padding-left: 15px;"> GAP </td>
																<td id="sellout4"> - </td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<!-- END SAMPLE TABLE PORTLET-->
								</div>
							</div>

						</div>

						@if(Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Admin')
						<div class="row">
							<div class="col-md-12" style="text-align: center;padding-bottom:20px;padding-top: 20px;">
								<h3>Salesman Achievement</h3>
							</div>
						</div>

						<div class="row">
							<div class="col-md-2"></div>
							<div id="content5" class="col-md-8">
								<!-- BEGIN SAMPLE TABLE PORTLET-->
									<div id="table3" class="portlet box blue" style="width: 100%">
										<div class="portlet-title" style="text-align: center">
											<div style="font-size: 11pt;padding-top: 10px;">
												<i class="fa fa-bar-chart"></i>
												Total Achievement - Salesman </div>
										</div>
										<div id="portlet2" class="portlet-body" style="display: block;">
												<div class="table-scrollable">
												<table class="table table-striped">
													<tbody>
														<tr>
															<td></td>
															<td><b>Sales</b></td>
															<td style="text-align: center;"><b>Sales Product Focus</b></td>
															<td style="text-align: center;"><b>Call</b></td>
															<td style="text-align: center;"><b>Active Outlet</b></td>
															<td style="text-align: center;"><b>Effective Call</b></td>
														</tr>
														<tr>
															<td><b>Target</b></td>
															<td id="salesmanTargetSales">-</td>
															<td id="salesmanTargetSalesPF">-</td>
															<td id="salesmanTargetCall" style="text-align: center;">-</td>
															<td id="salesmanTargetAO" style="text-align: center;">-</td>
															<td id="salesmanTargetEC" style="text-align: center;">-</td>
														</tr>
														<tr>
															<td><b>Actual</b></td>
															<td id="salesmanActualSales">-</td>
															<td id="salesmanActualSalesPF">-</td>
															<td id="salesmanActualCall" style="text-align: center;">-</td>
															<td id="salesmanActualAO" style="text-align: center;">-</td>
															<td id="salesmanActualEC" style="text-align: center;">-</td>
														</tr>
														<tr>
															<td><b>A/T%</b></td>
															<td id="salesmanATSales" style="text-align: center;">-</td>
															<td id="salesmanATSalesPF" style="text-align: center;">-</td>
															<td id="salesmanATCall" style="text-align: center;">-</td>
															<td id="salesmanATAO" style="text-align: center;">-</td>
															<td id="salesmanATEC" style="text-align: center;">-</td>
														</tr>
														<tr>
															<td><b>GAP</b></td>
															<td id="salesmanGAPSales">-</td>
															<td id="salesmanGAPSalesPF">-</td>
															<td id="salesmanGAPCall" style="text-align: center;">-</td>
															<td id="salesmanGAPAO" style="text-align: center;">-</td>
															<td id="salesmanGAPEC" style="text-align: center;">-</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<!-- END SAMPLE TABLE PORTLET-->
								<div class="col-md-2"></div>
							</div>
						</div>
						@endif

			</div><!-- 
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->

				<div id="contentt">

					<div class="demo-container2">
						<div id="placeholder" class="demo-placeholder2"></div>
					</div>

				</div>
	</div>
</div>
	
@endsection

@section('additional-scripts')

<!-- flot chats ------------------------------------------------------------------------- -->
<!-- <script language="javascript" type="text/javascript" src="../../jquery.flot.pie.js"></script> -->
<style type="text/css">

    .demo-container {
        position: relative;
        height: 400px;
    }

    #chartSellinTotal {
        /*width: 550px;*/
        width: 100%;
        height: 100%;
    }

    #chartSelloutTotal {
        /*width: 550px;*/
        width: 100%;
        height: 100%;
    }

    ul {
        font-size: 10pt;
    }

    ul li {
        margin-bottom: 0.5em;
    }

    ul.options li {
        list-style: none;
        margin-bottom: 1em;
    }

    ul li i {
        color: #999;
    }

    </style>
<script src="{{ asset('js/jquery.flot.pie.js') }}" type="text/javascript"></script>
    <script type="text/javascript">

    $(function() {

        var x = ($("#content").width()-$("#portlet1").width())/2;
        var y = ($("#content").height()-$("#portlet1").height())/2;

        document.getElementById('content2').setAttribute('style', 'padding-left:'+x+'px;padding-top:'+y+'px;');

        var x = ($("#content3").width()-$("#portlet2").width())/2;
        var y = ($("#content3").height()-$("#portlet2").height())/2;

        document.getElementById('content4').setAttribute('style', 'padding-left:'+x+'px;padding-top:'+y+'px;');

		var options = {
			series: {
				pie: {
					innerRadius: 0.5,
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 3/4,
						formatter: labelFormatter,
						background: {
							opacity: 0.5,
							color: '#000'
						}
					}
				}
			},
		};

		var role = '{{ Auth::user()->role->role_group  }}';

		var getDataUrl = "{{ url('chart/data-national') }}";

		if(role == 'RSM'){
		    getDataUrl = "{{ url('chart/data-region') }}";
		}else if(role == 'DM'){
		    getDataUrl = "{{ url('chart/data-area') }}";
        }else if(role == 'Supervisor' || role == 'Supervisor Hybrid'){
		    getDataUrl = "{{ url('chart/data-supervisor') }}";
        }

//        console.log(getDataUrl);

		// Create IDR currency formatter.
		var formatter = new Intl.NumberFormat('id', {
		  style: 'currency',
		  currency: 'IDR',
		  minimumFractionDigits: 2,
		});

		$.get(getDataUrl, function (data) {

		    if(data){
		        if(data.sell_in_target == '0') document.getElementById('sellin1').innerHTML = '-'
		        else document.getElementById('sellin1').innerHTML = formatter.format(data.sell_in_target.toFixed(2)).replace("Rp", "Rp. ");
		        if(data.sell_in_actual == '0') document.getElementById('sellin2').innerHTML = '-'
		        else document.getElementById('sellin2').innerHTML = formatter.format(data.sell_in_actual.toFixed(2)).replace("Rp", "Rp. ");
		        if(data.sell_in_at == '0') document.getElementById('sellin3').innerHTML = '-'
		        else document.getElementById('sellin3').innerHTML = data.sell_in_at.toFixed(1)+'%';
		        if(data.sell_in_gap == '0') document.getElementById('sellin4').innerHTML = '-'
		        else document.getElementById('sellin4').innerHTML = formatter.format(data.sell_in_gap.toFixed(2)).replace("Rp", "Rp. ");

		        if(data.sell_out_target == '0') document.getElementById('sellout1').innerHTML = '-'
		        else document.getElementById('sellout1').innerHTML = formatter.format(data.sell_out_target.toFixed(2)).replace("Rp", "Rp. ");
		        if(data.sell_out_actual == '0') document.getElementById('sellout2').innerHTML = '-'
		        else document.getElementById('sellout2').innerHTML = formatter.format(data.sell_out_actual.toFixed(2)).replace("Rp", "Rp. ");
		        if(data.sell_out_at == '0') document.getElementById('sellout3').innerHTML = '-'
		        else document.getElementById('sellout3').innerHTML = data.sell_out_at.toFixed(1)+'%';
		        if(data.sell_out_gap == '0') document.getElementById('sellout4').innerHTML = '-'
		        else document.getElementById('sellout4').innerHTML = formatter.format(data.sell_out_gap.toFixed(2)).replace("Rp", "Rp. ");
            }

        });

		//--- Fetch Salesman Data, If Master / Admin ---//

		if(role == 'Master' || role == 'Admin') {

            var getDataUrlSalesman = "{{ url('chart/data-national-salesman') }}";

            $.get(getDataUrlSalesman, function (data) {

                if (data) {
					if(data.sum_national_target_sales == '0') document.getElementById('salesmanTargetSales').innerHTML = '-'
		        	else document.getElementById('salesmanTargetSales').innerHTML = formatter.format(data.sum_national_target_sales.toFixed(2)).replace("Rp", "Rp. ");
					if(data.sum_national_actual_sales == '0') document.getElementById('salesmanActualSales').innerHTML = '-'
		        	else document.getElementById('salesmanActualSales').innerHTML = formatter.format(data.sum_national_actual_sales.toFixed(2)).replace("Rp", "Rp. ");
					if(data.sum_national_at_sales == '0') document.getElementById('salesmanATSales').innerHTML = '-'
		        	else document.getElementById('salesmanATSales').innerHTML = data.sum_national_at_sales.toFixed(1)+'%';
					if(data.sum_national_gap_sales == '0') document.getElementById('salesmanGAPSales').innerHTML = '-'
		        	else document.getElementById('salesmanGAPSales').innerHTML = formatter.format(data.sum_national_gap_sales.toFixed(2)).replace("Rp", "Rp. ");

					if(data.sum_national_target_sales_pf == '0') document.getElementById('salesmanTargetSalesPF').innerHTML = '-'
		        	else document.getElementById('salesmanTargetSalesPF').innerHTML = formatter.format(data.sum_national_target_sales_pf.toFixed(2)).replace("Rp", "Rp. ");
					if(data.sum_national_actual_sales_pf == '0') document.getElementById('salesmanActualSalesPF').innerHTML = '-'
		        	else document.getElementById('salesmanActualSalesPF').innerHTML = formatter.format(data.sum_national_actual_sales_pf.toFixed(2)).replace("Rp", "Rp. ");
					if(data.sum_national_at_sales_pf == '0') document.getElementById('salesmanATSalesPF').innerHTML = '-'
		        	else document.getElementById('salesmanATSalesPF').innerHTML = data.sum_national_at_sales_pf.toFixed(1)+'%';
					if(data.sum_national_gap_sales_pf == '0') document.getElementById('salesmanGAPSalesPF').innerHTML = '-'
		        	else document.getElementById('salesmanGAPSalesPF').innerHTML = formatter.format(data.sum_national_gap_sales_pf.toFixed(2)).replace("Rp", "Rp. ");

					if(data.sum_national_target_call == '0') document.getElementById('salesmanTargetCall').innerHTML = '-'
		        	else document.getElementById('salesmanTargetCall').innerHTML = data.sum_national_target_call.toFixed(0);
					if(data.sum_national_actual_call == '0') document.getElementById('salesmanActualCall').innerHTML = '-'
		        	else document.getElementById('salesmanActualCall').innerHTML = data.sum_national_actual_call.toFixed(0);
					if(data.sum_national_at_call == '0') document.getElementById('salesmanATCall').innerHTML = '-'
		        	else document.getElementById('salesmanATCall').innerHTML = data.sum_national_at_call.toFixed(1)+'%';
					if(data.sum_national_gap_call == '0') document.getElementById('salesmanGAPCall').innerHTML = '-'
		        	else document.getElementById('salesmanGAPCall').innerHTML = data.sum_national_gap_call.toFixed(0);

					if(data.sum_national_target_active_outlet == '0') document.getElementById('salesmanTargetAO').innerHTML = '-'
		        	else document.getElementById('salesmanTargetAO').innerHTML = data.sum_national_target_active_outlet.toFixed(0);
					if(data.sum_national_actual_active_outlet == '0') document.getElementById('salesmanActualAO').innerHTML = '-'
		        	else document.getElementById('salesmanActualAO').innerHTML = data.sum_national_actual_active_outlet.toFixed(0);
					if(data.sum_national_at_active_outlet == '0') document.getElementById('salesmanATAO').innerHTML = '-'
		        	else document.getElementById('salesmanATAO').innerHTML = data.sum_national_at_active_outlet.toFixed(1)+'%';
					if(data.sum_national_gap_active_outlet == '0') document.getElementById('salesmanGAPAO').innerHTML = '-'
		        	else document.getElementById('salesmanGAPAO').innerHTML = data.sum_national_gap_active_outlet.toFixed(0);

					if(data.sum_national_target_effective_call == '0') document.getElementById('salesmanTargetEC').innerHTML = '-'
		        	else document.getElementById('salesmanTargetEC').innerHTML = data.sum_national_target_effective_call.toFixed(0);
					if(data.sum_national_actual_effective_call == '0') document.getElementById('salesmanActualEC').innerHTML = '-'
		        	else document.getElementById('salesmanActualEC').innerHTML = data.sum_national_actual_effective_call.toFixed(0);
					if(data.sum_national_at_effective_call == '0') document.getElementById('salesmanATEC').innerHTML = '-'
		        	else document.getElementById('salesmanATEC').innerHTML = data.sum_national_at_effective_call.toFixed(1)+'%';
					if(data.sum_national_gap_effective_call == '0') document.getElementById('salesmanGAPEC').innerHTML = '-'
		        	else document.getElementById('salesmanGAPEC').innerHTML = data.sum_national_gap_effective_call.toFixed(0);
                }

            });

        }

		//---------------------- SELL IN (Sell Thru)--------------------------------//

        $.get(getDataUrl, function (data) {

	        if(data){
	        	dataSellinTotal = [
				  { label: "Total Actual",  data: [[1,(data.sell_in_actual)]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,(data.sell_in_gap)]], color: '#FE0000'},
				];

	        	$.plot(chartSellinTotal, dataSellinTotal, options);

	        	$('#notSellIn').addClass('display-hide');
	        	$('#sellIn').removeClass('display-hide');

	        	if(data.sell_in_actual == 0 && data.sell_in_target == 0){
					$('#notSellIn').removeClass('display-hide');
					$('#sellIn').addClass('display-hide');
				}
	        }
	    });

        data = [
				  { label: "Total Actual",  data: [[1,1]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,1]], color: '#FE0000'},
				];

        var chartSellinTotal = $("#chartSellinTotal");

		chartSellinTotal.unbind();

		$("#titleSellinTotal").text("ACTUAL vs TARGET (%) - SELL THRU");

		$.plot(chartSellinTotal, data, options);


		//---------------------- SELL OUT --------------------------------//

        $.get(getDataUrl, function (data) {

	        if(data){
	        	dataSelloutTotal = [
				  { label: "Total Actual",  data: [[1,(data.sell_out_actual)]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,(data.sell_out_gap)]], color: '#FE0000'},
				];

	        	$.plot(chartSelloutTotal, dataSelloutTotal, options);

	        	$('#notSellOut').addClass('display-hide');
	        	$('#sellOut').removeClass('display-hide');

	        	if(data.sell_out_actual == 0 && data.sell_out_target == 0){
					$('#notSellOut').removeClass('display-hide');
					$('#sellOut').addClass('display-hide');
				}
	        }
	    });


        data = [
				  { label: "Total Actual",  data: [[1,1]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,1]], color: '#FE0000'},
				];

        var chartSelloutTotal = $("#chartSelloutTotal");

		chartSellinTotal.unbind();

		$("#titleSelloutTotal").text("ACTUAL vs TARGET (%) - SELL OUT");

		$.plot(chartSelloutTotal, data, options);

		//----------------------------------------------------------------//

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
    });

    // A custom label formatter used by several of the plots

    function labelFormatter(label, series) {
		var total = series.percent;

		// Create IDR currency formatter.
		var formatter = new Intl.NumberFormat('id', {
		  style: 'currency',
		  currency: 'IDR',
		  minimumFractionDigits: 2,
		});

		return "<div style='font-size:9pt; text-align:center; padding:2px; color:white;'>"    + label + "<br/>" +total.toFixed(1)+"% ( "+ formatter.format(series.data[0][1].toFixed(2)).replace("Rp", "Rp. ") + " )</div>";
	}

    //

    function setCode(lines) {
        $("#code").text(lines.join("\n"));
    }

    </script>
<!-- flot charts end --------------------------------------------------------------------- -->
@endsection
