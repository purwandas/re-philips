/* Fungsi buat nampilin list sport */

// Branch Sport List
// function branchSportList(){
// 	$("#branchSportList").empty();

// 	$.ajax({
//         url: "{{ route('data.branchsports') }}",
//         type: 'POST',
//         data: {},
//         dataType: 'json',                        
//         processData: false,
//         contentType: false,
//         success: function (data) {
// 			console.log(data);            
//         }
//     });
// }


function tambah(){
		
		// $("#branchSportList").append("<>");
		// $("#appendToThis").append("<p>HELLO</p>");

		// $.get("../resources/views/partial/list/branchsport-list.blade.php", function (data) {
  //                   $("#branchSportList").append(data);
  //               });
  		// $("#branchSportList").empty();

  		var image = 'IMAGE';
  		var text = "TEXT";

  		// var images = '<img width="30px" height="30px" src="{{ asset(&#39;image/missing.png&#39;) }}">'

  		var element = 	"<li class='mt-list-item sport-block' onclick='test()'>"+
	  					  	"<div class='row sport-block-content'>"+
	                        	"<div class='col-md-2 sport-block-icon'>"+
	                            	"<img width='30px' height='30px' src='"+ image +"'>"+
	                            "</div>"+
	                        	"<div class='col-md-8'>"+ text +"</div>"+
	                        	"<div class='col-md-2 sport-block-icon'>"+
	                        		"<i class='fa fa-chevron-circle-right'></i>"+
	                        	"</div>"+
	                        "</div>"+
                    	"</li>";

  		$("#branchSportList").append(element);
}
