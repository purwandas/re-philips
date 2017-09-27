// Check all relation
function areaAreaAppsRelation(areaId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/areaareaapps',
        dataType: 'json',
        data: { areaId: areaId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function areaDmRelation(areaId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/areadm',
        dataType: 'json',
        data: { areaId: areaId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function accountTypeAccountRelation(accountTypeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/accounttypeaccount',
        dataType: 'json',
        data: { accountTypeId: accountTypeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function storeAccountRelation(accountId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/storeaccount',
        dataType: 'json',
        data: { accountId: accountId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function storeAreaAppRelation(areaAppId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/areaapp',
        dataType: 'json',
        data: { areaAppId: areaAppId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function storeSpvRelation(employeeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/storespv',
        dataType: 'json',
        data: { employeeId: employeeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}