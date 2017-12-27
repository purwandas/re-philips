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
                    ->select('targets.*', 'users.name as promoter_name', DB::raw('CONCAT(stores.store_id, " - ", stores.store_name_1, " (", stores.store_name_2, ") - ", stores.dedicate) AS store_name'))->get();

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
            'sell_type' => 'required',
            'target_da' => 'numeric',
            'target_pf_da' => 'numeric',
            'target_pc' => 'numeric',
            'target_pf_pc' => 'numeric',
            'target_mcc' => 'numeric',
            'target_pf_mcc' => 'numeric',
            ]);

        $target = Target::where('user_id', $request['user_id'])
                    ->where('store_id', $request['store_id'])
                    ->where('sell_type', $request['sell_type']);

        if($target->count() > 0){

            $targetOldDa = $target->first()->target_da;
            $targetOldPfDa = $target->first()->target_pf_da;
            $targetOldPc = $target->first()->target_pc;
            $targetOldPfPc = $target->first()->target_pf_pc;
            $targetOldMcc = $target->first()->target_mcc;
            $targetOldPfMcc = $target->first()->target_pf_mcc;

            $target->update(['target_da'=>$request->target_da]);
            $target->update(['target_pf_da'=>$request->target_pf_da]);
            $target->update(['target_pc'=>$request->target_pc]);
            $target->update(['target_pf_pc'=>$request->target_pf_pc]);
            $target->update(['target_mcc'=>$request->target_mcc]);
            $target->update(['target_pf_mcc'=>$request->target_pf_mcc]);

            /* Summary Target Add and/or Change */ // On Progress
            $summary['user_id'] = $target->first()->user_id;
            $summary['store_id'] = $target->first()->store_id;
            $summary['targetOldDa'] = $targetOldDa;
            $summary['targetOldPfDa'] = $targetOldPfDa;
            $summary['targetOldPc'] = $targetOldPc;
            $summary['targetOldPfPc'] = $targetOldPfPc;
            $summary['targetOldMcc'] = $targetOldMcc;
            $summary['targetOldPfMcc'] = $targetOldPfMcc;
            $summary['target_da'] = $target->first()->target_da;
            $summary['target_pf_da'] = $target->first()->target_pf_da;
            $summary['target_pc'] = $target->first()->target_pc;
            $summary['target_pf_pc'] = $target->first()->target_pf_pc;
            $summary['target_mcc'] = $target->first()->target_mcc;
            $summary['target_pf_mcc'] = $target->first()->target_pf_mcc;
            $summary['sell_type'] = $target->first()->sell_type;

            $this->changeTarget($summary, 'change');

            return response()->json(['url' => url('/target'), 'method' => 'PATCH']);

        }else{
            $target = Target::create($request->all());

            /* Summary Target Add and/or Change */ // On Progress
            $summary['user_id'] = $target->user_id;
            $summary['store_id'] = $target->store_id;
            $summary['target_da'] = $target->target_da;
            $summary['target_pf_da'] = $target->target_pf_da;
            $summary['target_pc'] = $target->target_pc;
            $summary['target_pf_pc'] = $target->target_pf_pc;
            $summary['target_mcc'] = $target->target_mcc;
            $summary['target_pf_mcc'] = $target->target_pf_mcc;
            $summary['sell_type'] = $target->sell_type;

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
        $data = Target::with('user', 'store')->where('id', $id)->first();

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
            'sell_type' => 'required',
            'target_da' => 'numeric',
            'target_pf_da' => 'numeric',
            'target_pc' => 'numeric',
            'target_pf_pc' => 'numeric',
            'target_mcc' => 'numeric',
            'target_pf_mcc' => 'numeric',
            ]);

        $target = Target::where('id', $id)->first();

        $targetOldDa = $target->target_da;
        $targetOldPfDa = $target->target_pf_da;
        $targetOldPc = $target->target_pc;
        $targetOldPfPc = $target->target_pf_pc;
        $targetOldMcc = $target->target_mcc;
        $targetOldPfMcc = $target->target_pf_mcc;

        $target->update($request->all());

        /* Summary Target Add and/or Change */
        $summary['user_id'] = $target->user_id;
        $summary['store_id'] = $target->store_id;
        $summary['targetOldDa'] = $targetOldDa;
        $summary['targetOldPfDa'] = $targetOldPfDa;
        $summary['targetOldPc'] = $targetOldPc;
        $summary['targetOldPfPc'] = $targetOldPfPc;
        $summary['targetOldMcc'] = $targetOldMcc;
        $summary['targetOldPfMcc'] = $targetOldPfMcc;
        $summary['target_da'] = $target->target_da;
        $summary['target_pf_da'] = $target->target_pf_da;
        $summary['target_pc'] = $target->target_pc;
        $summary['target_pf_pc'] = $target->target_pf_pc;
        $summary['target_mcc'] = $target->target_mcc;
        $summary['target_pf_mcc'] = $target->target_pf_mcc;
        $summary['sell_type'] = $target->sell_type;

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
        $summary['target_da'] = $target->target_da;
        $summary['target_pf_da'] = $target->target_pf_da;
        $summary['target_pc'] = $target->target_pc;
        $summary['target_pf_pc'] = $target->target_pf_pc;
        $summary['target_mcc'] = $target->target_mcc;
        $summary['target_pf_mcc'] = $target->target_pf_mcc;
        $summary['sell_type'] = $target->sell_type;

        $this->changeTarget($summary, 'delete');

        /* Chage promoter title from Hybrid to One Dedicate */
        $this->changePromoterTitle($summary['user_id'], $summary['store_id'], $summary['sell_type']);

        $target->delete();

        return response()->json($id);
    }
}
