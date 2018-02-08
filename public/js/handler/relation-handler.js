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

function groupProductRelation(groupProductId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/groupproductrelation',
        dataType: 'json',
        data: { groupProductId: groupProductId },
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

function districtRelation(districtId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/districtrelation',
        dataType: 'json',
        data: { districtId: districtId },
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

function channelRelation(channelId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/channelrelation',
        dataType: 'json',
        data: { channelId: channelId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function subChannelRelation(subChannelId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/subchannelrelation',
        dataType: 'json',
        data: { subChannelId: subChannelId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function distributorRelation(distributorId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/distributorrelation',
        dataType: 'json',
        data: { distributorId: distributorId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function roleRelation(roleId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/rolerelation',
        dataType: 'json',
        data: { roleId: roleId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function gradingRelation(gradingId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/gradingrelation',
        dataType: 'json',
        data: { gradingId: gradingId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

function classificationRelation(classificationId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/classificationrelation',
        dataType: 'json',
        data: { classificationId: classificationId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

/* End Check Relation */