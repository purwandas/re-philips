<?php

namespace App\Http\Controllers\Master;

use App\Filters\TargetSalesmanFilters;
use App\Traits\TargetTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\TargetFilters;
use App\Traits\StringTrait;
use DB;
use App\SalesmanTarget;

class TargetSalesmanController extends Controller
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
        return view('master.targetsalesman');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = SalesmanTarget::where('salesman_targets.deleted_at', null)
        			->join('users', 'salesman_targets.user_id', '=', 'users.id')
                    ->select('salesman_targets.*', 'users.name as salesman_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(TargetSalesmanFilters $filters){
        $data = SalesmanTarget::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#targetsalesman' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-targetsalesman'><i class='fa fa-pencil'></i></a>
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
            'target_call' => 'numeric',
            'target_active_outlet' => 'numeric',
            'target_effective_call' => 'numeric',
            'target_sales' => 'numeric',
            'target_sales_pf' => 'numeric',
            ]);

        $target = SalesmanTarget::where('user_id', $request['user_id']);

        if($target->count() > 0){

            $targetOldCall = $target->first()->target_call;
            $targetOldActiveOutlet = $target->first()->target_active_outlet;
            $targetOldEffectiveCall = $target->first()->target_effective_call;
            $targetOldSales = $target->first()->target_sales;
            $targetOldSalesPf = $target->first()->target_sales_pf;

            $target->update(['target_call'=>$request->target_call]);
            $target->update(['target_active_outlet'=>$request->target_active_outlet]);
            $target->update(['target_effective_call'=>$request->target_effective_call]);
            $target->update(['target_sales'=>$request->target_sales]);
            $target->update(['target_sales_pf'=>$request->target_sales_pf]);

            /* Summary Target Add and/or Change */ // On Progress
            $summary['user_id'] = $target->first()->user_id;
            $summary['targetOldCall'] = $targetOldCall;
            $summary['targetOldActiveOutlet'] = $targetOldActiveOutlet;
            $summary['targetOldEffectiveCall'] = $targetOldEffectiveCall;
            $summary['targetOldSales'] = $targetOldSales;
            $summary['targetOldSalesPf'] = $targetOldSalesPf;
            $summary['target_call'] = $target->first()->target_call;
            $summary['target_active_outlet'] = $target->first()->target_active_outlet;
            $summary['target_effective_call'] = $target->first()->target_effective_call;
            $summary['target_sales'] = $target->first()->target_sales;
            $summary['target_sales_pf'] = $target->first()->target_sales_pf;

//            return $summary;

            $this->changeTargetSalesman($summary, 'change');

            return response()->json(['url' => url('/targetsalesman'), 'method' => 'PATCH']);

        }else{
            $target = SalesmanTarget::create($request->all());

            /* Summary Target Add and/or Change */ // On Progress
            $summary['user_id'] = $target->user_id;
            $summary['target_call'] = $target->target_call;
            $summary['target_active_outlet'] = $target->target_active_outlet;
            $summary['target_effective_call'] = $target->target_effective_call;
            $summary['target_sales'] = $target->target_sales;
            $summary['target_sales_pf'] = $target->target_sales_pf;

            $this->changeTargetSalesman($summary, 'change');
        }

        return response()->json(['url' => url('/targetsalesman')]);
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
        $data = SalesmanTarget::with('user')->where('id', $id)->first();

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
            'target_call' => 'numeric',
            'target_active_outlet' => 'numeric',
            'target_effective_call' => 'numeric',
            'target_sales' => 'numeric',
            'target_sales_pf' => 'numeric',
            ]);

        $target = SalesmanTarget::where('id', $id)->first();

        $targetOldCall = $target->target_call;
        $targetOldActiveOutlet = $target->target_active_outlet;
        $targetOldEffectiveCall = $target->target_effective_call;
        $targetOldSales = $target->target_sales;
        $targetOldSalesPf = $target->target_sales_pf;

        $target->update($request->all());

        /* Summary Target Add and/or Change */
        $summary['user_id'] = $target->user_id;
        $summary['targetOldCall'] = $targetOldCall;
        $summary['targetOldActiveOutlet'] = $targetOldActiveOutlet;
        $summary['targetOldEffectiveCall'] = $targetOldEffectiveCall;
        $summary['targetOldSales'] = $targetOldSales;
        $summary['targetOldSalesPf'] = $targetOldSalesPf;
        $summary['target_call'] = $target->target_call;
        $summary['target_active_outlet'] = $target->target_active_outlet;
        $summary['target_effective_call'] = $target->target_effective_call;
        $summary['target_sales'] = $target->target_sales;
        $summary['target_sales_pf'] = $target->target_sales_pf;

        $this->changeTargetSalesman($summary, 'change');

        return response()->json(
            ['url' => url('/targetsalesman'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $target = SalesmanTarget::where('id', $id)->first();

        /* Summary Target Delete */
        $summary['user_id'] = $target->first()->user_id;
        $summary['target_call'] = $target->first()->target_call;
        $summary['target_active_outlet'] = $target->first()->target_active_outlet;
        $summary['target_effective_call'] = $target->first()->target_effective_call;
        $summary['target_sales'] = $target->first()->target_sales;
        $summary['target_sales_pf'] = $target->first()->target_sales_pf;

        $this->changeTargetSalesman($summary, 'delete');

        $target->delete();

        return response()->json($id);
    }
}
