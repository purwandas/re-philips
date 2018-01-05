@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Sell Out
            <small>manage sell out</small>
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
        <span class="active">Sell Out Management</span>
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
                    <i class="fa fa-share-alt font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">Sell Out</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <table class="table table-striped table-hover table-bordered" id="sellOutTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> User Name </th>
                                <th> User NIK </th>
                                <th> Store Name 1 </th>
                                <th> Store Name 2 </th>
                                <th> Store ID </th>
                                <th> Product </th>
                                <th> Quantity </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                    </table>

                    

                </div>

                @include('partial.modal.editsellout-modal')

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
<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/editsellout-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>

    /*
     * ACCOUNT
     *
     */
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table
        var table = $('#sellOutTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.editsellout') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'user_name', name: 'user_name'},
                {data: 'user_nik', name: 'user_nik'},
                {data: 'store_name_1', name: 'store_name_1'},
                {data: 'store_name_2', name: 'store_name_2'},
                {data: 'store_id', name: 'store_id'},
                {data: 'product', name: 'product'},
                {data: 'quantity', name: 'quantity'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#sellOutTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

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
                            url:  'editsellout/' + id,
                            success: function (data) {
                                $("#"+id).remove();
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


        initSelect2Account();

    });


    // For editing data
    $(document).on("click", ".edit-sellout", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('editsellout/edit/') }}";
        var postDataUrl = "{{ url('editsellout') }}"+"/"+id;

        // Set action url form for update
        $("#form_editsellout").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_editsellout").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#quantity').val(data.quantity);

                    // setSelect2IfPatchModal($("#globalchannel"), data.globalchannel_id, data.global_channel.name);

        })

    });

    function initSelect2Account(){

        /*
         * Select 2 init
         *
         */

         $('#globalchannel').select2(setOptions('{{ route("data.globalchannel") }}', 'Global Channel', function (params) {
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

<!-- am charts --------------------------------------------------- -->

<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
  font-size: 11px;
}

.amcharts-pie-slice {
  transform: scale(1);
  transform-origin: 50% 50%;
  transition-duration: 0.3s;
  transition: all .3s ease-out;
  -webkit-transition: all .3s ease-out;
  -moz-transition: all .3s ease-out;
  -o-transition: all .3s ease-out;
  cursor: pointer;
  box-shadow: 0 0 30px 0 #000;
}

.amcharts-pie-slice:hover {
  transform: scale(1.1);
  filter: url(#shadow);
}                           
</style>

<!-- Resources -->
<!-- <script src="https://www.amcharts.com/lib/3/amcharts.js"></script> -->
<!-- <script src="https://www.amcharts.com/lib/3/pie.js"></script> -->
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/patterns.js"></script>

<!-- Chart code -->
<script>
var chart = AmCharts.makeChart("chartdiv", {
  "type": "pie",
  "startDuration": 0,
   "theme": "light",
  "addClassNames": true,
  "legend":{
    "position":"right",
    "marginRight":100,
    "autoMargins":false
  },
  "innerRadius": "30%",
  "defs": {
    "filter": [{
      "id": "shadow",
      "width": "200%",
      "height": "200%",
      "feOffset": {
        "result": "offOut",
        "in": "SourceAlpha",
        "dx": 0,
        "dy": 0
      },
      "feGaussianBlur": {
        "result": "blurOut",
        "in": "offOut",
        "stdDeviation": 5
      },
      "feBlend": {
        "in": "SourceGraphic",
        "in2": "blurOut",
        "mode": "normal"
      }
    }]
  },
  "dataProvider": [{
    "nagara": "Lithuania",
    "basara": 501.9
  }, {
    "nagara": "Czech Republic",
    "basara": 301.9
  }
  ],
  "valueField": "basara",
  "titleField": "nagara",
  "export": {
    "enabled": true
  }
});

chart.addListener("init", handleInit);

chart.addListener("rollOverSlice", function(e) {
  handleRollOver(e);
});

function handleInit(){
  chart.legend.addListener("rollOverItem", handleRollOver);
}

function handleRollOver(e){
  var wedge = e.dataItem.wedge.node;
  wedge.parentNode.appendChild(wedge);
}

jQuery(document).ajaxComplete(function() {
    jQuery("a[title='JavaScript charts']").hide();
});
</script>

<!-- flot chats ------------------------------------------------------------------------- -->
<!-- <script language="javascript" type="text/javascript" src="../../jquery.flot.pie.js"></script> -->
<style type="text/css">

    .demo-container {
        position: relative;
        height: 400px;
    }

    #placeholder {
        width: 550px;
        /*width: 100%;*/
        height: 100%;
    }

    #menu {
        position: absolute;
        top: 20px;
        left: 625px;
        bottom: 20px;
        right: 20px;
        width: 200px;
    }

    #menu button {
        display: inline-block;
        width: 200px;
        padding: 3px 0 2px 0;
        margin-bottom: 4px;
        background: #eee;
        border: 1px solid #999;
        border-radius: 2px;
        font-size: 16px;
        -o-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        -ms-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        cursor: pointer;
    }

    #description {
        margin: 15px 10px 20px 10px;
    }

    #code {
        display: block;
        width: 870px;
        padding: 15px;
        margin: 10px auto;
        border: 1px dashed #999;
        background-color: #f8f8f8;
        font-size: 16px;
        line-height: 20px;
        color: #666;
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

        // Randomly Generated Data

        var data = [],
            series = Math.floor(Math.random() * 6) + 3;

        for (var i = 0; i < series; i++) {
            data[i] = {
                label: "Series" + (i + 1),
                data: Math.floor(Math.random() * 100) + 1
            }
        }

        var placeholder = $("#placeholder");

        // $("#example-1").click(function() {

            placeholder.unbind();

            $("#title").text("Default pie chart");
            $("#description").text("The default pie chart with no options set.");

            $.plot(placeholder, data, {
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

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true",
                "        }",
                "    }",
                "});"
            ]);

            placeholder.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
            });

            placeholder.bind("plotclick", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                percent = parseFloat(obj.series.percent).toFixed(2);
                alert(""  + obj.series.label + ": " + percent + "%");
            });
        // });

        $("#example-2").click(function() {

            placeholder.unbind();

            $("#title").text("Default without legend");
            $("#description").text("The default pie chart when the legend is disabled. Since the labels would normally be outside the container, the chart is resized to fit.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-3").click(function() {

            placeholder.unbind();

            $("#title").text("Custom Label Formatter");
            $("#description").text("Added a semi-transparent background to the labels and a custom labelFormatter function.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.8
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 1,",
                "            label: {",
                "                show: true,",
                "                radius: 1,",
                "                formatter: labelFormatter,",
                "                background: {",
                "                    opacity: 0.8",
                "                }",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-4").click(function() {

            placeholder.unbind();

            $("#title").text("Label Radius");
            $("#description").text("Slightly more transparent label backgrounds and adjusted the radius values to place them within the pie.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 3/4,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.5
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 1,",
                "            label: {",
                "                show: true,",
                "                radius: 3/4,",
                "                formatter: labelFormatter,",
                "                background: {",
                "                    opacity: 0.5",
                "                }",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-5").click(function() {

            placeholder.unbind();

            $("#title").text("Label Styles #1");
            $("#description").text("Semi-transparent, black-colored label background.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 3/4,
                            formatter: labelFormatter,
                            background: { 
                                opacity: 0.5,
                                color: "#000"
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: { ",
                "            show: true,",
                "            radius: 1,",
                "            label: {",
                "                show: true,",
                "                radius: 3/4,",
                "                formatter: labelFormatter,",
                "                background: { ",
                "                    opacity: 0.5,",
                "                    color: '#000'",
                "                }",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-6").click(function() {

            placeholder.unbind();

            $("#title").text("Label Styles #2");
            $("#description").text("Semi-transparent, black-colored label background placed at pie edge.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 3/4,
                        label: {
                            show: true,
                            radius: 3/4,
                            formatter: labelFormatter,
                            background: { 
                                opacity: 0.5,
                                color: "#000"
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 3/4,",
                "            label: {",
                "                show: true,",
                "                radius: 3/4,",
                "                formatter: labelFormatter,",
                "                background: {",
                "                    opacity: 0.5,",
                "                    color: '#000'",
                "                }",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-7").click(function() {

            placeholder.unbind();

            $("#title").text("Hidden Labels");
            $("#description").text("Labels can be hidden if the slice is less than a given percentage of the pie (10% in this case).");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 2/3,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 1,",
                "            label: {",
                "                show: true,",
                "                radius: 2/3,",
                "                formatter: labelFormatter,",
                "                threshold: 0.1",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-8").click(function() {

            placeholder.unbind();

            $("#title").text("Combined Slice");
            $("#description").text("Multiple slices less than a given percentage (5% in this case) of the pie can be combined into a single, larger slice.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        combine: {
                            color: "#999",
                            threshold: 0.05
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            combine: {",
                "                color: '#999',",
                "                threshold: 0.1",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-9").click(function() {

            placeholder.unbind();

            $("#title").text("Rectangular Pie");
            $("#description").text("The radius can also be set to a specific size (even larger than the container itself).");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        show: true,
                        radius: 500,
                        label: {
                            show: true,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 500,",
                "            label: {",
                "                show: true,",
                "                formatter: labelFormatter,",
                "                threshold: 0.1",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});"
            ]);
        });

        $("#example-10").click(function() {

            placeholder.unbind();

            $("#title").text("Tilted Pie");
            $("#description").text("The pie can be tilted at an angle.");

            $.plot(placeholder, data, {
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
                            color: "#999",
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true,",
                "            radius: 1,",
                "            tilt: 0.5,",
                "            label: {",
                "                show: true,",
                "                radius: 1,",
                "                formatter: labelFormatter,",
                "                background: {",
                "                    opacity: 0.8",
                "                }",
                "            },",
                "            combine: {",
                "                color: '#999',",
                "                threshold: 0.1",
                "            }",
                "        }",
                "    },",
                "    legend: {",
                "        show: false",
                "    }",
                "});",
            ]);
        });

        $("#example-11").click(function() {

            placeholder.unbind();

            $("#title").text("Donut Hole");
            $("#description").text("A donut hole can be added.");

            $.plot(placeholder, data, {
                series: {
                    pie: { 
                        innerRadius: 0.5,
                        show: true
                    }
                }
            });

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            innerRadius: 0.5,",
                "            show: true",
                "        }",
                "    }",
                "});"
            ]);
        });

        $("#example-12").click(function() {

            placeholder.unbind();

            $("#title").text("Interactivity");
            $("#description").text("The pie can be made interactive with hover and click events.");

            $.plot(placeholder, data, {
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

            setCode([
                "$.plot('#placeholder', data, {",
                "    series: {",
                "        pie: {",
                "            show: true",
                "        }",
                "    },",
                "    grid: {",
                "        hoverable: true,",
                "        clickable: true",
                "    }",
                "});"
            ]);

            placeholder.bind("plothover", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                var percent = parseFloat(obj.series.percent).toFixed(2);
                $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
            });

            placeholder.bind("plotclick", function(event, pos, obj) {

                if (!obj) {
                    return;
                }

                percent = parseFloat(obj.series.percent).toFixed(2);
                alert(""  + obj.series.label + ": " + percent + "%");
            });
        });

        // Show the initial default chart

        $("#example-1").click();

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
