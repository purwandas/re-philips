// Check all relation
/*
 * CHILD -> PARENT (PARENT ID)
 *
 */


function salesStoreRelation(storeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/salesstore',
        dataType: 'json',
        data: { storeId: storeId },
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


/* Check Relation ( [filename]Relation ) */

function userRelation(userId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/userrelation',
        dataType: 'json',
        data: { userId: userId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function storeRelation(storeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/storerelation',
        dataType: 'json',
        data: { storeId: storeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function productRelation(productId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/productionrelation',
        dataType: 'json',
        data: { productId: productId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function posmRelation(posmId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/posmrelation',
        dataType: 'json',
        data: { posmId: posmId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function groupCompetitorRelation(groupCompetitorId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/groupcompetitorrelation',
        dataType: 'json',
        data: { groupCompetitorId: groupCompetitorId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function groupRelation(groupId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/grouprelation',
        dataType: 'json',
        data: { groupId: groupId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function employeeRelation(userId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/employeerelation',
        dataType: 'json',
        data: { userId: userId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function categoryRelation(categoryId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/categoryrelation',
        dataType: 'json',
        data: { categoryId: categoryId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function areaAppRelation(areaAppId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/areaapprelation',
        dataType: 'json',
        data: { areaAppId: areaAppId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function areaRelation(areaId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/arearelation',
        dataType: 'json',
        data: { areaId: areaId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function accountTypeRelation(accountTypeId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/accounttyperelation',
        dataType: 'json',
        data: { accountTypeId: accountTypeId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function accountRelation(accountId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/accountrelation',
        dataType: 'json',
        data: { accountId: accountId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

/* End Check Relation */