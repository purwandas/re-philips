<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\AreaApp;
use App\DmArea;
use App\Account;
use App\Store;
use App\Employee;

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

    public function storeAccountRelation(Request $request){
        $countStore = Store::where('account_id', $request->accountId)->count();

        return response()->json($countStore);
    }

    public function storeAreaAppRelation(Request $request){
        $countStore = Store::where('areaapp_id', $request->areaAppId)->count();

        return response()->json($countStore);
    }

    public function storeSpvRelation(Request $request){

        $employee = Employee::find($request->employeeId);

        $countStore = 0;

        if($employee->role == 'Supervisor'){

            $countStore = Store::where('employee_id', $request->employeeId)->count();

        }

        return response()->json($countStore);
    }
    
}
