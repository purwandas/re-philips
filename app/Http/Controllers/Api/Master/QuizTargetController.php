<?php

namespace App\Http\Controllers\Api\Master;

use App\QuizTarget;
use App\Filters\QuizTargetFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class QuizTargetController extends Controller
{
    public function store(Request $request)
    {
        // return response()->json($request);
        $this->validate($request, [
            'role_id' => 'required',
            'grading_id' => 'required',
            ]);  

        // return response()->json($request);
        $quiz = QuizTarget::create([
            'role_id' => $request['role_id'],
            'grading_id' => $request['grading_id'],
        ]);
        
        return response()->json(['url' => url('/quiz')]);
    }

    public function getData($param)
    {
        $target = QuizTarget::get();

        return response()->json($target);
    }

    public function getDataWithFilters(QuizTargetFilters $filters)
    {
        $data = QuizTarget::join('roles','roles.id','quiz_targets.role_id')
                ->join('gradings','gradings.id','quiz_targets.grading_id')
                ->select('quiz_targets.id','roles.role','gradings.grading')
                ->filter($filters)->get();

        return response()->json($data);
    }

}
