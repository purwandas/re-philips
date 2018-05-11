<?php

namespace App\Http\Controllers;

use App\Store;
use App\SpvDemo;
use App\TrainerArea;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use App\Attendance;
use App\AttendanceDetail;
use App\SalesmanDedicate;
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
use App\ProductKnowledge;
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
        
        $data = User::where('is_resign', 0)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('users.id', '<>', Auth::user()->id)
            ->whereIn('role_group',$roles);

//        $data = User::all();



        $filter = $data;

        /* If filter */
            if($request['byName2']){
                $filter = $filter->where('users.id', $request['byName2']);
            }

            if($request['byNik']){
                $filter = $filter->where('users.id', $request['byNik']);
            }

            if($request['byRole']){
                $filter = $filter->where('role', $request['byRole']);
            }

        return Datatables::of($filter->get())
                ->editColumn('role',function ($item) {
                    
                    if ($item->role == 'Salesman Explorer') {
                        $dedicate = '';
                        $salesmanDedicate = SalesmanDedicate::where('user_id',$item->id)->first();
                        if (isset($salesmanDedicate)) {
                            $dedicate = ' - '.$salesmanDedicate->dedicate;
                        }
                        return $item->role.$dedicate;
                    }


                    return $item->role;                    
                    
                })
                ->addColumn('action', function ($item) {
                    $action='';
                    if ($item->status_login == 'Login' or $item->hp_id != null or $item->jenis_hp != null) {
                    $action .= "<a href='".url('userpromoter/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-success btn-sm openAccessButton' data-toggle='confirmation' data-singleton='true' title='Open access new Phone' value='".$item->id." '><i class='fa fa-unlock'></i></button>";
                    } else {
                    $action .= "<a href='".url('userpromoter/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger disabled'><i class='fa fa-lock'></i></button>";
                    }
                    
                    // $action .= "<button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' title='Resign' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                    return $action;
                    
                })
                ->addColumn('store', function ($item) {

                    $countStore = $item->employeeStores()->count();

                    if($countStore > 0){
                        return
                        "<a class='open-employee-store-modal btn btn-primary' data-target='#employee-store-modal' data-toggle='modal' data-url='util/empstore' data-title='List Store' data-promoter-name='".$item->name."' data-id='".$item->id."'> See Details </a>";
                    }

                    return;

                })
                ->addColumn('supervisor', function ($item) {
                    if($item->role == 'Demonstrator DA'){
                        $storeIds = EmployeeStore::
                                        with('store.user')
                                        // ->distinct('store.user.name')
                                        ->where('user_id', $item->id)
                                        // ->groupBy('user_id')
                                        ->pluck('store_id');

                                        // return $storeIds;

                        $spvDemoIds = SpvDemo::whereIn('store_id', $storeIds)->pluck('user_id')->toArray();
                        $newSpvDemoIds = array_unique($spvDemoIds);

                        $spvDemo = '';

                        if(count($newSpvDemoIds) > 0){
                            $check = 0;
                            foreach ($newSpvDemoIds as $key => $value){
                                $user = User::where('id', $value)->first();
                                $userTmp = (isset($user)) ? $user->name : '';
                                
                                
                                if ($check > 0 && $userTmp != '') {
                                    $spvDemo .= ', ';
                                }
                                
                                
                                if (isset($userTmp) && !empty($userTmp) && $userTmp != ''){
                                    $spvDemo .= $userTmp;
                                    $check = 1;
                                }
                                
                            }
                        }

                        return $spvDemo;
                    }
                    $storeIds = EmployeeStore::
                                        with('store.user')
                                        // ->distinct('store.user.name')
                                        ->where('user_id', $item->id)
                                        // ->groupBy('user_id')
                                        ->get();

                    $storeSpv = [];
                    foreach ($storeIds as $storeId){
                        if (isset($storeId->store->user->name)) {
                            $storeSpv[] = $storeId->store->user->name;
                        }
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
                ->addColumn('area', function ($item) {
                    $store = EmployeeStore::
                                        where('employee_stores.user_id', $item->id)
                                        ->join('stores','stores.id','employee_stores.store_id')
                                        ->join('districts','districts.id','stores.district_id')
                                        ->join('areas','areas.id','districts.area_id')
                                        ->groupBy('areas.id')
                                        ->select('areas.name as area_name')
                                        ->get();
                        
                        $area='';
                        foreach ($store as $key => $value) {
                            if ($key == 0) {
                                $area = $value->area_name;
                            }else{
                                $area .= ", ".$value->area_name;
                            }
                        }
                        return $area;

                    return $area;

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
        $data = User::filter($filters)->where('is_resign', 0)->get();

        return $data;
    }
    public function getDataPromoterWithFilters(UserFilters $filters){ 
        $data = User::filter($filters)
                ->join('roles','roles.id','users.role_id')
                ->where('is_resign', 0)
                ->whereIn('roles.role_group','=','Promoter')->get();

        return $data;
    }
    public function getDataGroupPromoterWithFilters(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        $data = User::filter($filters)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('is_resign', 0)
            ->whereIn('role_group',$roles)->get();

        return $data;
    }

    public function getDataGroupPromoterWithFiltersCheck(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        $data = User::filter($filters)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('is_resign', 0)
            ->limit(1)
            ->whereIn('role_group',$roles)->get();

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
        // return response()->json($request->all());
        if (!empty($request['nik'])) {
            $oldUser = User::where('nik', $request->nik);
            if($oldUser->count() > 0){
                return response()->json(['error'=>"NIK already exist!"]);
            }
        }

        $oldUser = User::where('email', $request->email);
        if($oldUser->count() > 0){
            return response()->json(['error'=>"Email already exist!"]);
        }

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'role_id' => 'required',
            'join_date' => 'required',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);        

        // return response()->json($request);

        $request['password'] = bcrypt($request['password']);

        $role = explode('`',$request['role_id']);
        $request['selectedRole'] = $role[1];

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
            /* Employee One Store */
            if($request['store_id'] && $request['status'] == 'stay'){
                EmployeeStore::create([
                    'user_id' => $user->id,
                    'store_id' => $request['store_id'],
                ]);
            }

            /* Employee Multiple Store */
            if($request['store_ids'] && $request['status'] == 'mobile'){
                foreach ($request['store_ids'] as $storeId) {
                    EmployeeStore::create([
                        'user_id' => $user->id,
                        'store_id' => $storeId,
                    ]);
                }
            }
            
        // If DM or Trainer
        if(isset($request->area)){
            if($request['role'] == 'DM') {
                $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 
                    // 'dedicate' => $request->dedicate
                    ]);
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

        if(in_array($user->role->role_group, $promoterArray)){
            $this->generateAttendace($user->id);
        }
        
        if($request['store_id']){
            $newEmStore = $request->store_id;
            HistoryEmployeeStore::create([
                            'user_id' => $user->id,
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
                            'user_id' => $user->id,
                            'month' => Carbon::now()->format('m'),
                            'year' => Carbon::now()->format('Y'),
                            'details' => $newEmStore2,
                    ]);
        }

        if($request['selectedRole'] == 'Salesman Explorer' || $request['selectedRole'] == 'SMD'){
            if (isset($request['salesman_dedicate'])) {
                SalesmanDedicate::create([
                    'user_id' => $user->id,
                    'dedicate' => $request['salesman_dedicate'],
                ]);
            }
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
        $data = User::
            where('users.id', $id)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->leftJoin('employee_stores','users.id','employee_stores.user_id')
            ->leftJoin('stores','employee_stores.store_id','stores.id')
            ->select('users.*', 'stores.dedicate as dedicate', 'roles.id as role_id', 'roles.role_group as role_group', 'roles.role as role', 'gradings.id as grading_id', 'gradings.grading as grading')
            ->first();

        if($data){

            if ($data->role_group == 'Salesman Explorer' || $data->role_group == 'SMD') {
                $salesmanDedicate = SalesmanDedicate::where('user_id',$data->id)
                    ->first();
                // $salesmanDedicate = $data->id;
            }

        }

        // return response()->json($salesmanDedicate);  

        return view('master.form.userpromoter-form', compact('data','salesmanDedicate'));
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

        // return response()->json($request['store_id']);
        if (!empty($request->nik)) {
            $user = User::where('nik', $request->nik);
            if($user->count() > 0){
                $oldUser = User::find($id);
                if($oldUser->nik != $request['nik']){
                    return response()->json(['error'=>"NIK already exist!"]);
                }
            }
        }

        $user = User::where('email', $request->email);
        if($user->count() > 0){
            $oldUser = User::find($id);
            if($oldUser->email != $request['email']){
                return response()->json(['error'=>"Email already exist!"]);
            }
        }

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users'. ($id ? ",email,$id" : ''),
            'role_id' => 'required',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $user = User::find($id);
        $oldPhoto = "";

        $role = explode('`',$request['role_id']);
        $request['selectedRole'] = $role[1];

        if($user->photo != null && $request->photo_file != null) {
            /* Save old photo path */
            $oldPhoto = $user->photo;
        }

        /* Delete if any relation exist in employee store */
        $empStore = EmployeeStore::where('user_id', $user->id);
        if($empStore->count() > 0){
            $empStore->delete();
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


        $requestNew['certificate'] = $request['certificate'];
        $requestNew['name'] = $request['name'];
        $requestNew['email'] = $request['email'];
        $requestNew['role_id'] = $request['role_id'];

        $requestNew['status'] = null;
        $requestNew['nik'] = null;

        if($request['status']){
            $requestNew['status'] = $request['status'];
        }

        if($request['nik']){
            $requestNew['nik'] = $request['nik'];
        }

        $requestNew['grading_id'] = null;

        if($request['grading_id']){
            $requestNew['grading_id'] = $request['grading_id'];
        }

        $requestNew['join_date'] = $request['join_date'];

        $user->update($requestNew->all()); 

        /* Insert user relation */

            if($request['selectedRole'] == 'Salesman Explorer' || $request['selectedRole'] == 'SMD'){
                if (isset($request['salesman_dedicate'])) {
                    $salesmanDedicate = SalesmanDedicate::where('user_id',$user->id);
                    if ($salesmanDedicate->count() >0) {
                        $salesmanDedicate->delete();
                    }
                    SalesmanDedicate::create([
                        'user_id' => $user->id,
                        'dedicate' => $request['salesman_dedicate'],
                    ]);
                }
            }
        
            EmployeeStore::where('user_id',$user->id)->delete();

            if ($request['status'] == 'stay') {
                /* Employee One Store */
                if($request['store_id']){
                    // $store = Store::find($request['store_id'])->update(['user_id'=>$user->id]);
                    EmployeeStore::create([
                        'user_id' => $user->id,
                        'store_id' => $request['store_id'],
                    ]);
                }
            }else{
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

        $emStore2='';
            if (isset($emStore->details)) {
                $emStore2 = $emStore->details;
            }
        
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

        // UPDATE RESIGN = 0
        $user->update(['is_resign' => 0]);

        // UPDATE ATTENDANCE
        $attendance = Attendance::onlyTrashed()->where('user_id', $user->id);
        $cek = [];
            foreach ($attendance as $key => $value) {
                $attendanceDetail = AttendanceDetail::onlyTrashed()->where('attendance_id', $value->id);
                $cek[] = $value;
                if($attendanceDetail->count() > 0){
                    $attendanceDetail->restore();
                }            
            }
        if($attendance->count() > 0){
            $attendance->restore();
        }


        return response()->json(
            [
                'url' => url('userpromoter'),
                'method' => $request->_method
            ]);
    }

    // update new phone for user
    public function updatehp($id)
    {   
        $user = User::find($id); 

        $attendance = Attendance::where('user_id', $user->id)->where('date', Carbon::now()->format('Y-m-d'))->first();

        if($attendance){

            $attendanceDetail = AttendanceDetail::where('attendance_id', $attendance->id)->orderBy('id', 'DESC')->first();

            if($attendanceDetail){

                $attendanceDetail->update([
                    'check_out' => Carbon::now()->format('h:i:s'),
                ]);

            }

        }

        $user->update([
            'status_login' => 'Logout',
            'hp_id' => null,
            'jenis_hp' => null,
            'fcm_token' => null,
            ]);

        return response()->json($id);
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

    public function resign(Request $request)
    {
        // return response()->json($request->employeeId);

        $id = $request->employeeId;

        // Update Is Resign
        $user = User::where('id', $id)->first();

        $user->update(['is_resign' => 1, 'alasan_resign' => $request->alasan_resign]);

        /* Deleting related to user */

        /* Delete if any relation exist in employee store */
        $empStore = EmployeeStore::where('user_id', $id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        // News Reads
        $newsRead = NewsRead::where('user_id', $id);
        if($newsRead->count() > 0){
            $newsRead->delete();
        }

        // Guidelines Reads
        $guidelinesRead = ProductKnowledgeRead::where('user_id', $id);
        $updateGuidelinesRead = $guidelinesRead->get();
        if($guidelinesRead->count() > 0){

            // update total read Guidelines
            foreach ($updateGuidelinesRead as $key => $value) {
                $guidelines = ProductKnowledge::where('id',$value->productknowledge_id)->decrement('total_read');
            }

            $guidelinesRead->delete();
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

        return response()->json(['url' => url('resign')]);
        // return response()->json($id);
    }

    public function getData($id)
    {
        $data = User::with('role', 'grading')->where('id', $id)->first();

        return response()->json($data);
    }
}
