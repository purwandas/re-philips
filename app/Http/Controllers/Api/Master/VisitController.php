<?php

namespace App\Http\Controllers\Api\Master;

use App\VisitPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class VisitController extends Controller
{
    public function store(Request $request){

        $dataCount = count($request->store_id);
        $user = JWTAuth::parseToken()->authenticate();

        if($dataCount > 0){

            try{

                DB::transaction(function () use ($request, $user) {

                    foreach ($request->store_id as $data){

                        $visitExist = VisitPlan::where('user_id', $user->id)->where('store_id', $data)->where('date', Carbon::now()->format('Y-m-d'))->first();

                        if(!$visitExist){

                            VisitPlan::create([
                                'user_id' => $user->id,
                                'store_id' => $data,
                                'date' => Carbon::now(),
                                'visit_status' => 0
                            ]);

                        }

                    }

                });

            }catch (\Exception $exception){
                return response()->json(['status' => true, 'message' => 'Gagal memasukkan Visit Plan'], 500);
            }

        }

        return response()->json(['status' => true, 'message' => 'Berhasil memasukkan Visit Plan']);

    }

    public function delete(Request $request){

        try{

            DB::transaction(function () use ($request) {

                $visit = VisitPlan::where('id', $request->id);
                $visit->forceDelete();

            });

        }catch (\Exception $exception){
            return response()->json(['status' => true, 'message' => 'Gagal menghapus Visit Plan'], 500);
        }

        return response()->json(['status' => true, 'message' => 'Berhasil menghapus Visit Plan']);

    }

    public function getVisit(){

        $user = JWTAuth::parseToken()->authenticate();
        $visit = VisitPlan::where('visit_plans.user_id', $user->id)->where('visit_plans.date', Carbon::now()->format('Y-m-d'))
                 ->join('stores', 'stores.id', '=', 'visit_plans.store_id')
                 ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'visit_plans.visit_status', 'visit_plans.id as visit_id', 'visit_plans.check_in', 'visit_plans.check_in_location', 'visit_plans.check_out', 'visit_plans.check_out_location')->get();

        return response()->json($visit);

    }

}
