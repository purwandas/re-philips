var filters = {};
var data = {};

function showFilter() {
    console.log(this.filters);
}

// Reset all filter for search
function triggerReset (arrayOfData) {

    var data = arrayOfData[0];
    var table = arrayOfData[1];
    var element = arrayOfData[2];
    var url = arrayOfData[3];
    var tableColumns = arrayOfData[4];
    var columnDefs = arrayOfData[5];
    var order = arrayOfData[6];

    data.map((id) => {
        $(id).val('').trigger('change');
    });

    this.filters = {};

     // Datatable setup

    if($.fn.dataTable.isDataTable('#'+table)){
        element.DataTable().clear();
        element.DataTable().destroy();
    }
    // element.dataTable({
    //     "fnCreatedRow": function (nRow, data) {
    //         $(nRow).attr('class', data.id);
    //     },
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": {
    //         url: url,
    //         type: 'POST',
    //         dataType: 'json',
    //     },
    //     "rowId": "id",
    //     "columns": tableColumns,
    //     "columnDefs": columnDefs,
    //     "order": order,
    // })
}

// Set the selected value to key in filter
function selected (key, val) {
    this.filters[key] = val;
    console.log(this.filters);
}

// Filtering data
function filteringReport(arrayOfData) {

    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];

    this.moreParams = [];
    this.moreParamsPost  = {};

    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }

    var self = this;
    $(document).ready(function () {
        // console.log(self.moreParamsPost);
        if($.fn.dataTable.isDataTable('#'+table)){
            element.DataTable().clear();
            element.DataTable().destroy();
        }
        element.dataTable({
            "fnCreatedRow": function( nRow, data ) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url,
                data: self.moreParamsPost,
                type: 'POST',
                dataType: 'json',
                dataSrc: function(result){

                    if(typeof arrayOfData[6] !== 'undefined') {
                        // export option exist

                        var count = result.data.length;

                        if(count > 0){
                            $(arrayOfData[6]).removeAttr('disabled');
                        }else{
                            $(arrayOfData[6]).attr('disabled','disabled');
                        }
                    }

                    data = result.data;
                    return result.data;
                }
            },
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
        })
    })
}

// Filtering data
function filteringAttendanceReport(arrayOfData) {

    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];

    // console.log(tableColumns);

    this.moreParams = [];
    this.moreParamsPost  = {};

    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }

    var self = this;
    $(document).ready(function () {

        if($.fn.dataTable.isDataTable('#'+table)){
            // element.DataTable().clear();
            element.DataTable().destroy();
        }

        element.dataTable({
            "fnCreatedRow": function( nRow, data ) {
                $(nRow).attr('class', data.id);
            },
            // "scrollY":        "300px", 
                "scrollX":        true, 
                "scrollCollapse": true, 
                "paging":         true, 
                "fixedColumns":   { 
                    "leftColumns": "4",
                    // {{--"rightColumns": 1 --}}
                },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url,
                data: self.moreParamsPost,
                type: 'POST',
                dataType: 'json',
                dataSrc: function(result){

                    if(typeof arrayOfData[6] !== 'undefined') {
                        // export option exist

                        var count = result.data.length;

                        if(count > 0){
                            $(arrayOfData[6]).removeAttr('disabled');
                        }else{
                            $(arrayOfData[6]).attr('disabled','disabled');
                        }
                    }

                    data = result.data;
                    return result.data;
                }
            },
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
        })
    })
}

// Set options
function setOptions (url, placeholder, data, processResults) {
    return {
        ajax: {
            url: url,
            method: 'POST',
            dataType: 'json',
            delay: 250,
            data: data,
            processResults: processResults
        },   
        // minimumInputLength: 2,     
        width: '100%',
        placeholder: placeholder,
    }
}

// Filter data method
function filterData (search, term) {

    // Check if term is ""
    (term == "" || term == null) ? term = "all" : term = term;

    var results = {};
    if ($.isEmptyObject(filters)) {

        // Check search term is array or string
        if(!$.isArray(search)){

            return {
                [search]: term
            }
        }

        search.forEach(function(item) {
            results[item] = term
        });

        // console.log('result-search');
        console.log(results);

        return results;
    }

    for (var filter in filters) {
        results[filter] = filters[filter];        
    }

    // Check search term is array or string
    if(!$.isArray(search)){
        results[search] = term
    }else{
        search.forEach(function(item) {
            results[item] = term
        });
    }

    // console.log('results');
    console.log(results);

    return results;
}

// Set select2 for PATCH METHOD
function setSelect2IfPatch(element, id, text){

    if($('input[name=_method]').val() == "PATCH"){

        element.select2("trigger", "select", {
            data: { id: id, text: text }
        });

        // Remove validation of success
        element.closest('.form-group').removeClass('has-success');

        var span = element.parent('.input-group').children('.input-group-addon');
        span.addClass('display-hide');

        // Remove focus from selection
        element.next().removeClass('select2-container--focus');
        // $(".select2-container--focus").removeClass("select2-container--focus");

        // Disable select2 focus
        $('.select2-search input').prop('focus',false);
        window.scrollTo(0, 0);
        $('html, body').scrollTop();
    }

}

// Set select2 for PATCH METHOD => FOR MODALS
function setSelect2IfPatchModal(element, id, text){

    element.select2("trigger", "select", {
        data: { id: id, text: text }
    });

    // Remove validation of success
    element.closest('.form-group').removeClass('has-success');

    var span = element.parent('.input-group').children('.input-group-addon');
    span.addClass('display-hide');

    // Remove focus from selection
    element.next().removeClass('select2-container--focus');

}

// Reset select2
function select2Reset(element){

    element.select2('val','All');

    // Remove validation of success
    element.closest('.form-group').removeClass('has-success');

    var span = element.parent('.input-group').children('.input-group-addon');
    span.addClass('display-hide');

    // Remove focus from selection
    element.next().removeClass('select2-container--focus');

}

/*
 * Select2 validation
 *
 */ 

window.select2Change = function(element, formParam){

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