<?php

namespace App\Http\Controllers;

use App\User;
use App\RsmRegion;
use App\DmArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Auth;

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

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('user/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
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

       	// Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "user/".$this->getRandomPath()) : $photo_url = "";        

        if($request->photo_file != null) $request['photo'] = $photo_url;

        $user = User::create($request->all());

        // If DM
        if($request->area){
            $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
        }

        // If RSM
        if($request->region){
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

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "user/".$this->getRandomPath()) : $photo_url = "";        

        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Check if password empty
        if($request['password']){

        	$request['password'] = bcrypt($request['password']);
        	$user->update($request->all());

        }else{

        	if($photo_url != ""){

        		$user->update([
	        			'name' => $request['name'],
	        			'email' => $request['email'],
	        			'role' => $request['role'],    
	        			'photo' => $request['photo']
	        		]);

        	}else{

	        	$user->update([
	        			'name' => $request['name'],
	        			'email' => $request['email'],
	        			'role' => $request['role'],        			
	        		]);

        	}

        }

        // If DM
        if($request->area){
            $dmArea = DmArea::where('user_id', $user->id)->first();
            $dmArea->update(['area_id' => $request->area]);
        }

        // If RSM
        if($request->region){
            $rsmRegion = RsmRegion::where('user_id', $user->id)->first();
            $rsmRegion->update(['region_id' => $request->region]);

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
        $user = User::destroy($id);

        return response()->json($id);
    }
}

