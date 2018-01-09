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
							<div id="content" class="col-md-6">
								<h3 id="titleSellinTotal" style="text-align: center;margin-right:50px;margin-bottom: 20px;"></h3>
								<div class="demo-container col-md-12 align-middle">
									<div id="chartSellinTotal" class="align-middle"></div>
								</div>
							</div>

							<div id="content2" class="col-md-6">
									<!-- BEGIN SAMPLE TABLE PORTLET-->
									<div id="table2" class="portlet box blue" style="width: 75%">
										<div class="portlet-title" style="text-align: center">
											<div style="font-size: 11pt;padding-top: 10px;">
												<i class="fa fa-line-chart"></i>
												Total Achievement - Sell In </div>
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


						<div class="row" style="margin-top: 30px;">
							<div id="content3" class="col-md-6">
								<h3 id="titleSelloutTotal" style="text-align: center;margin-right:50px;margin-bottom: 20px;"></h3>
								<div class="demo-container col-md-12 align-middle">
									<div id="chartSelloutTotal" class="align-middle"></div>
								</div>
							</div>

							<div id="content4" class="col-md-6">
								<!-- BEGIN SAMPLE TABLE PORTLET-->
									<div id="table2" class="portlet box blue" style="width: 75%">
										<div class="portlet-title" style="text-align: center">
											<div style="font-size: 11pt;padding-top: 10px;">
												<i class="fa fa-line-chart"></i>
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

			</div><!-- 
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
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

		var role = '{{ Auth::user()->role  }}';

		var getDataUrl = "{{ url('chart/data-national') }}";

		if(role == 'RSM'){
		    getDataUrl = "{{ url('chart/data-region') }}";
		}else if(role == 'DM'){
		    getDataUrl = "{{ url('chart/data-area') }}";
        }else if(role == 'Supervisor' || role == 'Supervisor Hybrid'){
		    getDataUrl = "{{ url('chart/data-supervisor') }}";
        }

        console.log(getDataUrl);

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

		//---------------------- SELL IN --------------------------------//

        $.get(getDataUrl, function (data) {

	        if(data){
	        	dataSellinTotal = [
				  { label: "Total Actual",  data: [[1,(data.sell_in_actual)]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,(data.sell_in_gap)]], color: '#FE0000'},
				];

	        	$.plot(chartSellinTotal, dataSellinTotal, options);
	        }
	    });

        data = [
				  { label: "Total Actual",  data: [[1,1]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,1]], color: '#FE0000'},
				];

        var chartSellinTotal = $("#chartSellinTotal");

		chartSellinTotal.unbind();

		$("#titleSellinTotal").text("ACTUAL vs TARGET (%) - SELL IN");

		$.plot(chartSellinTotal, data, options);

		//---------------------- SELL OUT --------------------------------//

        $.get(getDataUrl, function (data) {

	        if(data){
	        	dataSelloutTotal = [
				  { label: "Total Actual",  data: [[1,(data.sell_out_actual)]], color: '#16E221'},
				  { label: "GAP Target",  data: [[1,(data.sell_out_gap)]], color: '#FE0000'},
				];

	        	$.plot(chartSelloutTotal, dataSelloutTotal, options);
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

		return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>"    + label + "<br/>" +total.toFixed(1)+"% ( "+ formatter.format(series.data[0][1].toFixed(2)).replace("Rp", "Rp. ") + " )</div>";
	}

    //

    function setCode(lines) {
        $("#code").text(lines.join("\n"));
    }

    </script>
<!-- flot charts end --------------------------------------------------------------------- -->
@endsection
