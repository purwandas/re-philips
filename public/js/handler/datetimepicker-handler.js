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