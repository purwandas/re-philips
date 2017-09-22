// Checking if schedule details must have one match entry data
function checkScheduleDetail(scheduleId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'util/checkscheduledetail',
        dataType: 'json',
        data: { scheduleId: scheduleId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}