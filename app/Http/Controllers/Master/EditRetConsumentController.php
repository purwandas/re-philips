<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use App\RetConsument;
use App\RetConsumentDetail;
use App\Filters\RetConsumentFilters;

class EditRetConsumentController extends Controller
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
        return view('master.retconsument');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = RetConsument::
                    where('ret_consuments.deleted_at', null)
                    ->where('ret_consument_details.deleted_at', null)
        			->join('ret_consument_details', 'ret_consuments.id', '=', 'ret_consument_details.retconsument_id')
                    ->join('stores', 'ret_consuments.store_id', '=', 'stores.id')
                    ->join('users', 'ret_consuments.user_id', '=', 'users.id')
                    ->join('products', 'ret_consument_details.product_id', '=', 'products.id')
                    ->select('ret_consuments.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'ret_consument_details.id as id', 'ret_consument_details.quantity as quantity', 'products.name as product')->get();

        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#editretconsument' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-retconsument'><i class='fa fa-pencil'></i></a>
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
        $data = RetConsumentDetail::where('id', $id)->first();

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

        $this->updateRetConsument($id, $request['quantity']);

        return response()->json(
            ['url' => url('/editretconsument'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteRetConsument($id);

        return response()->json($id);
    }
}
