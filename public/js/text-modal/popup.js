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

// Employee Store Detail Popup
$(document).on("click", ".open-employee-store-modal", function () {
    var title_modal = document.getElementById('title-modal');
    var title_list = document.getElementById('title-list');
    var content = document.getElementById('content');
    title_modal.innerHTML = $(this).data('title');
    title_list.innerHTML = "Promoter : "+$(this).data('promoter-name');

    var getDataUrl = $(this).data('url');
    var userId = $(this).data('id');
    content.innerHTML = "";

    $.get(getDataUrl + '/' + userId, function (data) {
        if(data) {

            $.each(data, function() {

                if(this.spv_name != "") {

                    content.innerHTML += '<div class="list-todo-line blue"></div>' +
                        '<ul>' +
                        '<li class="mt-list-item">' +
                        '<div class="list-todo-icon bg-white font-blue">' +
                        '<i class="fa fa-database"></i>' +
                        '</div>' +
                        '<div class="list-todo-item grey">' +
                        '<a class="list-toggle-container font-white collapsed" data-toggle="collapse" href="#' + this.store_id + this.id + '"  aria-expanded="false">' +
                        '<div class="list-toggle done uppercase">' +
                        '<div class="list-toggle-title bold">' + this.store_id + ' - ' + this.store_name_1 + ' (' + this.store_name_2 + ')' + '</div>' +
                        '</div>' +
                        '</a>' +
                        '<div class="task-list panel-collapse collapse" id="' + this.store_id + this.id + '" aria-expanded="false" style="height: 0px;">' +
                        '<ul>' +
                        '<li class="task-list-item" >' +
                        '<div class="task-content">' +
                        '<ul>' +
                        '<li>' +
                        '<h5> Store ID : ' + this.store_id + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Store Name 1 : ' + this.store_name_1 + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Store Name 2 : ' + this.store_name_2 + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Region : ' + this.region_name + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Area : ' + this.area_name + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> District : ' + this.district_name + '</h5>' +
                        '</li>' +
                        '<li>'+
                    '<h5> Supervisor : ' + this.spv_name + '</h5>' +
                    '</li>' +
                    '</ul>' +
                    '</div>' +
                    '</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '</li>' +
                    '</ul>';

                }else{
                    content.innerHTML += '<div class="list-todo-line blue"></div>' +
                        '<ul>' +
                        '<li class="mt-list-item">' +
                        '<div class="list-todo-icon bg-white font-blue">' +
                        '<i class="fa fa-database"></i>' +
                        '</div>' +
                        '<div class="list-todo-item grey">' +
                        '<a class="list-toggle-container font-white collapsed" data-toggle="collapse" href="#' + this.store_id + this.id + '"  aria-expanded="false">' +
                        '<div class="list-toggle done uppercase">' +
                        '<div class="list-toggle-title bold">' + this.store_id + ' - ' + this.store_name_1 + ' (' + this.store_name_2 + ')' + '</div>' +
                        '</div>' +
                        '</a>' +
                        '<div class="task-list panel-collapse collapse" id="' + this.store_id + this.id + '" aria-expanded="false" style="height: 0px;">' +
                        '<ul>' +
                        '<li class="task-list-item" >' +
                        '<div class="task-content">' +
                        '<ul>' +
                        '<li>' +
                        '<h5> Store ID : ' + this.store_id + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Store Name 1 : ' + this.store_name_1 + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Store Name 2 : ' + this.store_name_2 + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Region : ' + this.region_name + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> Area : ' + this.area_name + '</h5>' +
                        '</li>' +
                        '<li>' +
                        '<h5> District : ' + this.district_name + '</h5>' +
                        '</li>' +
                    '</ul>' +
                    '</div>' +
                    '</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '</li>' +
                    '</ul>';
                }


            });
        }
    });

});
