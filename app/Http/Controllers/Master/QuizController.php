<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Quiz;
use App\TargetQuiz;
use App\Filters\QuizFilters;
use Auth;
use DB;
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
                    $target = TargetQuiz::where('quiz_id',$item->id)
                        ->join('quiz_targets','quiz_targets.id','target_quizs.quiz_target_id')
                        ->select('quiz_targets.role','quiz_targets.grading')
                        ->get();
                    // if (count($target > 0)) 
                    // {
                        
                        foreach ($target as $key => $value) {
                            if ($key >0) {
                                $result.=', ';
                            }
                            $result .= $value->role." (".$value->grading.")";
                        }
                    // }
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
            'link' => 'required',
            'target' => 'required',
            ]);  

        // return response()->json($request);
        $quiz = Quiz::create([
            'title' => $request['title'],
            'description' => $request['description'],
            'link' => $request['link'],
            'date' => Carbon::now(),
        ]);

        foreach ($request['target'] as $key => $value) {
            TargetQuiz::create([
                'quiz_id' => $quiz->id,
                'quiz_target_id' => $value,
            ]);
        }
        
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
        $target = TargetQuiz::where('quiz_id',$id)->get();

        return view('master.form.quiz-form', compact('data','target'));
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'target' => 'required',
            ]);  

        $quiz = Quiz::find($id);
    	$quiz->update([
            'title' => $request['title'],
            'description' => $request['description'],
            'link' => $request['link'],
            'date' => Carbon::now(),
        ]);

        /* begin Re-create Target relation */
        $targetQuiz = TargetQuiz::where('quiz_id',$id);
        if ($targetQuiz->count() > 0) {
            $targetQuiz->delete();
        }

        foreach ($request['target'] as $key => $value) {
            TargetQuiz::create([
                'quiz_id' => $id,
                'quiz_target_id' => $value,
            ]);
        }      
        /* end Re-create Target relation */

        return response()->json([
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

        /* begin Delete Target relation */
        $targetQuiz = TargetQuiz::where('quiz_id',$id);
        if ($targetQuiz->count() > 0) {
            $targetQuiz->delete();
        }
        /* end Delete Target relation */

        return response()->json($id);
    }
}
