<?php

namespace App\Http\Controllers\Master;

use App\Distributor;
use App\DmArea;
use App\Filters\SellinFilters;
use App\ProductFocuses;
use App\Region;
use App\Reports\HistorySellIn;
use App\StoreDistributor;
use App\TrainerArea;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use League\Geotools\CLI\Command\Convert\DM;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
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
use App\Soh;
use App\SohDetail;
use App\Sos;
use App\SosDetail;
use App\Filters\ReportFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;

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
        return view('report.retConsument-report');
    }

    public function retDistributorIndex()
    {
        return view('report.retDistributor-report');
    }

    public function tbatIndex()
    {
        return view('report.tbat-report');
    }

    public function sohIndex()
    {
        return view('report.soh-report');
    }

    public function sosIndex()
    {
        return view('report.sos-report');
    }

    public function sellInData(Request $request, SellinFilters $filters){

        // Check data live atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            $data = SellInDetail::filter($filters);

            $data = $data->where('sell_in_details.deleted_at', null)
                ->join('sell_ins', 'sell_in_details.sellin_id', '=', 'sell_ins.id')
                ->join('stores', 'stores.id', '=', 'sell_ins.store_id')
                ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                ->join('districts', 'districts.id', '=', 'stores.district_id')
                ->join('areas', 'areas.id', '=', 'districts.area_id')
                ->join('regions', 'regions.id', '=', 'areas.region_id')
                ->join('users', 'sell_ins.user_id', '=', 'users.id')
                ->join('products', 'sell_in_details.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('groups', 'categories.group_id', '=', 'groups.id')
                ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->join('users as supervisor', 'stores.user_id', '=', 'supervisor.id')
                ->join('prices', function ($join) {
                    $join->on('prices.product_id', '=', 'products.id');
                    $join->on('prices.globalchannel_id', '=', 'global_channels.id');
                })
                ->select('sell_in_details.*', 'regions.name as region', 'areas.id as area_id', 'areas.name as area',
                    'districts.name as district', 'stores.store_name_1', 'stores.store_name_2', 'stores.id as storeId',
                    'stores.store_id', 'channels.name as channel', 'sub_channels.name as sub_channel', 'users.role as role',
                    'users.name as promoter_name', 'users.nik', 'sell_ins.date as date', 'group_products.name as group',
                    'categories.name as category', 'prices.price as unit_price', 'supervisor.name as spv_name', 'sell_ins.week',
                    'products.name as product_name', 'products.model as product_model', 'products.variants as product_variants')->get();

            /* Fetch some information about models */
            foreach ($data as $detail) {

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $detail->storeId)->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $detail['distributor_code'] = '';
                $detail['distributor_name'] = '';
                foreach ($dist as $distDetail) {
                    $detail['distributor_code'] .= $distDetail->code;
                    $detail['distributor_name'] .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $detail['distributor_code'] .= ', ';
                        $detail['distributor_name'] .= ', ';
                    }
                }

                /* DM */
                $dmIds = DmArea::where('area_id', $detail->area_id)->pluck('user_id');
                $dm = User::whereIn('id', $dmIds)->get();

                $detail['dm_name'] = '';
                foreach ($dm as $dmDetail) {
                    $detail['dm_name'] .= $dmDetail->name;

                    if ($dmDetail->id != $dm->last()->id) {
                        $detail['dm_name'] .= ', ';
                    }
                }

                /* Trainer */
                $trIds = TrainerArea::where('area_id', $detail->area_id)->pluck('user_id');
                $tr = User::whereIn('id', $trIds)->get();

                $detail['trainer_name'] = '';
                foreach ($tr as $trDetail) {
                    $detail['trainer_name'] .= $trDetail->name;

                    if ($trDetail->id != $tr->last()->id) {
                        $detail['trainer_name'] .= ', ';
                    }
                }

                /* Variant */
                $detail['model'] = $detail['product_model'] . '/' . $detail['product_variants'];

                /* Value */
                $detail['value'] = $detail['quantity'] * $detail['unit_price'];

                /* Value - Product Focus */
                $detail['value_pf_mr'] = '';
                $detail['value_pf_tr'] = '';
                $detail['value_pf_ppe'] = '';

                $productFocus = ProductFocuses::where('product_id', $detail->product_id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $detail['value_pf_mr'] = $detail->value;
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $detail['value_pf_tr'] = $detail->value;
                    } else if ($productFocusDetail->type == 'PPE') {
                        $detail['value_pf_ppe'] = $detail->value;
                    }
                }

            }

            return Datatables::of($data)
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

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
                        $collection['store_id'] = $detail->storeId;
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

                        $historyData->push($collection);

                    }

                }

            }

//            $historyData->where('nik', 787);
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
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }



//            return $historyData;

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function sellOutData(ReportFilters $filters){

        $data = SellOutDetail::filter($filters);

        $data = $data->where('sell_out_details.deleted_at', null)
            ->join('sell_outs', 'sell_out_details.sellout_id', '=', 'sell_outs.id')
            ->join('stores', 'stores.id', '=', 'sell_outs.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'sell_outs.user_id')
            ->join('products', 'sell_out_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('sell_out_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'sell_outs.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        return Datatables::of($data)
            ->make(true);

    }

    public function retConsumentData(ReportFilters $filters){

        $data = RetConsumentDetail::filter($filters);

        $data = $data->where('ret_consument_details.deleted_at', null)
            ->join('ret_consuments', 'ret_consument_details.retconsument_id', '=', 'ret_consuments.id')
            ->join('stores', 'stores.id', '=', 'ret_consuments.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'ret_consuments.user_id')
            ->join('products', 'ret_consument_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('ret_consument_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id', 'users.name as promoter_name', 'users.nik', 'ret_consuments.date as date', 'products.model', 'groups.name as group', 'categories.name as category', 'products.name as product_name')->get();

        return Datatables::of($data)
            ->make(true);

    }

    public function retDistributorData(ReportFilters $filters){

        $data = RetDistributorDetail::filter($filters);

        $data = $data->where('ret_distributor_details.deleted_at', null)
            ->join('ret_distributors', 'ret_distributor_details.retdistributor_id', '=', 'ret_distributors.id')
            ->join('stores', 'stores.id', '=', 'ret_distributors.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'ret_distributors.user_id')
            ->join('products', 'ret_distributor_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('ret_distributor_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'ret_distributors.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        return Datatables::of($data)
            ->make(true);

    }

    public function tbatData(ReportFilters $filters){

        $data = TbatDetail::filter($filters);

        $data = $data->where('tbat_details.deleted_at', null)
            ->join('tbats', 'tbat_details.tbat_id', '=', 'tbats.id')
            ->join('stores', 'stores.id', '=', 'tbats.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'tbats.user_id')
            ->join('products', 'tbat_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('tbat_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'tbats.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        return Datatables::of($data)
            ->make(true);

    }

    public function sohData(ReportFilters $filters){

        $data = SohDetail::filter($filters);

        $data = $data->where('soh_details.deleted_at', null)
            ->join('sohs', 'sohs.id', '=', 'soh_details.soh_id')
            ->join('stores', 'stores.id', '=', 'sohs.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'sohs.user_id')
            ->join('products', 'products.id', '=', 'soh_details.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('soh_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'sohs.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        
        return Datatables::of($data)
            ->make(true);
    }

    public function sosData(ReportFilters $filters){

        $data = SosDetail::filter($filters);

        $data = $data->where('sos_details.deleted_at', null)
            ->join('sos', 'sos.id', '=', 'sos_details.sos_id')
            ->join('stores', 'stores.id', '=', 'sos.store_id')
            ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
            ->join('areas', 'areas.id', '=', 'area_apps.area_id')
            ->join('users', 'users.id', '=', 'sos.user_id')
            ->join('products', 'products.id', '=', 'sos_details.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('groups', 'categories.group_id', '=', 'groups.id')
            ->select('sos_details.*', 'areas.name as area', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id',
                'users.name as promoter_name', 'users.nik', 'sos.date as date', 'products.model', 'groups.name as group', 'categories.name as category',
                'products.name as product_name')->get();

        
        return Datatables::of($data)
            ->make(true);
    }
}
