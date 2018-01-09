<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Quiz;
use App\Filters\QuizFilters;
use Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.quiz');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Quiz::where('quizs.deleted_at', null)
                    ->select('quizs.*')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(QuizFilters $filters){ 
        $data = Quiz::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->editColumn('link', function ($item) {
                    return
                    "<a href='$item->link'>".$item->link."</a>";
                })
                ->editColumn('target', function ($item) {
                    $result = '';
                    if ($item->target == 'All') 
                    {
                        $result = "Promoter, Promoter Additional, Promoter Event, Demonstrator MCC, Demonstrator DA, ACT, PPE, BDT, Salesman Explorer, SMD, SMD Coordinator, HIC, HIE, SMD, Additional, ASC";
                    }
                    else if ($item->target == 'Demonstrator') 
                    {
                        $result = "Demonstrator MCC, Demonstrator DA";
                    }
                    else if ($item->target == 'Promoter') 
                    {
                        $result = "Promoter, Promoter Additional, Promoter Event, ACT, PPE, BDT, Salesman Explorer, SMD, SMD Coordinator, HIC, HIE, SMD, Additional, ASC";
                    }
                    return $result;
                })

                
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('quiz/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                })
                ->rawColumns(['link','action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.quiz-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return response()->json($request);
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'target' => 'required',
            ]);  

        // Admin
        $request['user_id'] = Auth::user()->id;

        // Date
        $request['date'] = Carbon::now();

        // $allTarget = '';
        // $counTarget=0;

        // foreach ($request['target'] as $key => $value) {
        //     if ($counTarget == 0) {
        //         $allTarget .= $value;
        //         if ($value!='') {
        //             $counTarget++;
        //         }
        //     }else{
        //         $allTarget .= ','.$value;
        //     }            
        // }

        // $request->merge(array('target'=> $allTarget));

        // return $request->all();

        // dd($request->all());
        $quiz = Quiz::create($request->all());
        
        return response()->json(['url' => url('/quiz')]);
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
        $data = Quiz::where('id', $id)->first();

        return view('master.form.quiz-form', compact('data'));
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'target' => 'required',
            ]);  

        // $allTarget = '';
        // $counTarget=0;
        
        // foreach ($request['target'] as $key => $value) {
        //     if ($counTarget == 0) {
        //         $allTarget .= $value;
        //         if ($value!='') {
        //             $counTarget++;
        //         }
        //     }else{
        //         $allTarget .= ','.$value;
        //     }            
        // }

        // $request->merge(array('target'=> $allTarget));

        $quiz = Quiz::find($id);
    	$quiz->update($request->all());        

        return response()->json(
            [
                'url' => url('/quiz'),
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
        /* Deleting related to quiz */
        $quiz = Quiz::destroy($id);

        return response()->json($id);
    }
}
