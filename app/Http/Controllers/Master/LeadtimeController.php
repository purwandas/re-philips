<?php

namespace App\Http\Controllers\Master;

use App\Leadtime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\LeadtimeFilters;
use App\Traits\StringTrait;
use DB;
use App\Area;
use Auth;

class LeadtimeController extends Controller
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
        return view('master.leadtime');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Leadtime::where('leadtimes.deleted_at', null)
                    ->join('areas', 'areas.id', '=', 'leadtimes.area_id')
                    ->select('leadtimes.*', 'areas.name as area_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(LeadtimeFilters $filters){
        $data = Leadtime::filter($filters)->join('areas', 'areas.id', '=', 'leadtimes.area_id')
                    ->select('leadtimes.*', 'areas.name as area_name')->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#leadtime' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-leadtime'><i class='fa fa-pencil'></i></a>
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
            'area_id' => 'required',
            'leadtime' => 'required',
            ]);

        $leadtime = Leadtime::where('area_id', $request['area_id']);

        if($leadtime->count() == 0){

            Leadtime::create($request->all());

        }else{

            $leadtime->update([
                'leadtime' => $request['leadtime'],
            ]);

        }

        return response()->json(['url' => url('/leadtime')]);
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
        $data = Leadtime::with('area')->where('id', $id)->first();

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
            'area_id' => 'required',
            'leadtime' => 'required',
            ]);

        $leadtime = Leadtime::where('area_id', $request['area_id']);

        if($leadtime->count() == 0){

            Leadtime::find($id)->update($request->all());

        }else{

            Leadtime::find($id)->update([
                'leadtime' => $request['leadtime'],
            ]);

        }

        return response()->json(
            ['url' => url('/leadtime'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leadtime = Leadtime::destroy($id);

        return response()->json($id);
    }
}
