<?php

/*
|--------------------------------------------------------------------------
| Utilities Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are related to utilities methods
|
*/

Route::group(['middleware' => ['auth']], function () {

    /**
     * Datatable
     */

    Route::post('datatable/user', ['as'=> 'datatable.user','uses'=>'UserController@masterDataTable']);
    Route::post('datatable/userpromoter', ['as'=> 'datatable.userpromoter','uses'=>'UserPromoterController@masterDataTable']);
    Route::post('datatable/resign', ['as'=> 'datatable.resign','uses'=>'Master\ResignController@masterDataTable']);
    Route::post('datatable/rejoin', ['as'=> 'datatable.rejoin','uses'=>'Master\ResignController@masterDataTableRejoin']);
    Route::post('datatable/area', ['as'=> 'datatable.area','uses'=>'Master\AreaController@masterDataTable']);
    Route::post('datatable/sellin', ['as'=> 'datatable.sellin','uses'=>'Master\SellInController@masterDataTable']);
    Route::post('datatable/sellout', ['as'=> 'datatable.sellout','uses'=>'Master\SellOutController@masterDataTable']);
    Route::post('datatable/district', ['as'=> 'datatable.district','uses'=>'Master\DistrictController@masterDataTable']);
    Route::post('datatable/channel', ['as'=> 'datatable.channel','uses'=>'Master\ChannelController@masterDataTable']);
    Route::post('datatable/subchannel', ['as'=> 'datatable.subchannel','uses'=>'Master\SubChannelController@masterDataTable']);
    Route::post('datatable/distributor', ['as'=> 'datatable.distributor','uses'=>'Master\DistributorController@masterDataTable']);
    Route::post('datatable/employee', ['as'=> 'datatable.employee','uses'=>'Master\EmployeeController@masterDataTable']);
    Route::post('datatable/fanspage', ['as'=> 'datatable.fanspage','uses'=>'Master\FanspageController@masterDataTable']);
    Route::post('datatable/store', ['as'=> 'datatable.store','uses'=>'Master\StoreController@masterDataTable']);
    Route::post('datatable/place', ['as'=> 'datatable.place','uses'=>'Master\PlaceController@masterDataTable']);
    Route::post('datatable/groupcompetitor', ['as'=> 'datatable.groupcompetitor','uses'=>'Master\GroupCompetitorController@masterDataTable']);
    Route::post('datatable/group', ['as'=> 'datatable.group','uses'=>'Master\GroupController@masterDataTable']);
    Route::post('datatable/groupproduct', ['as'=> 'datatable.groupproduct','uses'=>'Master\GroupProductController@masterDataTable']);
    Route::post('datatable/category', ['as'=> 'datatable.category','uses'=>'Master\CategoryController@masterDataTable']);
    Route::post('datatable/product', ['as'=> 'datatable.product','uses'=>'Master\ProductController@masterDataTable']);
    Route::post('datatable/news', ['as'=> 'datatable.news','uses'=>'Master\NewsController@masterDataTable']);
    Route::post('datatable/productknowledge', ['as'=> 'datatable.productknowledge','uses'=>'Master\ProductKnowledgeController@masterDataTable']);
    Route::post('datatable/posm', ['as'=> 'datatable.posm','uses'=>'Master\PosmController@masterDataTable']);
    Route::post('datatable/quiz', ['as'=> 'datatable.quiz','uses'=>'Master\QuizController@masterDataTable']);
    Route::post('datatable/price', ['as'=> 'datatable.price','uses'=>'Master\PriceController@masterDataTable']);
    Route::post('datatable/target', ['as'=> 'datatable.target','uses'=>'Master\TargetController@masterDataTable']);
    Route::post('datatable/targetsalesman', ['as'=> 'datatable.targetsalesman','uses'=>'Master\TargetSalesmanController@masterDataTable']);
    Route::post('datatable/productfocus', ['as'=> 'datatable.productfocus','uses'=>'Master\ProductFocusController@masterDataTable']);
    Route::post('datatable/productpromo', ['as'=> 'datatable.productpromo','uses'=>'Master\ProductPromoController@masterDataTable']);
    Route::post('datatable/productfocussalesman', ['as'=> 'datatable.productfocussalesman','uses'=>'Master\ProductFocusSalesmanController@masterDataTable']);
    Route::post('datatable/messageToAdmin', ['as'=> 'datatable.messageToAdmin','uses'=>'MessageToAdminController@masterDataTable']);
    Route::post('datatable/feedbackCategory', ['as'=> 'datatable.feedbackCategory','uses'=>'Master\FeedbackCategoryController@masterDataTable']);
    Route::post('datatable/feedbackQuestion', ['as'=> 'datatable.feedbackQuestion','uses'=>'Master\FeedbackQuestionController@masterDataTable']);
    Route::post('datatable/feedbackAnswer', ['as'=> 'datatable.feedbackAnswer','uses'=>'Master\FeedbackAnswerController@masterDataTable']);
    Route::post('datatable/faq', ['as'=> 'datatable.faq','uses'=>'Master\FaqController@masterDataTable']);
    Route::post('datatable/role', ['as'=> 'datatable.role','uses'=>'Master\RoleController@masterDataTable']);
    Route::post('datatable/grading', ['as'=> 'datatable.grading','uses'=>'Master\GradingController@masterDataTable']);
    Route::post('datatable/classification', ['as'=> 'datatable.classification','uses'=>'Master\ClassificationController@masterDataTable']);
    Route::post('datatable/leadtime', ['as'=> 'datatable.leadtime','uses'=>'Master\LeadtimeController@masterDataTable']);
    Route::post('datatable/timegone', ['as'=> 'datatable.timegone','uses'=>'Master\TimeGoneController@masterDataTable']);
    Route::post('datatable/apm', ['as'=> 'datatable.apm','uses'=>'Master\ApmController@masterDataTable']);

    /**
     * Edit Sales
     */

    Route::post('datatable/editsellin', ['as'=> 'datatable.editsellin','uses'=>'Master\EditSellInController@masterDataTable']);
    Route::post('datatable/editsellout', ['as'=> 'datatable.editsellout','uses'=>'Master\EditSellOutController@masterDataTable']);
    Route::post('datatable/editretdistributor', ['as'=> 'datatable.editretdistributor','uses'=>'Master\EditRetDistributorController@masterDataTable']);
    Route::post('datatable/editretconsument', ['as'=> 'datatable.editretconsument','uses'=>'Master\EditRetConsumentController@masterDataTable']);
    Route::post('datatable/editfreeproduct', ['as'=> 'datatable.editfreeproduct','uses'=>'Master\EditFreeProductController@masterDataTable']);
    Route::post('datatable/edittbat', ['as'=> 'datatable.edittbat','uses'=>'Master\EditTbatController@masterDataTable']);
    Route::post('datatable/editsoh', ['as'=> 'datatable.editsoh','uses'=>'Master\EditSohController@masterDataTable']);
    Route::post('datatable/editdisplayshare', ['as'=> 'datatable.editdisplayshare','uses'=>'Master\EditDisplayShareController@masterDataTable']);
    Route::post('datatable/editposmactivity', ['as'=> 'datatable.editposmactivity','uses'=>'Master\EditPosmActivityController@masterDataTable']);

    
    /**
     * Report
     */

    Route::post('datatable/sellinreport', ['as'=> 'datatable.sellinreport','uses'=>'Master\ReportController@sellInData']);
    Route::post('datatable/selloutreport', ['as'=> 'datatable.selloutreport','uses'=>'Master\ReportController@sellOutData']);
    Route::post('datatable/retconsumentreport', ['as'=> 'datatable.retconsumentreport','uses'=>'Master\ReportController@retConsumentData']);
    Route::post('datatable/retdistributorreport', ['as'=> 'datatable.retdistributorreport','uses'=>'Master\ReportController@retDistributorData']);
    Route::post('datatable/tbatreport', ['as'=> 'datatable.tbatreport','uses'=>'Master\ReportController@tbatData']);
    Route::post('datatable/displaysharereport', ['as'=> 'datatable.displaysharereport','uses'=>'Master\ReportController@displayShareData']);
    Route::post('datatable/freeproductreport', ['as'=> 'datatable.freeproductreport','uses'=>'Master\ReportController@freeproductData']);
    Route::post('datatable/sohreport', ['as'=> 'datatable.sohreport','uses'=>'Master\ReportController@sohData']);
    Route::post('datatable/sosreport', ['as'=> 'datatable.sosreport','uses'=>'Master\ReportController@sosData']);
    Route::post('datatable/maintenancerequestreport', ['as'=> 'datatable.maintenancerequestreport','uses'=>'Master\ReportController@maintenanceRequestData']);
    Route::post('datatable/competitoractivityreport', ['as'=> 'datatable.competitoractivityreport','uses'=>'Master\ReportController@competitorActivityData']);
    Route::post('datatable/promoactivityreport', ['as'=> 'datatable.promoactivityreport','uses'=>'Master\ReportController@promoActivityData']);
    Route::post('datatable/posmactivityreport', ['as'=> 'datatable.posmactivityreport','uses'=>'Master\ReportController@posmActivityData']);
    Route::post('datatable/attendancereport', ['as'=> 'datatable.attendancereport','uses'=>'Master\ReportController@attendanceData']);
    Route::post('datatable/attendancereportspv', ['as'=> 'datatable.attendancereportspv','uses'=>'Master\ReportController@attendanceDataSpv']);
    Route::post('datatable/attendancereportdemo', ['as'=> 'datatable.attendancereportdemo','uses'=>'Master\ReportController@attendanceDataDemo']);
    Route::post('datatable/attendancereportothers', ['as'=> 'datatable.attendancereportothers','uses'=>'Master\ReportController@attendanceDataOthers']);

    

    Route::post('datatable/visitplan', ['as'=> 'datatable.visitplan','uses'=>'Master\ReportController@visitPlanData']);
    Route::post('datatable/salesmanreport', ['as'=> 'datatable.salesmanreport','uses'=>'Master\ReportController@salesmanData']);
    Route::post('datatable/achievementreport', ['as'=> 'datatable.achievementreport','uses'=>'Master\AchievementController@achievementData']);
    Route::post('datatable/salesmanachievementreport', ['as'=> 'datatable.salesmanachievementreport','uses'=>'Master\AchievementController@salesmanAchievementData']);
    Route::post('datatable/salesactivityreport', ['as'=> 'datatable.salesactivityreport','uses'=>'Master\ReportController@salesActivityData']);
    Route::post('datatable/storelocationactivityreport', ['as'=> 'datatable.storelocationactivityreport','uses'=>'Master\ReportController@storeLocationActivityData']);
    Route::post('datatable/storecreateactivityreport', ['as'=> 'datatable.storecreateactivityreport','uses'=>'Master\ReportController@storeCreateActivityData']);

    

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
    Route::post('data/regionspv', ['as'=> 'data.regionspv','uses'=>'Master\RegionController@getDataSpvWithFilters']);
    Route::post('data/regiondemo', ['as'=> 'data.regiondemo','uses'=>'Master\RegionController@getDataDemoWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/areaspv', ['as'=> 'data.areaspv','uses'=>'Master\AreaController@getDataSpvWithFilters']);
    Route::post('data/areademo', ['as'=> 'data.areademo','uses'=>'Master\AreaController@getDataDemoWithFilters']);
    Route::post('data/areaC', ['as'=> 'data.areaC','uses'=>'Master\AreaController@getDataWithFiltersCheck']);
    Route::post('data/globalchannel', ['as'=> 'data.globalchannel','uses'=>'Master\GlobalChannelController@getDataWithFilters']);
    Route::post('data/channel', ['as'=> 'data.channel','uses'=>'Master\ChannelController@getDataWithFilters']);
    Route::post('data/channelC', ['as'=> 'data.channelC','uses'=>'Master\ChannelController@getDataWithFiltersCheck']);
    Route::post('data/subchannel', ['as'=> 'data.subchannel','uses'=>'Master\SubChannelController@getDataWithFilters']);
    Route::post('data/subchannelC', ['as'=> 'data.subchannelC','uses'=>'Master\SubChannelController@getDataWithFiltersCheck']);
    Route::post('data/distributor', ['as'=> 'data.distributor','uses'=>'Master\DistributorController@getDataWithFilters']);
    Route::post('data/distributorC', ['as'=> 'data.distributorC','uses'=>'Master\DistributorController@getDataWithFiltersCheck']);
    Route::post('data/district', ['as'=> 'data.district','uses'=>'Master\DistrictController@getDataWithFilters']);
    Route::post('data/districtspv', ['as'=> 'data.districtspv','uses'=>'Master\DistrictController@getDataSpvWithFilters']);
    Route::post('data/districtdemo', ['as'=> 'data.districtdemo','uses'=>'Master\DistrictController@getDataDemoWithFilters']);
    Route::post('data/districtC', ['as'=> 'data.districtC','uses'=>'Master\DistrictController@getDataWithFiltersCheck']);
    Route::post('data/userothers', ['as'=> 'data.userothers','uses'=>'UserController@getDataUserOthersWithFilters']);
    Route::post('data/spvdemo', ['as'=> 'data.spvdemo','uses'=>'UserController@getDataSupervisorDemonstratorWithFilters']);
    Route::post('data/spvpromo', ['as'=> 'data.spvpromo','uses'=>'UserController@getDataSupervisorPromoterWithFilters']);
    Route::post('data/employee', ['as'=> 'data.employee','uses'=>'UserController@getDataWithFilters']);
    Route::post('data/promoter', ['as'=> 'data.promoter','uses'=>'UserController@getDataPromoterWithFilters']);
    Route::post('data/nonPromoter', ['as'=> 'data.nonPromoter','uses'=>'UserController@getDataNonPromoterWithFilters']);
    Route::post('data/nonPromoterC', ['as'=> 'data.nonPromoterC','uses'=>'UserController@getDataNonPromoterWithFiltersCheck']);
    Route::post('data/groupPromoter', ['as'=> 'data.groupPromoter','uses'=>'UserPromoterController@getDataGroupPromoterWithFilters']);
    Route::post('data/groupPromoterRejoin', ['as'=> 'data.groupPromoterRejoin','uses'=>'Master\ResignController@getDataGroupPromoterWithFilters']);
    Route::post('data/groupPromoterC', ['as'=> 'data.groupPromoterC','uses'=>'UserPromoterController@getDataGroupPromoterWithFiltersCheck']);
    Route::post('data/groupPromoterCRejoin', ['as'=> 'data.groupPromoterCRejoin','uses'=>'Master\ResignController@getDataGroupPromoterWithFiltersCheck']);
    Route::post('data/userpromoter', ['as'=> 'data.userpromoter','uses'=>'UserPromoterController@getDataWithFilters']);
    Route::post('data/store', ['as'=> 'data.store','uses'=>'Master\StoreController@getDataWithFilters']);
    Route::post('data/storespv', ['as'=> 'data.storespv','uses'=>'Master\StoreController@getDataSpvWithFilters']);
    Route::post('data/storedemo', ['as'=> 'data.storedemo','uses'=>'Master\StoreController@getDataDemoWithFilters']);
    Route::post('data/stores', ['as'=> 'data.stores','uses'=>'Master\StoreController@getStoresDataWithFilters']);
    Route::post('data/storesC', ['as'=> 'data.storesC','uses'=>'Master\StoreController@getStoresDataWithFiltersCheck']);
    Route::post('data/place', ['as'=> 'data.place','uses'=>'Master\PlaceController@getDataWithFilters']);
    Route::post('data/placeC', ['as'=> 'data.placeC','uses'=>'Master\PlaceController@getDataWithFiltersCheck']);
    Route::post('data/product', ['as'=> 'data.product','uses'=>'Master\ProductController@getDataWithFilters']);
    Route::post('data/productC', ['as'=> 'data.productC','uses'=>'Master\ProductController@getDataWithFiltersCheck']);
    Route::post('data/price', ['as'=> 'data.price','uses'=>'Master\PriceController@getDataWithFilters']);
    Route::post('data/priceC', ['as'=> 'data.priceC','uses'=>'Master\PriceController@getDataWithFiltersCheck']);
    Route::post('data/target', ['as'=> 'data.target','uses'=>'Master\TargetController@getDataWithFilters']);
    Route::post('data/targetC', ['as'=> 'data.targetC','uses'=>'Master\TargetController@getDataWithFiltersCheck']);
    Route::post('data/productfocus', ['as'=> 'data.productfocus','uses'=>'Master\ProductFocusController@getDataWithFilters']);
    Route::post('data/productfocusC', ['as'=> 'data.productfocusC','uses'=>'Master\ProductFocusController@getDataWithFiltersCheck']);
    Route::post('data/salesmantarget', ['as'=> 'data.salesmantarget','uses'=>'Master\TargetSalesmanController@getDataWithFilters']);
    Route::post('data/salesmantargetC', ['as'=> 'data.salesmantargetC','uses'=>'Master\TargetSalesmanController@getDataWithFiltersCheck']);
    Route::post('data/salesmanproductfocus', ['as'=> 'data.salesmanproductfocus','uses'=>'Master\ProductFocusSalesmanController@getDataWithFilters']);
    Route::post('data/salesmanproductfocusC', ['as'=> 'data.salesmanproductfocusC','uses'=>'Master\ProductFocusSalesmanController@getDataWithFiltersCheck']);
    Route::post('data/posm', ['as'=> 'data.posm','uses'=>'Master\PosmController@getDataWithFilters']);
    Route::post('data/posmC', ['as'=> 'data.posmC','uses'=>'Master\PosmController@getDataWithFiltersCheck']);
    Route::post('data/messagetoadmin', ['as'=> 'data.messagetoadmin','uses'=>'MessageToAdminController@getDataWithFilters']);
    Route::post('data/role', ['as'=> 'data.role','uses'=>'Master\RoleController@getDataWithFilters']);
    Route::post('data/grading', ['as'=> 'data.grading','uses'=>'Master\GradingController@getDataWithFilters']);
    Route::post('data/classification', ['as'=> 'data.classification','uses'=>'Master\ClassificationController@getDataWithFilters']);
    
    Route::post('data/groupproduct', ['as'=> 'data.groupproduct','uses'=>'Master\GroupProductController@getDataWithFilters']);
    Route::post('data/group', ['as'=> 'data.group','uses'=>'Master\GroupController@getDataWithFilters']);
    Route::post('data/groupC', ['as'=> 'data.groupC','uses'=>'Master\GroupController@getDataWithFiltersCheck']);
    Route::post('data/category', ['as'=> 'data.category','uses'=>'Master\CategoryController@getDataWithFilters']);
    Route::post('data/feedbackCategory', ['as'=> 'data.feedbackCategory','uses'=>'Master\FeedbackCategoryController@getDataWithFilters']);
    Route::post('data/feedbackQuestion', ['as'=> 'data.feedbackQuestion','uses'=>'Master\FeedbackQuestionController@getDataWithFilters']);
    Route::post('data/feedbackAnswer', ['as'=> 'data.feedbackAnswer','uses'=>'Master\FeedbackQuestionController@getDataWithFilters']);
    Route::post('data/groupcompetitor', ['as'=> 'data.groupcompetitor','uses'=>'Master\GroupCompetitorController@getDataWithFilters']);
    Route::post('data/groupcompetitorC', ['as'=> 'data.groupcompetitorC','uses'=>'Master\GroupCompetitorController@getDataWithFiltersCheck']);

    Route::post('data/quiztarget', ['as'=> 'data.quiztarget','uses'=>'Api\Master\QuizTargetController@getDataWithFilters']);
    Route::post('data/konfigpromoter', ['as'=> 'data.konfigpromoter','uses'=>'Master\KonfigController@promoterData']);
    Route::post('data/konfigstore', ['as'=> 'data.konfigstore','uses'=>'Master\KonfigController@storeData']);
    Route::post('data/leadtime', ['as'=> 'data.leadtime','uses'=>'Master\LeadtimeController@getDataWithFilters']);
    Route::post('data/leadtimeC', ['as'=> 'data.leadtimeC','uses'=>'Master\LeadtimeController@getDataWithFiltersCheck']);
    Route::post('data/timegone', ['as'=> 'data.timegone','uses'=>'Master\TimeGoneController@getDataWithFilters']);
    Route::post('data/timegoneC', ['as'=> 'data.timegoneC','uses'=>'Master\TimeGoneController@getDataWithFiltersCheck']);
    Route::post('data/productpromo', ['as'=> 'data.productpromo','uses'=>'Master\ProductPromoController@getDataWithFilters']);
    Route::post('data/productpromoC', ['as'=> 'data.productpromoC','uses'=>'Master\ProductPromoController@getDataWithFiltersCheck']);
    Route::post('data/sellinreport', ['as'=> 'data.sellinreport','uses'=>'Master\ReportController@sellInDataAll']);
    Route::post('data/sellinreportC', ['as'=> 'data.sellinreportC','uses'=>'Master\ReportController@sellInDataAllCheck']);
    Route::post('data/selloutreport', ['as'=> 'data.selloutreport','uses'=>'Master\ReportController@sellOutDataAll']);
    Route::post('data/selloutreportC', ['as'=> 'data.selloutreportC','uses'=>'Master\ReportController@sellOutDataAllCheck']);
    Route::post('data/attendanceDataC', ['as'=> 'data.attendanceDataC','uses'=>'Master\ReportController@attendanceDataC']);
    Route::post('data/attendanceDataSpvC', ['as'=> 'data.attendanceDataSpvC','uses'=>'Master\ReportController@attendanceDataSpvC']);
    Route::post('data/attendanceDataDemoC', ['as'=> 'data.attendanceDataDemoC','uses'=>'Master\ReportController@attendanceDataDemoC']);
    Route::post('data/attendanceDataOthersC', ['as'=> 'data.attendanceDataOthersC','uses'=>'Master\ReportController@attendanceDataOthersC']);
    
    Route::post('data/retdistributorreport', ['as'=> 'data.retdistributorreport','uses'=>'Master\ReportController@retDistributorDataAll']);
    Route::post('data/retconsumentreport', ['as'=> 'data.retconsumentreport','uses'=>'Master\ReportController@retConsumentDataAll']);
    Route::post('data/freeproductreport', ['as'=> 'data.freeproductreport','uses'=>'Master\ReportController@freeproductDataAll']);
    Route::post('data/tbatreport', ['as'=> 'data.tbatreport','uses'=>'Master\ReportController@tbatDataAll']);
    Route::post('data/apm', ['as'=> 'data.apm','uses'=>'Master\ApmController@getDataWithFilters']);
    Route::post('data/apmC', ['as'=> 'data.apmC','uses'=>'Master\ApmController@getDataWithFiltersCheck']);
    Route::post('data/sohreport', ['as'=> 'data.sohreport','uses'=>'Master\ReportController@sohDataAll']);
    Route::post('data/competitoractivityreport', ['as'=> 'data.competitoractivityreport','uses'=>'Master\ReportController@competitorActivityDataAll']);
    Route::post('data/promoactivityreport', ['as'=> 'data.promoactivityreport','uses'=>'Master\ReportController@promoActivityDataAll']);
    Route::post('data/displaysharereport', ['as'=> 'data.displaysharereport','uses'=>'Master\ReportController@displayShareDataAll']);
    Route::post('data/posmactivityreport', ['as'=> 'data.posmactivityreport','uses'=>'Master\ReportController@posmActivityDataAll']);
    Route::post('data/maintenancerequestreport', ['as'=> 'data.maintenancerequestreport','uses'=>'Master\ReportController@maintenanceRequestDataAll']);
    Route::post('data/visitplanreportC', ['as'=> 'data.visitplanreportC','uses'=>'Master\ReportController@visitPlanDataAllCheck']);


    /**
     * Relation
     */

    // Route::post('relation/areaaappsarea', ['as'=> 'relation.areaaappsarea','uses'=>'RelationController@areaAppsAreaRelation']);    
    // Route::post('relation/accountaccounttype', ['as'=> 'relation.accountaccounttype','uses'=>'RelationController@accountAccountTypeRelation']);
    // Route::post('relation/storeaccount', ['as'=> 'relation.storeaccount','uses'=>'RelationController@storeAccountRelation']);
    // Route::post('relation/areaapp', ['as'=> 'relation.areaapp','uses'=>'RelationController@storeAreaAppRelation']);
    // Route::post('relation/storespv', ['as'=> 'relation.storespv','uses'=>'RelationController@storeSpvRelation']);
    // Route::post('relation/salesemployee', ['as'=> 'relation.salesemployee','uses'=>'RelationController@salesEmployeeRelation']);
    // Route::post('relation/salesstore', ['as'=> 'relation.salesstore','uses'=>'RelationController@salesStoreRelation']);
    // Route::post('relation/categorygroup', ['as'=> 'relation.categorygroup','uses'=>'RelationController@categoryGroupRelation']);
    // Route::post('relation/productcategory', ['as'=> 'relation.productcategory','uses'=>'RelationController@productCategoryRelation']);
    Route::post('relation/storespvchange', ['as'=> 'relation.storespvchange','uses'=>'RelationController@storeSpvChangeRelation']);
    Route::post('relation/salesemployeechange', ['as'=> 'relation.salesemployeechange','uses'=>'RelationController@salesEmployeeChangeRelation']);
    // Route::post('relation/newsemployee', ['as'=> 'relation.newsemployee','uses'=>'RelationController@newsEmployeeRelation']);
    // Route::post('relation/newsstore', ['as'=> 'relation.newsstore','uses'=>'RelationController@newsStoreRelation']);
    // Route::post('relation/newsarea', ['as'=> 'relation.newsarea','uses'=>'RelationController@newsAreaRelation']);
    // Route::post('relation/posmactivitydetailposm', ['as'=> 'relation.posmactivitydetailposm','uses'=>'RelationController@posmActivityDetailPosmRelation']);
    // Route::post('relation/posmactivityemployee', ['as'=> 'relation.posmactivityemployee','uses'=>'RelationController@posmActivityEmployeeRelation']);
    // Route::post('relation/posmactivitystore', ['as'=> 'relation.posmactivitystore','uses'=>'RelationController@posmActivityStoreRelation']);
    // Route::post('relation/newsadmin', ['as'=> 'relation.newsadmin','uses'=>'RelationController@newsAdminRelation']);
    // Route::post('relation/productknowledgeemployee', ['as'=> 'relation.productknowledgeemployee','uses'=>'RelationController@productKnowledgeEmployeeRelation']);
    // Route::post('relation/productknowledgestore', ['as'=> 'relation.productknowledgestore','uses'=>'RelationController@productKnowledgeStoreRelation']);
    Route::post('relation/productknowledgearea', ['as'=> 'relation.productknowledgearea','uses'=>'RelationController@productKnowledgeAreaRelation']);
    // Route::post('relation/productknowledgeadmin', ['as'=> 'relation.productknowledgeadmin','uses'=>'RelationController@productKnowledgeAdminRelation']);
    // Route::post('relation/competitoractivitygroup', ['as'=> 'relation.competitoractivitygroup','uses'=>'RelationController@competitorActivityGroupRelation']);
    // Route::post('relation/salesproduction', ['as'=> 'relation.salesproduction','uses'=>'RelationController@salesProductRelation']);
    Route::post('relation/userrelation', ['as'=> 'relation.userrelation','uses'=>'RelationController@checkUserRelation']);
    Route::post('relation/storerelation', ['as'=> 'relation.storerelation','uses'=>'RelationController@checkStoreRelation']);
    Route::post('relation/productionrelation', ['as'=> 'relation.productionrelation','uses'=>'RelationController@checkProductRelation']);
    Route::post('relation/posmrelation', ['as'=> 'relation.posmrelation','uses'=>'RelationController@checkPosmRelation']);
    Route::post('relation/groupcompetitorrelation', ['as'=> 'relation.groupcompetitor','uses'=>'RelationController@checkGroupCompetitorRelation']);
    Route::post('relation/groupproductrelation', ['as'=> 'relation.groupproductrelation','uses'=>'RelationController@checkGroupProductRelation']);
    Route::post('relation/grouprelation', ['as'=> 'relation.grouprelation','uses'=>'RelationController@checkGroupRelation']);
    Route::post('relation/employeerelation', ['as'=> 'relation.employeerelation','uses'=>'RelationController@checkEmployeeRelation']);
    Route::post('relation/categoryrelation', ['as'=> 'relation.categoryrelation','uses'=>'RelationController@checkCategoryRelation']);
    Route::post('relation/districtrelation', ['as'=> 'relation.districtrelation','uses'=>'RelationController@CheckDistrictRelation']);
    Route::post('relation/arearelation', ['as'=> 'relation.arearelation','uses'=>'RelationController@checkAreaRelation']);
    Route::post('relation/channelrelation', ['as'=> 'relation.channelrelation','uses'=>'RelationController@checkChannelRelation']);
    Route::post('relation/subchannelrelation', ['as'=> 'relation.subchannelrelation','uses'=>'RelationController@checkSubChannelRelation']);
    Route::post('relation/distributorrelation', ['as'=> 'relation.distributorrelation','uses'=>'RelationController@checkDistributorRelation']);
    
    Route::post('relation/rolerelation', ['as'=> 'relation.rolerelation','uses'=>'RelationController@checkRoleRelation']);
    Route::post('relation/gradingrelation', ['as'=> 'relation.gradingrelation','uses'=>'RelationController@checkGradingRelation']);
    Route::post('relation/classificationrelation', ['as'=> 'relation.classificationrelation','uses'=>'RelationController@checkClassificationRelation']);
    

    /**
     * Util
     */

    Route::post('util/existemailuser', ['uses'=>'UtilController@existEmailUser']);
    Route::post('util/existnikuser', ['uses'=>'UtilController@existNikUser']);
    Route::post('util/existemailemployee', ['uses'=>'UtilController@existEmailEmployee']);
    Route::get('util/empstore/{id}', ['uses'=>'UtilController@getStoreForEmployee']);
    Route::get('util/spvstore/{id}', ['uses'=>'UtilController@getStoreForSpvEmployee']);
    Route::get('util/spvdemostore/{id}', ['uses'=>'UtilController@getStoreForSpvDemoEmployee']);    
    Route::get('util/storedist/{id}', ['uses'=>'UtilController@getDistributorForStore']);
    Route::get('util/areaapp/{id}', ['uses'=>'UtilController@getAreaApp']);
    Route::get('util/area/{id}', ['uses'=>'UtilController@getArea']);
    Route::get('util/store/{id}', ['uses'=>'UtilController@getStore']);
    Route::get('util/user/{id}', ['uses'=>'UtilController@getUser']);
    Route::get('util/newsread/{id}', ['uses'=>'UtilController@getNewsRead']);
    Route::get('util/productread/{id}', ['uses'=>'UtilController@getProductRead']);
    Route::get('util/user-online', ['uses'=>'UtilController@getUserOnline']);
    Route::get('util/sales-history', ['uses'=>'UtilController@getSalesHistory']);
    Route::post('util/sales-history-read', ['uses'=>'UtilController@readSalesHistory']);
    Route::get('util/salesman-sales-history', ['uses'=>'UtilController@getSalesmanSalesHistory']);
    Route::post('util/salesman-sales-history-read', ['uses'=>'UtilController@readSalesmanSalesHistory']);
    Route::get('util/sales-history-count', ['uses'=>'UtilController@getSalesHistoryCount']);
    Route::get('util/get-store-id', ['uses'=>'UtilController@getStoreId']);
    Route::get('util/attendancedetail/{id}', ['uses'=>'UtilController@getAttendanceDetail']);
    Route::get('util/attendancedetailplace/{id}', ['uses'=>'UtilController@getAttendanceDetailPlace']);
    Route::get('util/historyempstore/{id}', ['uses'=>'UtilController@getHistoryStoreForEmployee']);
    Route::get('util/target/{id}', ['uses'=>'UtilController@getTargetQuiz']);
    Route::get('util/rsmregion/{id}', ['uses'=>'UtilController@getRegionForRSM']);
    Route::get('util/dmarea/{id}', ['uses'=>'UtilController@getAreaForDM']);
    Route::get('util/trainerarea/{id}', ['uses'=>'UtilController@getAreaForTrainer']);

    /**
     * Export
     */

    Route::post('util/export-sellin', ['uses'=>'Master\ExportController@exportSellIn']);
    Route::post('util/export-sellin-all', ['uses'=>'Master\ExportController@exportSellInAll']);
    Route::post('util/export-sellout', ['uses'=>'Master\ExportController@exportSellOut']);
    Route::post('util/export-sellout-all', ['uses'=>'Master\ExportController@exportSellOutAll']);
    Route::post('util/export-retconsument', ['uses'=>'Master\ExportController@exportRetConsument']);
    Route::post('util/export-retconsument-all', ['uses'=>'Master\ExportController@exportRetConsumentAll']);
    Route::post('util/export-retdistributor', ['uses'=>'Master\ExportController@exportRetDistributor']);
    Route::post('util/export-retdistributor-all', ['uses'=>'Master\ExportController@exportRetDistributorAll']);
    Route::post('util/export-freeproduct', ['uses'=>'Master\ExportController@exportFreeProduct']);
    Route::post('util/export-freeproduct-all', ['uses'=>'Master\ExportController@exportFreeProductAll']);
    Route::post('util/export-tbat', ['uses'=>'Master\ExportController@exportTbat']);
    Route::post('util/export-tbat-all', ['uses'=>'Master\ExportController@exportTbatAll']);
    Route::post('util/export-soh', ['uses'=>'Master\ExportController@exportSoh']);
    Route::post('util/export-sos', ['uses'=>'Master\ExportController@exportSos']);
    Route::post('util/export-displayshare', ['uses'=>'Master\ExportController@exportDisplayShare']);
    Route::post('util/export-maintenancerequest', ['uses'=>'Master\ExportController@exportMaintenanceRequest']);
    Route::post('util/export-competitoractivity', ['uses'=>'Master\ExportController@exportCompetitorActivity']);
    Route::post('util/export-promoactivity', ['uses'=>'Master\ExportController@exportPromoActivity']);
    Route::post('util/export-posmactivity', ['uses'=>'Master\ExportController@exportPosmActivity']);
    Route::post('util/export-attendance', ['uses'=>'Master\ExportController@exportAttendanceReport']);
    Route::post('util/export-attendance-all/{param}', ['uses'=>'Master\ExportController@exportAttendanceReportAll']);
    Route::post('util/export-salesman', ['uses'=>'Master\ExportController@exportSalesman']);
    Route::post('util/export-achievement', ['uses'=>'Master\ExportController@exportAchievementReport']);
    Route::post('util/export-salesman-achievement', ['uses'=>'Master\ExportController@exportSalesmanAchievementReport']);
    Route::post('util/export-delete', ['uses'=>'Master\ExportController@deleteExport']);
    
    // -------- MASTER --------
    Route::post('util/export-area', ['uses'=>'Master\ExportController@exportArea']);
    Route::post('util/export-area-all', ['uses'=>'Master\ExportController@exportAreaAll']);
    Route::post('util/export-district', ['uses'=>'Master\ExportController@exportDistrict']);
    Route::post('util/export-district-all', ['uses'=>'Master\ExportController@exportDistrictAll']);
    Route::post('util/export-store', ['uses'=>'Master\ExportController@exportStore']);
    Route::post('util/export-store-all', ['uses'=>'Master\ExportController@exportStoreAll']);
    Route::post('util/export-store-all-alt', ['uses'=>'Master\ExportController@exportStoreAllAlt']);
    Route::post('util/export-channel', ['uses'=>'Master\ExportController@exportChannel']);
    Route::post('util/export-channel-all', ['uses'=>'Master\ExportController@exportChannelAll']);
    Route::post('util/export-subchannel', ['uses'=>'Master\ExportController@exportSubchannel']);
    Route::post('util/export-subchannel-all', ['uses'=>'Master\ExportController@exportSubchannelAll']);
    Route::post('util/export-distributor', ['uses'=>'Master\ExportController@exportDistributor']);
    Route::post('util/export-distributor-all', ['uses'=>'Master\ExportController@exportDistributorAll']);
    Route::post('util/export-place', ['uses'=>'Master\ExportController@exportPlace']);
    Route::post('util/export-place-all', ['uses'=>'Master\ExportController@exportPlaceAll']);
    Route::post('util/export-promoter', ['uses'=>'Master\ExportController@exportUserPromoter']);
    Route::post('util/export-promoter-all', ['uses'=>'Master\ExportController@exportUserPromoterAll']);
    Route::post('util/export-nonpromoter', ['uses'=>'Master\ExportController@exportUserNonPromoter']);
    Route::post('util/export-nonpromoter-all', ['uses'=>'Master\ExportController@exportUserNonPromoterAll']);
    Route::post('util/export-group', ['uses'=>'Master\ExportController@exportGroup']);
    Route::post('util/export-group-all', ['uses'=>'Master\ExportController@exportGroupAll']);
    Route::post('util/export-category', ['uses'=>'Master\ExportController@exportCategory']);
    Route::post('util/export-category-all', ['uses'=>'Master\ExportController@exportCategoryAll']);
    Route::post('util/export-product', ['uses'=>'Master\ExportController@exportProduct']);
    Route::post('util/export-product-all', ['uses'=>'Master\ExportController@exportProductAll']);
    Route::post('util/export-price', ['uses'=>'Master\ExportController@exportPrice']);
    Route::post('util/export-price-all', ['uses'=>'Master\ExportController@exportPriceAll']);
    Route::post('util/export-price-template', ['uses'=>'Master\ExportController@exportPriceTemplate']);
    Route::post('util/export-target', ['uses'=>'Master\ExportController@exportTarget']);
    Route::post('util/export-target-all', ['uses'=>'Master\ExportController@exportTargetAll']);
    Route::post('util/export-target-template', ['uses'=>'Master\ExportController@exportTargetTemplate']);
    Route::post('util/export-productfocus', ['uses'=>'Master\ExportController@exportProductFocus']);
    Route::post('util/export-productfocus-all', ['uses'=>'Master\ExportController@exportProductFocusAll']);
    Route::post('util/export-productfocus-template', ['uses'=>'Master\ExportController@exportProductFocusTemplate']);
    Route::post('util/export-salesmantarget', ['uses'=>'Master\ExportController@exportSalesmanTarget']);
    Route::post('util/export-salesmantarget-all', ['uses'=>'Master\ExportController@exportSalesmanTargetAll']);
    Route::post('util/export-salesmantarget-template', ['uses'=>'Master\ExportController@exportSalesmanTargetTemplate']);
    Route::post('util/export-salesmanproductfocus', ['uses'=>'Master\ExportController@exportSalesmanProductFocus']);
    Route::post('util/export-salesmanproductfocus-all', ['uses'=>'Master\ExportController@exportSalesmanProductFocusAll']);
    Route::post('util/export-salesmanproductfocus-template', ['uses'=>'Master\ExportController@exportSalesmanProductFocusTemplate']);
    Route::post('util/export-posm', ['uses'=>'Master\ExportController@exportPosm']);
    Route::post('util/export-groupcompetitor', ['uses'=>'Master\ExportController@exportGroupCompetitor']);
    Route::post('util/export-groupcompetitor-all', ['uses'=>'Master\ExportController@exportGroupCompetitorAll']);
    Route::post('util/export-messagetoadmin', ['uses'=>'Master\ExportController@exportMessageToAdmin']);
    Route::post('util/export-konfig-promoter', ['uses'=>'Master\ExportController@exportKonfigPromoter']);
    Route::post('util/export-konfig-store', ['uses'=>'Master\ExportController@exportKonfigStore']);
    Route::post('util/export-leadtime', ['uses'=>'Master\ExportController@exportLeadtime']);
    Route::post('util/export-leadtime-all', ['uses'=>'Master\ExportController@exportLeadtimeAll']);
    Route::post('util/export-leadtime-template', ['uses'=>'Master\ExportController@exportLeadtimeTemplate']);
    Route::post('util/export-timegone', ['uses'=>'Master\ExportController@exportTimeGone']);
    Route::post('util/export-timegone-all', ['uses'=>'Master\ExportController@exportTimeGoneAll']);
    Route::post('util/export-timegone-template', ['uses'=>'Master\ExportController@exportTimeGoneTemplate']);
    Route::post('util/export-productpromo', ['uses'=>'Master\ExportController@exportProductPromo']);
    Route::post('util/export-productpromo-all', ['uses'=>'Master\ExportController@exportProductPromoAll']);
    Route::post('util/export-productpromo-template', ['uses'=>'Master\ExportController@exportProductPromoTemplate']);
    Route::post('util/export-apm', ['uses'=>'Master\ExportController@exportApm']);
    Route::post('util/export-apm-all', ['uses'=>'Master\ExportController@exportApmAll']);
    Route::post('util/export-apm-template', ['uses'=>'Master\ExportController@exportApmTemplate']);
    Route::post('util/export-soh-all', ['uses'=>'Master\ExportController@exportSohAll']);
    Route::post('util/export-competitoractivity-all', ['uses'=>'Master\ExportController@exportCompetitorActivityAll']);
    Route::post('util/export-promoactivity-all', ['uses'=>'Master\ExportController@exportPromoActivityAll']);
    Route::post('util/export-displayshare-all', ['uses'=>'Master\ExportController@exportDisplayShareAll']);
    Route::post('util/export-posm-all', ['uses'=>'Master\ExportController@exportPosmAll']);
    Route::post('util/export-posmactivity-all', ['uses'=>'Master\ExportController@exportPosmActivityAll']);
    Route::post('util/export-visitplan', ['uses'=>'Master\ExportController@exportVisitPlan']);
    Route::post('util/export-visitplan-all', ['uses'=>'Master\ExportController@exportVisitPlanAll']);
    Route::post('util/export-news-read', ['uses'=>'Master\ExportController@exportNewsRead']);
    Route::post('util/export-guideline-read', ['uses'=>'Master\ExportController@exportGuideLineRead']);


    /**
     * Dashboard
     */

    Route::get('chart/data-national', ['uses'=>'DashboardController@getDataNational']);
    Route::get('chart/data-region', ['uses'=>'DashboardController@getDataRegion']);
    Route::get('chart/data-area', ['uses'=>'DashboardController@getDataArea']);
    Route::get('chart/data-supervisor', ['uses'=>'DashboardController@getDataSupervisor']);
    Route::get('chart/data-national-salesman', ['uses'=>'DashboardController@getDataNationalSalesman']);
    
});


