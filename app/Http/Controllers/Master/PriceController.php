<?php

namespace App\Http\Controllers\Master;

use App\ProductFocuses;
use App\Traits\ActualTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\PriceFilters;
use App\Traits\StringTrait;
use DB;
use App\Price;
use App\SellIn;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use Carbon\Carbon;
use App\Traits\SummaryTrait;

class PriceController extends Controller
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
        return view('master.price');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){

        $data = Price::where('prices.deleted_at', null)
        			->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'), 'global_channels.name as globalchannel_name')->get();

        
        $filter = $data;

        /* If filter */
            if($request['byGChannel']){
                $filter = $data->where('globalchannel_id', $request['byGChannel']);
            }

        return $this->makeTable($filter);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(PriceFilters $filters){
        $data = Price::filter($filters)->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'), 'global_channels.name as globalchannel_name')->get();

        return $data;
    }

    public function getDataWithFiltersCheck(PriceFilters $filters){
        $data = Price::filter($filters)->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'), 'global_channels.name as globalchannel_name')
                    ->limit(1)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
                ->editColumn('sell_type',function ($item) {
                    
                    if ($item->sell_type == 'Sell In') {
                        $item->sell_type = 'Sell Thru';
                    }
                    return $item->sell_type;
                })
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#price' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-price'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['sell_type','action'])
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
            'globalchannel_id' => 'required',
            'sell_type' => 'required',
            'price' => 'required|numeric',
            'release_date' => 'required',
            ]);

//        return $this->changeSellInSummary($request['product_id'], $request['globalchannel_id'], $request['price']);

        $price = Price::where('product_id', $request['product_id'])
                    ->where('globalchannel_id', $request['globalchannel_id'])
                    ->where('sell_type', $request['sell_type'])
                    ->whereDate('release_date', Carbon::parse($request['release_date']));

        if($price->count() > 0){
            $price->update(['price'=>$request->price]);

            /* Summary Change */
            $summary['product_id'] = $request['product_id'];
            $summary['globalchannel_id'] = $request['globalchannel_id'];
            $summary['sell_type'] = $request['sell_type'];
            $summary['price'] = $request['price'];
            // $this->changeSummary($summary, 'change');

        }else{
            $price = Price::create($request->all());

            /* Summary Change */
            $summary['product_id'] = $price->product_id;
            $summary['globalchannel_id'] = $price->globalchannel_id;
            $summary['sell_type'] = $request['sell_type'];
            $summary['price'] = $price->price;
            // $this->changeSummary($summary, 'change');
        }

        return response()->json(['url' => url('/price')]);
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
        $data = Price::with('product', 'globalChannel')->where('id', $id)->first();

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
            'globalchannel_id' => 'required',
            'sell_type' => 'required',
            'price' => 'required|numeric'
            ]);

        $price = Price::find($id);

        $priceCount = Price::where('product_id', $request['product_id'])
                    ->where('globalchannel_id', $request['globalchannel_id'])
                    ->where('sell_type', $request['sell_type'])
                    ->whereDate('release_date', Carbon::parse($request['release_date']))
                    ->where('id', '<>', $id)
                    ->count();

        if($priceCount > 0){
            return;
        }

        $price->update($request->all());

        /* Summary Change */
        $summary['product_id'] = $request['product_id'];
        $summary['globalchannel_id'] = $request['globalchannel_id'];
        $summary['sell_type'] = $request['sell_type'];
        $summary['price'] = $request['price'];
        // $this->changeSummary($summary, 'change');

        return response()->json(
            ['url' => url('/price'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price = Price::find($id);

        /* Summary Delete */
        $summary['product_id'] = $price->product_id;
        $summary['globalchannel_id'] = $price->globalchannel_id;
        $summary['sell_type'] = $price['sell_type'];
        $summary['price'] = $price->price;
        // $this->changeSummary($summary, 'delete');

        $price->delete();

        return response()->json($id);
    }

}
