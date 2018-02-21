<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\GroupFilters;
use App\Filters\PosmFilters;
use App\Traits\StringTrait;
use DB;
use App\Posm;

class PosmController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.posm');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Posm::where('posms.deleted_at', null)
            ->join('groups', 'posms.group_id', '=', 'groups.id')
            ->select('posms.*', 'groups.name as group_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(PosmFilters $filters){
        $data = Posm::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
            ->addColumn('action', function ($item) {

                return
                    "<a href='#posm' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-posm'><i class='fa fa-pencil'></i></a>
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
            'name' => 'required|string|max:255',
            'group_id' => 'required'
        ]);

        $posm = Posm::create($request->all());

        return response()->json(['url' => url('/posm')]);
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
        $data = Posm::with('group')->where('id', $id)->first();

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
            'name' => 'required|string|max:255',
            'group_id' => 'required'
        ]);

        $posm = Posm::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/posm'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $posm = Posm::destroy($id);

        return response()->json($id);
    }
}
