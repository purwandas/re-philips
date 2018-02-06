<?php

namespace App\Http\Controllers\Master;

use App\TrainerArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use DB;
use App\Role;
use App\Filters\RoleFilters;
use Auth;

class RoleController extends Controller
{
    //
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.role');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Role::where('roles.deleted_at', null)->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(RoleFilters $filters){     
        $userId = Auth::user()->id;       

        $data = Role::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return 
                    "<a href='#roleModal' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-role'><i class='fa fa-pencil'></i></a>
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	// return $request->all();

        $this->validate($request, [
            'role' => 'required',
            'role_group' => 'required',
            ]);

       	$role = Role::create($request->all());
        
        return response()->json(['url' => url('/role')]);
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
        $data = Role::where('id', $id)->first();

        // return view('master.form.area-form', compact('data'));
        return response()->json($data);
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
            'role' => 'required',
            'role_group' => 'required',
            ]);

        $role = Role::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/role'), 'method' => $request->_method]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Deleting related to role */
        $role = Role::destroy($id);

        return response()->json($id);
    }
}
