<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\ChannelFilters;
use App\Traits\StringTrait;
use DB;
use App\Channel;

class ChannelController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.channel');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Channel::where('channels.deleted_at', null)
        			->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('channels.*', 'global_channels.name as globalchannel_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ChannelFilters $filters){
        $data = Channel::filter($filters)->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')->select('channels.*', 'global_channels.name as globalchannel_name')->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#channel' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-channel'><i class='fa fa-pencil'></i></a>
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
            'name' => 'required',
            'globalchannel_id' => 'required',
            ]);

       	$channel = Channel::create($request->all());

        return response()->json(['url' => url('/channel')]);
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
        $data = Channel::with('globalChannel')->where('id', $id)->first();

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
            'name' => 'required',
            'globalchannel_id' => 'required',
            ]);

        $channel = Channel::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/channel'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $channel = Channel::destroy($id);

        return response()->json($id);
    }
}
