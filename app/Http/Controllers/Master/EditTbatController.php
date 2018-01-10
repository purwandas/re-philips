<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use Auth;
use App\Store;
use App\Tbat;
use App\TbatDetail;
use App\Filters\TbatFilters;

class EditTbatController extends Controller
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
        return view('master.tbat');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        $data = Tbat::
                    where('tbats.deleted_at', null)
                    ->where('tbat_details.deleted_at', null)
        			->join('tbat_details', 'tbats.id', '=', 'tbat_details.tbat_id')
                    ->join('stores', 'tbats.store_id', '=', 'stores.id')
                    ->join('stores as storesD', 'tbats.store_destination_id', '=', 'storesD.id')
                    ->join('users', 'tbats.user_id', '=', 'users.id')
                    ->join('products', 'tbat_details.product_id', '=', 'products.id')
                    ->select('tbats.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'storesD.store_id as storeD_id', 'storesD.store_name_1 as storeD_name_1', 'storesD.store_name_2 as storeD_name_2', 'storesD.dedicate as dedicateD', 'tbat_details.id as id', 'tbat_details.quantity as quantity', 'products.name as product')->get();

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                $data = $data->whereIn('store_id', $store);
            }
        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#edittbat' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-tbat'><i class='fa fa-pencil'></i></a>
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
        $data = TbatDetail::where('id', $id)->first();

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

        $this->updateTbat($id, $request['quantity']);

        return response()->json(
            ['url' => url('/edittbat'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteTbat($id);

        return response()->json($id);
    }
}
