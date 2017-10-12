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
            var readerTable = '';
            readerTable = "<table class='table table-striped table-bordered responsive reader'>"+
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
                                '</table>';
            resourceTable = $('.reader').dataTable({
                               "bInfo" : false
                           });
        }


    });

});