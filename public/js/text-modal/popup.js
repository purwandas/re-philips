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

            content.innerHTML = "";
            content.innerHTML += '<div class="row" style="margin-bottom: 15px;">'+
                                    '<div class="col-md-3">'+
                                        '<b>NIK</b>'+
                                    '</div>'+
                                    '<div class="col-md-5">'+
                                        '<b>Name</b>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<b>Read at</b>'+
                                    '</div>'+
                                '</div>';

            $.each(data, function() {
                content.innerHTML += '<div class="row" style="margin-bottom: 7px;">'+
                                        '<div class="col-md-3">'+this.nik+'</div>'+
                                        '<div class="col-md-5">'+this.name+'</div>'+
                                        '<div class="col-md-4">'+this.read_at+'</div>'+
                                     '</div>';
            });

        }


    });

});