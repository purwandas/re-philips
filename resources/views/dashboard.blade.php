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
                    	<div id="content" class="col-md-6">
	                        <h3 id="titleSellinTotal"></h3>
	                        <div class="demo-container col-md-12 align-middle">
	                            <div id="chartSellinTotal" class="align-middle"></div>
	                        </div>
	                        <p id="descriptionSellinTotal"></p>
	                        <p id="hoverSellinTotal"></p>
	                    </div>

	                    <div id="content2" class="col-md-6">
	                        <h3 id="titleSelloutTotal"></h3>
	                        <div class="demo-container col-md-12 align-middle">
	                            <div id="chartSelloutTotal" class="align-middle"></div>
	                        </div>
	                        <p id="descriptionSelloutTotal"></p>
	                        <p id="hoverSelloutTotal"></p>
	                    </div>


	                    <div id="content3" class="col-md-6">
	                        <h3 id="titleSellinDa"></h3>
	                        <div class="demo-container col-md-12 align-middle">
	                            <div id="chartSellinDa" class="align-middle"></div>
	                        </div>
	                        <p id="descriptionSellinDa"></p>
	                        <p id="hoverSellinDa"></p>
	                    </div>

	                    <div id="content4" class="col-md-6">
	                        <h3 id="titleSelloutDa"></h3>
	                        <div class="demo-container col-md-12 align-middle">
	                            <div id="chartSelloutDa" class="align-middle"></div>
	                        </div>
	                        <p id="descriptionSelloutDa"></p>
	                        <p id="hoverSelloutDa"></p>
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

    #chartSellinDa {
        /*width: 550px;*/
        width: 100%;
        height: 100%;
    }

    #chartSelloutDa {
        /*width: 550px;*/
        width: 100%;
        height: 100%;
    }

    #descriptionSellinTotal {
        margin: 15px 10px 20px 10px;
    }

    #descriptionSelloutTotal {
        margin: 15px 10px 20px 10px;
    }

    #descriptionSellinDa {
        margin: 15px 10px 20px 10px;
    }

    #descriptionSelloutDa {
        margin: 15px 10px 20px 10px;
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

        // Example Data

        // var data = [
        //  { label: "Series1",  data: 10},
        //  { label: "Series2",  data: 30},
        //  { label: "Series3",  data: 90},
        //  { label: "Series4",  data: 70},
        //  { label: "Series5",  data: 80},
        //  { label: "Series6",  data: 110}
        // ];

        //var data = [
        //  { label: "Series1",  data: [[1,10]]},
        //  { label: "Series2",  data: [[1,30]]},
        //  { label: "Series3",  data: [[1,90]]},
        //  { label: "Series4",  data: [[1,70]]},
        //  { label: "Series5",  data: [[1,80]]},
        //  { label: "Series6",  data: [[1,0]]}
        //];

        //var data = [
        //  { label: "Series A",  data: 0.2063},
        //  { label: "Series B",  data: 38888}
        //];
        var index = 0;
        var dataSellinTotal = [];
        var getDataUrl = 'achievement-by-national/1';
        $.get(getDataUrl, function (data) {
	        if(data){
	        	dataSellinTotal[index] = {
	                label: "Series" + (index + 1),
	                data: data.id
	            }
	            index++;
	        }
	    });
        console.log(dataSellinTotal);
        // Randomly Generated Data

        var data = [],
            series = Math.floor(Math.random() * 6) + 3;

        for (var i = 0; i < series; i++) {
            data[i] = {
                label: "Series" + (i + 1),
                data: Math.floor(Math.random() * 100) + 1
            }
        }

        var chartSellinTotal = $("#chartSellinTotal");

            chartSellinTotal.unbind();

            $("#titleSellinTotal").text("Sell In TOTAL");
            
            $("#descriptionSellinTotal").text("The default pie chart with no options set.");
            

            $.plot(chartSellinTotal, data, {
                series: {
                    pie: { 
                        show: true
                    }
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });

            chartSellinTotal.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hoverSellinTotal").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
            });

            chartSellinTotal.bind("plotclick", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                percent = parseFloat(obj.series.percent).toFixed(2);
                alert(""  + obj.series.label + ": " + percent + "%");
            });

            //-----------------------
            function labelFormatter(label, series) {
            	var total = series.percent;
			    return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>"    + label + "<br/>" +total.toFixed(1)+"% ("+ series.data[0][1] + ")</div>";
			}
            var chartSelloutTotal = $("#chartSelloutTotal");
            chartSelloutTotal.unbind();
            $("#titleSelloutTotal").text("Sell Out TOTAL");
            $("#descriptionSelloutTotal").text("The default pie chart with no options set.");
            $.plot(chartSelloutTotal, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
			            tilt: 0.5,
			            label: {
			                show: true,
			                radius: 1,
			                formatter: labelFormatter,
			                background: {
			                    opacity: 0.8
			                }
			            },
			            combine: {
			                color: '#999',
			                threshold: 0.1
			            }
                    },
	                valueLabels: {
	                    show: true,
	                    showAsHtml: true,
	                    align: "center"
	                }   

                },
                grid: {
                    hoverable: true,
                    // clickable: true
                },
			    legend: {
			        show: false
			    }
            });
            chartSelloutTotal.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }
 
                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hoverSelloutTotal").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + obj.series.data[0][1] + ")</span>");

            });

            // chartSelloutTotal.bind("plotclick", function(event, pos, obj) {

            //     if (!obj) {
            //         return;
            //     }

            //     percent = parseFloat(obj.series.percent).toFixed(2);
            //     alert(""  + obj.series.label + ": " + percent + "%");
            // });

            //-------------------------------------------------------
            var chartSellinDa = $("#chartSellinDa");

            chartSellinDa.unbind();

            $("#titleSellinDa").text("Sell In DA");
            
            $("#descriptionSellinDa").text("The default pie chart with no options set.");
            

            $.plot(chartSellinDa, data, {
                series: {
                    pie: { 
                        show: true
                    }
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });

            chartSellinDa.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hoverSellinDa").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
            });

            chartSellinDa.bind("plotclick", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                percent = parseFloat(obj.series.percent).toFixed(2);
                alert(""  + obj.series.label + ": " + percent + "%");
            });
            //------------------------------------------------------------
            var chartSelloutDa = $("#chartSelloutDa");
            chartSelloutDa.unbind();
            $("#titleSelloutDa").text("Sell Out DA");
            $("#descriptionSelloutDa").text("The default pie chart with no options set.");
            $.plot(chartSelloutDa, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
			            tilt: 0.5,
			            label: {
			                show: true,
			                radius: 1,
			                formatter: labelFormatter,
			                background: {
			                    opacity: 0.8
			                }
			            },
			            combine: {
			                color: '#999',
			                threshold: 0.1
			            }
                    },
	                valueLabels: {
	                    show: true,
	                    showAsHtml: true,
	                    align: "center"
	                }   

                },
                grid: {
                    hoverable: true,
                    // clickable: true
                },
			    legend: {
			        show: false
			    }
            });
            chartSelloutDa.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }
 
                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hoverSelloutDa").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + obj.series.data[0][1] + ")</span>");

            });
            //------------------------------------------------------------

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
    });

    // A custom label formatter used by several of the plots

    function labelFormatter(label, series) {
        return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }

    //

    function setCode(lines) {
        $("#code").text(lines.join("\n"));
    }

    </script>
<!-- flot charts end --------------------------------------------------------------------- -->
@endsection
