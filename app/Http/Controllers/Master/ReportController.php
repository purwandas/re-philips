<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
