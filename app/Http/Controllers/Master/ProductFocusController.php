<?php

namespace App\Http\Controllers\Master;

use App\SellIn;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\ProductFocusFilters;
use App\Traits\StringTrait;
use DB;
use App\ProductFocuses;
use App\Traits\SummaryTrait;

class ProductFocusController extends Controller
{
    use StringTrait;
    use SummaryTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.productfocus');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = ProductFocuses::where('product_focuses.deleted_at', null)
        			->join('products', 'product_focuses.product_id', '=', 'products.id')
                    ->select('product_focuses.*', 'products.name as product_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ProductFocusFilters $filters){
        $data = ProductFocuses::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#productfocus' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-productfocus'><i class='fa fa-pencil'></i></a>
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
            'type' => 'required'
            ]);

        $productFocus = ProductFocuses::where('product_id', $request['product_id'])
                    ->where('type', $request['type']);

        if($productFocus->count() == 0){
            $newProductFocus = ProductFocuses::create($request->all());

            /* Summary Change */
            $summary['product_id'] = $newProductFocus->product_id;
            $summary['type'] = $newProductFocus->type;
            $this->changeSummary($summary, 'change');
        }

        return response()->json(['url' => url('/productfocus')]);
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
        $data = ProductFocuses::with('product')->where('id', $id)->first();

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
            'type' => 'required'
            ]);

        $productFocus = ProductFocuses::find($id);

        $productFocusCount = ProductFocuses::where('product_id', $request['product_id'])->where('type', $request['type'])->count();
        if($productFocusCount > 0){
            return;
        }

        /* Summary Delete */
        $summary['product_id'] = $productFocus->product_id;
        $summary['type'] = $productFocus->type;
        $this->changeSummary($summary, 'delete');

        $productFocus->update($request->all());

        /* Summary Change */
        $summary['product_id'] = $productFocus->product_id;
        $summary['type'] = $productFocus->type;
        $this->changeSummary($summary, 'change');

        return response()->json(
            ['url' => url('/productfocus'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productFocus = ProductFocuses::find($id);

        /* Summary Delete */
        $summary['product_id'] = $productFocus->product_id;
        $summary['type'] = $productFocus->type;
        $this->changeSummary($summary, 'delete');

        $productFocus->delete();

        return response()->json($id);
    }

}
