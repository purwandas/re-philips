<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\SubChannelFilters;
use App\Traits\StringTrait;
use DB;
use App\SubChannel;

class SubChannelController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.subchannel');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = SubChannel::where('sub_channels.deleted_at', null)
        			->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('sub_channels.*', 'channels.name as channel_name', 'global_channels.name as globalchannel_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(SubChannelFilters $filters){
        $data = SubChannel::filter($filters)->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('sub_channels.*', 'channels.name as channel_name', 'global_channels.name as globalchannel_name')->get();

        return $data;
    }

    public function getDataWithFiltersCheck(SubChannelFilters $filters){
        $data = SubChannel::filter($filters)->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('sub_channels.*', 'channels.name as channel_name', 'global_channels.name as globalchannel_name')
                    ->limit(1)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#subchannel' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-subchannel'><i class='fa fa-pencil'></i></a>
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
            'channel_id' => 'required',
            ]);

       	$subChannel = SubChannel::create($request->all());

        return response()->json(['url' => url('/subchannel')]);
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
        $data = SubChannel::with('channel')->where('id', $id)->first();

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
            'channel_id' => 'required',
            ]);

        $subChannel = SubChannel::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/subchannel'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subChannel = SubChannel::destroy($id);

        return response()->json($id);
    }
}
