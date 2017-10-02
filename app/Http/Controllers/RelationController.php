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
    
}
