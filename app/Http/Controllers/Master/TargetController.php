<?php

namespace App\Http\Controllers\Master;

use App\Reports\SummaryTargetActual;
use App\Traits\PromoterTrait;
use App\Traits\TargetTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\TargetFilters;
use App\Traits\StringTrait;
use DB;
use App\Target;

class TargetController extends Controller
{
    use StringTrait;
    use TargetTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.target');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Target::where('targets.deleted_at', null)
        			->join('users', 'targets.user_id', '=', 'users.id')
                    ->join('stores', 'targets.store_id', '=', 'stores.id')
                    ->join('group_products', 'targets.groupproduct_id', '=', 'group_products.id')
                    ->select('targets.*', 'users.name as promoter_name', 'stores.store_id as store_name', 'group_products.name as groupproduct_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(TargetFilters $filters){
        $data = Target::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#target' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-target'><i class='fa fa-pencil'></i></a>
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
            'user_id' => 'required',
            'store_id' => 'required',
            'groupproduct_id' => 'required',
            'type' => 'required',
            'target' => 'required|numeric'
            ]);

        $target = Target::where('user_id', $request['user_id'])
                    ->where('store_id', $request['store_id'])
                    ->where('groupproduct_id', $request['groupproduct_id']);

        if($target->count() > 0){

            $targetOld = $target->first()->target;

            $target->update(['target'=>$request->target]);

            /* Summary Target Add and/or Change */
            $summary['user_id'] = $target->user_id;
            $summary['store_id'] = $target->store_id;
            $summary['groupproduct_id'] = $target->groupproduct_id;
            $summary['targetOld'] = $targetOld;
            $summary['target'] = $target->target;
            $summary['type'] = $target->type;

            $this->changeTarget($summary, 'change');

        }else{
            $target = Target::create($request->all());

            /* Summary Target Add and/or Change */
            $summary['user_id'] = $target->user_id;
            $summary['store_id'] = $target->store_id;
            $summary['groupproduct_id'] = $target->groupproduct_id;
            $summary['target'] = $target->target;
            $summary['type'] = $target->type;

            $this->changeTarget($summary, 'change');
        }

        return response()->json(['url' => url('/target')]);
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
        $data = Target::with('user', 'store', 'groupProduct')->where('id', $id)->first();

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
            'user_id' => 'required',
            'store_id' => 'required',
            'groupproduct_id' => 'required',
            'type' => 'required',
            'target' => 'required|numeric'
            ]);

        $target = Target::find($id);

        $targetOld = $target->target;

        $target->update($request->all());

        /* Summary Target Add and/or Change */
        $summary['user_id'] = $target->user_id;
        $summary['store_id'] = $target->store_id;
        $summary['groupproduct_id'] = $target->groupproduct_id;
        $summary['targetOld'] = $targetOld;
        $summary['target'] = $target->target;
        $summary['type'] = $target->type;

        $this->changeTarget($summary, 'change');

        return response()->json(
            ['url' => url('/target'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $target = Target::where('id', $id)->first();

        /* Summary Target Delete */
        $summary['user_id'] = $target->user_id;
        $summary['store_id'] = $target->store_id;
        $summary['groupproduct_id'] = $target->groupproduct_id;
        $summary['target'] = $target->target;
        $summary['type'] = $target->type;

        $this->changeTarget($summary, 'delete');

        $target->delete();

        /* Chage promoter title from Hybrid to One Dedicate */
        $this->changePromoterTitle($summary['user_id'], $summary['store_id']);

        return response()->json($id);
    }
}
