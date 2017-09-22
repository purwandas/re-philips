// Checking if branch still has child data in kind sport
function branchKindRelation(branchSportId) {
    // return branchSportId;
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/branchkind',
        dataType: 'json',
        data: { branchSportId: branchSportId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if kind sport still has child data in type sport
function kindTypeRelation(kindSportId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/kindtype',
        dataType: 'json',
        data: { kindSportId: kindSportId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}


// Checking if countries still has child data in athletes
function countryAthleteRelation(countryId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/countryathlete',
        dataType: 'json',
        data: { countryId: countryId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if type sport still has child data in match entry
function typeMatchRelation(typeSportId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/typematch',
        dataType: 'json',
        data: { typeSportId: typeSportId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if type sport still has child data in schedule
function typeScheduleRelation(typeSportId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/typeschedule',
        dataType: 'json',
        data: { typeSportId: typeSportId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if type sport still has child data in athlete
function typeAthleteRelation(typeSportId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/typeathlete',
        dataType: 'json',
        data: { typeSportId: typeSportId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if athlete still has child data in match group
function athleteMatchGroupRelation(athleteId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/athletematchgroup',
        dataType: 'json',
        data: { athleteId: athleteId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if match entry still has child data in match group
function matchEntryMatchGroupRelation(matchEntryId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/matchentrymatchgroup',
        dataType: 'json',
        data: { matchEntryId: matchEntryId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}


// Checking if match entry still has child data in schedule details
function matchEntryScheduleDetailsRelation(matchEntryId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/matchentryscheduledetails',
        dataType: 'json',
        data: { matchEntryId: matchEntryId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}

// Checking if schedule still has child data in schedule details
function scheduleScheduleDetailsRelation(scheduleId) {
    return JSON.parse($.ajax({
        type: 'POST',
        url: 'relation/schedulescheduledetails',
        dataType: 'json',
        data: { scheduleId: scheduleId },
        global: false,
        async: false,
        success: function (data) {
            return data;
        }
    }).responseText);
}