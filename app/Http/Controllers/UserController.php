<?php

namespace App\Http\Controllers;

use Auth;
use File;
use DB;
use App\Store;
use App\TrainerArea;
use App\User;
use App\SpvDemo;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use App\NewsRead;
use App\ProductKnowledgeRead;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Traits\AttendanceTrait;
use App\Filters\UserFilters;
use App\Reports\HistoryEmployeeStore;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

class UserController extends Controller
{
    use UploadTrait;
    use StringTrait;
    use AttendanceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.user');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::
            join('roles','roles.id','users.role_id')
            ->select('users.*','roles.role_group as role','roles.role as roles','roles.role_group')
            ->where('users.id', '<>', Auth::user()->id)
            ->whereNotIn('role_group',$roles);
//        $data = User::all();

        $filter = $data;

        /* If filter */
            if($request['byName']){
                $filter = $data->where('id', $request['byName']);
            }

            if($request['byNik']){
                $filter = $data->where('id', $request['byNik']);
            }

            if($request['byRole']){
                $filter = $data->where('role', $request['byRole']);
            }

    //     return $this->makeTable($filter);
    // }

    // // Datatable template
    // public function makeTable($data){


    //     // Datatables::of($filter->all())
    //     //     ->make(true);

        return Datatables::of($filter->get())
                ->editColumn('roles',function ($item) {
                    $dedicate = '';
                    $dmarea = DmArea::where('user_id', $item->id)->get();
                    foreach ($dmarea as $key => $value) {
                        $dedicate = $value->dedicate;
                    }
                    if ($item->role_group == 'DM') {
                        return $item->roles.' - '.$dedicate;
                    }

                    return $item->roles;
                    
                })
                ->addColumn('area', function ($item) {
                    
                    if($item->role_group == 'DM') {
                        $area = DmArea::where('user_id', $item->id)->first();
                    }elseif($item->role_group == 'Trainer') {
                        $area = TrainerArea::where('user_id', $item->id)->first();
                    }elseif($item->role_group == 'Supervisor' || $item->role_group == 'Supervisor Hybrid') {
                        $store = Store::where('user_id', $item->id)
                                ->join('districts','districts.id','stores.district_id')
                                ->join('areas','areas.id','districts.area_id')
                                ->groupBy('areas.id')
                                ->select('areas.name as area_name')
                                ->get();
                        $result = $store;
                            $spvDemo = SpvDemo::where('spv_demos.user_id', $item->id)
                                    ->join('stores','stores.id','spv_demos.store_id')
                                    ->join('districts','districts.id','stores.district_id')
                                    ->join('areas','areas.id','districts.area_id')
                                    ->groupBy('areas.id')
                                    ->select('areas.name as area_name')
                                    ->get();
                        if(count($spvDemo) > 0){
                            $result = $spvDemo;
                        }
                        
                        $area='';
                        foreach ($result as $key => $value) {
                            if ($key == 0) {
                                $area = $value->area_name;
                            }else{
                                $area .= ", ".$value->area_name;
                            }
                        }
                        return $area;
                    }

                    $area = (isset($area->area->name)) ? $area->area->name : '';

                    return $area;

                })
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('usernon/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(UserFilters $filters){ 

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;       

        $data = User::filter($filters)
                ->join('roles','roles.id','users.role_id')
                ->select('users.*','roles.role_group as role_group');

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $area);
        }
            
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('stores.user_id', $userId)
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $store);
        }

        return $data->get();
    }
    public function getDataPromoterWithFilters(UserFilters $filters){ 
        // $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::filter($filters)
                ->join('roles','roles.id','users.role_id')
                ->where('roles.role_group','=','Promoter')->get();

        return $data;
    }
    public function getDataNonPromoterWithFilters(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::filter($filters)
                ->join('roles','roles.id','users.role_id')
                ->whereNotIn('roles.role_group',$roles)->get();

        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.user-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return response()->json($request->all());

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'role_id' => 'required',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $request['password'] = bcrypt($request['password']);

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        $role = explode('`',$request['role_id']);
        $request['role_id'] = $role[0];
        $request['selectedRole'] = $role[1];

//        return response()->json($request->all());

        $user = User::create(
            [
                // $request->all()
                'nik' => $request['nik'],
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => $request['password'],
                'role_id' => $request['role_id'],
                'status' => $request['status'],
                'photo' => $request['photo'],
                'join_date' => $request['join_date'],
            ]
        );

//        return response()->json($request->all());

        /* Insert user relation */
        if ($request['selectedRole'] == 'Supervisor') {

            /* SPV Multiple Store */
            if($request['store_ids']){
                // return response()->json($request['store_ids']);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId); // id,store_id
                    // return response()->json($stores[1]);
                    if ($request['status_spv'] == "Demonstrator") 
                    {
                        
                        SpvDemo::create([
                            'user_id' => $user->id,
                            'store_id' => $stores[0],
                        ]);

                    }else{
                        
                        $store = Store::where('deleted_at',null)
                                    ->where('store_id',$stores[1])->get();
                        $status = false;
                        $store_id   = '';
                        $store_name_1   = '';
                        $store_name_2   = '';
                        $latitude   = '';
                        $longitude  = '';
                        $address    = '';
                        $classification     = '';
                        $subchannel_id  = '';
                        $district_id    = '';
                        $no_telp_toko = '';
                        $no_telp_pemilik_toko = '';
                        $kepemilikan_toko = '';
                        $district_id = '';
                        $lokasi_toko = '';
                        $tipe_transaksi_2 = '';
                        $tipe_transaksi = '';
                        $kondisi_toko = '';
                        $history = '';
                        
                        foreach ($store as $key => $value) {
                            /* ini masih foreach, harusnya cuma 1 kali aja untuk setiap store*/
                            $storesDedicate = '';
                            if (isset($value->dedicate)) {
                                $storesDedicate = $value->dedicate;
                            }
                            if ( ($storesDedicate == '' || $storesDedicate == $request['dedicate']) && $status == false)
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }
                            if ( ($storesDedicate == 'DA' || $storesDedicate == 'PC' || $storesDedicate == 'HYBRID') && $status == false)
                            {
                                if ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC' || $request['dedicate'] == 'HYBRID')
                                {
                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }

                            $store_id = $value->store_id;
                            $store_name_1 = $value->store_name_1;
                            $store_name_2 = $value->store_name_2;
                            $latitude = $value->latitude;
                            $longitude = $value->longitude;
                            $address = $value->address;
                            $classification = $value->classification->classification;
                            $subchannel_id = $value->subchannel_id;
                            $district_id = $value->district_id;

                            $no_telp_toko = $value->no_telp_toko;
                            $no_telp_pemilik_toko = $value->no_telp_pemilik_toko;
                            $kepemilikan_toko = $value->kepemilikan_toko;
                            $district_id = $value->district_id;
                            $lokasi_toko = $value->lokasi_toko;
                            $tipe_transaksi_2 = $value->tipe_transaksi_2;
                            $tipe_transaksi = $value->tipe_transaksi;
                            $kondisi_toko = $value->kondisi_toko;
                        }

                        if ($status == false) {
                            Store::create([
                                'store_id' => $store_id,
                                'store_name_1' => $store_name_1,
                                'store_name_2' => $store_name_2,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'address' => $address,
                                'classification_id' => $classification,
                                'subchannel_id' => $subchannel_id,
                                'district_id' => $district_id,
                                'user_id' => $user->id,
                                'dedicate' => $request['dedicate'],

                                'no_telp_toko' => $no_telp_toko,
                                'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                'kepemilikan_toko' => $kepemilikan_toko,
                                'district_id' => $district_id,
                                'lokasi_toko' => $lokasi_toko,
                                'tipe_transaksi_2' => $tipe_transaksi_2,
                                'tipe_transaksi' => $tipe_transaksi,
                                'kondisi_toko' => $kondisi_toko,
                            ]);
                            $status = true;
                        }

                    }
                            
                }
            }
        }else if ($request['selectedRole'] == 'Supervisor Hybrid') {
            if($request['store_ids']){
                // return response()->json($request['store_ids']);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId);
                    $store = Store::where('deleted_at',null)
                                    ->where('store_id',$stores[1])->get();
                        $status = false;
                        $store_id   = '';
                        $store_name_1   = '';
                        $store_name_2   = '';
                        $latitude   = '';
                        $longitude  = '';
                        $address    = '';
                        $classification     = '';
                        $subchannel_id  = '';
                        $district_id    = '';
                        $hybrid = false;
                        $hybridData = false;
                        $mccData = false;
                        $dedicateStored = '';
                        $no_telp_toko = '';
                        $no_telp_pemilik_toko = '';
                        $kepemilikan_toko = '';
                        $district_id = '';
                        $lokasi_toko = '';
                        $tipe_transaksi_2 = '';
                        $tipe_transaksi = '';
                        $kondisi_toko = '';
                        
                        // return response()->json($store);
                        foreach ($store as $key => $value) {
                            /* ini masih foreach, harusnya cuma 1 kali aja untuk setiap store*/
                            $storesDedicate = '';
                            $store_id = $value->store_id;
                            $store_name_1 = $value->store_name_1;
                            $store_name_2 = $value->store_name_2;
                            $latitude = $value->latitude;
                            $longitude = $value->longitude;
                            $address = $value->address;
                            $classification = $value->classification->classification;
                            $subchannel_id = $value->subchannel_id;
                            $district_id = $value->district_id;
                                $no_telp_toko = $value->no_telp_toko;
                                $no_telp_pemilik_toko = $value->no_telp_pemilik_toko;
                                $kepemilikan_toko = $value->kepemilikan_toko;
                                $district_id = $value->district_id;
                                $lokasi_toko = $value->lokasi_toko;
                                $tipe_transaksi_2 = $value->tipe_transaksi_2;
                                $tipe_transaksi = $value->tipe_transaksi;
                                $kondisi_toko = $value->kondisi_toko;

                            if (isset($value->dedicate)) {
                                $storesDedicate = $value->dedicate;
                            }
                            if ( ($storesDedicate == '') && $status == false)
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);

                                if ($request['dedicate'] == 'HYBRID') {
                                    Store::create([
                                        'store_id' => $store_id,
                                        'store_name_1' => $store_name_1,
                                        'store_name_2' => $store_name_2,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'address' => $address,
                                        'classification_id' => $classification,
                                        'subchannel_id' => $subchannel_id,
                                        'district_id' => $district_id,
                                        'user_id' => $user->id,
                                        'dedicate' => 'MCC',

                                        'no_telp_toko' => $no_telp_toko,
                                        'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                        'kepemilikan_toko' => $kepemilikan_toko,
                                        'district_id' => $district_id,
                                        'lokasi_toko' => $lokasi_toko,
                                        'tipe_transaksi_2' => $tipe_transaksi_2,
                                        'tipe_transaksi' => $tipe_transaksi,
                                        'kondisi_toko' => $kondisi_toko,
                                    ]);
                                }
                                $status = true;
                            }
                            
                            if ( ($storesDedicate == $request['dedicate']) && $status == false && $request['dedicate'] != 'HYBRID')
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }

                            if ( ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC') && $status == false)
                            {
                                
                                if ($storesDedicate == 'DA' || $storesDedicate == 'PC' || $storesDedicate == 'HYBRID')
                                {
                                    $hybridData = true;
                                    $dedicateStored = $storesDedicate;

                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }
                            
                            if ( ($request['dedicate'] == 'MCC') && $status == false ) 
                            {
                                if ($storesDedicate == 'MCC') {
                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }

                            if ( ($request['dedicate'] == 'HYBRID') && $status == false ) 
                            {
                                $hybrid = true;
                            }
                            if ( ($storesDedicate == 'HYBRID') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'HYBRID';
                            }
                            if ( ($storesDedicate == 'DA') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'DA';
                            }
                            if ( ($storesDedicate == 'PC') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'PC';
                            }
                            if ( ($storesDedicate == 'MCC') && $status == false ) 
                            {
                                $mccData = true;
                            }

                        }

                        if ($hybrid == true) 
                        {
                            // return response()->json($hybridData);
                            if ($hybridData == true) 
                            {
                                Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])
                                ->where('dedicate',$dedicateStored)
                                ->update([
                                    'user_id'=>$user->id,'dedicate'=>'HYBRID',
                                ]);
                                $status = true;
                            }

                            if ($mccData == true) 
                            {
                                Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])
                                ->where('dedicate','MCC')
                                ->update([
                                    'user_id'=>$user->id,'dedicate'=>'MCC',
                                ]);
                                $status = true;
                            }

                            if ($hybridData == false) {
                                $checkStore = Store::where('deleted_at',null)
                                        ->where('store_id',$stores[1])
                                        ->whereIn('dedicate',['HYBRID','DA','PC'])
                                        ->get();
                                if ($checkStore->count() == 0 ) {
                                    Store::create([
                                        'store_id' => $store_id,
                                        'store_name_1' => $store_name_1,
                                        'store_name_2' => $store_name_2,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'address' => $address,
                                        'classification_id' => $classification,
                                        'subchannel_id' => $subchannel_id,
                                        'district_id' => $district_id,
                                        'user_id' => $user->id,
                                        'dedicate' => 'HYBRID',

                                        'no_telp_toko' => $no_telp_toko,
                                        'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                        'kepemilikan_toko' => $kepemilikan_toko,
                                        'district_id' => $district_id,
                                        'lokasi_toko' => $lokasi_toko,
                                        'tipe_transaksi_2' => $tipe_transaksi_2,
                                        'tipe_transaksi' => $tipe_transaksi,
                                        'kondisi_toko' => $kondisi_toko,
                                    ]);
                                }else{
                                    Store::where('deleted_at',null)
                                        ->where('store_id',$stores[1])
                                        ->whereIn('dedicate',['HYBRID','DA','PC'])
                                        ->update([
                                            'user_id'=>$user->id,'dedicate'=>'HYBRID',
                                        ]);
                                }
                                $status = true;
                            }
                            
                            if ($mccData == false) {
                                Store::create([
                                    'store_id' => $store_id,
                                    'store_name_1' => $store_name_1,
                                    'store_name_2' => $store_name_2,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'address' => $address,
                                    'classification_id' => $classification,
                                    'subchannel_id' => $subchannel_id,
                                    'district_id' => $district_id,
                                    'user_id' => $user->id,
                                    'dedicate' => 'MCC',

                                    'no_telp_toko' => $no_telp_toko,
                                    'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                    'kepemilikan_toko' => $kepemilikan_toko,
                                    'district_id' => $district_id,
                                    'lokasi_toko' => $lokasi_toko,
                                    'tipe_transaksi_2' => $tipe_transaksi_2,
                                    'tipe_transaksi' => $tipe_transaksi,
                                    'kondisi_toko' => $kondisi_toko,
                                ]);
                                $status = true;
                            }

                        }

                        if ( ($request['dedicate'] == 'MCC') && $status == false ) 
                        {
                            Store::create([
                                'store_id' => $store_id,
                                'store_name_1' => $store_name_1,
                                'store_name_2' => $store_name_2,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'address' => $address,
                                'classification_id' => $classification,
                                'subchannel_id' => $subchannel_id,
                                'district_id' => $district_id,
                                'user_id' => $user->id,
                                'dedicate' => 'MCC',

                                'no_telp_toko' => $no_telp_toko,
                                'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                'kepemilikan_toko' => $kepemilikan_toko,
                                'district_id' => $district_id,
                                'lokasi_toko' => $lokasi_toko,
                                'tipe_transaksi_2' => $tipe_transaksi_2,
                                'tipe_transaksi' => $tipe_transaksi,
                                'kondisi_toko' => $kondisi_toko,
                            ]);
                            $status = true;
                        }
                        
                }
            }
        }

        // If DM or Trainer
        if(isset($request->area)){
            if($request['selectedRole'] == 'DM') {
                $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 'dedicate' => $request->dedicate]);
            }elseif($request['selectedRole'] == 'Trainer') {
                $trainerArea = TrainerArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
            }
        }
        // If RSM
        if(isset($request->region)){
            $rsmRegion = RsmRegion::create(['user_id' => $user->id, 'region_id' => $request->region]);
        }

        if($request->photo_file != null){

            /* Upload updated image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $imageFolder = "user/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo_file, $imageFolder, $imageName);

        }


        /*
         * Generate attendance from day promoter works till end of month
         * (Just work for promoter group)
         */

        $promoterArray = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        if(in_array($user->role, $promoterArray)){
            $this->generateAttendace($user->id);
        }
        
        $userId = User::where('email', $request->email)->first();
        // echo response()->json($userId);

        return response()->json(['url' => url('usernon')]);
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
        // $data = User::where('id', $id)->first();
        $data = User::
            where('users.id', $id)
            ->select('users.*')//, 'stores.dedicate as dedicate')
            ->first();
        
        if ($data->role->role_group == 'Supervisor' || $data->role->role_group == 'Supervisor Hybrid' ) {
            $spvDedicate = Store::
                where('user_id',$data->id)
                ->first();
            $spvDemo = SpvDemo::
                where('user_id',$data->id)
                ->first();
            $spvDemoDedicate = SpvDemo::
                where('user_id',$data->id)
                ->first();
        }

        return view('master.form.user-form', compact('data','spvDedicate','spvDemo'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users'. ($id ? ",id,$id" : ''),
            'role_id' => 'required|string',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $user = User::find($id);
        $oldPhoto = "";
        
        if($user->photo != null && $request->photo_file != null) {
            /* Save old photo path */
            $oldPhoto = $user->photo;
        }

        /* Delete if any relation exist in employee store */
        // $empStore = EmployeeStore::where('user_id', $user->id);
        // if($empStore->count() > 0){
        //     $empStore->delete();
        // }

        // If Exists SpvDemo Data
        $spvDemo = SpvDemo::where('user_id', $user->id);
        if($spvDemo->count() > 0){
            $spvDemo->delete();
        } 

        // If Exists Spv Data
        // Store::where('user_id',$user->id)
        //     ->update(['user_id'=>null]);    
        $role = explode('`',$request['selectedRole']);
        $request['selectedRole'] = $role[1];

        if ($request['selectedRole'] == 'Supervisor' || $request['selectedRole'] == 'Supervisor Hybrid') {
            /* SPV Multiple Store */
            if($request['store_ids']){
                foreach ($request['store_ids'] as $storeId) {
                    $store = Store::where('user_id', $user->id)->update(['user_id'=>null]);
                }
            }
        }else{
            $empStore = Store::where('user_id', $user->id);
            if($empStore->count() > 0){
                $empStore->update(['user_id'=>null]);
            }
        }
        

        // DM AREA 
        $dmArea = DmArea::where('user_id', $user->id);
        if($dmArea->count() > 0){
            $dmArea->delete();
        }

        // TRAINER AREA
        $trainerArea = TrainerArea::where('user_id', $user->id);
        if($trainerArea->count() > 0){
            $trainerArea->delete();
        }

        // RSM REGION 
        $rsmRegion = RsmRegion::where('user_id', $user->id);
        if($rsmRegion->count() > 0){
            $rsmRegion->delete();
        }
        /* ================================================= */

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Create new request
        $requestNew = new Request;

        // Check if password empty
        if($request['password']){

            $requestNew['password'] = bcrypt($request['password']);

        }

        if($request->photo_file != null){

            $requestNew['photo'] = $request['photo'];

        }

        $requestNew['name'] = $request['name'];
        $requestNew['email'] = $request['email'];
        $requestNew['role_id'] = $role[0];

        $requestNew['status'] = null;
        $requestNew['nik'] = null;
        $requestNew['join_date'] = null;

        if($request['status']){
            $requestNew['status'] = $request['status'];
        }

        if($request['nik']){
            $requestNew['nik'] = $request['nik'];
        }

        if($request['join_date']){
            $requestNew['join_date'] = $request['join_date'];
        }

        $user->update($requestNew->all()); 

        /* Insert user relation */
        if ($request['selectedRole'] == 'Supervisor') {

            /* SPV Multiple Store */
            if($request['store_ids']){
                // return response()->json($request['store_ids']);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId); // id,store_id
                    // return response()->json($stores[1]);
                    if ($request['status_spv'] == "Demonstrator") 
                    {
                        
                        SpvDemo::create([
                            'user_id' => $user->id,
                            'store_id' => $stores[0],
                        ]);

                    }else{
                        
                        $store = Store::where('deleted_at',null)
                                    ->where('store_id',$stores[1])->get();
                        $status = false;
                        $store_id   = '';
                        $store_name_1   = '';
                        $store_name_2   = '';
                        $latitude   = '';
                        $longitude  = '';
                        $address    = '';
                        $classification     = '';
                        $subchannel_id  = '';
                        $district_id    = '';
                        $history = '';
                        
                        $no_telp_toko = '';
                        $no_telp_pemilik_toko = '';
                        $kepemilikan_toko = '';
                        $district_id = '';
                        $lokasi_toko = '';
                        $tipe_transaksi_2 = '';
                        $tipe_transaksi = '';
                        $kondisi_toko = '';
                        
                        foreach ($store as $key => $value) {
                            /* ini masih foreach, harusnya cuma 1 kali aja untuk setiap store*/
                            $storesDedicate = '';
                            if (isset($value->dedicate)) {
                                $storesDedicate = $value->dedicate;
                            }
                            if ( ($storesDedicate == '' || $storesDedicate == $request['dedicate']) && $status == false)
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }
                            if ( ($storesDedicate == 'DA' || $storesDedicate == 'PC' || $storesDedicate == 'HYBRID') && $status == false)
                            {
                                if ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC' || $request['dedicate'] == 'HYBRID')
                                {
                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }

                            $store_id = $value->store_id;
                            $store_name_1 = $value->store_name_1;
                            $store_name_2 = $value->store_name_2;
                            $latitude = $value->latitude;
                            $longitude = $value->longitude;
                            $address = $value->address;
                            $classification = $value->classification->classification;
                            $subchannel_id = $value->subchannel_id;
                            $district_id = $value->district_id;

                            $no_telp_toko = $value->no_telp_toko;
                            $no_telp_pemilik_toko = $value->no_telp_pemilik_toko;
                            $kepemilikan_toko = $value->kepemilikan_toko;
                            $district_id = $value->district_id;
                            $lokasi_toko = $value->lokasi_toko;
                            $tipe_transaksi_2 = $value->tipe_transaksi_2;
                            $tipe_transaksi = $value->tipe_transaksi;
                            $kondisi_toko = $value->kondisi_toko;
                        }

                        if ($status == false) {
                            Store::create([
                                'store_id' => $store_id,
                                'store_name_1' => $store_name_1,
                                'store_name_2' => $store_name_2,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'address' => $address,
                                'classification_id' => $classification,
                                'subchannel_id' => $subchannel_id,
                                'district_id' => $district_id,
                                'user_id' => $user->id,
                                'dedicate' => $request['dedicate'],

                                'no_telp_toko' => $no_telp_toko,
                                'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                'kepemilikan_toko' => $kepemilikan_toko,
                                'district_id' => $district_id,
                                'lokasi_toko' => $lokasi_toko,
                                'tipe_transaksi_2' => $tipe_transaksi_2,
                                'tipe_transaksi' => $tipe_transaksi,
                                'kondisi_toko' => $kondisi_toko,
                            ]);
                            $status = true;
                        }

                    }
                            
                }
            }
        }else if ($request['selectedRole'] == 'Supervisor Hybrid') {
            // return response()->json($request['store_ids']);
            if($request['store_ids']){
                // return response()->json($request['store_ids']);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId);
                    $store = Store::where('deleted_at',null)
                                    ->where('store_id',$stores[1])->get();
                        $status = false;
                        $store_id   = '';
                        $store_name_1   = '';
                        $store_name_2   = '';
                        $latitude   = '';
                        $longitude  = '';
                        $address    = '';
                        $classification     = '';
                        $subchannel_id  = '';
                        $district_id    = '';
                        $hybrid = false;
                        $hybridData = false;
                        $mccData = false;
                        $dedicateStored = '';
                        $no_telp_toko = '';
                        $no_telp_pemilik_toko = '';
                        $kepemilikan_toko = '';
                        $district_id = '';
                        $lokasi_toko = '';
                        $tipe_transaksi_2 = '';
                        $tipe_transaksi = '';
                        $kondisi_toko = '';
                        
                        // return response()->json($store);
                        foreach ($store as $key => $value) {
                            /* ini masih foreach, harusnya cuma 1 kali aja untuk setiap store*/
                            $storesDedicate = '';
                            $store_id = $value->store_id;
                            $store_name_1 = $value->store_name_1;
                            $store_name_2 = $value->store_name_2;
                            $latitude = $value->latitude;
                            $longitude = $value->longitude;
                            $address = $value->address;
                            $classification = $value->classification->classification;
                            $subchannel_id = $value->subchannel_id;
                            $district_id = $value->district_id;
                                $no_telp_toko = $value->no_telp_toko;
                                $no_telp_pemilik_toko = $value->no_telp_pemilik_toko;
                                $kepemilikan_toko = $value->kepemilikan_toko;
                                $district_id = $value->district_id;
                                $lokasi_toko = $value->lokasi_toko;
                                $tipe_transaksi_2 = $value->tipe_transaksi_2;
                                $tipe_transaksi = $value->tipe_transaksi;
                                $kondisi_toko = $value->kondisi_toko;

                            if (isset($value->dedicate)) {
                                $storesDedicate = $value->dedicate;
                            }
                            if ( ($storesDedicate == '') && $status == false)
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);

                                if ($request['dedicate'] == 'HYBRID') {
                                    Store::create([
                                        'store_id' => $store_id,
                                        'store_name_1' => $store_name_1,
                                        'store_name_2' => $store_name_2,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'address' => $address,
                                        'classification_id' => $classification,
                                        'subchannel_id' => $subchannel_id,
                                        'district_id' => $district_id,
                                        'user_id' => $user->id,
                                        'dedicate' => 'MCC',

                                        'no_telp_toko' => $no_telp_toko,
                                        'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                        'kepemilikan_toko' => $kepemilikan_toko,
                                        'district_id' => $district_id,
                                        'lokasi_toko' => $lokasi_toko,
                                        'tipe_transaksi_2' => $tipe_transaksi_2,
                                        'tipe_transaksi' => $tipe_transaksi,
                                        'kondisi_toko' => $kondisi_toko,
                                    ]);
                                }
                                $status = true;
                            }
                            
                            if ( ($storesDedicate == $request['dedicate']) && $status == false && $request['dedicate'] != 'HYBRID')
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }

                            if ( ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC') && $status == false)
                            {
                                
                                if ($storesDedicate == 'DA' || $storesDedicate == 'PC' || $storesDedicate == 'HYBRID')
                                {
                                    $hybridData = true;
                                    $dedicateStored = $storesDedicate;

                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }
                            
                            if ( ($request['dedicate'] == 'MCC') && $status == false ) 
                            {
                                if ($storesDedicate == 'MCC') {
                                    Store::where('id',$value->id)
                                    ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                    $status = true;
                                }
                            }

                            if ( ($request['dedicate'] == 'HYBRID') && $status == false ) 
                            {
                                $hybrid = true;
                            }
                            if ( ($storesDedicate == 'HYBRID') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'HYBRID';
                            }
                            if ( ($storesDedicate == 'DA') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'DA';
                            }
                            if ( ($storesDedicate == 'PC') && $status == false ) 
                            {
                                $hybridData = true;
                                $dedicateStored = 'PC';
                            }
                            if ( ($storesDedicate == 'MCC') && $status == false ) 
                            {
                                $mccData = true;
                            }

                        }

                        if ($hybrid == true) 
                        {
                            // return response()->json($hybridData);
                            if ($hybridData == true) 
                            {
                                Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])
                                ->where('dedicate',$dedicateStored)
                                ->update([
                                    'user_id'=>$user->id,'dedicate'=>'HYBRID',
                                ]);
                                $status = true;
                            }

                            if ($mccData == true) 
                            {
                                Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])
                                ->where('dedicate','MCC')
                                ->update([
                                    'user_id'=>$user->id,'dedicate'=>'MCC',
                                ]);
                                $status = true;
                            }

                            if ($hybridData == false) {
                                $checkStore = Store::where('deleted_at',null)
                                        ->where('store_id',$stores[1])
                                        ->whereIn('dedicate',['HYBRID','DA','PC'])
                                        ->get();
                                if ($checkStore->count() == 0 ) {
                                    Store::create([
                                        'store_id' => $store_id,
                                        'store_name_1' => $store_name_1,
                                        'store_name_2' => $store_name_2,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'address' => $address,
                                        'classification_id' => $classification,
                                        'subchannel_id' => $subchannel_id,
                                        'district_id' => $district_id,
                                        'user_id' => $user->id,
                                        'dedicate' => 'HYBRID',

                                        'no_telp_toko' => $no_telp_toko,
                                        'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                        'kepemilikan_toko' => $kepemilikan_toko,
                                        'district_id' => $district_id,
                                        'lokasi_toko' => $lokasi_toko,
                                        'tipe_transaksi_2' => $tipe_transaksi_2,
                                        'tipe_transaksi' => $tipe_transaksi,
                                        'kondisi_toko' => $kondisi_toko,
                                    ]);
                                }else{
                                    Store::where('deleted_at',null)
                                        ->where('store_id',$stores[1])
                                        ->whereIn('dedicate',['HYBRID','DA','PC'])
                                        ->update([
                                            'user_id'=>$user->id,'dedicate'=>'HYBRID',
                                        ]);
                                }
                                $status = true;
                            }
                            
                            if ($mccData == false) {
                                Store::create([
                                    'store_id' => $store_id,
                                    'store_name_1' => $store_name_1,
                                    'store_name_2' => $store_name_2,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'address' => $address,
                                    'classification_id' => $classification,
                                    'subchannel_id' => $subchannel_id,
                                    'district_id' => $district_id,
                                    'user_id' => $user->id,
                                    'dedicate' => 'MCC',

                                    'no_telp_toko' => $no_telp_toko,
                                    'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                    'kepemilikan_toko' => $kepemilikan_toko,
                                    'district_id' => $district_id,
                                    'lokasi_toko' => $lokasi_toko,
                                    'tipe_transaksi_2' => $tipe_transaksi_2,
                                    'tipe_transaksi' => $tipe_transaksi,
                                    'kondisi_toko' => $kondisi_toko,
                                ]);
                                $status = true;
                            }

                        }

                        if ( ($request['dedicate'] == 'MCC') && $status == false ) 
                        {
                            Store::create([
                                'store_id' => $store_id,
                                'store_name_1' => $store_name_1,
                                'store_name_2' => $store_name_2,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'address' => $address,
                                'classification_id' => $classification,
                                'subchannel_id' => $subchannel_id,
                                'district_id' => $district_id,
                                'user_id' => $user->id,
                                'dedicate' => 'MCC',

                                'no_telp_toko' => $no_telp_toko,
                                'no_telp_pemilik_toko' => $no_telp_pemilik_toko,
                                'kepemilikan_toko' => $kepemilikan_toko,
                                'district_id' => $district_id,
                                'lokasi_toko' => $lokasi_toko,
                                'tipe_transaksi_2' => $tipe_transaksi_2,
                                'tipe_transaksi' => $tipe_transaksi,
                                'kondisi_toko' => $kondisi_toko,
                            ]);
                            $status = true;
                        }
                        
                }
            }
        }

        // If DM
        if($request->area){
            
            if($request['selectedRole'] == 'DM') {
                $dmArea = DmArea::where('user_id', $user->id);
                if($dmArea->count() > 0){
                    $dmArea->first()->update(['area_id' => $request->area]);
                    // $dmArea->first()->update(['dedicate' => $request->dedicate]);
                }else{
                    DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 
                        // 'dedicate' => $request->dedicate
                        ]);
                }
            }elseif($request['selectedRole'] == 'Trainer') {
                $trainerArea = TrainerArea::where('user_id', $user->id);
                if($trainerArea->count() > 0){
                    $trainerArea->first()->update(['area_id' => $request->area]);
                }else{
                    TrainerArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
                }
            }

            
        }
        // If RSM
        if($request->region){
            $rsmRegion = RsmRegion::where('user_id', $user->id);
        
            if($rsmRegion->count() > 0){
                $rsmRegion->first()->update(['region_id' => $request->region]);
            }else{
                RsmRegion::create(['user_id' => $user->id, 'region_id' => $request->region]);
            }
        }

        if($user->photo != null && $request->photo_file != null && $oldPhoto != "") {
            /* Delete Image */
            $imagePath = explode('/', $oldPhoto);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);

        }

        if($user->photo != null && $request->photo_file != null){
            /* Upload updated image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $imageFolder = "user/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo_file, $imageFolder, $imageName);
        }

        

        return response()->json(
            [
                'url' => url('usernon'),
                'method' => $request->_method
            ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Deleting related to user */

        /* Delete if any relation exist in employee store */
        $empStore = EmployeeStore::where('user_id', $id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        // DM AREA 
        $dmArea = DmArea::where('user_id', $id);
        if($dmArea->count() > 0){
            $dmArea->delete();
        }

        // TRAINER AREA
        $trainerArea = TrainerArea::where('user_id', $id);
        if($trainerArea->count() > 0){
            $trainerArea->delete();
        }

        // RSM REGION 
        $rsmRegion = RsmRegion::where('user_id', $id);
        if($rsmRegion->count() > 0){
            $rsmRegion->delete();
        }

        // News Reads
        $newsRead = NewsRead::where('user_id', $id);
        if($newsRead->count() > 0){
            $newsRead->delete();
        }

        // Product Knowledge Reads
        $productKnowledgeRead = ProductKnowledgeRead::where('user_id', $id);
        if($productKnowledgeRead->count() > 0){
            $productKnowledgeRead->delete();
        }

        $user = User::find($id);

        if($user->photo != "") {
            /* Delete Image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);
        }

        $user->destroy($id);

        return response()->json($id);
    }
}
