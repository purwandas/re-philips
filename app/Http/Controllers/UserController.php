<?php

namespace App\Http\Controllers;

use App\Store;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Auth;
use App\Filters\UserFilters;
use File;
use App\NewsRead;
use App\ProductKnowledgeRead;

class UserController extends Controller
{
    use UploadTrait;
    use StringTrait;

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
    public function masterDataTable(){

        $data = User::where('id', '<>', Auth::user()->id);

        return $this->makeTable($data);
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
    

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('user/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
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

                    $countStore = EmployeeStore::where('user_id', $item->id)->count();

                    if($countStore > 0){
                        return
                        "<a class='open-employee-store-modal btn btn-primary' data-target='#employee-store-modal' data-toggle='modal' data-url='util/empstore' data-title='List Store' data-promoter-name='".$item->name."' data-id='".$item->id."'> See Details </a>";
                    }

                    return;

                })
                ->rawColumns(['store', 'action'])
                ->make(true);
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
            $photo_url = $this->imageUpload($request->photo_file, "user/".$this->getRandomPath()) : $photo_url = "";        
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        $user = User::create($request->all());

        /* Employee One Store */
        if($request['store_id']){
            EmployeeStore::create([
                'user_id' => $user->id,
                'store_id' => $request['store_id'],
            ]);
        }

        /* Employee Multiple Store */
        if($request['store_ids']){
            foreach ($request['store_ids'] as $storeId) {
                EmployeeStore::create([
                    'user_id' => $user->id,
                    'store_id' => $storeId,
                ]);
            }
        }

        // If DM
        if(isset($request->area)){
            $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
        }
        // If RSM
        if(isset($request->region)){
            $rsmRegion = RsmRegion::create(['user_id' => $user->id, 'region_id' => $request->region]);
        }
        
        return response()->json(['url' => url('user')]);
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

        return view('master.form.user-form', compact('data'));
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

        /* Delete Image */
        if($user->photo != "") {
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);
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

        // RSM REGION 
        $rsmRegion = RsmRegion::where('user_id', $user->id);
        if($rsmRegion->count() > 0){
            $rsmRegion->delete();
        }
        /* ================================================= */

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "user/".$this->getRandomPath()) : $photo_url = "";        
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Create new request
        $requestNew = new Request;

        // Check if password empty
        if($request['password']){

            $requestNew['password'] = bcrypt($request['password']);

        }

        if($photo_url != ""){

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

        /* Employee One Store */
        if($request['store_id']){
            EmployeeStore::create([
                'user_id' => $user->id,
                'store_id' => $request['store_id'],
            ]);
        }

        /* Employee Multiple Store */
        if($request['store_ids']){
            foreach ($request['store_ids'] as $storeId) {
                EmployeeStore::create([
                    'user_id' => $user->id,
                    'store_id' => $storeId,
                ]);
            }
        } 

        // If DM
        if($request->area){
            $dmArea = DmArea::where('user_id', $user->id);
            if($dmArea->count() > 0){
                $dmArea->first()->update(['area_id' => $request->area]);    
            }else{
                DmArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
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
        return response()->json(
            [
                'url' => url('user'),
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
