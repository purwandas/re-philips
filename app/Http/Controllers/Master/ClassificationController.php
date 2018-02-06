<?php

namespace App\Http\Controllers\Master;

use App\TrainerArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use DB;
use App\Classification;
use App\Filters\ClassificationFilters;
use Auth;

class ClassificationController extends Controller
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
        return view('master.classification');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Classification::where('classifications.deleted_at', null)->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ClassificationFilters $filters){     
        $userId = Auth::user()->id;       

        $data = Classification::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return 
                    "<a href='#classificationModal' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-classification'><i class='fa fa-pencil'></i></a>
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
            'classification' => 'required',
            ]);

       	$classification = Classification::create($request->all());
        
        return response()->json(['url' => url('/classification')]);
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
        $data = Classification::where('id', $id)->first();

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
            'classification' => 'required',
            ]);

        $classification = Classification::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/classification'), 'method' => $request->_method]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Deleting related to classification */
        $classification = Classification::destroy($id);

        return response()->json($id);
    }
}
