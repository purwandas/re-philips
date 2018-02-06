<?php

namespace App\Http\Controllers\Master;

use App\ProductPromos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\StringTrait;
use DB;
use Yajra\Datatables\Facades\Datatables;
use Carbon\Carbon;

class ProductPromoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.productpromo');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = ProductPromos::where('product_promos.deleted_at', null)
        			->join('products', 'product_promos.product_id', '=', 'products.id')
                    ->select('product_promos.*', 'products.name as product_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ProductFocusFilters $filters){
        $data = ProductPromos::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#productpromo' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-productpromo'><i class='fa fa-pencil'></i></a>
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
            'product_id' => 'required',
            ]);

        $productPromo = ProductPromos::where('product_id', $request['product_id']);

        if($productPromo->count() == 0){
            $newProductPromo = ProductPromos::create($request->all());
        }

        return response()->json(['url' => url('/productpromo')]);
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
        $data = ProductPromos::with('product')->where('id', $id)->first();

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
            'product_id' => 'required',
            ]);

        $productPromo = ProductPromos::find($id);

        $productPromoCount = ProductPromos::where('product_id', $request['product_id'])->count();
        if($productPromoCount > 0){
            return;
        }

        $productPromo->update($request->all());

        return response()->json(
            ['url' => url('/productpromo'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productPromo = ProductPromos::find($id);

        $productPromo->delete();

        return response()->json($id);
    }
}
