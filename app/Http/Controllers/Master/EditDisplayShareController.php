<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use Auth;
use Store;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\Filters\DisplayShareFilters;

class EditDisplayShareController extends Controller
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
        return view('master.displayshare');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        $data = DisplayShare::
                    where('display_shares.deleted_at', null)
                    ->where('display_share_details.deleted_at', null)
        			->join('display_share_details', 'display_shares.id', '=', 'display_share_details.display_share_id')
                    ->join('stores', 'display_shares.store_id', '=', 'stores.id')
                    ->join('users', 'display_shares.user_id', '=', 'users.id')
                    ->join('categories', 'display_share_details.category_id', '=', 'categories.id')
                    ->select('display_shares.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'display_share_details.id as id', 'display_share_details.philips as philips', 'display_share_details.all as all', 'categories.name as category')->get();

            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                $data = $data->whereIn('store_id', $store);
                }
            }

        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
               ->addColumn('percentage', function ($item) {

                   return round(($item->philips/$item->all)*100,2).'%';

                })
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#editdisplayshare' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-displayshare'><i class='fa fa-pencil'></i></a>
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
        $data = DisplayShareDetail::where('id', $id)->first();

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
            'philips' => 'required',
            'all' => 'required',
            ]);

        $this->updateDisplayShare($id, $request['philips'], $request['all']);
        return response()->json(
            ['url' => url('/editdisplayshare'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteDisplayShare($id);

        return response()->json($id);
    }
}
