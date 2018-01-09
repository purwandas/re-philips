<?php

namespace App\Http\Controllers;

use App\Store;
use App\TrainerArea;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use App\Attendance;
use App\AttendanceDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Traits\AttendanceTrait;
use Illuminate\Support\Collection;
use Auth;
use App\Filters\UserFilters;
use File;
use App\NewsRead;
use App\ProductKnowledgeRead;
use App\Reports\HistoryEmployeeStore;
use Carbon\Carbon;
use DB;

class UserPromoterController extends Controller
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
        return view('master.userpromoter');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::
            where('id', '<>', Auth::user()->id)
            ->whereIn('role',$roles);

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
                ->editColumn('role',function ($item) {
                    
                    if ($item->role == 'DM') {
                        $dedicate = '';
                        $dmarea = DmArea::where('user_id', $item->id)->get();
                        foreach ($dmarea as $key => $value) {
                            $dedicate = $value->dedicate;
                        }
                        return $item->role.' - '.$dedicate;
                    }

                    return $item->role;                    
                    
                })
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('userpromoter/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>";
                    // <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                })
                ->addColumn('store', function ($item) {

//                    $storeIds = EmployeeStore::where('user_id', $item->id)->pluck('store_id');
//                    $storeName = "";
//
//                    foreach ($storeIds as $storeId){
//
//                        $store = Store::find(trim($storeId));
//                        $storeName .= $store->store_id." - ".$store->store_name_1." (".$store->store_name_2.")";
//
//                        if($storeId != $storeIds[count($storeIds)-1]){
//                                $storeName .= ", ";
//                        }
//
//                    }

                    $countStore = $item->employeeStores()->count();

                    if($countStore > 0){
                        return
                        "<a class='open-employee-store-modal btn btn-primary' data-target='#employee-store-modal' data-toggle='modal' data-url='util/empstore' data-title='List Store' data-promoter-name='".$item->name."' data-id='".$item->id."'> See Details </a>";
                    }

                    return;

                })
                ->addColumn('supervisor', function ($item) {
                    $storeIds = EmployeeStore::
                                        with('store.user')
                                        // ->distinct('store.user.name')
                                        ->where('user_id', $item->id)
                                        // ->groupBy('user_id')
                                        ->get();

                    $storeSpv = [];
                    foreach ($storeIds as $storeId){
                        $storeSpv[] = $storeId->store->user->name;
                    }

                    $newStoreSpv = array_unique($storeSpv);

                    $storeSpvName = '';
                    foreach ($newStoreSpv as $key => $value) {
                        if ($key > 0) {
                            $storeSpvName .= ', ';
                        }
                        $storeSpvName .= $value;
                    }

                    return $storeSpvName;

                })
                ->addColumn('history', function ($item) {


                    $countStore = $item->historyEmployeeStores()->count();

                    if($countStore > 0){
                        return
                        "<a class='open-history-employee-store-modal btn btn-primary' data-target='#history-employee-store-modal' data-toggle='modal' data-url='util/historyempstore' data-title='List History' data-promoter-name='".$item->name."' data-id='".$item->id."'> See History </a>";
                    }

                    return;

                })
                ->rawColumns(['history', 'store', 'action'])
                ->make(true);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(UserFilters $filters){ 
        $data = User::filter($filters)->get();

        return $data;
    }
    public function getDataPromoterWithFilters(UserFilters $filters){ 
        $data = User::filter($filters)->where('role','=','Promoter')->get();

        return $data;
    }
    public function getDataGroupPromoterWithFilters(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::filter($filters)->where('role',$roles)->get();

        return $data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.userpromoter-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'role' => 'required|string',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $request['password'] = bcrypt($request['password']);

        // dd(public_path());        

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";
        
        if($request->photo_file != null) $request['photo'] = $photo_url;
        /*
        1. select store
        2. get dedicate
        3. replace or add by formula
            3.a. sekarang cuma replace/UPDATE user id aja di stores
                promoter savenya ke employeestore.user_id
                supervisor savenya ke store.user_id
                SE save ke employeestore, tanpa pilih dedicate
        */
            
        $user = User::create($request->all());

        // /* Insert user relation */
        // if ($request['role'] == 'Supervisor' || $request['role'] == 'Supervisor Hybrid') {
        //     /* Employee One Store */
        //     if($request['store_id']){
        //         $store = Store::find($request['store_id'])->update(['user_id'=>$user->id]);
        //     }

        //     /* Employee Multiple Store */
        //     if($request['store_ids']){
        //         foreach ($request['store_ids'] as $storeId) {
        //             $store = Store::find($storeId)->update(['user_id'=>$user->id]);
        //         }
        //     }
        // }else{
            /* Employee One Store */
            if($request['store_id']){
                // $store = Store::find($request['store_id'])->update(['user_id'=>$user->id]);
                EmployeeStore::create([
                    'user_id' => $user->id,
                    'store_id' => $request['store_id'],
                ]);
            }

            /* Employee Multiple Store */
            if($request['store_ids']){
                foreach ($request['store_ids'] as $storeId) {
                    // $store = Store::find($storeId)->update(['user_id'=>$user->id]);
                    EmployeeStore::create([
                        'user_id' => $user->id,
                        'store_id' => $storeId,
                    ]);
                }
            }
        // }

        // If DM or Trainer
        if(isset($request->area)){
            if($request['role'] == 'DM') {
                $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 'dedicate' => $request->dedicate]);
            }elseif($request['role'] == 'Trainer') {
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
        
        if($request['store_id']){
            $newEmStore = $request->store_id;
            HistoryEmployeeStore::create([
                            'user_id' => $userId->id,
                            'month' => Carbon::now()->format('m'),
                            'year' => Carbon::now()->format('Y'),
                            'details' => $newEmStore,
                    ]);
        }
        /* Employee Multiple Store */
        if($request['store_ids']){
            $newEmStore = $request->store_ids;
            $newEmStore2 = implode(",",$newEmStore);
            HistoryEmployeeStore::create([
                            'user_id' => $userId->id,
                            'month' => Carbon::now()->format('m'),
                            'year' => Carbon::now()->format('Y'),
                            'details' => $newEmStore2,
                    ]);
        }
        
        return response()->json(['url' => url('userpromoter')]);
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
        $data = User::where('id', $id)->first();

        return view('master.form.userpromoter-form', compact('data'));
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
            'role' => 'required|string',
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

        if ($request['role'] == 'Supervisor' || $request['role'] == 'Supervisor Hybrid') {
            /* SPV Multiple Store */
            if($request['store_ids']){
                foreach ($request['store_ids'] as $storeId) {
                    $store = Store::find($storeId)->update(['user_id'=>null]);
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
        $requestNew['role'] = $request['role'];

        $requestNew['status'] = null;
        $requestNew['nik'] = null;

        if($request['status']){
            $requestNew['status'] = $request['status'];
        }

        if($request['nik']){
            $requestNew['nik'] = $request['nik'];
        }

        $user->update($requestNew->all()); 

        /* Insert user relation */
        
            EmployeeStore::where('user_id',$user->id)->delete();

            /* Employee One Store */
            if($request['store_id']){
                // $store = Store::find($request['store_id'])->update(['user_id'=>$user->id]);
                EmployeeStore::create([
                    'user_id' => $user->id,
                    'store_id' => $request['store_id'],
                ]);
            }

            /* Employee Multiple Store */
            if($request['store_ids']){
                foreach ($request['store_ids'] as $storeId) {
                    // $store = Store::find($storeId)->update(['user_id'=>$user->id]);
                    EmployeeStore::create([
                        'user_id' => $user->id,
                        'store_id' => $storeId,
                    ]);
                }
            }

        // If DM
        if($request->area){
            if($request['role'] == 'DM') {
                $dmArea = DmArea::where('user_id', $user->id);
                if($dmArea->count() > 0){
                    $dmArea->first()->update(['area_id' => $request->area]);
                    $dmArea->first()->update(['dedicate' => $request->dedicate]);
                }else{
                    DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 'dedicate' => $request->dedicate]);
                }
            }elseif($request['role'] == 'Trainer') {
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

        $emStore = HistoryEmployeeStore::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        // $emStore2 = $emStore->details;
        $emStore2='';
            if (isset($emStore->details)) {
                $emStore2 = $emStore->details;
            }
        // $newEmStore = $request 'store_id';
        // $newEmStore2 = implode(",",$newEmStore);
        // echo response()->json($user);
        // echo response()->json($emStore2);
        // echo response()->json($newEmStore);

        // foreach ($emStore as $key => $value) {
        //     foreach ($newEmStore as $key2 => $value2) {
        //         if ($value == $value2) {
        //             $stay[] = $value;
        //             $c = deleteElement($value2,$c);
        //             $d = deleteElement($value2,$d);
        //         }
        //     }
        // }
        // if (isset($stay)) {
        
            if($request['store_id']){
                $newEmStore = $request->store_id;
                if ($newEmStore != $emStore2) {
                HistoryEmployeeStore::create([
                                'user_id' => $user->id,
                                'month' => Carbon::now()->format('m'),
                                'year' => Carbon::now()->format('Y'),
                                'details' => $newEmStore,
                        ]);
                }
            }
            /* Employee Multiple Store */
            if($request['store_ids']){
                $newEmStore = $request->store_ids;
                $newEmStore2 = implode(",",$newEmStore);
                if ($newEmStore2 != $emStore2) {
                HistoryEmployeeStore::create([
                                'user_id' => $user->id,
                                'month' => Carbon::now()->format('m'),
                                'year' => Carbon::now()->format('Y'),
                                'details' => $newEmStore2,
                        ]);
                }
            }
        // }


        // if (isset($stay)) {
        //     foreach ($c as $key => $value) {
        //             $headerDetails->push($value);
        //     }
        //     foreach ($stay as $key => $value) {
        //             $headerDetails->push($value);
        //     }
        //     foreach ($d as $key => $value) {
        //             $headerDetails->push($value);
        //     }
        // }

        // function deleteElement( $item, $array ) {
        //     $index = array_search($item, $array);
        //     if ( $index !== false ) {
        //         unset( $array[$index] );
        // }
        //     return $array;
        // }
        

        return response()->json(
            [
                'url' => url('userpromoter'),
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

        // Attendance
        $attendance = Attendance::where('user_id', $id);
        $cek = [];
            foreach ($attendance as $key => $value) {
                $attendanceDetail = AttendanceDetail::where('attendance_id', $value->id);
                $cek[] = $value;
                if($attendanceDetail->count() > 0){
                    $attendanceDetail->delete();
                }            
            }
        if($attendance->count() > 0){
            $attendance->delete();
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
