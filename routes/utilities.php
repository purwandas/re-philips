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
    Route::post('datatable/visitplan', ['as'=> 'datatable.visitplan','uses'=>'Master\ReportController@visitPlanData']);
    Route::post('datatable/salesmanreport', ['as'=> 'datatable.salesmanreport','uses'=>'Master\ReportController@salesmanData']);
    Route::post('datatable/achievementreport', ['as'=> 'datatable.achievementreport','uses'=>'Master\AchievementController@achievementData']);
    Route::post('datatable/salesmanachievementreport', ['as'=> 'datatable.salesmanachievementreport','uses'=>'Master\AchievementController@salesmanAchievementData']);
    Route::post('datatable/salesactivityreport', ['as'=> 'datatable.salesactivityreport','uses'=>'Master\ReportController@salesActivityData']);
    Route::post('datatable/storelocationactivityreport', ['as'=> 'datatable.storelocationactivityreport','uses'=>'Master\ReportController@storeLocationActivityData']);

    

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/globalchannel', ['as'=> 'data.globalchannel','uses'=>'Master\GlobalChannelController@getDataWithFilters']);
    Route::post('data/channel', ['as'=> 'data.channel','uses'=>'Master\ChannelController@getDataWithFilters']);
    Route::post('data/subchannel', ['as'=> 'data.subchannel','uses'=>'Master\SubChannelController@getDataWithFilters']);
    Route::post('data/distributor', ['as'=> 'data.distributor','uses'=>'Master\DistributorController@getDataWithFilters']);
    Route::post('data/district', ['as'=> 'data.district','uses'=>'Master\DistrictController@getDataWithFilters']);
    Route::post('data/employee', ['as'=> 'data.employee','uses'=>'UserController@getDataWithFilters']);
    Route::post('data/promoter', ['as'=> 'data.promoter','uses'=>'UserController@getDataPromoterWithFilters']);
    Route::post('data/nonPromoter', ['as'=> 'data.nonPromoter','uses'=>'UserController@getDataNonPromoterWithFilters']);
    Route::post('data/groupPromoter', ['as'=> 'data.groupPromoter','uses'=>'UserPromoterController@getDataGroupPromoterWithFilters']);
    Route::post('data/userpromoter', ['as'=> 'data.userpromoter','uses'=>'UserPromoterController@getDataWithFilters']);
    Route::post('data/store', ['as'=> 'data.store','uses'=>'Master\StoreController@getDataWithFilters']);
    Route::post('data/stores', ['as'=> 'data.stores','uses'=>'Master\StoreController@getStoresDataWithFilters']);
    Route::post('data/place', ['as'=> 'data.place','uses'=>'Master\PlaceController@getDataWithFilters']);
    Route::post('data/product', ['as'=> 'data.product','uses'=>'Master\ProductController@getDataWithFilters']);
    Route::post('data/price', ['as'=> 'data.price','uses'=>'Master\PriceController@getDataWithFilters']);
    Route::post('data/target', ['as'=> 'data.target','uses'=>'Master\TargetController@getDataWithFilters']);
    Route::post('data/productfocus', ['as'=> 'data.productfocus','uses'=>'Master\ProductFocusController@getDataWithFilters']);
    Route::post('data/salesmantarget', ['as'=> 'data.salesmantarget','uses'=>'Master\TargetSalesmanController@getDataWithFilters']);
    Route::post('data/salesmanproductfocus', ['as'=> 'data.salesmanproductfocus','uses'=>'Master\ProductFocusSalesmanController@getDataWithFilters']);
    Route::post('data/posm', ['as'=> 'data.posm','uses'=>'Master\PosmController@getDataWithFilters']);
    Route::post('data/messagetoadmin', ['as'=> 'data.messagetoadmin','uses'=>'MessageToAdminController@getDataWithFilters']);
    Route::post('data/role', ['as'=> 'data.role','uses'=>'Master\RoleController@getDataWithFilters']);
    Route::post('data/grading', ['as'=> 'data.grading','uses'=>'Master\GradingController@getDataWithFilters']);
    Route::post('data/classification', ['as'=> 'data.classification','uses'=>'Master\ClassificationController@getDataWithFilters']);
    
    Route::post('data/groupproduct', ['as'=> 'data.groupproduct','uses'=>'Master\GroupProductController@getDataWithFilters']);
    Route::post('data/group', ['as'=> 'data.group','uses'=>'Master\GroupController@getDataWithFilters']);
    Route::post('data/category', ['as'=> 'data.category','uses'=>'Master\CategoryController@getDataWithFilters']);
    Route::post('data/feedbackCategory', ['as'=> 'data.feedbackCategory','uses'=>'Master\FeedbackCategoryController@getDataWithFilters']);
    Route::post('data/feedbackQuestion', ['as'=> 'data.feedbackQuestion','uses'=>'Master\FeedbackQuestionController@getDataWithFilters']);
    Route::post('data/feedbackAnswer', ['as'=> 'data.feedbackAnswer','uses'=>'Master\FeedbackQuestionController@getDataWithFilters']);
    Route::post('data/groupcompetitor', ['as'=> 'data.groupcompetitor','uses'=>'Master\GroupCompetitorController@getDataWithFilters']);

    Route::post('data/quiztarget', ['as'=> 'data.quiztarget','uses'=>'Api\Master\QuizTargetController@getDataWithFilters']);
    Route::post('data/konfigpromoter', ['as'=> 'data.konfigpromoter','uses'=>'Master\KonfigController@promoterData']);

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
    Route::post('util/existemailemployee', ['uses'=>'UtilController@existEmailEmployee']);
    Route::get('util/empstore/{id}', ['uses'=>'UtilController@getStoreForEmployee']);
    Route::get('util/spvstore/{id}', ['uses'=>'UtilController@getStoreForSpvEmployee']);
    Route::get('util/spvdemostore/{id}', ['uses'=>'UtilController@getStoreForSpvDemoEmployee']);    
    Route::get('util/storedist/{id}', ['uses'=>'UtilController@getDistributorForStore']);
    Route::get('util/areaapp/{id}', ['uses'=>'UtilController@getAreaApp']);
    Route::get('util/store/{id}', ['uses'=>'UtilController@getStore']);
    Route::get('util/user/{id}', ['uses'=>'UtilController@getUser']);
    Route::get('util/newsread/{id}', ['uses'=>'UtilController@getNewsRead']);
    Route::get('util/productread/{id}', ['uses'=>'UtilController@getProductRead']);
    Route::get('util/user-online', ['uses'=>'UtilController@getUserOnline']);
    Route::get('util/sales-history', ['uses'=>'UtilController@getSalesHistory']);
    Route::post('util/sales-history-read', ['uses'=>'UtilController@readSalesHistory']);
    Route::get('util/get-store-id', ['uses'=>'UtilController@getStoreId']);
    Route::get('util/attendancedetail/{id}', ['uses'=>'UtilController@getAttendanceDetail']);
    Route::get('util/historyempstore/{id}', ['uses'=>'UtilController@getHistoryStoreForEmployee']);
    Route::get('util/target/{id}', ['uses'=>'UtilController@getTargetQuiz']);

    /**
     * Export
     */

    Route::post('util/export-sellin', ['uses'=>'Master\ExportController@exportSellIn']);
    Route::post('util/export-sellout', ['uses'=>'Master\ExportController@exportSellOut']);
    Route::post('util/export-retconsument', ['uses'=>'Master\ExportController@exportRetConsument']);
    Route::post('util/export-retdistributor', ['uses'=>'Master\ExportController@exportRetDistributor']);
    Route::post('util/export-freeproduct', ['uses'=>'Master\ExportController@exportFreeProduct']);
    Route::post('util/export-tbat', ['uses'=>'Master\ExportController@exportTbat']);
    Route::post('util/export-soh', ['uses'=>'Master\ExportController@exportSoh']);
    Route::post('util/export-sos', ['uses'=>'Master\ExportController@exportSos']);
    Route::post('util/export-displayshare', ['uses'=>'Master\ExportController@exportDisplayShare']);
    Route::post('util/export-maintenancerequest', ['uses'=>'Master\ExportController@exportMaintenanceRequest']);
    Route::post('util/export-competitoractivity', ['uses'=>'Master\ExportController@exportCompetitorActivity']);
    Route::post('util/export-promoactivity', ['uses'=>'Master\ExportController@exportPromoActivity']);
    Route::post('util/export-attendance', ['uses'=>'Master\ExportController@exportAttendanceReport']);
    Route::post('util/export-salesman', ['uses'=>'Master\ExportController@exportSalesman']);
    Route::post('util/export-achievement', ['uses'=>'Master\ExportController@exportAchievementReport']);
    Route::post('util/export-salesman-achievement', ['uses'=>'Master\ExportController@exportSalesmanAchievementReport']);
    Route::post('util/export-delete', ['uses'=>'Master\ExportController@deleteExport']);
                    // -------- MASTER --------
    Route::post('util/export-area', ['uses'=>'Master\ExportController@exportArea']);
    Route::post('util/export-district', ['uses'=>'Master\ExportController@exportDistrict']);
    Route::post('util/export-store', ['uses'=>'Master\ExportController@exportStore']);
    Route::post('util/export-store-all', ['uses'=>'Master\ExportController@exportStoreAll']);
    Route::post('util/export-channel', ['uses'=>'Master\ExportController@exportChannel']);
    Route::post('util/export-subchannel', ['uses'=>'Master\ExportController@exportSubchannel']);
    Route::post('util/export-distributor', ['uses'=>'Master\ExportController@exportDistributor']);
    Route::post('util/export-place', ['uses'=>'Master\ExportController@exportPlace']);
    Route::post('util/export-promoter', ['uses'=>'Master\ExportController@exportUserPromoter']);
    Route::post('util/export-nonpromoter', ['uses'=>'Master\ExportController@exportUserNonPromoter']);
    Route::post('util/export-group', ['uses'=>'Master\ExportController@exportGroup']);
    Route::post('util/export-category', ['uses'=>'Master\ExportController@exportCategory']);
    Route::post('util/export-product', ['uses'=>'Master\ExportController@exportProduct']);
    Route::post('util/export-price', ['uses'=>'Master\ExportController@exportPrice']);
    Route::post('util/export-target', ['uses'=>'Master\ExportController@exportTarget']);
    Route::post('util/export-productfocus', ['uses'=>'Master\ExportController@exportProductFocus']);
    Route::post('util/export-salesmantarget', ['uses'=>'Master\ExportController@exportSalesmanTarget']);
    Route::post('util/export-salesmanproductfocus', ['uses'=>'Master\ExportController@exportSalesmanProductFocus']);
    Route::post('util/export-posm', ['uses'=>'Master\ExportController@exportPosm']);
    Route::post('util/export-groupcompetitor', ['uses'=>'Master\ExportController@exportGroupCompetitor']);
    Route::post('util/export-messagetoadmin', ['uses'=>'Master\ExportController@exportMessageToAdmin']);
    Route::post('util/export-konfig-promoter', ['uses'=>'Master\ExportController@exportKonfigPromoter']);
    Route::post('util/export-konfig-store', ['uses'=>'Master\ExportController@exportKonfigStore']);


    /**
     * Dashboard
     */

    Route::get('chart/data-national', ['uses'=>'DashboardController@getDataNational']);
    Route::get('chart/data-region', ['uses'=>'DashboardController@getDataRegion']);
    Route::get('chart/data-area', ['uses'=>'DashboardController@getDataArea']);
    Route::get('chart/data-supervisor', ['uses'=>'DashboardController@getDataSupervisor']);
    Route::get('chart/data-national-salesman', ['uses'=>'DashboardController@getDataNationalSalesman']);
    
});


