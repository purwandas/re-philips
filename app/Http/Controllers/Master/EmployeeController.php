<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Employee;
use App\EmployeeStore;
use App\Filters\EmployeeFilters;

class EmployeeController extends Controller
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
        return view('master.employee');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Employee::all();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(EmployeeFilters $filters){ 
        $data = Employee::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('employee/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
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
        return view('master.form.employee-form');
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
        	'nik' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'password' => 'required|string|min:3|confirmed',
            'role' => 'required',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);        

       	$request['password'] = bcrypt($request['password']);        

       	// Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "employee/".$this->getRandomPath()) : $photo_url = "";        

        if($request->photo_file != null) $request['photo'] = $photo_url;

        $employee = Employee::create($request->all());

        /* Employee One Store */
        if($request['store_id']){
            EmployeeStore::create([
                'employee_id' => $employee->id,
                'store_id' => $request['store_id'],
            ]);
        }

        /* Employee Multiple Store */
        if($request['store_ids']){
            foreach ($request['store_ids'] as $storeId) {
                EmployeeStore::create([
                    'employee_id' => $employee->id,
                    'store_id' => $storeId,
                ]);
            }
        }
        
        return response()->json(['url' => url('/employee')]);
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
        $data = Employee::where('id', $id)->first();

        return view('master.form.employee-form', compact('data'));
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
            'nik' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees'. ($id ? ",id,$id" : ''),
            'role' => 'required',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $employee = Employee::find($id);

        /* Delete if any relation exist in employee store */
        $empStore = EmployeeStore::where('employee_id', $employee->id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "employee/".$this->getRandomPath()) : $photo_url = "";        

        if($request->photo_file != null) $request['photo'] = $photo_url;

       $requestNew = new Request;

        // Check if password empty
        if($request['password']){

        	$requestNew['password'] = bcrypt($request['password']);

        }

        if($photo_url != ""){

    		$requestNew['photo'] = $request['photo'];

    	}

    	if($request['status']){
    		$requestNew['status'] = $request['status'];
    	}

    	$requestNew['nik'] = $request['nik'];
    	$requestNew['name'] = $request['name'];
    	$requestNew['email'] = $request['email'];
    	$requestNew['role'] = $request['role'];

    	$employee->update($requestNew->all());        

        /* Employee One Store */
        if($request['store_id']){
            EmployeeStore::create([
                'employee_id' => $employee->id,
                'store_id' => $request['store_id'],
            ]);
        }

        /* Employee Multiple Store */
        if($request['store_ids']){
            foreach ($request['store_ids'] as $storeId) {
                EmployeeStore::create([
                    'employee_id' => $employee->id,
                    'store_id' => $storeId,
                ]);
            }
        }

        return response()->json(
            [
                'url' => url('/employee'),
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
        // Employee Store
        $empStore = EmployeeStore::where('employee_id', $id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        $employee = Employee::destroy($id);

        return response()->json($id);
    }
}
