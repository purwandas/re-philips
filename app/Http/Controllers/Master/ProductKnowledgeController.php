<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\UploadTrait;
use App\ProductKnowledge;
use App\Filters\ProductKnowledgeFilters;
use Auth;
use Carbon\Carbon;
use App\AreaApp;
use App\Store;
use App\User;
use App\ProductKnowledgeRead;
use File;

class ProductKnowledgeController extends Controller
{
    use StringTrait;
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.product-knowledge');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = ProductKnowledge::where('product_knowledges.deleted_at', null)
        			->join('users', 'product_knowledges.user_id', '=', 'users.id')
                    ->select('product_knowledges.*', 'users.name as user_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ProductKnowledgeFilters $filters){
        $data = ProductKnowledge::filter($filters)->get();

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
                ->editColumn('file', function ($item) {
                    if($item->file != "") {
                        return "<a target='_blank' href='" . $item->file . "' class='btn btn-sm btn-danger'><i class='fa fa-file-pdf-o'></i> &nbsp; Download PDF</a>";
                    }else{
                        return "<label class='btn btn-sm btn-primary'>No File Uploaded</label>";
                    }

                })
                ->editColumn('total_read', function ($item) {
                    return
                    "<a class='open-read-who-modal' data-target='#read-who-modal' data-toggle='modal' data-total-read='".$item->total_read."' data-url='util/productread' data-title='Who`s read this Product Knowledge' data-id='".$item->id."'> ".$item->total_read." </a>";
                })
                ->addColumn('action', function ($item) {

                    return
                    "<a href='".url('product-knowledge/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['file', 'total_read', 'action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.product-knowledge-form');
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

        // Upload file process
        ($request->upload_file != null) ?
            $file_url = $this->fileUpload($request->upload_file, "productknowledge/".$this->getRandomPath()) : $file_url = "";

        if($request->upload_file != null) $request['file'] = $file_url;

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
        $productKnowledge = ProductKnowledge::create($request->all());

        return response()->json(['url' => url('/product-knowledge')]);
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
        $data = ProductKnowledge::where('id', $id)->first();

        return view('master.form.product-knowledge-form', compact('data'));
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

        $productKnowledge = ProductKnowledge::find($id);

        if($request->upload_file != null) {
            /* Delete File PDF */
            if ($productKnowledge->file != "") {
                $filePath = explode('/', $productKnowledge->file);
                $count = count($filePath);
                $folderpath = $filePath[$count - 2];
                File::deleteDirectory(public_path() . "/file/productknowledge/" . $folderpath);
            }
        }

        // Upload file process
        ($request->upload_file != null) ?
            $file_url = $this->fileUpload($request->upload_file, "productknowledge/".$this->getRandomPath()) : $file_url = "";

        if($request->upload_file != null) $request['file'] = $file_url;

        // Update data
    	$productKnowledge->update($request->all());

        return response()->json(
            [
                'url' => url('/product-knowledge'),
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
        /* Deleting related to product knowledge */
        // Product Knowledge Reads
        $productKnowledgeRead = ProductKnowledgeRead::where('productknowledge_id', $id);
        if($productKnowledgeRead->count() > 0){
            $productKnowledgeRead->delete();
        }

        $productKnowledge = ProductKnowledge::destroy($id);

        return response()->json($id);
    }
}
