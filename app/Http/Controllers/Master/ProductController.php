<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\ProductFilters;
use App\Traits\StringTrait;
use DB;
use App\Product;
use App\ProductHistory;

class ProductController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.product');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Product::where('products.deleted_at', null)
        			->join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                    ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ProductFilters $filters){        
        $data = Product::filter($filters)
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))
                ->get();

        return $data;
    }

    public function getDataWithFiltersCheck(ProductFilters $filters){        
        $data = Product::filter($filters)
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))
                ->limit(1)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return 
                    "<a href='#product' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-product'><i class='fa fa-pencil'></i></a>
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
            'model' => 'required|string|max:255',
            'name' => 'required',
            'variants' => 'required',
            'category_id' => 'required'
            ]);

       	$product = Product::create($request->all());
        
        return response()->json(['url' => url('/product')]);
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
        $data = Product::with('category')->where('id', $id)->first();

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
            'model' => 'required|string|max:255',
            'name' => 'required',
            'variants' => 'required',
            'category_id' => 'required'
            ]);

        /* CREATE HISTORY BEFORE UPDATE */

        $productOld = Product::find($id);        
        $history = $productOld;
        $history['product_id'] = $id;

        // return $history;

        ProductHistory::create($history->toArray());


        $product = Product::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/product'), 'method' => $request->_method]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::destroy($id);

        return response()->json($id);
    }
}
