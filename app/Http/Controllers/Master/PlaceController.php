<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Place;
use App\Filters\PlaceFilters;
use Validator;

class PlaceController extends Controller
{
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.place');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Place::all();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(PlaceFilters $filters){        
        $data = Place::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                   return 
                    "<a href='#place' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-place'><i class='fa fa-pencil'></i></a>
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
        $this->validate($request, [
            'store_id' => 'required',
            'name' => 'required|string|max:255',
            ]);

       	$place = Place::create($request->all());
        
        return response()->json(['url' => url('/place')]);
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
        $data = Place::find($id);

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
            'store_id' => 'required',
            'name' => 'required|string|max:255',
            ]);

        $place = Place::find($id)->update($request->all());

        return response()->json(
            [
                'url' => url('/place'),
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
        $place = Place::destroy($id);

        return response()->json($id);
    }
}
