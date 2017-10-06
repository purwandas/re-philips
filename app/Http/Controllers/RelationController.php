<?php

namespace App\Http\Controllers;

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

    public function storeSpvRelation(Request $request){

        $user = User::find($request->userId);

        $countStore = 0;

        if($user->role == 'Supervisor'){

            $countStore = Store::where('user_id', $request->userId)->count();

        }

        return response()->json($countStore);
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

    public function salesEmployeeRelation(Request $request){

        $countSales = 0;   

        // COUNT IN SELL IN
        $sellInCount = SellIn::where('user_id', $request->userId)->count();
        if($sellInCount > 0){
            $countSales += 1;
        }

        // COUNT IN SELL OUT
        $sellOutCount = SellOut::where('user_id', $request->userId)->count();
        if($sellOutCount > 0){
            $countSales += 1;
        }

        // COUNT IN RET DISTRIBUTOR
        $retDistributorCount = RetDistributor::where('user_id', $request->userId)->count();
        if($retDistributorCount > 0){
            $countSales += 1;
        }

        // COUNT IN RET CONSUMENT
        $retConsumentCount = RetConsument::where('user_id', $request->userId)->count();
        if($retConsumentCount > 0){
            $countSales += 1;
        }

        // COUNT IN FREE PRODUCT
        $freeProductCount = FreeProduct::where('user_id', $request->userId)->count();
        if($freeProductCount > 0){
            $countSales += 1;
        }

        // COUNT IN TBAT
        $tbatCount = Tbat::where('user_id', $request->userId)->count();
        if($tbatCount > 0){
            $countSales += 1;
        }

        return response()->json($countSales);
    }

    public function newsEmployeeRelation(Request $request){

        $news = News::where('target_type', 'Promoter')->get();

        $countNews = 0;

        foreach ($news as $data) {

            $array = explode(', ', $data->target_detail);
            if(in_array($request->employeeId, $array)){
                $countNews += 1;
            }

        }

        return response()->json($countNews);

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
    
}
