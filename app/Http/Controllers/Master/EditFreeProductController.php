<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Filters\FreeProductFilters;

class EditFreeProductController extends Controller
{
    use StringTrait;
    use SalesTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.freeproduct');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = FreeProduct::
                    where('free_products.deleted_at', null)
                    ->where('free_product_details.deleted_at', null)
        			->join('free_product_details', 'free_products.id', '=', 'free_product_details.freeproduct_id')
                    ->join('stores', 'free_products.store_id', '=', 'stores.id')
                    ->join('users', 'free_products.user_id', '=', 'users.id')
                    ->join('products', 'free_product_details.product_id', '=', 'products.id')
                    ->select('free_products.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'free_product_details.id as id', 'free_product_details.quantity as quantity', 'products.name as product')->get();

        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#editfreeproduct' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-freeproduct'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['action'])
                ->make(true);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = FreeProductDetail::where('id', $id)->first();

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
            'quantity' => 'required',
            ]);

        $this->updateFreeProduct($id, $request['quantity']);

        return response()->json(
            ['url' => url('/editfreeproduct'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteFreeProduct($id);

        return response()->json($id);
    }
}
