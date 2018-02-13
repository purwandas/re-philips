<?php

namespace App\Http\Controllers\Master;

use App\Reports\HistorySalesmanTargetActual;
use App\Reports\SalesmanSummaryTargetActual;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Auth;
use App\Filters\AchievementFilters;
use App\Reports\SummaryTargetActual;
use App\Reports\HistoryTargetActual;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class AchievementController extends Controller
{
    use UploadTrait;
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function achievementIndex()
    {
        return view('master.achievement');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function achievementData(Request $request, AchievementFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryTargetActual::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryTargetActual::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryTargetActual::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryTargetActual::all();
            }          

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter)
                ->editColumn('sell_type',function ($item) {
                    if ($item->sell_type == 'Sell In') {
                        $item->sell_type = 'Sell Thru';
                    }
                    return $item->sell_type;
                })
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryTargetActual::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    $collection = new Collection();

                    /* Get Data and Push them to collection */
                    $collection['id'] = $data->id;
                    $collection['region_id'] = $detail->region_id;
                    $collection['area_id'] = $detail->area_id;
                    $collection['district_id'] = $detail->district_id;
                    $collection['storeId'] = $detail->storeId;
                    $collection['user_id'] = $detail->user_id;
                    $collection['region'] = $detail->region;
                    $collection['area'] = $detail->area;
                    $collection['district'] = $detail->district;
                    $collection['nik'] = $detail->nik;
                    $collection['promoter_name'] = $detail->promoter_name;
                    $collection['account_type'] = $detail->account_type;
                    $collection['title_of_promoter'] = $detail->title_of_promoter;
                    $collection['classification_store'] = $detail->classification_store;
                    $collection['account'] = $detail->account;
                    $collection['store_id'] = $detail->store_id;
                    $collection['store_name_1'] = $detail->store_name_1;
                    $collection['store_name_2'] = $detail->store_name_2;
                    $collection['spv_name'] = $detail->spv_name;
                    $collection['trainer'] = $detail->trainer;
                    $collection['sell_type'] = $detail->sell_type;

                    $collection['target_dapc'] = $detail->target_dapc;
                    $collection['actual_dapc'] = $detail->actual_dapc;
                    $collection['target_da'] = $detail->target_da;
                    $collection['actual_da'] = $detail->actual_da;
                    $collection['target_pc'] = $detail->target_pc;
                    $collection['actual_pc'] = $detail->actual_dapc;
                    $collection['target_mcc'] = $detail->actual_dapc;
                    $collection['actual_mcc'] = $detail->actual_dapc;
                    $collection['target_pf_da'] = $detail->actual_dapc;
                    $collection['actual_pf_da'] = $detail->actual_dapc;
                    $collection['target_pf_pc'] = $detail->actual_dapc;
                    $collection['actual_pf_pc'] = $detail->actual_dapc;
                    $collection['target_pf_mcc'] = $detail->actual_dapc;
                    $collection['actual_pf_mcc'] = $detail->actual_dapc;

                    $collection['target_da_w1'] = $detail->actual_dapc;
                    $collection['actual_da_w1'] = $detail->actual_dapc;
                    $collection['target_da_w2'] = $detail->actual_dapc;
                    $collection['actual_da_w2'] = $detail->actual_dapc;
                    $collection['target_da_w3'] = $detail->actual_dapc;
                    $collection['actual_da_w3'] = $detail->actual_dapc;
                    $collection['target_da_w4'] = $detail->actual_dapc;
                    $collection['actual_da_w4'] = $detail->actual_dapc;
                    $collection['target_da_w5'] = $detail->actual_dapc;
                    $collection['actual_da_w5'] = $detail->actual_dapc;
                    $collection['target_pc_w1'] = $detail->actual_dapc;
                    $collection['actual_pc_w1'] = $detail->actual_dapc;
                    $collection['target_pc_w2'] = $detail->actual_dapc;
                    $collection['actual_pc_w2'] = $detail->actual_dapc;
                    $collection['target_pc_w3'] = $detail->actual_dapc;
                    $collection['actual_pc_w3'] = $detail->actual_dapc;
                    $collection['target_pc_w4'] = $detail->actual_dapc;
                    $collection['actual_pc_w4'] = $detail->actual_dapc;
                    $collection['target_pc_w5'] = $detail->actual_dapc;
                    $collection['actual_pc_w5'] = $detail->actual_dapc;
                    $collection['target_mcc_w1'] = $detail->actual_dapc;
                    $collection['actual_mcc_w1'] = $detail->actual_dapc;
                    $collection['target_mcc_w2'] = $detail->actual_dapc;
                    $collection['actual_mcc_w2'] = $detail->actual_dapc;
                    $collection['target_mcc_w3'] = $detail->actual_dapc;
                    $collection['actual_mcc_w3'] = $detail->actual_dapc;
                    $collection['target_mcc_w4'] = $detail->actual_dapc;
                    $collection['actual_mcc_w4'] = $detail->actual_dapc;
                    $collection['target_mcc_w5'] = $detail->actual_dapc;
                    $collection['actual_mcc_w5'] = $detail->actual_dapc;


                    $historyData->push($collection);

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
                ->editColumn('sell_type',function ($item) {
                    if ($item->sell_type == 'Sell In') {
                        $item->sell_type = 'Sell Thru';
                    }
                    return $item->sell_type;
                })
            ->make(true);

        }

    }

    public function salesmanAchievementIndex()
    {
        return view('master.salesmanachievement');
    }

    public function salesmanAchievementData(Request $request, AchievementFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            $data = SalesmanSummaryTargetActual::all();

            $filter = $data;

            /* If filter */
            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter)
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySalesmanTargetActual::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    $collection = new Collection();

                    /* Get Data and Push them to collection */
                    $collection['id'] = $data->id;
                    $collection['user_id'] = $detail->user_id;
                    $collection['nik'] = $detail->nik;
                    $collection['salesman_name'] = $detail->salesman_name;
                    $collection['area'] = $detail->area;
                    $collection['target_call'] = $detail->target_call;
                    $collection['actual_call'] = $detail->actual_call;
                    $collection['target_active_outlet'] = $detail->target_active_outlet;
                    $collection['actual_active_outlet'] = $detail->actual_active_outlet;
                    $collection['target_effective_call'] = $detail->target_effective_call;
                    $collection['actual_effective_call'] = $detail->actual_effective_call;
                    $collection['target_sales'] = $detail->target_sales;
                    $collection['actual_sales'] = $detail->actual_sales;
                    $collection['target_sales_pf'] = $detail->target_sales_pf;
                    $collection['actual_sales_pf'] = $detail->actual_sales_pf;

                    $historyData->push($collection);

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

}

