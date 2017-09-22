// Code for Match Entry
function getMatchEntryCode() {
    return JSON.parse($.ajax({
        type: 'GET',
        url: 'service/matchentrycode',
        dataType: 'json',
        data: {},
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}