<?php

namespace App\Http\Controllers\Master;

use App\EmployeeStore;
use App\Filters\KonfigPromoFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;

class KonfigController extends Controller
{
    public function konfigPromoterIndex(){
        return view('report.konfig-promoter');
    }

    public function promoterData(Request $request, KonfigPromoFilters $filters){

        $data = EmployeeStore::join('users', 'users.id', 'employee_stores.user_id')
                ->join()
                ->join('stores', 'stores.id', 'employee_stores.store_id')->get();

        return Datatables::of($data)
            ->make(true);

    }
}
