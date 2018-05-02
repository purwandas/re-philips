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
use App\SellInDetail;
use App\Reports\SummarySellIn;
use App\Reports\SalesmanSummarySales;
use App\SellOutDetail;
use App\Reports\SummarySellOut;
use App\SOHDetail;
use App\Reports\SummarySoh;
use App\SellIn;
use App\SellOut;
use App\Soh;
use App\Store;
use App\SpvDemo;
use App\TrainerArea;
use App\DmArea;
use App\Product;
use App\Price;
use App\User;
use App\SalesmanDedicate;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\SalesmanProductFocuses;
use App\Traits\ActualTrait;

class AchievementController extends Controller
{
    use UploadTrait;
    use StringTrait;
    use ActualTrait;
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

        $userRole = Auth::user()->role->role_group;
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

        $userRole = Auth::user()->role->role_group;
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

    public function summaryGenerate($param){

        /* 
            PARAM
            100 = MONTH
            0 = TODAY
            1 - 31 = DAY OF MONTH
        */

            // $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
            //             ->whereMonth('sohs.date', Carbon::now()->format('m'))
            //             ->whereDay('sohs.date', 4)
            //             ->get();

            // return $soh;

            // return Carbon::now()->format('d');

        $this->generateSellInMonth($param);
        $this->generateSellOutMonth($param);
        $this->generateSOHMonth($param);

    }

    public function generateSOHMonth($param){

        // SOH
        if($param == 100){
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))
                        ->whereDay('sohs.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))
                        ->whereDay('sohs.date', $param)
                        ->get();
        }

        foreach ($soh as $data) {

            $sohDetails = SohDetail::where('soh_id', $data['id'])->get();

            foreach ($sohDetails as $detail) {

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell In')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
                    }
                }

                /* DM */
                $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $dm = User::whereIn('id', $dmIds)->get();

                $dm_name = '';
                foreach ($dm as $dmDetail) {
                    $dm_name .= $dmDetail->name;

                    if ($dmDetail->id != $dm->last()->id) {
                        $dm_name .= ', ';
                    }
                }

                /* Trainer */
                $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $tr = User::whereIn('id', $trIds)->get();

                $trainer_name = '';
                foreach ($tr as $trDetail) {
                    $trainer_name .= $trDetail->name;

                    if ($trDetail->id != $tr->last()->id) {
                        $trainer_name .= ', ';
                    }
                }

                if (isset($store->subChannel->channel->name)){
                    $channel = $store->subChannel->channel->name;
                }else{
                    $channel = '';
                }

                if (isset($store->subChannel->name)){
                    $subChannel = $store->subChannel->name;
                }else{
                    $subChannel = '';
                }

                $summary = SummarySoh::where('soh_detail_id', $detail['id'])->first();

                if($summary){ // UPDATE

                    $value = $detail['quantity'] * $realPrice;

                    $summary->update([
                        // 'quantity' => $summary->quantity + $data['quantity'],
                        'unit_price' => $realPrice,
                        'quantity' => $detail['quantity'],
                        'value' => $value,
                    ]);

                }else{

                    SummarySOH::create([
                        'soh_detail_id' => $detail['id'],
                        'region_id' => $store->district->area->region->id,
                        'area_id' => $store->district->area->id,
                        'district_id' => $store->district->id,
                        'storeId' => $data['store_id'],
                        'user_id' => $data['user_id'],
                        'week' => $data['week'],
                        'distributor_code' => $distributor_code,
                        'distributor_name' => $distributor_name,
                        'region' => $store->district->area->region->name,
                        'channel' => $store->subChannel->channel->name,
                        'sub_channel' => $store->subChannel->name,
                        'area' => $store->district->area->name,
                        'district' => $store->district->name,
                        'store_name_1' => $store->store_name_1,
                        'store_name_2' => $customerCode,
                        'store_id' => $store->store_id,
                        'nik' => $data->user->nik,
                        'promoter_name' => $data->user->name,
                        'date' => $data['date'],
                        'model' => $product->model . '/' . $product->variants,
                        'group' => $product->category->group->groupProduct->name,
                        'category' => $product->category->name,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $detail['quantity'],
                        'unit_price' => $realPrice,
                        'value' => $realPrice * $detail['quantity'],
                        'value_pf_mr' => 0,
                        'value_pf_tr' => 0,
                        'value_pf_ppe' => 0,
                        'role' => $data->user->role->role,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                    ]);

                }

            }

        }

    }


    public function generateSellOutMonth($param){

        // SELL OUT
        // $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
        //                 ->whereMonth('sell_outs.date', Carbon::now()->format('m'))->get();

        if($param == 100){
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))
                        ->whereDay('sell_outs.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))
                        ->whereDay('sell_outs.date', $param)
                        ->get();
        }

        foreach ($sellOut as $data) {

            $sellOutDetails = SellOutDetail::where('sellout_id', $data['id'])->get();

            foreach ($sellOutDetails as $detail) {

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell Out')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell Out')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell Out')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
                    }
                }

                /* DM */
                $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $dm = User::whereIn('id', $dmIds)->get();

                $dm_name = '';
                foreach ($dm as $dmDetail) {
                    $dm_name .= $dmDetail->name;

                    if ($dmDetail->id != $dm->last()->id) {
                        $dm_name .= ', ';
                    }
                }

                /* Trainer */
                $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $tr = User::whereIn('id', $trIds)->get();

                $trainer_name = '';
                foreach ($tr as $trDetail) {
                    $trainer_name .= $trDetail->name;

                    if ($trDetail->id != $tr->last()->id) {
                        $trainer_name .= ', ';
                    }
                }

                if (isset($store->subChannel->channel->name)){
                    $channel = $store->subChannel->channel->name;
                }else{
                    $channel = '';
                }

                if (isset($store->subChannel->name)){
                    $subChannel = $store->subChannel->name;
                }else{
                    $subChannel = '';
                }

                $summary = SummarySellOut::where('sellout_detail_id', $detail['id'])->first();

                if($summary){ // UPDATE

                    $value = $detail['quantity'] * $realPrice;

                    ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                    ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                    ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                    $summary->update([
                        'unit_price' => $realPrice,
                        'quantity' => $detail['quantity'],
                        'value' => $value,
                        'value_pf_mr' => $value_pf_mr,
                        'value_pf_tr' => $value_pf_tr,
                        'value_pf_ppe' => $value_pf_ppe,
                    ]);

                }else{ // CREATE

                    $summary = SummarySellOut::create([
                        'sellout_detail_id' => $detail['id'],
                        'region_id' => $store->district->area->region->id,
                        'area_id' => $store->district->area->id,
                        'district_id' => $store->district->id,
                        'storeId' => $data['store_id'],
                        'user_id' => $data['user_id'],
                        'week' => $data['week'],
                        'distributor_code' => $distributor_code,
                        'distributor_name' => $distributor_name,
                        'region' => $store->district->area->region->name,
                        'channel' => $channel,
                        'sub_channel' => $subChannel,
                        'area' => $store->district->area->name,
                        'district' => $store->district->name,
                        'store_name_1' => $store->store_name_1,
                        'store_name_2' => $customerCode,
                        'store_id' => $store->store_id,
                        'dedicate' => $store->dedicate,
                        'nik' => $data->user->nik,
                        'promoter_name' => $data->user->name,
                        'date' => $data['date'],
                        'model' => $product->model . '/' . $product->variants,
                        'group' => $product->category->group->groupProduct->name,
                        'category' => $product->category->name,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $detail['quantity'],
                        'irisan' => $detail['irisan'],
                        'unit_price' => $realPrice,
                        'value' => $realPrice * $detail['quantity'],
                        'value_pf_mr' => $value_pf_mr,
                        'value_pf_tr' => $value_pf_tr,
                        'value_pf_ppe' => $value_pf_ppe,
                        'role' => $data->user->role->role,
                        'role_id' => $data->user->role->id,
                        'role_group' => $data->user->role->role_group,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                    ]);

                }                

            }


            /* Reset Actual */
            $this->resetActual($data['user_id'], $data['store_id'], 'Sell Out');

        }

    }

    public function generateSellInMonth($param){

        // SELL THRU
        // $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
        //                 ->whereMonth('sell_ins.date', Carbon::now()->format('m'))->get();

        if($param == 100){
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))
                        ->whereDay('sell_ins.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))
                        ->whereDay('sell_ins.date', $param)
                        ->get();
        }

        foreach ($sellIn as $data) {
            
            $sellInDetails = SellInDetail::where('sellin_id', $data['id'])->get();

            foreach ($sellInDetails as $detail) {

                // return $detail;

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell In')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
                    }
                }

                /* DM */
                $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $dm = User::whereIn('id', $dmIds)->get();

                $dm_name = '';
                foreach ($dm as $dmDetail) {
                    $dm_name .= $dmDetail->name;

                    if ($dmDetail->id != $dm->last()->id) {
                        $dm_name .= ', ';
                    }
                }

                /* Trainer */
                $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                $tr = User::whereIn('id', $trIds)->get();

                $trainer_name = '';
                foreach ($tr as $trDetail) {
                    $trainer_name .= $trDetail->name;

                    if ($trDetail->id != $tr->last()->id) {
                        $trainer_name .= ', ';
                    }
                }

                // PROCESSING DATA
                if($data->user->role->role_group != 'Salesman Explorer'){ // PROMOTER

                    $summary = SummarySellIn::where('sellin_detail_id', $detail['id'])->first();

                    if($summary){ // UPDATE

                        $value = $detail['quantity'] * $realPrice;

                        ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                        ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                        ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                        $summary->update([
                            'unit_price' => $realPrice,
                            'quantity' => $detail['quantity'],
                            'value' => $value,
                            'value_pf_mr' => $value_pf_mr,
                            'value_pf_tr' => $value_pf_tr,
                            'value_pf_ppe' => $value_pf_ppe,
                        ]);

                    }else{ // CREATE

                        if (isset($store->subChannel->channel->name)){
                            $channel = $store->subChannel->channel->name;
                        }else{
                            $channel = '';
                        }

                        if (isset($store->subChannel->name)){
                            $subChannel = $store->subChannel->name;
                        }else{
                            $subChannel = '';
                        }

                        $summary = SummarySellIn::create([
                            'sellin_detail_id' => $detail['id'],
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $data['store_id'],
                            'user_id' => $data['user_id'],
                            'week' => $data['week'],
                            'distributor_code' => $distributor_code,
                            'distributor_name' => $distributor_name,
                            'region' => $store->district->area->region->name,
                            'channel' => $channel,
                            'sub_channel' => $subChannel,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $customerCode,
                            'store_id' => $store->store_id,
                            'dedicate' => $store->dedicate,
                            'nik' => $data->user->nik,
                            'promoter_name' => $data->user->name,
                            'date' => $data['date'],
                            'model' => $product->model . '/' . $product->variants,
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $detail['quantity'],
                            'irisan' => $detail['irisan'],
                            'unit_price' => $realPrice,
                            'value' => $realPrice * $detail['quantity'],
                            'value_pf_mr' => $value_pf_mr,
                            'value_pf_tr' => $value_pf_tr,
                            'value_pf_ppe' => $value_pf_ppe,
                            'role' => $data->user->role->role,
                            'role_id' => $data->user->role->id,
                            'role_group' => $data->user->role->role_group,
                            'spv_name' => $spvName,
                            'dm_name' => $dm_name,
                            'trainer_name' => $trainer_name,
                        ]);

                    }

                }else{ // SEE

                    $summary = SalesmanSummarySales::where('sellin_detail_id', $detail['id'])->first();

                    if($summary){ // UPDATE

                        $value = $detail['quantity'] * $realPrice;

                        ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                        $summary->update([
                            'unit_price' => $realPrice,
                            'quantity' => $detail['quantity'],
                            'value' => $value,
                            'value_pf' => $value_pf
                        ]);

                    }else{ // CREATE

                        $value_pf = 0;

                        $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                        if($productFocus){
                            $value_pf = $realPrice * $detail->quantity;
                        }

                        if (isset($store->subChannel->channel->name)){
                            $channel = $store->subChannel->channel->name;
                        }else{
                            $channel = '';
                        }

                        if (isset($store->subChannel->name)){
                            $subChannel = $store->subChannel->name;
                        }else{
                            $subChannel = '';
                        }


                        $summary = SalesmanSummarySales::create([
                            'sellin_detail_id' => $detail['id'],
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $data['store_id'],
                            'user_id' => $data['user_id'],
                            'week' => $data['week'],
                            'distributor_code' => $distributor_code,
                            'distributor_name' => $distributor_name,
                            'region' => $store->district->area->region->name,
                            'channel' => $channel,
                            'sub_channel' => $subChannel,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $customerCode,
                            'store_id' => $store->store_id,
                            'dedicate' => $store->dedicate,
                            'nik' => $data->user->nik,
                            'promoter_name' => $data->user->name,
                            'date' => $data['date'],
                            'model' => $product->model . '/' . $product->variants,
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'product_name' => $product->name,
                            'quantity' => $detail['quantity'],
                            'unit_price' => $realPrice,
                            'value' => $realPrice * $detail['quantity'],
                            'value_pf' => $value_pf,
                            'role' => $data->user->role->role,
                            'role_id' => $data->user->role->id,
                            'role_group' => $data->user->role->role_group,
                        ]);

                    }

                }
                
            }

            if($data->user->role->role_group != 'Salesman Explorer'){ // PROMOTER
                /* Reset Actual */
                $this->resetActual($data['user_id'], $data['store_id'], 'Sell In');
            }else{ // SEE
                /* Reset Actual */
                $this->resetActualSalesman($data['user_id']);
            }

        }

        // $sellInDetails = SellInDetail::whereHas('sellIn', function($query){
        //                     return $query->whereYear('sell_ins.date', Carbon::now()->format('Y'))
        //                                  ->whereMonth('sell_ins.date', Carbon::now()->format('m'));
        //                  })->get();

        //                     // return $sellInDetails;

        // foreach ($sellInDetails as $data) {

        //     return $data;
            
        //     if($data->sellIn->user->role->role_group != 'Salesman Explorer'){
        //         return 'No SEE';

        //         $summary = SummarySellIn::where('sellin_detail_id', $data['id'])->first();

        //         if($summary){
        //             // $
        //         }

        //     }else{
        //         return 'SEE';
        //     }

        // }

    }

}

