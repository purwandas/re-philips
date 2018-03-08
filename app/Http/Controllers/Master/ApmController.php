<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\ApmFilters;
use App\Traits\StringTrait;
use DB;
use App\Apm;
use App\ApmMonth;
use Auth;
use App\Store;
use App\Product;
use Carbon\Carbon;

class ApmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    	
    	$arMonth = [
    				Carbon::now()->subMonths(1)->format('F Y'),
    				Carbon::now()->subMonths(2)->format('F Y'),
    				Carbon::now()->subMonths(3)->format('F Y'),
    				Carbon::now()->subMonths(4)->format('F Y'),
    				Carbon::now()->subMonths(5)->format('F Y'),
    				Carbon::now()->subMonths(6)->format('F Y'),
    			   ];

    	$apmMonth = ApmMonth::all();

    			   // return response()->json(['asd' => $arMonth]);

        return view('master.apm')->with('arMonth', $arMonth)->with('apmMonth', $apmMonth);
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){

        $data = Apm::where('apms.deleted_at', null)
                    ->join('stores', 'apms.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('products', 'apms.product_id', '=', 'products.id')
                    ->select('apms.*', 'stores.store_name_1 as store_name', 'products.name as product_name', 'districts.name as district', 'areas.name as area', 'regions.name as region');

        $filter = $data;

        /* If filter */            
            if($request['byRegion']){
                $filter = $data->whereHas('store.district.area.region', function($query) use ($request) {
                    return $query->where('regions.id', $request['byRegion']);
                });
            }

            if($request['byArea']){
                $filter = $data->whereHas('store.district.area', function($query) use ($request) {
                    return $query->where('areas.id', $request['byArea']);
                });
            }

            if($request['byDistrict']){
                $filter = $data->whereHas('store.district', function($query) use ($request) {
                    return $query->where('districts.id', $request['byDistrict']);
                });
            }

            if($request['byStore']){
                $filter = $data->where('apms.store_id', $request['byStore']);
            }

            if($request['byProduct']){
                $filter = $data->where('apms.product_id', $request['byProduct']);
            }

        return $this->makeTable($filter->get());
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ApmFilters $filters){        

        $data = Apm::filter($filters)
                ->join('stores', 'apms.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('products', 'apms.product_id', '=', 'products.id')
                    ->select('apms.*', 'stores.store_name_1 as store_name', 'products.name as product_name', 'districts.name as district', 'areas.name as area', 'regions.name as region')->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->editColumn('month_minus_1_value', function ($item){
		        	return number_format($item->month_minus_1_value);
		        })
		        ->editColumn('month_minus_2_value', function ($item) {
		        	return number_format($item->month_minus_2_value);
		        })
		        ->editColumn('month_minus_3_value', function ($item) {
		        	return number_format($item->month_minus_3_value);
		        })
		        ->editColumn('month_minus_4_value', function ($item) {
		        	return number_format($item->month_minus_4_value);
		        })
		        ->editColumn('month_minus_5_value', function ($item) {
		        	return number_format($item->month_minus_5_value);
		        })
		        ->editColumn('month_minus_6_value', function ($item) {
		        	return number_format($item->month_minus_6_value);
		        })
                ->make(true);

    }

    public function setMonth(Request $request){    	

    	$count = 0;
    	if(isset($request['month1'])) $count += 1;
    	if(isset($request['month2'])) $count += 1;
    	if(isset($request['month3'])) $count += 1;
    	if(isset($request['month4'])) $count += 1;
    	if(isset($request['month5'])) $count += 1;
    	if(isset($request['month6'])) $count += 1;

    	if($count > 3){

    		return response()->json(['error' => 1, 'message' => 'Cannot select more than 3 month.']);

    	}

    	// Reset all selection
    	DB::table('apm_months')->update(['selected' => 0]);

    	if(isset($request['month1'])) ApmMonth::where('previous_month', 1)->update(['selected' => 1]);
    	if(isset($request['month2'])) ApmMonth::where('previous_month', 2)->update(['selected' => 1]);
    	if(isset($request['month3'])) ApmMonth::where('previous_month', 3)->update(['selected' => 1]);
    	if(isset($request['month4'])) ApmMonth::where('previous_month', 4)->update(['selected' => 1]);
    	if(isset($request['month5'])) ApmMonth::where('previous_month', 5)->update(['selected' => 1]);
    	if(isset($request['month6'])) ApmMonth::where('previous_month', 6)->update(['selected' => 1]);
    	
    	return response()->json(['url' => url('/apm')]);
    }
}
