<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\News;
use App\Filters\NewsFilters;
use Auth;
use Carbon\Carbon;
use App\AreaApp;
use App\Store;
use App\User;
use App\NewsRead;
use App\Soh;
use App\SohDetail;

class SohController extends Controller
{
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.soh');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Soh::where('sohs.deleted_at', null)
        			->join('soh_details', 'soh_details.soh_id', '=', 'sohs.id')
                    ->join('stores', 'stores.id', '=', 'sohs.store_id')
                    ->join('area_apps', 'area_apps.id', '=', 'stores.areaapp_id')
                    ->join('areas', 'areas.id', '=', 'area_apps.area_id')
                    ->join('users', 'sohs.user_id', '=', 'users.id')
                    ->join('products', 'soh_details.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->join('groups', 'categories.group_id', '=', 'groups.id')
                    ->select('soh_details.id as soh_detail_id', 'areas.name as area_name', 'sohs.date as date', 'stores.store_id as store_id', 'stores.store_name_1 as store_name', 'stores.store_name_2 as store_name2', 'users.nik as nik', 'users.name as user_name', 'products.model as model', 'groups.name as group_name', 'categories.name as category_name', 'products.name as product_name', 'soh_details.quantity as quantity')->get();

        // $data = Soh::with('store.areaapp.area.region', 'user')->get();

        // dd($data->toArray());

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(NewsFilters $filters){ 
        $data = News::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                // ->editColumn('target_detail', function ($item) {

                //     $result = "";
                //     if($item->target_type == 'Area'){

                //         $data = explode(',' , $item->target_detail);
                //         foreach ($data as $dataSplit) { 

                //             $area = AreaApp::find(trim($dataSplit));
                //             $result .= $area->name;
                //             if($dataSplit != end($data)){
                //                 $result .= ", ";
                //             }

                //         }

                //     }else if($item->target_type == 'Store'){

                //         $data = explode(',' , $item->target_detail);
                //         foreach ($data as $dataSplit) { 

                //             $store = Store::find(trim($dataSplit));
                //             $result .= "(" . $store->store_id . ") " . $store->store_name_1;
                //             if($dataSplit != end($data)){
                //                 $result .= ", ";
                //             }

                //         }

                //     }else if($item->target_type == 'Promoter'){

                //         $data = explode(',' , $item->target_detail);
                //         foreach ($data as $dataSplit) { 

                //             $user = User::find(trim($dataSplit));
                //             $result .= $user->name;
                //             if($dataSplit != end($data)){
                //                 $result .= ", ";
                //             }

                //         }

                //     }

                //     return $result;
                    
                // })
                // ->editColumn('total_read', function ($item) {
                //     return
                //     "<a class='open-read-who-modal' data-target='#read-who-modal' data-toggle='modal' data-total-read='".$item->total_read."' data-url='util/newsread' data-title='Who`s read this news' data-id='".$item->id."'> ".$item->total_read." </a>";
                // })
                // ->addColumn('action', function ($item) {

                //     return 
                //     "<a href='".url('news/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                //     <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                // })
                // ->editColumn('store', function ($item) {
                //     return ;
                // })
                // ->rawColumns(['content','total_read', 'action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.news-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $this->validate($request, [
            'from' => 'required|string|max:255',
            'subject' => 'required|string|max:255',                  
            ]);  

        // Admin
        $request['user_id'] = Auth::user()->id;

        // Date
        $request['date'] = Carbon::now();

        // Total Read
        $request['total_read'] = 0;

         /* Area Targets */
        if($request['target_type'] == 'Area'){
            $target = null;
            $data = $request['area'];
            foreach ($data as $area) {
                $target .= $area;

                if($area != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Store Targets */
        if($request['target_type'] == 'Store'){
            $target = null;
            $data = $request['store'];
            foreach ($data as $store) {
                $target .= $store;

                if($store != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Employee Targets */
        if($request['target_type'] == 'Promoter'){
            $target = null;
            $data = $request['employee'];
            foreach ($data as $employee) {
                $target .= $employee;

                if($employee != end($data)){
                    $target .= ", ";
                }
            }
        }

        if($request['target_type'] != 'All'){
            $request['target_detail'] = $target;
        }else{
            $request['target_detail'] = null;
        }

        // dd($request->all());
        $news = News::create($request->all());
        
        return response()->json(['url' => url('/news')]);
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
        $data = News::where('id', $id)->first();

        return view('master.form.news-form', compact('data'));
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
        // dd($id);
        // dd($request->all());
        $this->validate($request, [
            'from' => 'required|string|max:255',
            'subject' => 'required|string|max:255',                  
            ]);  

        // Admin
        $request['user_id'] = Auth::user()->id;

         /* Area Targets */
        if($request['target_type'] == 'Area'){
            $target = null;
            $data = $request['area'];
            foreach ($data as $area) {
                $target .= $area;

                if($area != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Store Targets */
        if($request['target_type'] == 'Store'){
            $target = null;
            $data = $request['store'];
            foreach ($data as $store) {
                $target .= $store;

                if($store != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Employee Targets */
        if($request['target_type'] == 'Promoter'){
            $target = null;
            $data = $request['employee'];
            foreach ($data as $employee) {
                $target .= $employee;

                if($employee != end($data)){
                    $target .= ", ";
                }
            }
        }

        if($request['target_type'] != 'All'){
            $request['target_detail'] = $target;
        }else{
            $request['target_detail'] = null;
        }

        $news = News::find($id);
    	$news->update($request->all());        

        return response()->json(
            [
                'url' => url('/news'),
                'method' => $request->_method
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Deleting related to news */
        // News Reads
        $newsRead = NewsRead::where('news_id', $id);
        if($newsRead->count() > 0){
            $newsRead->delete();
        }

        $news = News::destroy($id);

        return response()->json($id);
    }
}
