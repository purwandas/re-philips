// Check all relation
/*
 * CHILD -> PARENT (PARENT ID)
 *
 */

function areaAppsAreaRelation(areaId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/areaaappsarea',
        dataType: 'json',
        data: { areaId: areaId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function accountAccountTypeRelation(accountTypeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/accountaccounttype',
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

function storeSpvRelation(userId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/storespv',
        dataType: 'json',
        data: { userId: userId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function salesEmployeeRelation(userId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/salesemployee',
        dataType: 'json',
        data: { userId: userId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function categoryGroupRelation(groupId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/categorygroup',
        dataType: 'json',
        data: { groupId: groupId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function productCategoryRelation(categoryId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/productcategory',
        dataType: 'json',
        data: { categoryId: categoryId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function newsEmployeeRelation(employeeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/newsemployee',
        dataType: 'json',
        data: { employeeId: employeeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function newsStoreRelation(storeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/newsstore',
        dataType: 'json',
        data: { storeId: storeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function newsAreaRelation(areaId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/newsarea',
        dataType: 'json',
        data: { areaId: areaId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Check role relation
function storeSpvChangeRelation(spvId, role) {    
    return JSON.parse($.ajax({
        type: 'POST',
        url: '../../relation/storespvchange',
        dataType: 'json',
        data: { 
            spvId: spvId,
            role: role
        },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function salesEmployeeChangeRelation(employeeId, role) {    
    return JSON.parse($.ajax({
        type: 'POST',
        url: '../../relation/salesemployeechange',
        dataType: 'json',
        data: { 
            employeeId: employeeId,
            role: role
        },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}