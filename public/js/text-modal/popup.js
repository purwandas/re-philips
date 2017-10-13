// Description Modal Popup
$(document).on("click", ".open-description-modal", function () {	
    var descriptionTitle = document.getElementById('descriptionTitle');
    var descriptionText = document.getElementById('descriptionText');
    descriptionTitle.innerHTML = $(this).data('title');
    descriptionText.innerHTML = $(this).data('description');   
});

// Read by Who Modal Popup
$(document).on("click", ".open-read-who-modal", function () {
    var title = document.getElementById('read-who-title');
    var content = document.getElementById('read-who-content');
    title.innerHTML = $(this).data('title');

    var getDataUrl = $(this).data('url');
    var newsId = $(this).data('id');
    var total_read = $(this).data('total-read');

    if(total_read == 0){
        content.innerHTML = "";
        content.innerHTML += '<div class="row" style="margin-top: 30px;"><div class="col-md-12" style="text-align: center">No data available</div></div>';

        return;
    }

    $.get(getDataUrl + '/' + newsId, function (data) {
        if(data){
            $("#reader").remove();
            var readerTable = '';
            readerTable = "<div id='reader'>"+
                            "<table class='table table-striped table-bordered responsive reader'>"+
                                '<thead>'+
                                    '<tr>'+
                                        "<th class='center'>NIK</th>"+
                                        '<th>Name</th>'+
                                        '<th>Read at</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>';

            $.each(data, function() {
                readerTable+=       '<tr>'+
                                        '<td>'+this.nik+'</td>'+
                                        '<td>'+this.name+'</td>'+
                                        '<td>'+this.read_at+'</td>'+
                                    '</tr>';
            });

            content.innerHTML += readerTable+
                                    '</tbody>'+
                                '</table>'+
                            '</div>';
            
            resourceTable = $('.reader').dataTable({
                               "bInfo" : false
                           });
            


            // $("#read-who-modal").css("position","absolute");
            
            // var windowHeight = $(window).height();//screen.height;//$(document).height();
            // var modalHeight = $("#read-who-modal").height();
            // var gapPersen = modalHeight/windowHeight*100;
            // var gap = windowHeight - modalHeight;
            // $("#reader").append("<b>Window: </b>"+windowHeight+"->> <b>Modal: </b>"+modalHeight+"->> <b>Gap: </b>"+gapPersen);
            $("#read-who-modal").css("top", "25%");
                // $("#read-who-modal").css({
                //     'max-height': 'calc(100% - 100px)',
                //     'position': 'fixed',
                //     'top': '50%',
                //     'left': '50%',
                //     'transform': 'translate(0%, -'+gapPersen+'%)'
                // });

    //                  css({'margin': '0',
    //                   'position': 'absolute'
    //                  });
            // dialog("option", "position", {my: "center", at: "center", of: window});
            // $("#dialog").dialog("option", "position", "center");
        }


    });

});