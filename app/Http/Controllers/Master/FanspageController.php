<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Filters\FanspageFilters;
use App\Fanspage;

class FanspageController extends Controller
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
        return view('master.fanspage');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Fanspage::all();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(FanspageFilters $filters){
        $data = Fanspage::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                   return
                    "<a href='#fanspage' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-fanspage'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->editColumn('url', function ($item) {

                   return "<a href='$item->url' target='_blank'>$item->url</a>";

                })
                ->rawColumns(['action','url'])
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
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            ]);

        $fanspage = Fanspage::create($request->all());

        return response()->json(['url' => url('/fanspage')]);
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
        $data = Fanspage::find($id);

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
            'url' => 'required|string|max:255',
            ]);

        $fanspage = Fanspage::find($id)->update($request->all());

        return response()->json(
            [
                'url' => url('/fanspage'),
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

        $fanspage = Fanspage::destroy($id);

        return response()->json($id);
    }

}

