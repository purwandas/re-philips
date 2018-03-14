<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\TimeGoneFilters;
use App\Traits\StringTrait;
use DB;
use App\Area;
use Auth;
use App\TimeGone;

class TimeGoneController extends Controller
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
        return view('master.timegone');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = TimeGone::where('time_gones.deleted_at', null)
                    ->select('time_gones.*')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(TimeGoneFilters $filters){
        $data = TimeGone::filter($filters)->get();

        return $data;
    }

    public function getDataWithFiltersCheck(TimeGoneFilters $filters){
        $data = TimeGone::filter($filters)->limit(1)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#timegone' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-timegone'><i class='fa fa-pencil'></i></a>
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
        	'day' => 'required',          
            'percent' => 'required',
            ]);

        $timegone = Timegone::where('day', $request['day']);

        if($timegone->count() == 0){

            TimeGone::create($request->all());

        }else{

            $timegone->update([
                'percent' => $request['percent'],
            ]);

        }

        return response()->json(['url' => url('/timegone')]);
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
        $data = TimeGone::where('id', $id)->first();

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
            'day' => 'required',
            'percent' => 'required',
            ]);

        $timegone = TimeGone::where('day', $request['day']);

        if($timegone->count() == 0){

            $timegone->update($request->all());

        }else{

            TimeGone::find($id)->update([
                'percent' => $request['percent'],
            ]);

        }

        return response()->json(
            ['url' => url('/timegone'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timegone = TimeGone::destroy($id);

        return response()->json($id);
    }
}
