<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use Auth;
use Carbon\Carbon;
use App\Store;
use App\RsmRegion;
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
    public function masterDataTable(Request $request){

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $data = FreeProduct::
                    where('free_products.deleted_at', null)
                    ->where('free_product_details.deleted_at', null)
        			->join('free_product_details', 'free_products.id', '=', 'free_product_details.freeproduct_id')
                    ->join('stores', 'free_products.store_id', '=', 'stores.id')
                    
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')

                    
                    ->join('users', 'free_products.user_id', '=', 'users.id')
                    ->join('products', 'free_product_details.product_id', '=', 'products.id')
                    ->select('free_products.*', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'free_product_details.id as id', 'free_product_details.quantity as quantity', 'products.name as product',
                        'stores.id as storeId', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id')
                    ->get();

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                $data = $data->whereIn('store_id', $store);
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $filter->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $filter->where('region_id', $value->region_id);
                }
            }

        return $this->makeTable($filter);
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
