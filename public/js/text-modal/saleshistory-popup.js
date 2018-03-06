// Sales History Modal Popup
$(document).on("click", ".open-sales-history-modal", function () {
    var title = document.getElementById('sales-history-title');
    var content = document.getElementById('sales-history-content');
    var activityId = $(this).data('id');
    var historyType = $(this).data('history');
    var listId = ".activity-"+activityId;
    title.innerHTML = $(this).data('title');

    // var newCount = $('.bedgecount2').text().toString();
    // $('.bedgecount2').text('');
    // $('.bedgecount2').text(1);
    
    var x = parseInt($('.bedgecount2').text().substr(0, $('.bedgecount2').text().length-1))-1;
    // console.log(x);
    $('.bedgecount2').text(x);
    // $(".open-sales-history-modal").removeAttr("data-count");
    // $(".open-sales-history-modal").attr("data-count", newCount);
    $(listId).remove();

    if(historyType == 'promoter'){
        var data = {id:activityId};
        $.ajax({
            url: "util/sales-history-read",
            type: "POST",
            data: data,
            dataType:"json"
        });
    }else{
        var data = {id:activityId};
        $.ajax({
            url: "util/salesman-sales-history-read",
            type: "POST",
            data: data,
            dataType:"json"
        });
    }

    // // var urlCount = "{{url('util/sales-history-count')}}";
    //     $.get('util/sales-history-count', function (data) {
    //        // console.log(data);
    //         $('.bedgecount2').text(data.count);

    //     });

    content.innerHTML = '';

    if(historyType == 'promoter'){

        content.innerHTML += '<div class="row" style="margin-top: 30px;">';
        
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> User Name </div><div class='col-md-8'>" + $(this).data('user')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> User Role </div><div class='col-md-8'>" + $(this).data('role')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Execution Date </div><div class='col-md-8'>" + $(this).data('date')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Activity </div><div class='col-md-8'>" + $(this).data('activity')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Sell Type </div><div class='col-md-8'>" + $(this).data('type')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Execution From </div><div class='col-md-8'>" + $(this).data('action_from')       + "</div></a>";
        content.innerHTML += "<div class='col-md-12 list-group-item'></div>";
        content.innerHTML += "<div class='col-md-12 list-group-item'><h5><b> DATA DETAIL </b></h5></div>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Week </div><div class='col-md-8'>" + $(this).data('week')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Distributor Code </div><div class='col-md-8'>" + $(this).data('distributor_code')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Distributor Name </div><div class='col-md-8'>" + $(this).data('distributor_name')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Region </div><div class='col-md-8'>" + $(this).data('region')        + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Channel </div><div class='col-md-8'>" + $(this).data('channel')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Sub Channel </div><div class='col-md-8'>" + $(this).data('sub_channel')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Area </div><div class='col-md-8'>" + $(this).data('area')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> District </div><div class='col-md-8'>" + $(this).data('district')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Store Name 1 </div><div class='col-md-8'>" + $(this).data('store_name_1')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Customer Code </div><div class='col-md-8'>" + $(this).data('store_name_2')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Store ID </div><div class='col-md-8'>" + $(this).data('store_id')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Input Date </div><div class='col-md-8'>" + $(this).data('date')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Promoter Name </div><div class='col-md-8'>" + $(this).data('promoter_name')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Promoter NIK </div><div class='col-md-8'>" + $(this).data('nik')     + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Role </div><div class='col-md-8'>" + $(this).data('role')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Supervisor Name </div><div class='col-md-8'>" + $(this).data('spv_name')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Dedicate </div><div class='col-md-8'>" + $(this).data('dedicate')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> DM Name </div><div class='col-md-8'>" + $(this).data('dm_name')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Trainer Name </div><div class='col-md-8'>" + $(this).data('trainer_name')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Product </div><div class='col-md-8'>" + $(this).data('product_name')     + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Model </div><div class='col-md-8'>" + $(this).data('model')     + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Category </div><div class='col-md-8'>" + $(this).data('category')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Group </div><div class='col-md-8'>" + $(this).data('group')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Unit Price </div><div class='col-md-8'>" + $(this).data('unit_price')        + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Quantity </div><div class='col-md-8'> <b>" + $(this).data('quantity')      + "</b></div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Value </div><div class='col-md-8'>" + $(this).data('value')     + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Value PF MR </div><div class='col-md-8'>" + $(this).data('value_pf_mr')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Value PF TR </div><div class='col-md-8'>" + $(this).data('value_pf_tr')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Value PF PPE </div><div class='col-md-8'>" + $(this).data('value_pf_ppe')      + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Quantity </div><div class='col-md-8'><b>" + $(this).data('new_quantity')      + "</b></div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> New Value </div><div class='col-md-8'>" + $(this).data('new_value')     + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Value PF MR </div><div class='col-md-8'>" + $(this).data('new_value_pf_mr')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> New Value PF TR </div><div class='col-md-8'>" + $(this).data('new_value_pf_tr')       + "</div></a>";
        content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Value PF PPR </div><div class='col-md-8'>" + $(this).data('new_value_pf_ppe')      + "</div></a>";

            content.innerHTML += '</div>';

        }else{

            content.innerHTML += '<div class="row" style="margin-top: 30px;">';
        
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> User Name </div><div class='col-md-8'>" + $(this).data('user')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> User Role </div><div class='col-md-8'>" + $(this).data('role')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Execution Date </div><div class='col-md-8'>" + $(this).data('date')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Activity </div><div class='col-md-8'>" + $(this).data('activity')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Sell Type </div><div class='col-md-8'>" + $(this).data('type')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Execution From </div><div class='col-md-8'>" + $(this).data('action_from')       + "</div></a>";
            content.innerHTML += "<div class='col-md-12 list-group-item'></div>";
            content.innerHTML += "<div class='col-md-12 list-group-item'><h5><b> DATA DETAIL </b></h5></div>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Week </div><div class='col-md-8'>" + $(this).data('week')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Distributor Code </div><div class='col-md-8'>" + $(this).data('distributor_code')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Distributor Name </div><div class='col-md-8'>" + $(this).data('distributor_name')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Region </div><div class='col-md-8'>" + $(this).data('region')        + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Channel </div><div class='col-md-8'>" + $(this).data('channel')       + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Sub Channel </div><div class='col-md-8'>" + $(this).data('sub_channel')       + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Area </div><div class='col-md-8'>" + $(this).data('area')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> District </div><div class='col-md-8'>" + $(this).data('district')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Store Name 1 </div><div class='col-md-8'>" + $(this).data('store_name_1')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Customer Code </div><div class='col-md-8'>" + $(this).data('store_name_2')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Store ID </div><div class='col-md-8'>" + $(this).data('store_id')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Input Date </div><div class='col-md-8'>" + $(this).data('date')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Promoter Name </div><div class='col-md-8'>" + $(this).data('promoter_name')       + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Promoter NIK </div><div class='col-md-8'>" + $(this).data('nik')     + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Role </div><div class='col-md-8'>" + $(this).data('role')      + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Supervisor Name </div><div class='col-md-8'>" + $(this).data('spv_name')      + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Dedicate </div><div class='col-md-8'>" + $(this).data('dedicate')      + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> DM Name </div><div class='col-md-8'>" + $(this).data('dm_name')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Trainer Name </div><div class='col-md-8'>" + $(this).data('trainer_name')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Product </div><div class='col-md-8'>" + $(this).data('product_name')     + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Model </div><div class='col-md-8'>" + $(this).data('model')     + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Category </div><div class='col-md-8'>" + $(this).data('category')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Group </div><div class='col-md-8'>" + $(this).data('group')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Unit Price </div><div class='col-md-8'>" + $(this).data('unit_price')        + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Quantity </div><div class='col-md-8'> <b>" + $(this).data('quantity')      + "</b></div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Value </div><div class='col-md-8'>" + $(this).data('value')     + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Value PF </div><div class='col-md-8'>" + $(this).data('value_pf')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Value PF MR </div><div class='col-md-8'>" + $(this).data('value_pf_mr')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> Value PF TR </div><div class='col-md-8'>" + $(this).data('value_pf_tr')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> Value PF PPE </div><div class='col-md-8'>" + $(this).data('value_pf_ppe')      + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Quantity </div><div class='col-md-8'><b>" + $(this).data('new_quantity')      + "</b></div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> New Value </div><div class='col-md-8'>" + $(this).data('new_value')     + "</div></a>";
            content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Value PF </div><div class='col-md-8'>" + $(this).data('new_value_pf')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Value PF MR </div><div class='col-md-8'>" + $(this).data('new_value_pf_mr')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item'><div class='col-md-4'> New Value PF TR </div><div class='col-md-8'>" + $(this).data('new_value_pf_tr')       + "</div></a>";
            // content.innerHTML += "<a href='#' class='col-md-12 list-group-item list-group-item-info'><div class='col-md-4'> New Value PF PPR </div><div class='col-md-8'>" + $(this).data('new_value_pf_ppe')      + "</div></a>";

                content.innerHTML += '</div>';

        }

});