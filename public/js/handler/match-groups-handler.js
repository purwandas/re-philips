// jQuery(document).ready(function ($) {
//     var form = $('#form_match_groups');

//     // Using FormData to append file type to form input
// 	var formData = new FormData($(form)[0]);

//     form.submit(function () {
//         $.ajax({
//             url: form.action,
//             type: form.method,
//             // data: $form.serialize(),
//             data: formData,
//             processData: false,
// 			contentType: false,
//             success: function (data) {                            
                            
//                 swal({
//                         title: 'Insert!',
//                         text: 'Data has been created!',
//                         type: 'success'
//                     },
//                     function(){                                               
//                         $('#matchGroupsTable').DataTable().ajax.reload();                              
//                     }
//                 )

//             },
//             error: function(response) {
//                 console.log('Error:', response);
//                 swal("Error!", "Failed to perform the task!", "error");
//             }

//         });

//     });
// });

jQuery(document).ready(function () {
        var form = $('#form_match_groups');

        form.submit(function () {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {

                    swal({
                        title: 'Insert!',
                        text: 'Data has been created!',
                        type: 'success'
                    },
                    function(){                                               
                        $('#matchGroupsTable').DataTable().ajax.reload();                        

                        // Set select2 again
                        select2Reset($("#matchGroupAthlete"));

                        var id = $('#matchGroupMatchEntryId').val();
                        initSelect2MatchGroup(id);
                    })

                },
                error: function(response) {
	                console.log('Error:', response);
	                swal("Error!", "Failed to perform the task!", "error");
	            }
            });

            return false;
        });
    });