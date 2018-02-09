<?php

namespace App\Http\Controllers\Master;

use App\TrainerArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\AreaFilters;
use App\Traits\StringTrait;
use DB;
use App\SellIn;
use App\Product;
use App\DmArea;
use Auth;
use App\Store;
use App\Region;
use App\RsmRegion;

class SellInController extends Controller
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
        return view('master.sell-in');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = SellIn::where('sell_ins.deleted_at', null)
                    ->join('stores', 'sell_ins.store_id', '=', 'stores.id')
                    ->join('sell_in_details', 'sell_ins.id', '=', 'sell_in_details.sellin_id')
                    ->join('products', 'sell_in_details.product_id', '=', 'products.id')
                    ->select('sell_ins.*', 'stores.store_name_1', 'stores.store_name_2', 'sell_in_details.quantity', 'products.name as product')
                    ->get();

        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return 
                    // "<a href='#sell-in' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-area'>
                   //<i class='fa fa-pencil'></i></a>
                    "<button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
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
        return view('master.form.sellin-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	return $request->all();

        // $this->validate($request, [
        //     'name' => 'required|string|max:255',
        //     'region_id' => 'required',
        //     ]);

       	// $area = Area::create($request->all());
        
        // return response()->json(['url' => url('/area')]);
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
        // $data = Area::with('region')->where('id', $id)->first();

        // // return view('master.form.area-form', compact('data'));
        // return response()->json($data);
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
        // $this->validate($request, [
        //     'name' => 'required|string|max:255',
        //     'region_id' => 'required',
        //     ]);

        // $area = Area::find($id)->update($request->all());

        // return response()->json(
        //     ['url' => url('/area'), 'method' => $request->_method]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // /* Deleting related to area */
        // // DM AREA 
        // $dmArea = DmArea::where('area_id', $id);
        // if($dmArea->count() > 0){
        //     $dmArea->delete();
        // }

        // // TRAINER AREA
        // $trainerArea = TrainerArea::where('area_id', $id);
        // if($trainerArea->count() > 0){
        //     $trainerArea->delete();
        // }

        // $area = Area::destroy($id);

        // return response()->json($id);
    }
}
