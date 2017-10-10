<?php

namespace App\Http\Controllers;

use App\CompetitorActivity;
use App\CompetitorActivityDetail;
use App\FreeProductDetail;
use App\PromoActivity;
use App\PromoActivityDetail;
use App\RetConsumentDetail;
use App\RetDistributorDetail;
use App\SellOutDetail;
use App\SellInDetail;
use App\TbatDetail;
use Illuminate\Http\Request;
use App\Area;
use App\AreaApp;
use App\DmArea;
use App\Account;
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
use App\PosmActivityDetail;
use App\PosmActivity;
use App\ProductKnowledge;

class RelationController extends Controller
{
    //
    public function areaAppsAreaRelation(Request $request){
    	$countAreaApps = AreaApp::where('area_id', $request->areaId)->count();

        return response()->json($countAreaApps);
    }

    public function accountAccountTypeRelation(Request $request){
    	$countAccount = Account::where('accounttype_id', $request->accountTypeId)->count();

        return response()->json($countAccount);
    }

    public function storeAccountRelation(Request $request){
        $countStore = Store::where('account_id', $request->accountId)->count();

        return response()->json($countStore);
    }

    public function storeAreaAppRelation(Request $request){
        $countStore = Store::where('areaapp_id', $request->areaAppId)->count();

        return response()->json($countStore);
    }

    public function storeSpvRelation($userId){

        $user = User::find($userId);

        // $countStore = 0;
        $checkStore =false;

        if($user->role == 'Supervisor'){

            $checkStore = Store::where('user_id', $userId)->first();

        }

        return $checkStore;
    }

    public function categoryGroupRelation(Request $request){
        $countCategory = Category::where('group_id', $request->groupId)->count();

        return response()->json($countCategory);
    }

    public function productCategoryRelation(Request $request){
        $countProduct = Product::where('category_id', $request->categoryId)->count();

        return response()->json($countProduct);
    }

    public function storeSpvChangeRelation(Request $request){
        $user = User::find($request->spvId);

        $countStore = 0;
        if(($request->role != $user->role) && ($user->role == "Supervisor")){
            $countStore = $user->store()->count();
        }

        return response()->json($countStore);
    }

    public function salesEmployeeChangeRelation(Request $request){

        $user = User::find($request->employeeId);

        $countSales = 0;
        $isPromoter = 0;

        if($request->role == 'Promoter' || $request->role == 'Promoter Additional' || $request->role == 'Promoter Event' || $request->role == 'Demonstrator MCC' || $request->role == 'Demonstrator DA' || $request->role == 'ACT'  || $request->role == 'PPE' || $request->role == 'BDT' || $request->role == 'Salesman Explorer' || $request->role == 'SMD' || $request->role == 'SMD Coordinator' || $request->role == 'HIC' || $request->role == 'HIE' || $request->role == 'SMD Additional' || $request->role == 'ASC'){
            $isPromoter = 1;
        }        

        if(($request->role != $user->role) && ($isPromoter == 0)){

            // COUNT IN SELL IN
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

        // CHECK IN SELL IN
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

        // COUNT IN COMPETITOR ACTIVITY
        $competitorActivity = CompetitorActivity::where('user_id', $request->userId)->count();
        if($competitorActivity > 0){
            $countSales += 1;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivity = PromoActivity::where('user_id', $request->userId)->count();
        if($promoActivity > 0){
            $countSales += 1;
        }


        // COUNT IN COMPETITOR ACTIVITY
        $competitorActivity = CompetitorActivity::where('user_id', $request->userId)->count();
        if($competitorActivity > 0){
            $countSales += 1;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivity = PromoActivity::where('user_id', $request->userId)->count();
        if($promoActivity > 0){
            $countSales += 1;
        }

        return response()->json($countSales);

    }

    public function salesStoreRelation(Request $request){

        $countSales = 0;

        // COUNT IN SELL IN
        $sellInCount = SellIn::where('store_id', $request->storeId)->count();
        if($sellInCount > 0){
            $countSales += 1;
        }

        // COUNT IN SELL OUT
        $sellOutCount = SellOut::where('store_id', $request->storeId)->count();
        if($sellOutCount > 0){
            $countSales += 1;
        }

        // COUNT IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributor::where('store_id', $request->storeId)->count();
        if($retDistributorCount > 0){
            $countSales += 1;
        }

        // COUNT IN RET CONSUMENT
        $retConsumentCount = RetConsument::where('store_id', $request->storeId)->count();
        if($retConsumentCount > 0){
            $countSales += 1;
        }

        // COUNT IN FREE PRODUCT
        $freeProductCount = FreeProduct::where('store_id', $request->storeId)->count();
        if($freeProductCount > 0){
            $countSales += 1;
        }

        // COUNT IN TBAT
        $tbatCount = Tbat::where('store_id', $request->storeId)->count();
        if($tbatCount > 0){
            $countSales += 1;
        }

        // COUNT IN COMPETITOR ACTIVITY
        $competitorActivity = CompetitorActivity::where('store_id', $request->storeId)->count();
        if($competitorActivity > 0){
            $countSales += 1;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivity = PromoActivity::where('store_id', $request->storeId)->count();
        if($promoActivity > 0){
            $countSales += 1;
        }

        return response()->json($countSales);
    }

    public function newsEmployeeRelation($userId){

        $news = News::where('target_type', 'Promoter')->get();

        // $countNews = 0;
        $checkEmployee=false;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($userId, $array)){
                // $countNews += 1;
                $checkEmployee=true;
                break;
            }

        }

        return $checkEmployee;

    }

    public function newsStoreRelation(Request $request){

        $news = News::where('target_type', 'Store')->get();

        $countNews = 0;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->storeId, $array)){
                $countNews += 1;
            }

        }

        return response()->json($countNews);

    }

    public function newsAreaRelation(Request $request){

        $news = News::where('target_type', 'Area')->get();

        $countNews = 0;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->areaId, $array)){
                $countNews += 1;
            }

        }

        return response()->json($countNews);

    }

    public function posmActivityDetailPosmRelation(Request $request){
        $countPosm = PosmActivityDetail::where('posm_id', $request->posmId)->count();

        return response()->json($countPosm);
    }

    public function posmActivityEmployeeRelation($userId){
        $countPosmActivity = PosmActivity::where('user_id', $userId)->first();

        return $countPosmActivity;
    }

    public function posmActivityStoreRelation(Request $request){
        $countPosmActivity = PosmActivity::where('store_id', $request->storeId)->count();

        return response()->json($countPosmActivity);
    }

    public function newsAdminRelation($userId){
        $countNews = News::where('user_id', $userId)->first();

        return $countNews;
    }

    public function productKnowledgeEmployeeRelation(Request $request){

        $productKnowledge = ProductKnowledge::where('target_type', 'Promoter')->get();

        $countProductKnowledge = 0;

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->employeeId, $array)){
                $countProductKnowledge += 1;
            }

        }

        return response()->json($countProductKnowledge);

    }

    public function productKnowledgeStoreRelation(Request $request){

        $productKnowledge = ProductKnowledge::where('target_type', 'Store')->get();

        $countProductKnowledge = 0;

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->storeId, $array)){
                $countProductKnowledge += 1;
            }

        }

        return response()->json($countProductKnowledge);

    }

    public function productKnowledgeAreaRelation(Request $request){

        $productKnowledge = ProductKnowledge::where('target_type', 'Area')->get();

        $countProductKnowledge = 0;

        foreach ($productKnowledge as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->areaId, $array)){
                $countProductKnowledge += 1;
            }

        }

        return response()->json($countProductKnowledge);

    }

    public function productKnowledgeAdminRelation(Request $request){
        $countProductKnowledge = ProductKnowledge::where('user_id', $request->userId)->count();

        return response()->json($countProductKnowledge);
    }

    public function competitorActivityGroupRelation(Request $request){
        $countActivity = CompetitorActivityDetail::where('groupcompetitor_id', $request->groupCompetitorId)->count();

        return response()->json($countActivity);
    }


    public function salesProductRelation(Request $request){

        $countSalesDetails = 0;

        // COUNT IN SELL IN
        $sellInCount = SellInDetail::where('product_id', $request->productId)->count();
        if($sellInCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN SELL OUT
        $sellOutCount = SellOutDetail::where('product_id', $request->productId)->count();
        if($sellOutCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributorDetail::where('product_id', $request->productId)->count();
        if($retDistributorCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN RET CONSUMENT
        $retConsumentCount = RetConsumentDetail::where('product_id', $request->productId)->count();
        if($retConsumentCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN FREE PRODUCT
        $freeProductCount = FreeProductDetail::where('product_id', $request->productId)->count();
        if($freeProductCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN TBAT
        $tbatCount = TbatDetail::where('product_id', $request->productId)->count();
        if($tbatCount > 0){
            $countSalesDetails += 1;
        }

        // COUNT IN PROMO ACTIVITY
        $promoActivityCount = PromoActivityDetail::where('product_id', $request->productId)->count();
        if($promoActivityCount > 0){
            $countSalesDetails += 1;
        }

        return response()->json($countSalesDetails);
    }
    
    public function checkUserRelation(Request $request){
        if(($this->salesEmployeeRelation($request->userId) && $this->storeSpvRelation($request->userId) && $this->newsEmployeeRelation($request->userId) && $this->posmActivityEmployeeRelation($request->userId) && $this->newsAdminRelation($request->userId))==false)
        {
            return response()->json(false);
        }

        return response()->json(true);
    }
}
