<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
use App\SellIn;
use App\SellInDetail;
use App\Filters\ReportFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;

class ReportController extends Controller
{
    use StringTrait;

    public function sellInIndex(){
        return view('report.sellin-report');
    }

    public function sellInData(ReportFilters $filters){

//        $data = SellInDetail::get();
//
//        $str = '10/27/2017';
//        $str2 = '03 October 2017';
//        return response()->json(Carbon::parse($str2)->format('Y-m-d'));

        $data = SellInDetail::filter($filters);

        $data = $data->where('sell_in_details.deleted_at', null)
            ->join('sell_ins', 'sell_in_details.sellin_id', '=', 'sell_ins.id')
            ->join('stores', 'stores.id', '=', 'sell_ins.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'sell_ins.user_id', '=', 'users.id')
            ->join('products', 'sell_in_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('sell_in_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'sell_ins.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        return Datatables::of($data)
            ->make(true);

    }
}
