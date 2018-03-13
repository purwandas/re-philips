<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\CategoryFilters;
use App\Traits\StringTrait;
use DB;
use App\Category;

class CategoryController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.category');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Category::where('categories.deleted_at', null)
        			->join('groups', 'categories.group_id', '=', 'groups.id')
                    ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(CategoryFilters $filters){        
        $data = Category::filter($filters)->join('groups', 'categories.group_id', '=', 'groups.id')
                    ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')->get();

        return $data;
    }

    public function getDataWithFiltersCheck(CategoryFilters $filters){        
        $data = Category::filter($filters)
                ->join('groups', 'categories.group_id', '=', 'groups.id')
                ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')
                ->limit(1)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return 
                    "<a href='#category' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-category'><i class='fa fa-pencil'></i></a>
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
    	// return $request->all();

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'group_id' => 'required'
            ]);

       	$category = Category::create($request->all());
        
        return response()->json(['url' => url('/category')]);
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
        $data = Category::with('group')->where('id', $id)->first();

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
            'name' => 'required|string|max:255',
            'group_id' => 'required'
            ]);

        $category = Category::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/category'), 'method' => $request->_method]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::destroy($id);

        return response()->json($id);
    }
}
