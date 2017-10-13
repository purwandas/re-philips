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

class NewsController extends Controller
{
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.news');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = News::where('news.deleted_at', null)
        			->join('users', 'news.user_id', '=', 'users.id')
                    ->select('news.*', 'users.name as user_name')->get();

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
                ->editColumn('target_detail', function ($item) {

                    $result = "";
                    if($item->target_type == 'Area'){

                        $data = explode(',' , $item->target_detail);
                        foreach ($data as $dataSplit) { 

                            $area = AreaApp::find(trim($dataSplit));
                            $result .= $area->name;
                            if($dataSplit != end($data)){
                                $result .= ", ";
                            }

                        }

                    }else if($item->target_type == 'Store'){

                        $data = explode(',' , $item->target_detail);
                        foreach ($data as $dataSplit) { 

                            $store = Store::find(trim($dataSplit));
                            $result .= "(" . $store->store_id . ") " . $store->store_name_1;
                            if($dataSplit != end($data)){
                                $result .= ", ";
                            }

                        }

                    }else if($item->target_type == 'Promoter'){

                        $data = explode(',' , $item->target_detail);
                        foreach ($data as $dataSplit) { 

                            $user = User::find(trim($dataSplit));
                            $result .= $user->name;
                            if($dataSplit != end($data)){
                                $result .= ", ";
                            }

                        }

                    }

                    return $result;
                    
                })
                ->editColumn('total_read', function ($item) {
                    return
                    "<a class='open-read-who-modal' data-target='#read-who-modal' data-toggle='modal' data-total-read='".$item->total_read."' data-url='util/newsread' data-title='Who`s read this news' data-id='".$item->id."'> ".$item->total_read." </a>";
                })
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('news/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                })
                ->rawColumns(['content','total_read', 'action'])
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
