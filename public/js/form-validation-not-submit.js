// Branch Sports Master Validation
var branchSportsValidation = function() {

        var formBranchSports = $('#form_branch_sports');
        var errorBranchSports = $('.alert-danger', formBranchSports);
        var successBranchSports = $('.alert-success', formBranchSports);

        formBranchSports.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                name: {
                    minlength: 2,
                    required: true
                },
                location: {
                    minlength: 2,
                    required: true                        
                },
            },

            invalidHandler: function (event, validator) { //display error alert on form submit              
                successBranchSports.hide();
                errorBranchSports.show();
                App.scrollTo(errorBranchSports, -200);
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");  
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },

            unhighlight: function (element) { // revert the change done by hightlight
                    
            },

            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            // submitHandler: function (form) {
            //     successBranchSports.show();
            //     errorBranchSports.hide();
            //     form[0].submit(); // submit the form
            // }
        });


}