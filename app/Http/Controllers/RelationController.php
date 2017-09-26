<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\AreaApp;
use App\DmArea;
use App\Account;

class RelationController extends Controller
{
    //
    public function areaAreaAppsRelation(Request $request){
    	$countAreaApps = AreaApp::where('area_id', $request->areaId)->count();

        return response()->json($countAreaApps);
    }

    public function areaDmRelation(Request $request){
    	$countDm = DmArea::where('area_id', $request->areaId)->count();

        return response()->json($countDm);
    }

    public function accountTypeAccountRelation(Request $request){
    	$countAccount = Account::where('accounttype_id', $request->accountTypeId)->count();

        return response()->json($countAccount);
    }
    
}
