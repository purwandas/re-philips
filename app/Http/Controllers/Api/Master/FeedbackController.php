<?php

namespace App\Http\Controllers\Api\Master;

use App\FeedbackAnswer;
use App\FeedbackCategory;
use App\FeedbackQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Store;
use App\EmployeeStore;
use App\User;
use DB;

class FeedbackController extends Controller
{
    public function getListPromoterFeedback(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)->get();

        return response()->json($promoters);
    }

    public function getListPromoterFeedbackWithParam($param){

        $user = User::where('id', $param)->first();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)->get();

        return response()->json($promoters);
    }

    public function getListCategoryFeedback($param){

        $type = 'PK';

        if($param == 1){
            $type = 'PK';
        }else if($param == 2){
            $type = 'POG';
        }else if($param == 3){
            $type = 'POSM';
        }

        $category = FeedbackCategory::where('type', $type)
                    ->select('id', 'name', 'type')
                    ->get();

        return response()->json($category);
    }

    public function getListQuestionFeedback($param){

        $question = FeedbackQuestion::where('feedbackCategory_id', $param)
                    ->select('id', 'feedbackCategory_id', 'question')
                    ->get();

        return response()->json($question);
    }

    public function feedbackSend(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $dataLength = count($request->question_id);

        try{

            DB::transaction(function () use ($request, $user, $dataLength) {

                for($i=0;$i<$dataLength;$i++){

                    FeedbackAnswer::create([
                        'assessor_id' => $user->id,
                        'promoter_id' => $request->promoter_id,
                        'feedbackQuestion_id' => $request->question_id[$i],
                        'answer' => $request->answer[$i],
                    ]);

                }

            });

        }catch (\Exception $exception){
            return response()->json(['status' => false, 'message' => 'Gagal melakukan feedback'], 500);
        }

        return response()->json(['status' => true, 'message' => 'Berhasil melakukan feedback']);
    }
}