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

// Employee Store Detail Popup
$(document).on("click", ".open-employee-store-modal", function () {
    var title_modal = document.getElementById('title-modal');
    var title_list = document.getElementById('title-list');
    var content = document.getElementById('content');
    title_modal.innerHTML = $(this).data('title');
    title_list.innerHTML = "Promoter : "+$(this).data('promoter-name');

    var getDataUrl = $(this).data('url');
    var userId = $(this).data('id');

    $.get(getDataUrl + '/' + userId, function (data) {
        if(data) {
            content.innerHTML = "";
            $.each(data, function() {
                content.innerHTML += '<div class="list-todo-line blue"></div>'+
                                     '<ul>'+
                                         '<li class="mt-list-item">'+
                                             '<div class="list-todo-icon bg-white font-blue">'+
                                                '<i class="fa fa-database"></i>'+
                                             '</div>'+
                                             '<div class="list-todo-item grey">'+
                                                 '<a class="list-toggle-container font-white collapsed" data-toggle="collapse" href="#'+this.store_id+this.id+'"  aria-expanded="false">'+
                                                    '<div class="list-toggle done uppercase">'+
                                                        '<div class="list-toggle-title bold">'+this.store_id+' - '+this.store_name_1+' ('+this.store_name_2+')'+'</div>'+
                                                    '</div>'+
                                                 '</a>'+
                                                 '<div class="task-list panel-collapse collapse" id="'+this.store_id+this.id+'" aria-expanded="false" style="height: 0px;">'+
                                                    '<ul>'+
                                                        '<li class="task-list-item" >'+
                                                            '<div class="task-content">'+
                                                                '<ul>'+
                                                                    '<li>'+
                                                                        '<h5> Store ID : '+this.store_id+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Store Name 1 : '+this.store_name_1+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Store Name 2 : '+this.store_name_2+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Region : '+this.region_name+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Area : '+this.area_name+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Area RE App : '+this.areaapp_name+'</h5>'+
                                                                    '</li>'+
                                                                    '<li>'+
                                                                        '<h5> Supervisor : '+this.spv_name+'</h5>'+
                                                                    '</li>'+
                                                                '</ul>'+
                                                            '</div>'+
                                                        '</li>'+
                                                    '</ul>'+
                                                 '</div>'+
                                             '</div>'+
                                         '</li>'+
                                     '</ul>';
            });
        }
    });

});