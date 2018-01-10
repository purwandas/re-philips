<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\RetDistributorFilters;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use Auth;
use App\Store;
use App\RetDistributor;
use App\RetDistributorDetail;

class EditRetDistributorController extends Controller
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
        return view('master.retdistributor');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        $data = RetDistributor::
                    where('ret_distributors.deleted_at', null)
                    ->where('ret_distributor_details.deleted_at', null)
        			->join('ret_distributor_details', 'ret_distributors.id', '=', 'ret_distributor_details.retdistributor_id')
                    ->join('stores', 'ret_distributors.store_id', '=', 'stores.id')
                    ->join('users', 'ret_distributors.user_id', '=', 'users.id')
                    ->join('products', 'ret_distributor_details.product_id', '=', 'products.id')
                    ->select('ret_distributors.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'ret_distributor_details.id as id', 'ret_distributor_details.quantity as quantity', 'products.name as product')->get();

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                $data = $data->whereIn('store_id', $store);
            }
        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ChannelFilters $filters){
        $data = Channel::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#editretdistributor' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-retdistributor'><i class='fa fa-pencil'></i></a>
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
        $data = RetDistributorDetail::where('id', $id)->first();

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

        $this->updateRetDistributor($id, $request['quantity']);

        return response()->json(
            ['url' => url('/retdistributor'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteRetDistributor($id);

        return response()->json($id);
    }
}
