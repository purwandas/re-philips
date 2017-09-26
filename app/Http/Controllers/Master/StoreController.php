<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use DB;
use App\Store;

class StoreController extends Controller
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
        return view('master.store');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Store::where('stores.deleted_at', null)
        			->join('accounts', 'stores.account_id', '=', 'accounts.id')
                    ->join('area_apps', 'stores.areaapp_id', '=', 'area_apps.id')
                    // ->join('users', 'stores.user_id', '=', 'users.id')
                    ->select('stores.*', 'accounts.name as account_name', 'area_apps.name as areaapp_name', 'users.name as spv_name')->get();

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
        $this->validate($request, [
            'store_name_1' => 'required|string|max:255',
            'store_name_2' => 'string|max:255',
            'longitude' => 'number',
            'latitude' => 'number',
            'channel' => 'required',
            'account_id' => 'required',
            'areaapp_id' => 'required',
            'user_id' => 'required'
        ]);

        $store = Store::create($request->all());
        
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
            'store_name_2' => 'string|max:255',
            'longitude' => 'number',
            'latitude' => 'number',
            'channel' => 'required',
            'account_id' => 'required',
            'areaapp_id' => 'required',
            'user_id' => 'required'
        ]);

        $store = Store::find($id)->update($request->all());

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
        $store = Store::destroy($id);

        return response()->json($id);
    }
}
