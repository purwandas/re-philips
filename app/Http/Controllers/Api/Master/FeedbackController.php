<?php

namespace App\Http\Controllers\Api\Master;

use App\FeedbackAnswer;
use App\FeedbackCategory;
use App\FeedbackQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Store;
use App\District;
use App\EmployeeStore;
use App\User;
use App\SpvDemo;
use DB;

class FeedbackController extends Controller
{
    public function getListPromoterFeedback(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');
        if(count($spvDemoIds) > 0){
            $storeIds = $spvDemoIds;
        }
        
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        
        $promoters = User::whereIn('id', $promoterIds);
        if(count($spvDemoIds) > 0){
            $promoters->whereHas('role', function($query){
               return $query->where('role_group','Demonstrator DA');
            });
        }else{
            $promoters->whereHas('role', function($query){
                return $query->whereNotIn('role_group', ['Demonstrator DA']);
            });
        }
        $promoters->whereHas('role', function($query){
           return $query->where('role_group', '<>', 'Salesman Explorer');
        });

        return response()->json($promoters->get());
    }

    public function getListPromoterFeedbackWithParam($param){

        $user = User::where('id', $param)->first();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');

        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');
        if(count($spvDemoIds) > 0){
            $storeIds = $spvDemoIds;
        }

        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
     
        $promoters = User::whereIn('id', $promoterIds);
        if(count($spvDemoIds) > 0){
            $promoters->whereHas('role', function($query){
               return $query->where('role_group','Demonstrator DA');
            });
        }else{
            $promoters->whereHas('role', function($query){
                return $query->whereNotIn('role_group', ['Demonstrator DA']);
            });
        }
        $promoters->whereHas('role', function($query){
           return $query->where('role_group', '<>', 'Salesman Explorer');
        });

        // $promoters->get();

        return response()->json($promoters->get());
    }

    public function getListStoreNearby(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $distance = 250;

        $user = JWTAuth::parseToken()->authenticate();
        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

        $data = Store::join('districts', 'stores.district_id', '=', 'districts.id')
                    ->where('latitude', '!=', null)
                    ->where('longitude', '!=', null)
                    ->whereNotIn('stores.id', $storeIds)
                    ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name');
//                    ->select('id', 'store_name_1 as nama', 'latitude', 'longitude');

        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $haversine = '( 6371 * acos( cos( radians('.$content['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$content['longitude'].') ) + sin( radians('.$content['latitude'].') ) * sin( radians( latitude ) ) ) ) * 1000';
        $data = $data->selectRaw("{$haversine} AS distance")->orderBy('distance', 'asc')->whereRaw("{$haversine} <= ?", [$distance])
            ->groupBy('store_id');

        return response()->json($data->get());
    }

    public function getListPromoterFeedbackWithParamStore($param){

        $storeIds = Store::where('store_id', $param)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)
                        ->whereHas('role', function($query){
                            return $query->where('role_group', '<>', 'Salesman Explorer');
                        })
                        ->get();

        return response()->json($promoters);
    }

    public function getListCategoryFeedback(Request $request, $param){

        $assessor = JWTAuth::parseToken()->authenticate();
        $promoter = $request->promoter_id;

            $date1 = Carbon::now()->toDateString().' 00:00:00';
            $date2 = Carbon::now()->toDateString().' 23:59:59';

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

        foreach ($category as $detail) {

            $count = 0;

            $feedbackQuestion = FeedbackQuestion::where('feedbackCategory_id', $detail->id);

            if($feedbackQuestion->count() > 0){

                foreach ($feedbackQuestion->get() as $data){
                    $feedbackAnswer = FeedbackAnswer::
                                        where('created_at','>=',$date1)
                                        ->where('created_at','<=',$date2)
                                        ->where('assessor_id', $assessor->id)
                                        ->where('promoter_id', $promoter)
                                        ->where('feedbackQuestion_id', $data->id);

                    if($feedbackAnswer->count() > 0){
                        $count += 1;
                    }
                }

            }

            if($count > 0){
                $detail['status'] = 1;
            }else{
                $detail['status'] = 0;
            }

        }

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
