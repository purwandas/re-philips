<?php

namespace App\Http\Controllers\Api\Master;

use App\Quiz;
use App\QuizRead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class QuizController extends Controller
{
    public function getListQuiz(){

        $user = JWTAuth::parseToken()->authenticate();

        $quiz = Quiz::all();
        $quizArray = [];

        foreach ($quiz as $data){
            $target = explode(',', $data['target']);
            if (in_array($user->role, $target)) {
                array_push($quizArray, $data['id']);
            }
        }

        $resultQuiz = Quiz::whereIn('id', $quizArray)->get();

        // Set has read
        $resultQuiz->map(function ($detail) use ($user) {
            $quizRead = QuizRead::where('quiz_id', $detail['id'])->where('user_id', $user->id)->first();

            if($quizRead) {
                $detail['hasRead'] = 1;
            }else{
                $detail['hasRead'] = 0;
            }

            return $detail;
        });

        return response()->json($resultQuiz);

    }

    public function read($param)
    {
    	$user = JWTAuth::parseToken()->authenticate();

        $quizRead = QuizRead::where('quiz_id', $param)->where('user_id', $user->id)->count();
        if($quizRead == 0){
            $quiz = Quiz::find($param);
            $quiz->update([ 'total_read' => $quiz->total_read+1 ]);

            QuizRead::create([
                'quiz_id' => $param,
                'user_id' => $user->id
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Quiz Readed']);
    }
}
