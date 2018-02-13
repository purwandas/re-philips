<?php

namespace App\Http\Controllers\Master;

use App\Distributor;
use App\StoreDistributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use App\Traits\StoreTrait;
use Auth;
use DB;
use App\Store;
use App\EmployeeStore;
use App\User;
use App\RsmRegion;
use App\DmArea;

class StoreController extends Controller
{
    use StringTrait;
    use StoreTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.store');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Store::where('stores.deleted_at', null)
        			// ->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
           //          ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
           //          ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
//                    ->join('users', 'stores.user_id', '=', 'users.id')
//                    ->where(function($query) {
//                        return $query->orWhere('stores.user_id', null);
//                    })
                    ->select('stores.*', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name'
                        // ,'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name'
                        )
                    ->get();

        foreach ($data as $detail){
            $distIds = StoreDistributor::where('store_id', $detail->id)->pluck('distributor_id');
            $dist = Distributor::whereIn('id', $distIds)->get();

            $detail['distributor'] = '';
            foreach ($dist as $distDetail){
                $detail['distributor'] .= '(' . $distDetail->code . ') ' . $distDetail->name;

                if($distDetail->id != $dist->last()->id){
                    $detail['distributor'] .= ', ';
                }
            }
        }

//        return response()->json($data);

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(StoreFilters $filters){

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;       

        $data = Store::filter($filters)->groupBy('store_id')->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.store_id');
            $data = $data->whereIn('store_id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.store_id');
            $data = $data->whereIn('store_id', $area);
        }
            
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('user_id', $userId)
                        ->pluck('stores.store_id');
            $data = $data->whereIn('store_id', $store);
        }

        return $data;
    }
    public function getStoresDataWithFilters(StoreFilters $filters){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;       

        $data = Store::filter($filters)->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $area);
        }
            
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('user_id', $userId)
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $store);
        }

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->editColumn('spv_name', function ($item) {
                    if($item->user_id != null){
                        $data = User::where('id', $item->user_id)
                                    ->select('users.name as spv_name')->first();
                        return $data->spv_name;
                    }
                    return "";

                })
                ->addColumn('subchannel_name', function ($item) {
                    if (isset($item->subchannel_id)) {
                        return $item->subChannel->name;
                    }
                    return '';
                })
                ->addColumn('channel_name', function ($item) {
                    if (isset($item->subchannel_id)) {
                        return $item->subChannel->channel->name;
                    }
                    return '';
                })
                ->addColumn('globalchannel_name', function ($item) {
                    if (isset($item->subchannel_id)) {
                        return $item->subChannel->channel->globalChannel->name;
                    }
                    return '';
                })
                ->addColumn('classification_id', function ($item) {
                    if (isset($item->classification->classification)) {
                        return $item->classification->classification;
                    }
                    return '';
                })
                ->addColumn('action', function ($item) {

                    return
                    "<a href='".url('store/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.store-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $error = '';
//        return response()->json($request->all());

            $this->validate($request, [
                'store_name_1' => 'required|string|max:255',
                'store_name_2' => 'max:255',
                'district_id' => 'required',
            ]);

        // return response()->json($request->all());

        $store = Store::create($request->all());

        /* Store Distributor */
        if($request['distributor_ids']){
            foreach ($request['distributor_ids'] as $distributorId) {
                StoreDistributor::create([
                    'store_id' => $store->id,
                    'distributor_id' => $distributorId,
                ]);
            }
        }

        return response()->json(['url' => url('/store')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Store::where('id', $id)->first();

        return view('master.form.store-form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'store_name_1' => 'required|string|max:255',
            'store_name_2' => 'max:255',
            'district_id' => 'required'
        ]);

        $store = Store::find($id);

        /* Delete if any relation exist in store distributor */
        $storeDist = StoreDistributor::where('store_id', $store->id);
        if($storeDist->count() > 0){
            $storeDist->delete();
        }

        $store->update($request->all());

        /* Store Distributor */
        if($request['distributor_ids']){
            foreach ($request['distributor_ids'] as $distributorId) {
                StoreDistributor::create([
                    'store_id' => $store->id,
                    'distributor_id' => $distributorId,
                ]);
            }
        }

        return response()->json(
            ['url' => url('/store'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Delete if any relation exist in store distributor */
        $storeDist = StoreDistributor::where('store_id', $id);
        if($storeDist->count() > 0){
            $storeDist->delete();
        }

        /* Deleting related to user */

        // Employee Store
        $empStore = EmployeeStore::where('store_id', $id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        $store = Store::destroy($id);

        return response()->json($id);
    }

    public function getStoreId(){
            return $this->traitGetStoreId();
    }
}

