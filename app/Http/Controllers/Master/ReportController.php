<?php

namespace App\Http\Controllers\Master;

use App\Distributor;
use App\DmArea;
use App\Filters\SellinFilters;
use App\Filters\SellOutFilters;
use App\Filters\RetConsumentFilters;
use App\Filters\RetDistributorFilters;
use App\Filters\FreeProductFilters;
use App\Filters\TbatFilters;
use App\Filters\DisplayShareFilters;
use App\Filters\SohFilters;
use App\Filters\SosFilters;
use App\ProductFocuses;
use App\Region;
use App\Reports\HistorySalesmanSales;
use App\Reports\SalesmanSummarySales;
use App\StoreDistributor;
use App\Reports\SummarySellIn;
use App\Reports\HistorySellIn;
use App\Reports\SummarySellOut;
use App\Reports\HistorySellOut;
use App\Reports\SummaryRetConsument;
use App\Reports\HistoryRetConsument;
use App\Reports\SummaryRetDistributor;
use App\Reports\HistoryRetDistributor;
use App\Reports\SummaryFreeProduct;
use App\Reports\HistoryFreeProduct;
use App\Reports\SummaryTbat;
use App\Reports\HistoryTbat;
use App\Reports\SummaryDisplayShare;
use App\Reports\HistoryDisplayShare;
use App\Reports\SummarySoh;
use App\Reports\HistorySoh;
use App\Reports\SummarySos;
use App\Reports\HistorySos;
use App\Reports\SalesActivity;
use App\Reports\StoreLocationActivity;
use App\TrainerArea;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use League\Geotools\CLI\Command\Convert\DM;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
use App\PosmActivity;
use App\PosmActivityDetail;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\RetConsument;
use App\RetConsumentDetail;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\Tbat;
use App\TbatDetail;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\Soh;
use App\SohDetail;
use App\Sos;
use App\SosDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\MaintenanceRequest;
use App\CompetitorActivity;
use App\PromoActivity;
use App\PromoActivityDetail;
use App\Attendance;
use App\AttendanceDetail;
use App\VisitPlan;
use App\EmployeeStore;
use App\District;
use App\Store;
use App\Area;
use App\RsmRegion;
use App\Filters\ReportFilters;
use App\Filters\ReportPosmActivityFilters;
use App\Filters\ReportSellOutFilters;
use App\Filters\ReportSohFilters;
use App\Filters\ReportSosFilters;
use App\Filters\ReportRetConsumentFilters;
use App\Filters\ReportRetDistributorFilters;
use App\Filters\ReportTbatFilters;
use App\Filters\ReportDisplayShareFilters;
use App\Filters\MaintenanceRequestFilters;
use App\Filters\CompetitorActivityFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;

class ReportController extends Controller
{
    use StringTrait;

    public function sellInIndex(){
        return view('report.sellin-report');
    }

    public function sellOutIndex(){
        return view('report.sellout-report');
    }

    public function retConsumentIndex()
    {
        return view('report.retconsument-report');
    }

    public function retDistributorIndex()
    {
        return view('report.retdistributor-report');
    }

    public function tbatIndex()
    {
        return view('report.tbat-report');
    }

    public function freeProductIndex()
    {
        return view('report.freeproduct-report');
    }

    public function sohIndex()
    {
        return view('report.soh-report');
    }

    public function sosIndex()
    {
        return view('report.sos-report');
    }

    public function displayShareIndex(){
        return view('report.displayshare-report');
    }

    public function maintenanceRequestIndex(){
        return view('report.maintenancerequest-report');
    }

    public function competitorActivityIndex(){
        return view('report.competitoractivity-report');
    }

    public function promoActivityIndex(){
        return view('report.promoactivity-report');
    }
   
    public function posmActivityIndex(){
        return view('report.posmactivity-report');
    }

    public function attendanceIndex(){
        return view('report.attendance-report');
    }
    
    public function attendanceForm(){
        return view('report.form.attendance-form');
    }

    public function visitPlanIndex(){
        return view('report.visitplan');
    }

    public function salesmanIndex(){
        return view('report.salesman-report');
    }

    public function salesActivityIndex(){
        return view('report.salesactivity-report');
    }

    public function storeLocationActivityIndex(){
        return view('report.storelocation-report');
    }

    public function sellInData(Request $request, SellinFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellIn::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellIn::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellIn::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySellIn::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->editColumn('value_pf_mr', function ($item) {
               return number_format($item->value_pf_mr);
            })
            ->editColumn('value_pf_tr', function ($item) {
               return number_format($item->value_pf_tr);
            })
            ->editColumn('value_pf_ppe', function ($item) {
               return number_format($item->value_pf_ppe);
            })
            ->editColumn('irisan', function ($item) {
               if($item->irisan == 0){
                    return '-';
               }else{
                    return 'Irisan';
               }
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();
            // return response()->json("kampret");
            $history = HistorySellIn::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['value_pf_mr'] = number_format($transaction->value_pf_mr);
                        $collection['value_pf_tr'] = number_format($transaction->value_pf_tr);
                        $collection['value_pf_ppe'] = number_format($transaction->value_pf_ppe);
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            // return response()->json($historyData);

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            // return response()->json($filter);

            return Datatables::of($filter->all())
            // ->editColumn('quantity', function ($item) {
            //    return number_format($item->quantity);
            // })
            // ->editColumn('unit_price', function ($item) {
            //    return number_format($item->unit_price);
            // })
            // ->editColumn('value', function ($item) {
            //    return number_format($item->value);
            // })
            // ->editColumn('value_pf_mr', function ($item) {
            //    return number_format($item->value_pf_mr);
            // })
            // ->editColumn('value_pf_tr', function ($item) {
            //    return number_format($item->value_pf_tr);
            // })
            // ->editColumn('value_pf_ppe', function ($item) {
            //    return number_format($item->value_pf_ppe);
            // })
            ->editColumn('irisan', function ($item) {
               if($item['irisan'] == 0){
                    return '-';
               }else{
                    return 'Irisan';
               }
            })
            ->make(true);

        }

    }

    public function sellInDataAll(Request $request, SellinFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellIn::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellIn::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellIn::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySellIn::all();
            }

            $filter = $data;            

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

            // if($request['byRegion']){
            //     $filter = $filter->where('region_id', $request['byRegion']);
            // }

            // if($request['byArea']){
            //     $filter = $filter->where('area_id', $request['byArea']);
            // }

            // if($request['byDistrict']){
            //     $filter = $filter->where('district_id', $request['byDistrict']);
            // }

            // if($request['byStore']){
            //     $store = Store::where('stores.id', $request['byStore'])
            //                     ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
            //                     ->pluck('storeses.id');
            //     $filter = $filter->whereIn('storeId', $store);
            // }
            //         // return response()->json($request['byStore']);

            // if($request['byEmployee']){
            //     $filter = $filter->where('user_id', $request['byEmployee']);
            // }

            // return Datatables::of($filter->all())
            // ->editColumn('quantity', function ($item) {
            //    return number_format($item->quantity);
            // })
            // ->editColumn('unit_price', function ($item) {
            //    return number_format($item->unit_price);
            // })
            // ->editColumn('value', function ($item) {
            //    return number_format($item->value);
            // })
            // ->editColumn('value_pf_mr', function ($item) {
            //    return number_format($item->value_pf_mr);
            // })
            // ->editColumn('value_pf_tr', function ($item) {
            //    return number_format($item->value_pf_tr);
            // })
            // ->editColumn('value_pf_ppe', function ($item) {
            //    return number_format($item->value_pf_ppe);
            // })
            // ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();
            // return response()->json("kampret");
            $history = HistorySellIn::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            // return response()->json($historyData);

            $filter = $historyData;            

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

            // if($request['byRegion']){
            //     $filter = $filter->where('region_id', $request['byRegion']);
            // }

            // if($request['byArea']){
            //     $filter = $filter->where('area_id', $request['byArea']);
            // }

            // if($request['byDistrict']){
            //     $filter = $filter->where('district_id', $request['byDistrict']);
            // }

            // if($request['byStore']){
            //     $store = Store::where('stores.id', $request['byStore'])
            //                     ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
            //                     ->pluck('storeses.id');
            //     $filter = $filter->whereIn('storeId', $store);
            // }

            // if($request['byEmployee']){
            //     $filter = $filter->where('user_id', $request['byEmployee']);
            // }

            // if ($userRole == 'RSM') {
            //     $regionIds = RsmRegion::where('user_id', $userId)
            //                         ->pluck('rsm_regions.region_id');
            //     $filter = $filter->whereIn('region_id', $regionIds);
            // }

            // if ($userRole == 'DM') {
            //     $areaIds = DmArea::where('user_id', $userId)
            //                         ->pluck('dm_areas.area_id');
            //     $filter = $filter->whereIn('area_id', $areaIds);
            // }

            // if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            //     $storeIds = Store::where('user_id', $userId)
            //                         ->pluck('stores.store_id');
            //     $filter = $filter->whereIn('store_id', $storeIds);
            // }

            // // return response()->json($filter);

            // return Datatables::of($filter->all())
            // ->make(true);

        }

    }

    public function sellInDataAllCheck(Request $request, SellinFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellIn::where('region_id', $region)->limit(1)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellIn::where('area_id', $area)->limit(1)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellIn::wherein('store_id', $store)->limit(1)->get();
            }
            else{
                $data = SummarySellIn::limit(1)->get();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();
            // return response()->json("kampret");
            $history = HistorySellIn::where('year', $yearRequest)
                        ->where('month', $monthRequest)->limit(1)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;            

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }

    }

    public function sellOutData(Request $request, SellOutFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellOut::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellOut::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellOut::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySellOut::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->editColumn('value_pf_mr', function ($item) {
               return number_format($item->value_pf_mr);
            })
            ->editColumn('value_pf_tr', function ($item) {
               return number_format($item->value_pf_tr);
            })
            ->editColumn('value_pf_ppe', function ($item) {
               return number_format($item->value_pf_ppe);
            })
            ->editColumn('irisan', function ($item) {
               if($item->irisan == 0){
                    return '-';
               }else{
                    return 'Irisan';
               }
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySellOut::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['value_pf_mr'] = number_format($transaction->value_pf_mr);
                        $collection['value_pf_tr'] = number_format($transaction->value_pf_tr);
                        $collection['value_pf_ppe'] = number_format($transaction->value_pf_ppe);
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            // ->editColumn('quantity', function ($item) {
            //    return number_format($item->quantity);
            // })
            // ->editColumn('unit_price', function ($item) {
            //    return number_format($item->unit_price);
            // })
            // ->editColumn('value', function ($item) {
            //    return number_format($item->value);
            // })
            // ->editColumn('value_pf_mr', function ($item) {
            //    return number_format($item->value_pf_mr);
            // })
            // ->editColumn('value_pf_tr', function ($item) {
            //    return number_format($item->value_pf_tr);
            // })
            // ->editColumn('value_pf_ppe', function ($item) {
            //    return number_format($item->value_pf_ppe);
            // })
            ->editColumn('irisan', function ($item) {
               if($item['irisan'] == 0){
                    return '-';
               }else{
                    return 'Irisan';
               }
            })
            ->make(true);

        }

    }

    public function sellOutDataAll(Request $request, SellOutFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellOut::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellOut::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellOut::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySellOut::all();
            }

            $filter = $data;            

            // /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

            // if($request['byRegion']){
            //     $filter = $filter->where('region_id', $request['byRegion']);
            // }

            // if($request['byArea']){
            //     $filter = $filter->where('area_id', $request['byArea']);
            // }

            // if($request['byDistrict']){
            //     $filter = $filter->where('district_id', $request['byDistrict']);
            // }

            // if($request['byStore']){
            //     $store = Store::where('stores.id', $request['byStore'])
            //                     ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
            //                     ->pluck('storeses.id');
            //     $filter = $filter->whereIn('storeId', $store);
            // }

            // if($request['byEmployee']){
            //     $filter = $filter->where('user_id', $request['byEmployee']);
            // }

            // return Datatables::of($filter->all())
            // ->editColumn('quantity', function ($item) {
            //    return number_format($item->quantity);
            // })
            // ->editColumn('unit_price', function ($item) {
            //    return number_format($item->unit_price);
            // })
            // ->editColumn('value', function ($item) {
            //    return number_format($item->value);
            // })
            // ->editColumn('value_pf_mr', function ($item) {
            //    return number_format($item->value_pf_mr);
            // })
            // ->editColumn('value_pf_tr', function ($item) {
            //    return number_format($item->value_pf_tr);
            // })
            // ->editColumn('value_pf_ppe', function ($item) {
            //    return number_format($item->value_pf_ppe);
            // })
            // ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySellOut::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;    

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();
            
            // if($request['byRegion']){
            //     $filter = $filter->where('region_id', $request['byRegion']);
            // }

            // if($request['byArea']){
            //     $filter = $filter->where('area_id', $request['byArea']);
            // }

            // if($request['byDistrict']){
            //     $filter = $filter->where('district_id', $request['byDistrict']);
            // }

            // if($request['byStore']){
            //     $store = Store::where('stores.id', $request['byStore'])
            //                     ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
            //                     ->pluck('storeses.id');
            //     $filter = $filter->whereIn('storeId', $store);
            // }

            // if($request['byEmployee']){
            //     $filter = $filter->where('user_id', $request['byEmployee']);
            // }

            // if ($userRole == 'RSM') {
            //     $regionIds = RsmRegion::where('user_id', $userId)
            //                         ->pluck('rsm_regions.region_id');
            //     $filter = $filter->whereIn('region_id', $regionIds);
            // }

            // if ($userRole == 'DM') {
            //     $areaIds = DmArea::where('user_id', $userId)
            //                         ->pluck('dm_areas.area_id');
            //     $filter = $filter->whereIn('area_id', $areaIds);
            // }

            // if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            //     $storeIds = Store::where('user_id', $userId)
            //                         ->pluck('stores.store_id');
            //     $filter = $filter->whereIn('store_id', $storeIds);
            // }
            // return Datatables::of($filter->all())
            // ->make(true);

        }

    }

    public function sellOutDataAllCheck(Request $request, SellOutFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySellOut::limit(1)->where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySellOut::limit(1)->where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySellOut::limit(1)->wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySellOut::limit(1)->get();
            }

            $filter = $data;            

            // /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySellOut::limit(1)->where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['irisan'] = $transaction->irisan;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;    

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

        }

        return $filter->all();

    }

    public function retConsumentData(Request $request, RetConsumentFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryRetConsument::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryRetConsument::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryRetConsument::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryRetConsument::all();
            }


            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetConsument::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            } 
            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function retConsumentDataAll(Request $request, RetConsumentFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryRetConsument::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryRetConsument::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryRetConsument::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryRetConsument::all();
            }


            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetConsument::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();        

        }

    }
    
    public function retDistributorData(Request $request, RetDistributorFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryRetDistributor::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryRetDistributor::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryRetDistributor::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryRetDistributor::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetDistributor::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function retDistributorDataAll(Request $request, RetDistributorFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryRetDistributor::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryRetDistributor::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryRetDistributor::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryRetDistributor::all();
            }

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();


        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetDistributor::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }

    }

    public function tbatData(Request $request, TbatFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryTbat::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryTbat::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryTbat::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryTbat::all();
            }
            

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byStore2']){
                $store = Store::where('stores.id', $request['byStore2'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeDestinationId', $store);
                    // return response()->json($store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }



            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryTbat::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['store_destinationId'] = $detail->store_destinationId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['store_destination_name_1'] = $detail->store_destination_name_1;
                        $collection['store_destination_name_2'] = $detail->store_destination_name_2;
                        $collection['store_destination_id'] = $detail->store_destination_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byStore2']){
                $store = Store::where('store_id', $request['byStore2'])
                            ->pluck('stores.id');
                $filter = $filter->where('storeDestinationId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function tbatDataAll(Request $request, TbatFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryTbat::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryTbat::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryTbat::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryTbat::all();
            }
            

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryTbat::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['store_destinationId'] = $detail->store_destinationId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['store_destination_name_1'] = $detail->store_destination_name_1;
                        $collection['store_destination_name_2'] = $detail->store_destination_name_2;
                        $collection['store_destination_id'] = $detail->store_destination_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }

    }

    public function freeproductData(Request $request, FreeProductFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryFreeProduct::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryFreeProduct::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryFreeProduct::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryFreeProduct::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryFreeProduct::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }
        
    }

    public function freeproductDataAll(Request $request, FreeProductFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryFreeProduct::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryFreeProduct::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryFreeProduct::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryFreeProduct::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryFreeProduct::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }
        
    }

    public function sohData(Request $request, SohFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');


        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySoh::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySoh::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySoh::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySoh::all();
            }


            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySoh::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }
            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function sohDataAll(Request $request, SohFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');


        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySoh::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySoh::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySoh::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySoh::all();
            }


            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySoh::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }

    }

    public function sosData(Request $request, SosFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummarySos::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummarySos::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummarySos::wherein('store_id', $store)->get();
            }
            else{
                $data = SummarySos::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySos::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function displayShareData(Request $request, DisplayShareFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

 
            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryDisplayShare::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryDisplayShare::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryDisplayShare::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryDisplayShare::all();
            }          

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter)
            ->editColumn('philips', function ($item) {
               return number_format($item->philips);
            })
            ->editColumn('all', function ($item) {
               return number_format($item->all);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryDisplayShare::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['category'] = $transaction->category;
                        $collection['philips'] = number_format($transaction->philips);
                        $collection['all'] = number_format($transaction->all);
                        $collection['percentage'] = $transaction->percentage;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function displayShareDataAll(Request $request, DisplayShareFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

 
            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryDisplayShare::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryDisplayShare::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryDisplayShare::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryDisplayShare::all();
            }          

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryDisplayShare::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['category'] = $transaction->category;
                        $collection['philips'] = $transaction->philips;
                        $collection['all'] = $transaction->all;
                        $collection['percentage'] = $transaction->percentage;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->all();

        }

    }

    public function maintenanceRequestData(Request $request, MaintenanceRequestFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = MaintenanceRequest::filter($filters)
                    ->join('regions', 'maintenance_requests.region_id', '=', 'regions.id')
                    ->join('areas', 'maintenance_requests.area_id', '=', 'areas.id')
                    ->join('stores', 'maintenance_requests.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('users', 'maintenance_requests.user_id', '=', 'users.id')
                    ->select('maintenance_requests.*', 'maintenance_requests.photo as photo2', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.district_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'stores.id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){

                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byDistrict']){
                $filter = $filter->where('area_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                $folderPath = explode('/', $item->photo);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= "<img src='".asset((string)$file)."' height='100px'>\n";
                }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function maintenanceRequestDataAll(Request $request, MaintenanceRequestFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = MaintenanceRequest::filter($filters)
                    ->join('regions', 'maintenance_requests.region_id', '=', 'regions.id')
                    ->join('areas', 'maintenance_requests.area_id', '=', 'areas.id')
                    ->join('stores', 'maintenance_requests.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('users', 'maintenance_requests.user_id', '=', 'users.id')
                    ->select('maintenance_requests.*', 'maintenance_requests.photo as photo2', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.district_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'stores.id as storeId');

            $filter = $data;

            if($request['searchMonth']){

                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->get();

    }

    public function competitorActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
            $data = CompetitorActivity::
                      join('stores', 'competitor_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'competitor_activities.user_id', '=', 'users.id')
                    ->join('group_competitors', 'competitor_activities.groupcompetitor_id', '=', 'group_competitors.id')
                    ->select('competitor_activities.*', 'competitor_activities.photo as photo2','regions.name as region_name', 'regions.id as region_id', 'areas.name as area_name', 'areas.id as area_id', 'districts.name as district_name', 'stores.district_id','stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'group_competitors.name as group_competitor', 'stores.id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if($request['byGroupCompetitor']){
                $filter = $filter->where('groupcompetitor_id', $request['byGroupCompetitor']);
            }


            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                // $folderPath = explode('/', $item->photo);
                // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                // $files = File::allFiles($folder);
                $images = '';
                // foreach ($files as $file)
                // {
                    $images .= "<img src='".$item->photo."' height='100px'>\n";
                // }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function competitorActivityDataAll(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
            $data = CompetitorActivity::
                      join('stores', 'competitor_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'competitor_activities.user_id', '=', 'users.id')
                    ->join('group_competitors', 'competitor_activities.groupcompetitor_id', '=', 'group_competitors.id')
                    ->select('competitor_activities.*', 'competitor_activities.photo as photo2','regions.name as region_name', 'regions.id as region_id', 'areas.name as area_name', 'areas.id as area_id', 'districts.name as district_name', 'stores.district_id','stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'group_competitors.name as group_competitor', 'stores.id as storeId');

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->get();

    }

    public function promoActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = PromoActivity::
                    join('promo_activity_details', 'promo_activity_details.promoactivity_id', '=', 'promo_activities.id')
                    ->join('stores', 'promo_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'promo_activities.user_id', '=', 'users.id')
                    ->join('products', 'promo_activity_details.product_id', '=', 'products.id')
                    ->select('promo_activities.*', 'promo_activity_details.promo as promo', 'promo_activity_details.product_id', 'promo_activities.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'products.model as product_model', 'products.name as product_name', 'products.variants as product_variants', 'stores.id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if($request['byProduct']){
                $filter = $filter->where('product_id', $request['byProduct']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                // $folderPath = explode('/', $item->photo);
                // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                // $files = File::allFiles($folder);
                $images = '';
                // foreach ($files as $file)
                // {
                    $images .= "<img src='".$item->photo."' height='100px'>\n";
                // }
                    return $images;
                })
            ->editColumn('promo', function ($item){
                if($item->promo == 0){
                    return '';
                }else{
                    return 'Promo Market';
                }
            })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function promoActivityDataAll(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = PromoActivity::
                    join('promo_activity_details', 'promo_activity_details.promoactivity_id', '=', 'promo_activities.id')
                    ->join('stores', 'promo_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'promo_activities.user_id', '=', 'users.id')
                    ->join('products', 'promo_activity_details.product_id', '=', 'products.id')
                    ->select('promo_activities.*', 'promo_activity_details.promo as promo', 'promo_activity_details.product_id', 'promo_activities.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'products.model as product_model', 'products.name as product_name', 'products.variants as product_variants', 'stores.id as storeId');

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->get();

    }

    public function posmActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = PosmActivity::
                    join('posm_activity_details', 'posm_activity_details.posmactivity_id', '=', 'posm_activities.id')
                    ->join('stores', 'posm_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'posm_activities.user_id', '=', 'users.id')
                    ->join('posms', 'posm_activity_details.posm_id', '=', 'posms.id')
                    ->join('groups', 'posms.group_id', '=', 'groups.id')
                    ->select('posm_activities.*', 'posm_activity_details.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'posms.name as posm_name', 'groups.name as group_product', 'posm_activity_details.quantity', 'posm_activity_details.photo', 'stores.id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if($request['byProduct']){
                $filter = $data->where('product_id', $request['byProduct']);
            }


            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                if ($item->photo != '') {
                    // $folderPath = explode('/', $item->photo);
                    // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                    // $files = File::allFiles($folder);
                    $images = '';
                    // foreach ($files as $file)
                    // {
                        $images .= "<img src='".$item->photo."' height='100px'>\n";
                    // }
                    return $images;
                }else{
                    return '';
                }
            })
            ->editColumn('photo2', function ($item) {
                if ($item->photo2 != '') {
                    $folderPath = explode('/', $item->photo2);
                    $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                    $files = File::allFiles($folder);
                    $images = '';
                    foreach ($files as $file)
                    {
                        $images .= asset((string)$file)."\n";
                    }
                        return $images;
                }else{
                    return '';
                }
            })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function posmActivityDataAll(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = PosmActivity::
                    join('posm_activity_details', 'posm_activity_details.posmactivity_id', '=', 'posm_activities.id')
                    ->join('stores', 'posm_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'posm_activities.user_id', '=', 'users.id')
                    ->join('posms', 'posm_activity_details.posm_id', '=', 'posms.id')
                    ->join('groups', 'posms.group_id', '=', 'groups.id')
                    ->select('posm_activities.*', 'posm_activity_details.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'posms.name as posm_name', 'groups.name as group_product', 'posm_activity_details.quantity', 'posm_activity_details.photo', 'stores.id as storeId');

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            return $filter->get();

    }

    public function attendanceData(Request $request){

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
                


       $month = Carbon::parse($request['searchMonth'])->format('m');
       $year = Carbon::parse($request['searchMonth'])->format('Y');
       $date1 = "$year-$month-01";
       $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
       $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
       
       $data = Attendance::
            join('employee_stores', 'employee_stores.user_id', '=', 'attendances.user_id')
            ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
            ->join('districts', 'stores.district_id', '=', 'districts.id')
            ->join('areas', 'districts.area_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('roles','roles.id','users.role_id')
            ->groupBy('attendances.user_id')
            ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'roles.role_group as user_role', 'stores.id as store_id', 'stores.id as storeId', 'districts.id as district_id', 'areas.id as area_id', 'regions.id as region_id')
            ->where('attendances.date','>=',(string)$date1)->where('attendances.date','<=',(string)$date2)
            // ->where('attendances.status', '!=', 'Off')
            ->get();

           $filter = $data;

           // return $filter->all();

            /* If filter */
            if($request['byStore']){
                // return $request['byStore'];
                $filter = $filter->where('storeId', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }


            return Datatables::of($filter->all())
            ->addColumn('total_hk', function ($item) {
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
                $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));
                $maxDate = date('Y-m-d');

                $dataD = Attendance::
                        select(DB::raw('count(*) as total_hk'))
                        ->where('attendances.status', '!=', 'Off')
                        ->where('attendances.status', '!=', 'Sakit')
                        ->where('attendances.status', '!=', 'Izin')
                        ->where('attendances.status', '!=', 'Pending Sakit')
                        ->where('attendances.status', '!=', 'Pending Izin')
                        ->where('attendances.status', '!=', 'Alpha')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->get()->all();
                $hk = 0;
                foreach ($dataD as $key => $value) {
                    $hk = $value->total_hk;
                }

                return "$hk";
                
            })
            ->addColumn('attendance_details', function ($item) {
                // return 'kampret';
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
                $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));
                    $status = ['Alpha','Masuk',     'Sakit',    'Izin',     'Sakit(P)','Izin(P)', 'Off'];
                    $warna = ['#e74c3c','#2ecc71',  '#3498db',  '#e67e22',  '#f1c40f',      '#f1c40f',      '#95a5a6'];
                    $text = ['#ecf0f1','#ecf0f1',  '#ecf0f1',  '#ecf0f1',  '#ecf0f1',      '#ecf0f1',      '#ecf0f1'];
                    $tomorrowColor = "#ecf0f1";
                // return $minDate.' / '.$maxDate; 

                    // $promoterGroup = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT' , 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];
                    $promoterGroup = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

                    /* Get data from attendanceDetails then convert them into colored table */
                    // return $item->user_id;
                    $dataDetail = Attendance::
                        select('attendances.*')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->join('users','users.id','attendances.user_id')
                        ->join('roles','roles.id','users.role_id')
                        ->whereIn('roles.role_group',$promoterGroup)
                        ->orderBy('id','asc')
                        ->get()->all();                        
                    foreach ($dataDetail as $key => $value) {
                        $statusAttendance[] = $value->status;
                        if ($key == 0) {
                            if (substr($value->date,-2) > 1) {
                                $joinDate = substr($value->date, -2);
                                $execOnce = false;
                            }
                        }
                    }
                    // return $statusAttendance;
                    $report = '<table><tr>';

                    /* Repeat as much as max day in month */
                    
                    // return $startNumber;
                    $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    if (isset($joinDate)) {
                        $totalDay -= ($joinDate-1);
                    }
                    for ($i=1; $i <= $totalDay ; $i++) { 

                        $index = 0;
                        $bgColor = $warna[0];
                        $textColor = $text[0];
                        foreach ($status as $key => $value) {
                            // $index = $key;
                            if (isset($statusAttendance[$i-1])) {
                                if ($value == $statusAttendance[$i-1]) {
                                    $bgColor = $warna[$key];
                                    $textColor = $text[$key];
                                    $index = $key;
                                    break;
                                }
                            }
                        }

                        $dateNow = Carbon::now()->format('Y-m-d');
                        $dateNow = explode('-', $dateNow);
                        $dateI = date("$year-$month-$i");
                        $dateI = explode('-', $dateI);


                        // if ($dateI[2] > $dateNow[2]) {
                        $indexz = $i;
                            if (isset($joinDate)) {
                                $indexz = $i + $joinDate - 1;
                            }
                        if ($indexz > $dateNow[2]) {
                            $bgColor = $tomorrowColor; 
                            $textColor = 'black';
                        }

                        if (!isset($bgColor)) {
                            $bgColor="#34495e";
                        }

                        if ($index == 1) {
                            if (isset($joinDate)) {
                                $data_id = ($item->id+1);
                            }else{
                                $data_id = $item->id;
                            }
                            $report .= "<td 
                            class='text-center open-attendance-detail-modal cursor-pointer' data-target='#attendance-detail-modal' data-toggle='modal' data-url='util/attendancedetail' data-title='Attendance Detail' data-employee-name='".$item->user_name."' data-employee-nik='".$item->user_nik."' data-id='".$data_id."'
                            style='background-color: $bgColor;color:$textColor;'
                            >";
                        }else{
                            if (isset($joinDate) && $execOnce==false) {
                                for ($jd=1; $jd < $joinDate; $jd++) { 
                                    $report .= "<td 
                                        class='text-center'
                                        style='background-color: $tomorrowColor;color:black;'
                                        >";
                                    $report .= "<div style='width:60px'><b>$jd</b><br>-</div><td>";
                                }
                                $execOnce = true;
                            }
                            $report .= "<td 
                            class='text-center'
                            style='background-color: $bgColor;color:$textColor;'
                            >";
                        }
                        if (isset($joinDate)) {
                            $displayDate = $i+$joinDate-1;
                        }else{
                            $displayDate = $i;
                        }
                        $report .= "<div style='width:60px'><b>$displayDate</b><br>".$status[$index]."</div><td>";
                    }

                    $report .= '</tr></table>';
                    return $report;
                })
            ->addColumn('attendance_detail_excell', function ($item) {
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
                $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));

                    $status = ['Alpha','Masuk',     'Sakit',    'Izin',     'Pending Sakit','Pending Izin', 'Off'];

                    $dataDetail = Attendance::
                        select('attendances.*')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->orderBy('id','asc')
                        ->get()->all();

                        $statusAttendance = '';
                    foreach ($dataDetail as $key => $value) {
                        if ($key==0) {
                            if (substr($value->date,-2) > 1) {
                                $joinDate = substr($value->date, -2);
                                $execOnce = false;
                            }

                            if (isset($joinDate)) {
                                $statusAttendance .= '-';
                                for ($jd=1; $jd < $joinDate; $jd++) { 
                                    $statusAttendance .= ',-';
                                }
                            }else{
                                $statusAttendance .= $value->status;
                            }
                        }else{
                            $statusAttendance .= ','.$value->status;
                        }
                    }

                    return $statusAttendance;
                })
            ->rawColumns(['attendance_details'])
            ->make(true);

    }
    
    public function visitPlanData(Request $request){



        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = VisitPlan::
                    join('stores', 'visit_plans.store_id', '=', 'stores.id')
                    ->join('users', 'visit_plans.user_id', '=', 'users.id')
                    ->join('roles','roles.id','users.role_id')
                    ->select('visit_plans.*', 'users.nik as user_nik', 'users.name as user_name',  'roles.role_group as user_role', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }


            if($request['byNik']){
                $filter = $filter->where('user_id', $request['byNik']);
            }

            if($request['byRole'] != ''){
                $filter = $filter->where('roles.role_group', $request['byRole']);
            }
            
            // if ($userRole == 'RSM') {
            //     $region = RsmRegion::where('user_id', $userId)->get();
            //     foreach ($region as $key => $value) {
            //         $filter = $data->where('region_id', $value->region_id);
            //     }
            // }

            // if ($userRole == 'DM') {
            //     $area = DmArea::where('user_id', $userId)->get();
            //     foreach ($area as $key => $value) {
            //         $filter = $data->where('area_id', $value->area_id);
            //     }
            // }
            
            // if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            //     $store = EmployeeStore::where('user_id', $userId)->get();
            //     foreach ($store as $key => $value) {
            //         $filter = $data->where('store_id', $value->store_id);
            //     }
            // }

            return Datatables::of($filter->all())
            ->editColumn('visit_status', function ($item) {
                if ($item->visit_status == 0) {
                    return "Not Visited";
                }else{
                    return "Visited";
                }
                
            })
            ->make(true);

    }

    public function salesmanData(Request $request){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)->pluck('region_id');
                $data = SalesmanSummarySales::whereIn('region_id', $regionIds)->get();
            }

            elseif ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)->pluck('area_id');
                $data = SalesmanSummarySales::whereIn('area_id', $areaIds)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)->pluck('id');
                $data = SalesmanSummarySales::whereIn('store_id', $storeIds)->get();
            }
            else{
                $data = SalesmanSummarySales::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->editColumn('quantity', function ($item) {
               return number_format($item->quantity);
            })
            ->editColumn('unit_price', function ($item) {
               return number_format($item->unit_price);
            })
            ->editColumn('value', function ($item) {
               return number_format($item->value);
            })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySalesmanSales::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['value_pf'] = number_format($transaction->value_pf);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)->pluck('region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)->pluck('area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)->pluck('id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function salesActivityData(Request $request){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        
            // Fetch data from history

            $historyData = new Collection();

            $history = SalesActivity::get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {


                        $collection = new Collection();

                        /* Get Data and Push them to collection */

                        $collection['id'] =  $data->id;
                        $collection['user_id'] =  $data->user_id;
                        $collection['date'] =  $data->date;
                        $collection['activity'] =  $detail->activity;
                        $collection['type'] =  $detail->type;
                        $collection['action_from'] =  $detail->action_from;
                        $collection['detail_id']=  $detail->detail_id;
                        $collection['week'] =  $detail->week; //
                        $collection['distributor_code'] =  $detail->distributor_code; //
                        $collection['distributor_name'] =  $detail->distributor_name; //
                        $collection['region'] =  $detail->region; //
                        $collection['region_id'] =  $detail->region_id;
                        $collection['channel'] =  $detail->channel; //
                        $collection['sub_channel'] =  $detail->sub_channel; //
                        $collection['area'] =  $detail->area; //
                        $collection['area_id'] =  $detail->area_id;
                        $collection['district'] =  $detail->district; //
                        $collection['district_id'] =  $detail->district_id;
                        $collection['store_name_1'] =  $detail->store_name_1; //
                        $collection['store_name_2'] =  $detail->store_name_2; //
                        $collection['store_id'] =  $detail->store_id; //
                        $collection['storeId'] =  $detail->storeId; //
                        $collection['dedicate'] =  $detail->dedicate;
                        $collection['nik'] =  $detail->nik; //
                        $collection['promoter_name'] =  $detail->promoter_name; //
                        $collection['user_id'] =  $detail->user_id;
                        $collection['date'] =  $detail->date; //
                        $collection['role'] =  $detail->role; //
                        $collection['spv_name'] =  $detail->spv_name; //
                        $collection['dm_name'] =  $detail->dm_name; //
                        $collection['trainer_name'] =  $detail->trainer_name; //
                        $collection['model'] =  $detail->model; //
                        $collection['group'] =  $detail->group; //
                        $collection['category'] =  $detail->category; //
                        $collection['product_name'] =  $detail->product_name; //
                        $collection['unit_price'] =  $detail->unit_price; //
                        $collection['quantity'] =  number_format($detail->quantity); //
                        $collection['value'] =  number_format($detail->value); //
                        $collection['value_pf_mr'] =  number_format($detail->value_pf_mr); //
                        $collection['value_pf_tr'] =  number_format($detail->value_pf_tr); //
                        $collection['value_pf_ppe'] =  number_format($detail->value_pf_ppe); //
                        $collection['new_quantity'] =  number_format($detail->new_quantity);
                        $collection['new_value'] =  number_format($detail->new_value);
                        $collection['new_value_pf_mr'] =  number_format($detail->new_value_pf_mr);
                        $collection['new_value_pf_tr'] =  number_format($detail->new_value_pf_tr);
                        $collection['new_value_pf_ppe'] =  number_format($detail->new_value_pf_ppe);

                        $historyData->push($collection);


                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $filter->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if($request['bySellType']){
                $filter = $filter->where('type', $request['bySellType']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }



            return Datatables::of($filter->all())
            ->make(true);

    }

    public function storeLocationActivityData(Request $request){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        
            // Fetch data from history

            $historyData = new Collection();

            $history = StoreLocationActivity::get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {


                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] =  $data->id;
                        $collection['user_id'] =  $data->user_id;
                        $collection['date'] =  $data->date;

                        $collection['storeId'] = $detail->storeId;
                        $collection['store_id'] = $detail->store_id;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['longitude'] = $detail->longitude;
                        $collection['latitude'] = $detail->latitude;
                        $collection['address'] = $detail->address;

                        $collection['subchannel_id'] = $detail->subchannel_id;
                        $collection['subchannel'] = $detail->subchannel;
                        $collection['channel_id'] = $detail->channel_id;
                        $collection['channel'] = $detail->channel;
                        $collection['globalchannel_id'] = $detail->globalchannel_id;
                        $collection['globalchannel'] = $detail->globalchannel;


                        $collection['no_telp_toko'] = $detail->no_telp_toko;
                        $collection['no_telp_pemilik_toko'] = $detail->no_telp_pemilik_toko;
                        $collection['kepemilikan_toko'] = $detail->kepemilikan_toko;
                        
                        $collection['district_id'] = $detail->district_id;
                        $collection['district'] = $detail->district;
                        $collection['area_id'] = $detail->area_id;
                        $collection['area'] = $detail->area;
                        $collection['region_id'] = $detail->region_id;
                        $collection['region'] = $detail->region;

                        $collection['user_id'] = $detail->user_id;
                        $collection['user'] = $detail->user;
                        $collection['nik'] = $detail->nik;
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['grading_id'] = $detail->grading_id;
                        $collection['grading'] = $detail->grading;

                        $collection['lokasi_toko'] = $detail->lokasi_toko;
                        $collection['tipe_transaksi_2'] = $detail->tipe_transaksi_2;
                        $collection['tipe_transaksi'] = $detail->tipe_transaksi;
                        $collection['kondisi_toko'] = $detail->kondisi_toko;
                        $collection['new_longitude'] = $detail->new_longitude;
                        $collection['new_latitude'] = $detail->new_latitude;
                        $collection['new_address'] = $detail->new_address;

                        $historyData->push($collection);


                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $filter->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

            return Datatables::of($filter->all())
            ->make(true);

    }
    
}
