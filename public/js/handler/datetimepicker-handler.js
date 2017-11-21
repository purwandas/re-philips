// Set datetime picker for PATCH METHOD
function setDateTimePickerIfPatch(element, text){

    if($('input[name=_method]').val() == "PATCH"){

        var dt = explodeDateTime(text);

        element.datetimepicker('setDate', (new Date(dt['year'], dt['month'], dt['day'], dt['hour'], dt['minute'])));

    }

}

// Explode string datetime
function explodeDateTime (text) {

    var result = [];

    var arrayDateTime = text.split(' ');
    var arrayDate = arrayDateTime[0].split('-');
    var arrayTime = arrayDateTime[1].split(':');

    result['year'] = arrayDate[0];
    result['month'] = arrayDate[1]-1; // Index bulan nya di mulai dari 0 => "Januari"
    result['day'] = arrayDate[2];
    result['hour'] = arrayTime[0];
    result['minute'] = arrayTime[1];
    result['second'] = arrayTime[2];

    return result;
    
}

/*
 * Datetime Picker validation
 *
 */ 

window.dateTimePickerChange = function(element, formParam){

    // harus cek di html nya (harus pake attr "required")
    if(element.prop('required')) {

        var form = formParam;
        var errorAlert = $('.alert-danger', form);
        var successAlert = $('.alert-success', form);

        // set success class to the control group
        element.closest('.form-group').removeClass('has-error').addClass('has-success');

        // For select2 option
        var span = element.parent('.input-group').children('.input-group-addon');
        span.removeClass('display-hide');

        var spanIcon = $(span).children('i');
        spanIcon.removeClass('fa-warning').addClass("fa-check");
        spanIcon.removeClass('font-red').addClass("font-green");
        spanIcon.attr("data-original-title", "");

        // Check if all requirement valid and show success text
        if(errorAlert.is(":visible") || successAlert.is(":visible")){
            var errors = 0;
            form.each(function(){
                if($(this).find('.form-group').hasClass('has-error')){
                    errors += 1;
                } 
            });            

            if(errors == 0){ 
                successAlert.show();
                errorAlert.hide();
            }else{
                successAlert.hide();
                errorAlert.show();
            }
        }

    }
}

// Format "December 2017" -> "[12, 2017]"
function monthYearFormat(text){

    var splited = text.split(' ');
    var array_temp = [];

    if(splited[0] == 'Januari' || splited[0] == 'January'){
        array_temp[0] = 1;
    }else if(splited[0] == 'Februari' || splited[0] == 'February'){
        array_temp[0] = 2;
    }else if(splited[0] == 'Maret' || splited[0] == 'March'){
        array_temp[0] = 3;
    }else if(splited[0] == 'April' || splited[0] == 'April'){
        array_temp[0] = 4;
    }else if(splited[0] == 'Mei' || splited[0] == 'May'){
        array_temp[0] = 5;
    }else if(splited[0] == 'Juni' || splited[0] == 'June'){
        array_temp[0] = 6;
    }else if(splited[0] == 'Juli' || splited[0] == 'July'){
        array_temp[0] = 7;
    }else if(splited[0] == 'Agustus' || splited[0] == 'August'){
        array_temp[0] = 8;
    }else if(splited[0] == 'September' || splited[0] == 'September'){
        array_temp[0] = 9;
    }else if(splited[0] == 'Oktober' || splited[0] == 'October'){
        array_temp[0] = 10;
    }else if(splited[0] == 'November' || splited[0] == 'November'){
        array_temp[0] = 11;
    }else if(splited[0] == 'Desember' || splited[0] == 'December'){
        array_temp[0] = 12;
    }

    array_temp[1] = splited[1];

    return array_temp;

}