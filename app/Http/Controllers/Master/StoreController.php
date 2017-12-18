<?php

namespace App\Http\Controllers\Master;

use App\Distributor;
use App\StoreDistributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use DB;
use App\Store;
use App\EmployeeStore;
use App\User;

class StoreController extends Controller
{    
    use StringTrait;
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
        			->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
//                    ->join('users', 'stores.user_id', '=', 'users.id')
//                    ->where(function($query) {
//                        return $query->orWhere('stores.user_id', null);
//                    })
                    ->select('stores.*', 'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name')->get();

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

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(StoreFilters $filters){        
        $data = Store::filter($filters)->get();        

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->editColumn('spv_name', function ($item) {

                    if($item->user_id == null){
                        return "";
                    }

                    return User::find($item->user_id)->first()->name;

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
        if ($request->store_status == 'old') {

            $this->validate($request, [
                'old_store_id' => 'required',
                'dedicate' => 'required',
            ]);

            // return $request->old_store_id;
            $storeData = Store::where('stores.id', $request->old_store_id)->get();
            $dedicate = [];
            $store_id = '';
            $store_name_1 = '';
            $store_name_2 = '';
            $latitude = '';
            $longitude = '';
            $address = '';
            $classification = '';
            $subchannel_id = '';
            $district_id = '';

            foreach ($storeData as $key => $value) {
                $dedicate[] = $value->dedicate;
                $store_id = $value->store_id;
                $store_name_1 = $value->store_name_1;
                $store_name_2 = $value->store_name_2;
                $latitude = $value->latitude;
                $longitude = $value->longitude;
                $address = $value->address;
                $classification = $value->classification;
                $subchannel_id = $value->subchannel_id;
                $district_id = $value->district_id;
            }
            
            // return response()->json($storeData);
            foreach ($dedicate as $key => $value) {
                if ($value == $request->dedicate) { //redudant
                    $error = "Dedicate: Duplicate Entry for ".$value;
                }
                if ($value == 'DA' && $request->dedicate == 'PC') {
                    $error = "Dedicate: PC cannot be Added, You can change your DA Store (".$request->store_id.'-'.$value.") to be HYBRID";
                }
                if ($value == 'PC' && $request->dedicate == 'DA') {
                    $error = "Dedicate: DA cannot be Added, You can change your PC Store (".$request->store_id.'-'.$value.") to be HYBRID";
                }
                if ($value == 'HYBRID' && $request->dedicate == 'PC') {
                    $error = "Dedicate: PC cannot be Added, You already have HYBRID. You can change your HYBRID Store (".$request->store_id.'-'.$value.") to be PC";
                }
                if ($value == 'HYBRID' && $request->dedicate == 'DA') {
                    $error = "Dedicate: DA cannot be Added, You already have HYBRID. You can change your HYBRID Store (".$request->store_id.'-'.$value.") to be DA";
                }
                if ($value == 'DA' && $request->dedicate == 'HYBRID') {
                    $error = "Dedicate: HYBRID cannot be Added, You already have DA. You can change your DA Store (".$request->store_id.'-'.$value.") to be HYBRID";
                }
                if ($value == 'PC' && $request->dedicate == 'HYBRID') {
                    $error = "Dedicate: HYBRID cannot be Added, You already have PC. You can change your PC Store (".$request->store_id.'-'.$value.") to be HYBRID";
                }

                // return error json, di store-handler.js success: if data.error -> message
            }

            if ($error == '') {
                // $request->store_id = $request->old_store_id;

                $request->merge(array('store_id'=> $store_id));
                $request->merge(array('store_name_1'=> $store_name_1));
                $request->merge(array('store_name_2'=> $store_name_2));
                $request->merge(array('latitude'=> $latitude));
                $request->merge(array('longitude'=> $longitude));
                $request->merge(array('address'=> $address));
                $request->merge(array('classification'=> $classification));
                $request->merge(array('subchannel_id'=> $subchannel_id));
                $request->merge(array('district_id'=> $district_id));
            }else{
                return response()->json([
                    'url' => url('/store'),
                    'error' => $error
                ]);
            }
        }else{
            $this->validate($request, [
                'store_name_1' => 'required|string|max:255',
                'store_name_2' => 'string|max:255',
                'dedicate' => 'required',
                'longitude' => 'number',
                'latitude' => 'number',
                'subchannel_id' => 'required',
                'district_id' => 'required',
            ]);
        }

        // return response()->json($request->all());

        $store = Store::create($request->all());

        /* Store Distributor */
        if ($request->store_status == 'old') {
            $distributor = StoreDistributor::where('store_distributors.store_id', $request->old_store_id)->get();
            foreach ($distributor as $key => $value) {
                StoreDistributor::create([
                    'store_id' => $store->id,
                    'distributor_id' => $value->distributor_id,
                ]);
            }

        }else if($request['distributor_ids']){
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
        $error = '';
        // if ($request->store_status == 'old') {
            // return $request->old_store_id;
            $storeData = Store::
            where('stores.store_id', $request->store_id)
            ->where('stores.id', '!=', $request->id)
            ->get();
            $count = 0;
            foreach ($storeData as $key => $value) {
                $count = 1;
                $dedicate[] = 
                $value->dedicate;
            }
            if ($count>0) {
            
                // return response()->json($storeData);
                foreach ($dedicate as $key => $value) {
                    if ($value == $request->dedicate) { //redudant
                        $error = "Dedicate: Duplicate Entry for ".$value;
                    }
                    if ($value == 'DA' && $request->dedicate == 'PC') {
                        $error = "Dedicate: PC cannot be Added, You can change your DA Store (".$request->store_id.'-'.$value.") to be HYBRID";
                    }
                    if ($value == 'PC' && $request->dedicate == 'DA') {
                        $error = "Dedicate: DA cannot be Added, You can change your PC Store (".$request->store_id.'-'.$value.") to be HYBRID";
                    }
                    if ($value == 'HYBRID' && $request->dedicate == 'PC') {
                        $error = "Dedicate: PC cannot be Added, You already have HYBRID. You can change your HYBRID Store (".$request->store_id.'-'.$value.") to be PC";
                    }
                    if ($value == 'HYBRID' && $request->dedicate == 'DA') {
                        $error = "Dedicate: DA cannot be Added, You already have HYBRID. You can change your HYBRID Store (".$request->store_id.'-'.$value.") to be DA";
                    }
                    if ($value == 'DA' && $request->dedicate == 'HYBRID') {
                        $error = "Dedicate: HYBRID cannot be Added, You already have DA. You can change your DA Store (".$request->store_id.'-'.$value.") to be HYBRID";
                    }
                    if ($value == 'PC' && $request->dedicate == 'HYBRID') {
                        $error = "Dedicate: HYBRID cannot be Added, You already have PC. You can change your PC Store (".$request->store_id.'-'.$value.") to be HYBRID";
                    }

                    // return error json, di store-handler.js success: if data.error -> message
                }

                if ($error == '') {
                    // $request->store_id = $request->old_store_id;
                     // $request->merge(array('store_id'=> $request->old_store_id));
                }else{
                    return response()->json([
                        'url' => url('/store'),
                        'error' => $error
                    ]);
                }

            }//end $count
        // }

        $this->validate($request, [
            'store_name_1' => 'required|string|max:255',
            'store_name_2' => 'string|max:255',
            'longitude' => 'number',
            'latitude' => 'number',
            'subchannel_id' => 'required',
            'district_id' => 'required'
        ]);

        $store = Store::find($id);

        /* Delete if any relation exist in store distributor */
        $storeDist = StoreDistributor::where('store_id', $store->id);
        if($storeDist->count() > 0){
            $storeDist->delete();
        }

// $request['store_id'];

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
}

