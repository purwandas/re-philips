<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Filters\FeedbackCategoryFilters;
use App\FeedbackAnswer;

class FeedbackAnswerController extends Controller
{
    use UploadTrait;
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.feedbackanswer');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = FeedbackAnswer::where('feedback_answers.deleted_at', null)
                    ->join('users as assessors', 'feedback_answers.assessor_id', '=', 'users.id')
                    ->join('users as promoters', 'feedback_answers.promoter_id', '=', 'users.id')
                    ->join('feedback_questions', 'feedback_answers.feedbackQuestion_id', '=', 'feedback_questions.id')
                    ->select('feedback_answers.*', 'assessors.name as assessor_name', 'promoters.name as assessor_name', 'feedback_questions.name as feedback_question_name' )->get();


        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(FeedbackAnswerFilters $filters){
        $data = FeedbackAnswer::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                   return
                    "<a href='#feedbackAnswer' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-feedbackAnswer'><i class='fa fa-pencil'></i></a>
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
            'feedbackCategory_id' => 'required',
            'Answer' => 'required|string|max:255',
            'type' => 'required',
            ]);

        $feedbackAnswer = FeedbackAnswer::create($request->all());

        return response()->json(['url' => url('/feedbackAnswer')]);
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
        $data = FeedbackAnswer::with('feedbackCategory')->where('id', $id)->first();

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
            'feedbackCategory_id' => 'required',
            'Answer' => 'required|string|max:255',
            'type' => 'required',
            ]);

        $feedbackAnswer = FeedbackAnswer::find($id)->update($request->all());

        return response()->json(
            [
                'url' => url('/feedbackAnswer'),
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

        $feedbackAnswer = FeedbackAnswer::destroy($id);

        return response()->json($id);
    }

}
