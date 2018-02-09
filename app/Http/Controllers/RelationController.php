<?php

namespace App\Http\Controllers;

use App\CompetitorActivity;
use App\CompetitorActivityDetail;
use App\District;
use App\FreeProductDetail;
use App\Price;
use App\ProductFocuses;
use App\PromoActivity;
use App\PromoActivityDetail;
use App\RetConsumentDetail;
use App\RetDistributorDetail;
use App\SellOutDetail;
use App\SellInDetail;
use App\StoreDistributor;
use App\SubChannel;
use App\Target;
use App\TbatDetail;
use Illuminate\Http\Request;
use App\Area;
use App\DmArea;
use App\Store;
use App\Category;
use App\Product;
use App\User;
use App\SellIn;
use App\SellOut;
use App\RetDistributor;
use App\RetConsument;
use App\FreeProduct;
use App\Tbat;
use App\News;
use App\Posm;
use App\Soh;
use App\Sos;
use App\PosmActivityDetail;
use App\PosmActivity;
use App\ProductKnowledge;
use App\Group;

class RelationController extends Controller
{
    //
    public function districtAreaRelation($areaId){
        $countDistrict = District::where('area_id', $areaId)->first();
        if ($countDistrict) {
            return true;
        }
        return false;
    }

    public function subChannelChannelRelation($channelId){
        $countSubChannel = SubChannel::where('channel_id', $channelId)->first();
        if ($countSubChannel) {
            return true;
        }

        return false;
    }

    public function storeSubChannelRelation($subChannelId){
        $countSubChannel = Store::where('subchannel_id', $subChannelId)->first();
        if ($countSubChannel) {
            return true;
        }
        return false;
    }

    public function storeDistrictRelation($districtId){
        $countStore = Store::where('district_id', $districtId)->first();
        if($countStore){
            return true;
        }
        return false;
    }

    public function storeSpvRelation($userId){

        $user = User::find($userId);

        // $countStore = 0;

        if($user->role->role_group == 'Supervisor'){

            $checkStore = Store::where('user_id', $userId)->first();

            if ($checkStore) {
                return true;
            }

        }

        return false;
    }

    public function groupGroupProductRelation($groupProductId){
        $countGroup = Group::where('groupproduct_id', $groupProductId)->first();
        if ($countGroup) {
            return true;
        }
        return false;
    }

    public function targetGroupProductRelation($groupProductId){
        $countTarget = Target::where('groupproduct_id', $groupProductId)->first();
        if ($countTarget) {
            return true;
        }
        return false;
    }

    public function categoryGroupRelation($groupId){
        $countCategory = Category::where('group_id', $groupId)->first();
        if ($countCategory) {
            return true;
        }
        return false;
    }



    public function productCategoryRelation($categoryId){
        $countProduct = Product::where('category_id', $categoryId)->first();
        if ($countProduct) {
            return true;
        }
        return false;
    }

    public function storeDistributorRelation($distributorId){
        $countStoreDist = StoreDistributor::where('distributor_id', $distributorId)->first();
        if ($countStoreDist) {
            return true;
        }
        return false;
    }

    public function storeSpvChangeRelation(Request $request){
        $user = User::find($request->spvId);

        $countStore = 0;
        if(($request->role != $user->role->role_group) && ($user->role->role_group == "Supervisor")){
            $countStore = $user->stores()->count();
        }

        return response()->json($countStore);
    }

    public function priceProductRelation($productId){
        $countPrice = Price::where('product_id', $productId)->first();
        if ($countPrice) {
            return true;
        }
        return false;
    }

    public function productFocusProductRelation($productId){
        $countProductFocus = ProductFocuses::where('product_id', $productId)->first();
        if ($countProductFocus) {
            return true;
        }
        return false;
    }

    public function salesEmployeeChangeRelation(Request $request){

        $user = User::find($request->employeeId);

        $countSales = 0;
        $isPromoter = 0;

        if($request->role == 'Promoter' || $request->role == 'Promoter Additional' || $request->role == 'Promoter Event' || $request->role == 'Demonstrator MCC' || $request->role == 'Demonstrator DA' || $request->role == 'ACT'  || $request->role == 'PPE' || $request->role == 'BDT' || $request->role == 'Salesman Explorer' || $request->role == 'SMD' || $request->role == 'SMD Coordinator' || $request->role == 'HIC' || $request->role == 'HIE' || $request->role == 'SMD Additional' || $request->role == 'ASC'){
            $isPromoter = 1;
        }        

        if(($request->role != $user->role->role_group) && ($isPromoter == 0)){

            // COUNT IN SELL IN(Sell Through)
            $sellInCount = SellIn::where('user_id', $request->employeeId)->count();
            if($sellInCount > 0){
                $countSales += 1;
            }

            // COUNT IN SELL OUT
            $sellOutCount = SellOut::where('user_id', $request->employeeId)->count();
            if($sellOutCount > 0){
                $countSales += 1;
            }

            // COUNT IN RET DISTRIBUTOR
            $retDistributorCount = RetDistributor::where('user_id', $request->employeeId)->count();
            if($retDistributorCount > 0){
                $countSales += 1;
            }

            // COUNT IN RET CONSUMENT
            $retConsumentCount = RetConsument::where('user_id', $request->employeeId)->count();
            if($retConsumentCount > 0){
                $countSales += 1;
            }

            // COUNT IN FREE PRODUCT
            $freeProductCount = FreeProduct::where('user_id', $request->employeeId)->count();
            if($freeProductCount > 0){
                $countSales += 1;
            }

            // COUNT IN TBAT
            $tbatCount = Tbat::where('user_id', $request->employeeId)->count();
            if($tbatCount > 0){
                $countSales += 1;
            }

        }

        return response()->json($countSales);
    }

    public function salesEmployeeRelation($userId){

        // CHECK IN SELL IN(Sell Through)
        $sellInCount = SellIn::where('user_id', $userId)->first();
        if($sellInCount){
            return true;
        }

        // CHECK IN SELL OUT
        $sellOutCount = SellOut::where('user_id', $userId)->first();
        if($sellOutCount){
            return true;
        }

        // CHECK IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributor::where('user_id', $userId)->first();
        if($retDistributorCount){
            return true;
        }

        // CHECK IN RET CONSUMENT
        $retConsumentCount = RetConsument::where('user_id', $userId)->first();
        if($retConsumentCount){
            return true;
        }

        // CHECK IN FREE PRODUCT
        $freeProductCount = FreeProduct::where('user_id', $userId)->first();
        if($freeProductCount){
            return true;
        }

        // CHECK IN TBAT
        $tbatCount = Tbat::where('user_id', $userId)->first();
        if($tbatCount){
            return true;
        }

        // CHECK IN COMPETITOR ACTIVITY
        $competitorActivity = CompetitorActivity::where('user_id', $userId)->first();
        if($competitorActivity){
            return true;
        }

        // CHECK IN PROMO ACTIVITY
        $promoActivity = PromoActivity::where('user_id', $userId)->first();
        if($promoActivity){
            return true;
        }

        // CHECK IN SOH
        $sohCount = Soh::where('user_id', $userId)->first();
        if($sohCount){
            return true;
        }

        // CHECK IN SOS
        $sosCount = Sos::where('user_id', $userId)->first();
        if($sosCount){
            return true;
        }

        return false;

    }

    public function salesStoreRelation($storeId){

        // COUNT IN SELL IN(Sell Through)
        $sellInCount = SellIn::where('store_id', $storeId)->first();
        if($sellInCount){
            return true;
        }

        // COUNT IN SELL OUT
        $sellOutCount = SellOut::where('store_id', $storeId)->first();
        if($sellOutCount){
            return true;
        }

        // COUNT IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributor::where('store_id', $storeId)->first();
        if($retDistributorCount){
            return true;
        }

        // COUNT IN RET CONSUMENT
        $retConsumentCount = RetConsument::where('store_id', $storeId)->first();
        if($retConsumentCount){
            return true;
        }

        // COUNT IN FREE PRODUCT
        $freeProductCount = FreeProduct::where('store_id', $storeId)->first();
        if($freeProductCount){
            return true;
        }

        // COUNT IN TBAT
        $tbatCount = Tbat::where('store_id', $storeId)->first();
        if($tbatCount){
            return true;
        }

        // COUNT IN COMPETITOR ACTIVITY
        $competitorActivity = CompetitorActivity::where('store_id', $storeId)->first();
        if($competitorActivity){
            return true;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivity = PromoActivity::where('store_id', $storeId)->first();
        if($promoActivity){
            return true;
        }

        // CHECK IN SOH
        $sohCount = Soh::where('store_id', $storeId)->first();
        if($sohCount){
            return true;
        }

        // CHECK IN SOS
        $sosCount = Sos::where('store_id', $storeId)->first();
        if($sosCount){
            return true;
        }

        return false;
    }

    public function newsEmployeeRelation($userId){

        $news = News::where('target_type', 'Promoter')->get();

        // $countNews = 0;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($userId, $array)){
                // $countNews += 1;
                return true;
            }

        }

        return false;

    }

    public function newsStoreRelation($storeId){

        $news = News::where('target_type', 'Store')->get();

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($storeId, $array)){
                return true;
            }

        }

        return false;

    }

    public function newsDistrictRelation($districtId){

        $news = News::where('target_type', 'Area')->get();

        $countNews = 0;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($districtId, $array)){
                return true;
            }

        }

        return false;

    }

    public function posmActivityDetailPosmRelation($posmId){
        $countPosm = PosmActivityDetail::where('posm_id', $posmId)->first();
        if ($countPosm) {
            return true;
        }
        return false;
    }

    public function posmActivityEmployeeRelation($userId){
        $countPosmActivity = PosmActivity::where('user_id', $userId)->first();

        if ($countPosmActivity) {
            return true;
        }

        return false;
    }

    public function targetEmployeeRelation($userId){
        $countTarget = Target::where('user_id', $userId)->first();

        if ($countTarget) {
            return true;
        }

        return false;
    }

    public function posmActivityStoreRelation($storeId){
        $countPosmActivity = PosmActivity::where('store_id', $storeId)->first();

        if ($countPosmActivity) {
            return true;
        }
        return false;
    }

    public function targetStoreRelation($storeId){
        $countTarget = Target::where('store_id', $storeId)->first();

        if ($countTarget) {
            return true;
        }
        return false;
    }

    public function newsAdminRelation($userId){
        $countNews = News::where('user_id', $userId)->first();

        if ($countNews) {
            return true;
        }

        return false;
    }

    public function productKnowledgeEmployeeRelation($userId){

        $productKnowledge = ProductKnowledge::where('target_type', 'Promoter')->get();

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($userId, $array)){
                return true;
            }

        }

        return false;

    }

    public function productKnowledgeStoreRelation($storeId){

        $productKnowledge = ProductKnowledge::where('target_type', 'Store')->get();

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($storeId, $array)){
                return true;
            }

        }

        return false;

    }

    public function productKnowledgeDistrictRelation($districtId){

        $productKnowledge = ProductKnowledge::where('target_type', 'Area')->get();

        $countProductKnowledge = 0;

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($districtId, $array)){
                return true;
            }

        }

        return false;

    }

    public function productKnowledgeAdminRelation($userId){
        $countProductKnowledge = ProductKnowledge::where('user_id', $userId)->first();

        if ($countProductKnowledge) {
            return true;
        }

        return false;
    }

    public function competitorActivityGroupRelation($groupCompetitorId){
        $countActivity = CompetitorActivity::where('groupcompetitor_id', $groupCompetitorId)->first();
        if($countActivity){
            return true;
        }
        return false;
    }


    public function salesProductRelation($productId){

        // COUNT IN SELL IN(Sell Through)
        $sellInCount = SellInDetail::where('product_id', $productId)->first();
        if($sellInCount){
            return true;
        }

        // COUNT IN SELL OUT
        $sellOutCount = SellOutDetail::where('product_id', $productId)->first();
        if($sellOutCount){
            return true;
        }

        // COUNT IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributorDetail::where('product_id', $productId)->first();
        if($retDistributorCount){
            return true;
        }

        // COUNT IN RET CONSUMENT
        $retConsumentCount = RetConsumentDetail::where('product_id', $productId)->first();
        if($retConsumentCount){
            return true;
        }

        // COUNT IN FREE PRODUCT
        $freeProductCount = FreeProductDetail::where('product_id', $productId)->first();
        if($freeProductCount){
            return true;
        }

        // COUNT IN TBAT
        $tbatCount = TbatDetail::where('product_id', $productId)->first();
        if($tbatCount){
            return true;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivityCount = PromoActivityDetail::where('product_id', $productId)->first();
        if($promoActivityCount){
            return true;
        }

        return false;
    }
    

    
    // Check Relation

    public function checkUserRelation(Request $request){
        if(($this->salesEmployeeRelation($request->userId) || $this->storeSpvRelation($request->userId) || $this->newsEmployeeRelation($request->userId) || $this->posmActivityEmployeeRelation($request->userId) || $this->newsAdminRelation($request->userId) || $this->targetEmployeeRelation($request->userId) || $this->productKnowledgeEmployeeRelation($request->userId) || $this->productKnowledgeAdminRelation($request->userId)) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkStoreRelation(Request $request){
        if ( ($this->newsStoreRelation($request->storeId) || $this->salesStoreRelation($request->storeId) || $this->targetStoreRelation($request->storeId) || $this->posmActivityStoreRelation($request->storeId) || $this->productKnowledgeStoreRelation($request->storeId)) ) {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkProductRelation(Request $request){
        if ( $this->salesProductRelation($request->productId) || $this->priceProductRelation($request->productId) || $this->productFocusProductRelation($request->productId)) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function checkPosmRelation(Request $request){
        if ( $this->posmActivityDetailPosmRelation($request->posmId) ) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    
    public function checkGroupCompetitorRelation(Request $request){
        if ( $this->competitorActivityGroupRelation($request->groupCompetitorId) ) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function checkGroupProductRelation(Request $request){
        if ( $this->groupGroupProductRelation($request->groupProductId) || $this->targetGroupProductRelation($request->groupProductId) ) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function checkGroupRelation(Request $request){
        if ( $this->categoryGroupRelation($request->groupId) ) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function checkEmployeeRelation(Request $request){
        if(( $this->storeSpvRelation($request->userId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkCategoryRelation(Request $request){
        // return $request->all();
        if(( $this->productCategoryRelation($request->categoryId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkDistrictRelation(Request $request){
        if(( $this->storeDistrictRelation($request->districtId) || $this->newsDistrictRelation($request->districtId) || $this->productKnowledgeDistrictRelation($request->districtId)) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }
    
    public function checkAreaRelation(Request $request){
        if(( $this->districtAreaRelation($request->areaId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkChannelRelation(Request $request){
        if(( $this->subChannelChannelRelation($request->channelId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkSubChannelRelation(Request $request){
        if(( $this->storeSubChannelRelation($request->subChannelId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function checkDistributorRelation(Request $request){
        if(( $this->storeDistributorRelation($request->distributorId) ) )
        {
            return response()->json(true);
        }

        return response()->json(false);
    }
    
    public function checkRoleRelation(Request $request){
        $count = User::where('role_id', $request->roleId)->first();
        if ($count) {
            response()->json(true);
        }

        return response()->json(false);
    }

    public function checkGradingRelation(Request $request){
        $count = User::where('grading_id', $request->gradingId)->first();
        if ($count) {
            response()->json(true);
        }

        return response()->json(false);
    }

    public function checkClassificationRelation(Request $request){
        $count = Store::where('classification_id', $request->classificationId)->first();
        if ($count) {
            response()->json(true);
        }

        return response()->json(false);
    }

}
