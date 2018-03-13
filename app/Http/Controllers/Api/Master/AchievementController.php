<?php

namespace App\Http\Controllers\Api\Master;

use App\Area;
use App\Region;
use App\Reports\SalesmanSummaryTargetActual;
use App\Reports\SummaryTargetActual;
use App\SpvDemo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Store;
use App\EmployeeStore;
use App\User;
use App\DmArea;
use App\RsmRegion;
use App\Attendance;
use DB;

class AchievementController extends Controller
{

    // Default summarize for total achievement
    public function achievement($id, $param){

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetDA = 0;
        $totalTargetPC = 0;
        $totalTargetMCC = 0;
        $totalActualDA = 0;
        $totalActualPC = 0;
        $totalActualMCC = 0;
        $totalTargetPFDA = 0;
        $totalTargetPFPC = 0;
        $totalTargetPFMCC = 0;
        $totalActualPFDA = 0;
        $totalActualPFPC = 0;
        $totalActualPFMCC = 0;
        $totalTargetW1 = 0;
        $totalActualW1 = 0;
        $totalTargetW2 = 0;
        $totalActualW2 = 0;
        $totalTargetW3 = 0;
        $totalActualW3 = 0;
        $totalTargetW4 = 0;
        $totalActualW4 = 0;
        $totalTargetW5 = 0;
        $totalActualW5 = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        $data = SummaryTargetActual::where('user_id', $id)->where('sell_type', 'Sell In')->get();

        if($param == 2){
            $data = SummaryTargetActual::where('user_id', $id)->where('sell_type', 'Sell Out')->get();
        }

        foreach ($data as $detail){
            $totalTarget += $detail['target_da'];
            $totalTarget += $detail['target_pc'];
            $totalTarget += $detail['target_mcc'];

            $totalActual += $detail['actual_da'];
            $totalActual += $detail['actual_pc'];
            $totalActual += $detail['actual_mcc'];

            $totalTargetDA += $detail['target_da'];
            $totalTargetPC += $detail['target_pc'];
            $totalTargetMCC += $detail['target_mcc'];

            $totalActualDA += $detail['actual_da'];
            $totalActualPC += $detail['actual_pc'];
            $totalActualMCC += $detail['actual_mcc'];

            $totalTargetPFDA += $detail['target_pf_da'];
            $totalTargetPFPC += $detail['target_pf_pc'];
            $totalTargetPFMCC += $detail['target_pf_mcc'];

            $totalActualPFDA += $detail['actual_pf_da'];
            $totalActualPFPC += $detail['actual_pf_pc'];
            $totalActualPFMCC += $detail['actual_pf_mcc'];

            $totalTargetW1 += $detail['target_da_w1'];
            $totalTargetW1 += $detail['target_pc_w1'];
            $totalTargetW1 += $detail['target_mcc_w1'];
            $totalActualW1 += $detail['actual_da_w1'];
            $totalActualW1 += $detail['actual_pc_w1'];
            $totalActualW1 += $detail['actual_mcc_w1'];

            $totalTargetW2 += $detail['target_da_w2'];
            $totalTargetW2 += $detail['target_pc_w2'];
            $totalTargetW2 += $detail['target_mcc_w2'];
            $totalActualW2 += $detail['actual_da_w2'];
            $totalActualW2 += $detail['actual_pc_w2'];
            $totalActualW2 += $detail['actual_mcc_w2'];

            $totalTargetW3 += $detail['target_da_w3'];
            $totalTargetW3 += $detail['target_pc_w3'];
            $totalTargetW3 += $detail['target_mcc_w3'];
            $totalActualW3 += $detail['actual_da_w3'];
            $totalActualW3 += $detail['actual_pc_w3'];
            $totalActualW3 += $detail['actual_mcc_w3'];

            $totalTargetW4 += $detail['target_da_w4'];
            $totalTargetW4 += $detail['target_pc_w4'];
            $totalTargetW4 += $detail['target_mcc_w4'];
            $totalActualW4 += $detail['actual_da_w4'];
            $totalActualW4 += $detail['actual_pc_w4'];
            $totalActualW4 += $detail['actual_mcc_w4'];

            $totalTargetW5 += $detail['target_da_w5'];
            $totalTargetW5 += $detail['target_pc_w5'];
            $totalTargetW5 += $detail['target_mcc_w5'];
            $totalActualW5 += $detail['actual_da_w5'];
            $totalActualW5 += $detail['actual_pc_w5'];
            $totalActualW5 += $detail['actual_mcc_w5'];

            $totalTargetPF += $detail['target_pf_da'];
            $totalTargetPF += $detail['target_pf_pc'];
            $totalTargetPF += $detail['target_pf_mcc'];

            $totalActualPF += $detail['actual_pf_da'];
            $totalActualPF += $detail['actual_pf_pc'];
            $totalActualPF += $detail['actual_pf_mcc'];
        }

        return array($totalTarget, $totalActual,
            $totalTargetDA, $totalActualDA, $totalTargetPC, $totalActualPC, $totalTargetMCC, $totalActualMCC,
            $totalTargetPFDA, $totalActualPFDA, $totalTargetPFPC, $totalActualPFPC, $totalTargetPFMCC, $totalActualPFMCC,
            $totalTargetW1, $totalActualW1, $totalTargetW2, $totalActualW2, $totalTargetW3, $totalActualW3,
            $totalTargetW4, $totalActualW4, $totalTargetW5, $totalActualW5, $totalTargetPF, $totalActualPF);

    }

    public function achievementByStore($id, $storeId, $param){

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetDA = 0;
        $totalTargetPC = 0;
        $totalTargetMCC = 0;
        $totalActualDA = 0;
        $totalActualPC = 0;
        $totalActualMCC = 0;
        $totalTargetPFDA = 0;
        $totalTargetPFPC = 0;
        $totalTargetPFMCC = 0;
        $totalActualPFDA = 0;
        $totalActualPFPC = 0;
        $totalActualPFMCC = 0;
        $totalTargetW1 = 0;
        $totalActualW1 = 0;
        $totalTargetW2 = 0;
        $totalActualW2 = 0;
        $totalTargetW3 = 0;
        $totalActualW3 = 0;
        $totalTargetW4 = 0;
        $totalActualW4 = 0;
        $totalTargetW5 = 0;
        $totalActualW5 = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        $data = SummaryTargetActual::where('user_id', $id)->where('storeId', $storeId)->where('sell_type', 'Sell In')->where('partner', 0)->get();

        if($param == 2){
            $data = SummaryTargetActual::where('user_id', $id)->where('storeId', $storeId)->where('sell_type', 'Sell Out')->where('partner', 0)->get();
        }

        foreach ($data as $detail){
            $totalTarget += $detail['target_da'];
            $totalTarget += $detail['target_pc'];
            $totalTarget += $detail['target_mcc'];

            $totalActual += $detail['actual_da'];
            $totalActual += $detail['actual_pc'];
            $totalActual += $detail['actual_mcc'];

            $totalTargetDA += $detail['target_da'];
            $totalTargetPC += $detail['target_pc'];
            $totalTargetMCC += $detail['target_mcc'];

            $totalActualDA += $detail['actual_da'];
            $totalActualPC += $detail['actual_pc'];
            $totalActualMCC += $detail['actual_mcc'];

            $totalTargetPFDA += $detail['target_pf_da'];
            $totalTargetPFPC += $detail['target_pf_pc'];
            $totalTargetPFMCC += $detail['target_pf_mcc'];

            $totalActualPFDA += $detail['actual_pf_da'];
            $totalActualPFPC += $detail['actual_pf_pc'];
            $totalActualPFMCC += $detail['actual_pf_mcc'];

            $totalTargetW1 += $detail['target_da_w1'];
            $totalTargetW1 += $detail['target_pc_w1'];
            $totalTargetW1 += $detail['target_mcc_w1'];
            $totalActualW1 += $detail['actual_da_w1'];
            $totalActualW1 += $detail['actual_pc_w1'];
            $totalActualW1 += $detail['actual_mcc_w1'];

            $totalTargetW2 += $detail['target_da_w2'];
            $totalTargetW2 += $detail['target_pc_w2'];
            $totalTargetW2 += $detail['target_mcc_w2'];
            $totalActualW2 += $detail['actual_da_w2'];
            $totalActualW2 += $detail['actual_pc_w2'];
            $totalActualW2 += $detail['actual_mcc_w2'];

            $totalTargetW3 += $detail['target_da_w3'];
            $totalTargetW3 += $detail['target_pc_w3'];
            $totalTargetW3 += $detail['target_mcc_w3'];
            $totalActualW3 += $detail['actual_da_w3'];
            $totalActualW3 += $detail['actual_pc_w3'];
            $totalActualW3 += $detail['actual_mcc_w3'];

            $totalTargetW4 += $detail['target_da_w4'];
            $totalTargetW4 += $detail['target_pc_w4'];
            $totalTargetW4 += $detail['target_mcc_w4'];
            $totalActualW4 += $detail['actual_da_w4'];
            $totalActualW4 += $detail['actual_pc_w4'];
            $totalActualW4 += $detail['actual_mcc_w4'];

            $totalTargetW5 += $detail['target_da_w5'];
            $totalTargetW5 += $detail['target_pc_w5'];
            $totalTargetW5 += $detail['target_mcc_w5'];
            $totalActualW5 += $detail['actual_da_w5'];
            $totalActualW5 += $detail['actual_pc_w5'];
            $totalActualW5 += $detail['actual_mcc_w5'];

            $totalTargetPF += $detail['target_pf_da'];
            $totalTargetPF += $detail['target_pf_pc'];
            $totalTargetPF += $detail['target_pf_mcc'];

            $totalActualPF += $detail['actual_pf_da'];
            $totalActualPF += $detail['actual_pf_pc'];
            $totalActualPF += $detail['actual_pf_mcc'];
        }

        return array($totalTarget, $totalActual,
            $totalTargetDA, $totalActualDA, $totalTargetPC, $totalActualPC, $totalTargetMCC, $totalActualMCC,
            $totalTargetPFDA, $totalActualPFDA, $totalTargetPFPC, $totalActualPFPC, $totalTargetPFMCC, $totalActualPFMCC,
            $totalTargetW1, $totalActualW1, $totalTargetW2, $totalActualW2, $totalTargetW3, $totalActualW3,
            $totalTargetW4, $totalActualW4, $totalTargetW5, $totalActualW5, $totalTargetPF, $totalActualPF);

    }

    public function getAchievement($param){

        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'target' => $this->achievement($user->id, $param)[0],
            'actual' => $this->achievement($user->id, $param)[1],
            'target_da' => $this->achievement($user->id, $param)[2],
            'actual_da' => $this->achievement($user->id, $param)[3],
            'target_pc' => $this->achievement($user->id, $param)[4],
            'actual_pc' => $this->achievement($user->id, $param)[5],
            'target_mcc' => $this->achievement($user->id, $param)[6],
            'actual_mcc' => $this->achievement($user->id, $param)[7],
            'target_pf_da' => $this->achievement($user->id, $param)[8],
            'actual_pf_da' => $this->achievement($user->id, $param)[9],
            'target_pf_pc' => $this->achievement($user->id, $param)[10],
            'actual_pf_pc' => $this->achievement($user->id, $param)[11],
            'target_pf_mcc' => $this->achievement($user->id, $param)[12],
            'actual_pf_mcc' => $this->achievement($user->id, $param)[13],
            'target_week1' => $this->achievement($user->id, $param)[14],
            'actual_week1' => $this->achievement($user->id, $param)[15],
            'target_week2' => $this->achievement($user->id, $param)[16],
            'actual_week2' => $this->achievement($user->id, $param)[17],
            'target_week3' => $this->achievement($user->id, $param)[18],
            'actual_week3' => $this->achievement($user->id, $param)[19],
            'target_week4' => $this->achievement($user->id, $param)[20],
            'actual_week4' => $this->achievement($user->id, $param)[21],
            'target_week5' => $this->achievement($user->id, $param)[22],
            'actual_week5' => $this->achievement($user->id, $param)[23],
            'working_days' => $this->getTotalHK($user->id)
        ]);
    }

    public function getAchievementWithParam($param, $id){

        $user = User::where('id', $id)->first();

        return response()->json([
            'target' => $this->achievement($user->id, $param)[0],
            'actual' => $this->achievement($user->id, $param)[1],
            'target_da' => $this->achievement($user->id, $param)[2],
            'actual_da' => $this->achievement($user->id, $param)[3],
            'target_pc' => $this->achievement($user->id, $param)[4],
            'actual_pc' => $this->achievement($user->id, $param)[5],
            'target_mcc' => $this->achievement($user->id, $param)[6],
            'actual_mcc' => $this->achievement($user->id, $param)[7],
            'target_pf_da' => $this->achievement($user->id, $param)[8],
            'actual_pf_da' => $this->achievement($user->id, $param)[9],
            'target_pf_pc' => $this->achievement($user->id, $param)[10],
            'actual_pf_pc' => $this->achievement($user->id, $param)[11],
            'target_pf_mcc' => $this->achievement($user->id, $param)[12],
            'actual_pf_mcc' => $this->achievement($user->id, $param)[13],
            'target_week1' => $this->achievement($user->id, $param)[14],
            'actual_week1' => $this->achievement($user->id, $param)[15],
            'target_week2' => $this->achievement($user->id, $param)[16],
            'actual_week2' => $this->achievement($user->id, $param)[17],
            'target_week3' => $this->achievement($user->id, $param)[18],
            'actual_week3' => $this->achievement($user->id, $param)[19],
            'target_week4' => $this->achievement($user->id, $param)[20],
            'actual_week4' => $this->achievement($user->id, $param)[21],
            'target_week5' => $this->achievement($user->id, $param)[22],
            'actual_week5' => $this->achievement($user->id, $param)[23],
            'working_days' => $this->getTotalHK($user->id)
        ]);
    }

    public function getTotalHK($id){

        $user = User::where('id', $id)->first();

        $countHK = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', Carbon::now()->format('m'))
                    ->whereYear('date', Carbon::now()->format('Y'))
                    ->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('status', '<>', 'Off')->count('id');

//        if($countHK > 26){
//            $countHK = 26;
//        }

        return $countHK;

    }

    public function getAchievementForSupervisor($param){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role_group', '<>','Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');

        if(count($spvDemoIds) > 0){
            $promoterIds = EmployeeStore::whereIn('store_id', $spvDemoIds)
                            ->whereHas('user', function ($query){
                                return $query->whereHas('role', function($query2){
                                    return $query2->where('role_group', 'Demonstrator DA');
                                });
                            })
                            ->pluck('user_id');
        }

        $promoters = User::whereIn('id', $promoterIds)->get();

        foreach($promoters as $promoter){

            $promoter['target'] =  $this->achievement($promoter['id'], $param)[0];
            $promoter['actual'] =  $this->achievement($promoter['id'], $param)[1];

        }

        return response()->json($promoters);
    }

    public function getAchievementForSupervisorWithParam($param, $id){

        $user = User::where('id', $id)->first();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            // return $query->where('role', '<>', 'Demonstrator DA');
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role_group', '<>','Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');

        if(count($spvDemoIds) > 0){
            $promoterIds = EmployeeStore::whereIn('store_id', $spvDemoIds)
                            ->whereHas('user', function ($query){
                                // return $query->where('role', 'Demonstrator DA');
                                return $query->whereHas('role', function($query2){
                                return $query2->where('role_group','Demonstrator DA');
                            });
                            })
                            ->pluck('user_id');
        }

        $promoters = User::whereIn('id', $promoterIds)->get();

        foreach($promoters as $promoter){

            $promoter['target'] =  $this->achievement($promoter['id'], $param)[0];
            $promoter['actual'] =  $this->achievement($promoter['id'], $param)[1];

        }

        return response()->json($promoters);
    }

    public function getAchievementForSupervisorWithParamAlt($param, $id){

        $user = User::where('id', $id)->first();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        $promoDemo = "Promoter";

        if(count($spvDemoIds) > 0){
            $promoDemo = "Demonstrator";
            $storeIds = $spvDemoIds;
        }

        $store = Store::whereIn('id', $storeIds)->get();

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        // foreach ($store as $data) {

            $summary = SummaryTargetActual::whereIn('storeId', $storeIds)
                        ->where('user_role', $promoDemo)
                        // ->where('partner', 0)
                        ->where('sell_type', 'Sell In')
                        ->get(
                            array(
                                DB::raw('SUM(target_da) + SUM(target_pc) + SUM(target_mcc) AS total_target'),
                                DB::raw('SUM(actual_da) + SUM(actual_pc) + SUM(actual_mcc) AS total_actual'),
                                DB::raw('SUM(target_pf_da) + SUM(target_pf_pc) + SUM(target_pf_mcc) AS total_target_pf'),
                                DB::raw('SUM(actual_pf_da) + SUM(actual_pf_pc) + SUM(actual_pf_mcc) AS total_actual_pf'),
                                )
                            );

            if($param == 2){
                $summary = SummaryTargetActual::whereIn('storeId', $storeIds)
                            ->where('user_role', $promoDemo)
                            // ->where('partner', 0)
                            ->where('sell_type', 'Sell Out')
                            ->get(
                                array(
                                    DB::raw('SUM(target_da) + SUM(target_pc) + SUM(target_mcc) AS total_target'),
                                    DB::raw('SUM(actual_da) + SUM(actual_pc) + SUM(actual_mcc) AS total_actual'),
                                    DB::raw('SUM(target_pf_da) + SUM(target_pf_pc) + SUM(target_pf_mcc) AS total_target_pf'),
                                    DB::raw('SUM(actual_pf_da) + SUM(actual_pf_pc) + SUM(actual_pf_mcc) AS total_actual_pf'),
                                    )
                                );
            }

            // if($summary){
                
            //     foreach ($summary as $detail) {
                    
            //         $totalTarget += $detail->target_da;
            //         $totalTarget += $detail->target_pc;
            //         $totalTarget += $detail->target_mcc;

            //         $totalActual += $detail->actual_da;
            //         $totalActual += $detail->actual_pc;
            //         $totalActual += $detail->actual_mcc;

            //         $totalTargetPF += $detail->target_pf_da;
            //         $totalTargetPF += $detail->target_pf_pc;
            //         $totalTargetPF += $detail->target_pf_mcc;

            //         $totalActualPF += $detail->actual_pf_da;
            //         $totalActualPF += $detail->actual_pf_pc;
            //         $totalActualPF += $detail->actual_pf_mcc;

            //     }

            // }

        // }

        // return $summary[0]->total_target;

        return array($summary[0]->total_target, $summary[0]->total_actual, $summary[0]->total_target_pf, $summary[0]->total_actual_pf);

        // PER PROMOTER

        // $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
        //                 ->whereHas('user', function ($query){
        //                     // return $query->where('role', '<>', 'Demonstrator DA');
        //                     return $query->whereHas('role', function($query2){
        //                         return $query2->where('role_group', '<>','Demonstrator DA');
        //                     });
        //                 })
        //                 ->pluck('user_id');

        // if(count($spvDemoIds) > 0){
        //     $promoterIds = EmployeeStore::whereIn('store_id', $spvDemoIds)
        //                     ->whereHas('user', function ($query){
        //                         // return $query->where('role', 'Demonstrator DA');
        //                         return $query->whereHas('role', function($query2){
        //                         return $query2->where('role_group','Demonstrator DA');
        //                     });
        //                     })
        //                     ->pluck('user_id');
        // }

        // $promoters = User::whereIn('id', $promoterIds)->get();

        // $target = 0;
        // $target_pf = 0;
        // $actual = 0;
        // $actual_pf = 0;

        // foreach($promoters as $promoter){

        //     $target +=  $this->achievement($promoter['id'], $param)[0];
        //     $actual +=  $this->achievement($promoter['id'], $param)[1];

        //     $target_pf +=  $this->achievement($promoter['id'], $param)[24];
        //     $actual_pf +=  $this->achievement($promoter['id'], $param)[25];
        // }

        // return response()->json(['target' => $target, 'actual' => $actual, 'target_pf' => $target_pf, 'actual_pf' => $actual_pf]);

        // return array($target, $actual, $target_pf, $actual_pf);
    }

    public function getSupervisorAchievement($param, $sell_param){

        $user = JWTAuth::parseToken()->authenticate();

        if($param == 1) { // BY NATIONAL

            $supervisor = User::where(function ($query) {
                return $query->whereHas('role', function($query2){
                    return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                });
            })->with('stores.district.area.region')->get();

            $result = $this->getSupervisorCollection($supervisor);

            foreach ($result as $item) {

                // WITH SUM T/A

//                 $target = 0;
//                 $actual = 0;
//                 $promoDemo = "Promoter";

//                 $stores = Store::where('user_id', $item['id'])->get();
//                 $spvDemoIds = SpvDemo::where('user_id', $item['id'])->pluck('store_id');

//                 if(count($spvDemoIds) > 0){
//                     $promoDemo = "Demonstrator";
//                     $stores = Store::whereIn('id', $spvDemoIds)->get();
//                 }                

//                 foreach ($stores as $data) {

//                     $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
//                                 ->where('user_role', $promoDemo)
//                                 ->where('sell_type', 'Sell In')->first();

//                     if($sell_param == 2){
//                         $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
//                                     ->where('user_role', $promoDemo)
//                                     ->where('sell_type', 'Sell Out')->first();
//                     }

//                     if($summary){
// //                        if(count($spvDemoIds) > 0){
// //                            $target += $summary->sum_target_store_demo;
// //                            $actual += $summary->sum_actual_store_demo;
// //                        }else{
// //                            if($summary->user_role == 'Promoter'){
// //                                $target += $summary->sum_target_store_promo;
// //                                $actual += $summary->sum_actual_store_promo;
// //                            }else{
// //                                $target += $summary->sum_target_store_demo;
// //                                $actual += $summary->sum_actual_store_demo;
// //                            }
// //                        }
//                         if($promoDemo == 'Promoter'){
//                             $target += $summary->sum_target_store_promo;
//                             $actual += $summary->sum_actual_store_promo;
//                         }else{
//                             $target += $summary->sum_target_store_demo;
//                             $actual += $summary->sum_actual_store_demo;
//                         }
//                     }

//                 }

//                 $item['target'] = $target;
//                 $item['actual'] = $actual;

                // WITHOUT SUM T/A                

                $item['target'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[0];
                $item['actual'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[1];

            }

            return response()->json($result);

        }else if($param == 2) { // BY REGION

            $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');



            $supervisor = User::where(function ($query) {
                // return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                 return $query->whereHas('role', function($query2){
                    return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                });
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area.region', function ($query) use ($regionIds){
                        return $query->whereIn('regions.id', $regionIds);
                    })->get();

            $demoStoreIds = SpvDemo::whereHas('store.district.area.region', function ($query) use ($regionIds){
                                return $query->whereIn('regions.id', $regionIds);
                           })->pluck('user_id');
            $spvdemo = User::with('spvDemos.store.district.area.region')->whereIn('id', $demoStoreIds)->get();

            $result = $this->getSupervisorCollection($supervisor, $spvdemo);

            return response()->json($result);

            foreach ($result as $item) {

                // $target = 0;
                // $actual = 0;
                // $promoDemo = "Promoter";

                // $stores = Store::where('user_id', $item['id'])->get();
                // $spvDemoIds = SpvDemo::where('user_id', $item['id'])->pluck('store_id');

                // if(count($spvDemoIds) > 0){
                //     $promoDemo = "Demonstrator";
                //     $stores = Store::whereIn('id', $spvDemoIds)->get();
                // }

                // foreach ($stores as $data) {

                //     $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
                //                 ->where('user_role', $promoDemo)
                //                 ->where('sell_type', 'Sell In')->first();

                //     if($sell_param == 2){
                //         $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
                //                     ->where('user_role', $promoDemo)
                //                     ->where('sell_type', 'Sell Out')->first();
                //     }

                //     if($summary){
                //         if($promoDemo == 'Promoter'){
                //             $target += $summary->sum_target_store_promo;
                //             $actual += $summary->sum_actual_store_promo;
                //         }else{
                //             $target += $summary->sum_target_store_demo;
                //             $actual += $summary->sum_actual_store_demo;
                //         }
                //     }

                // }

                // $item['target'] = $target;
                // $item['actual'] = $actual;

                // $item['target'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[0];
                // $item['actual'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[1];

            }

            return response()->json($result);

        }else if($param == 3) { // BY AREA

            $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');
            // $dedicates = DmArea::where('user_id', $user->id)->pluck('dedicate')->toArray();
            // if(in_array("HYBRID", $dedicates)){
            //     array_push($dedicates, "DA", "PC");
            // }

            $supervisor = User::where(function ($query) {
                // return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                 return $query->whereHas('role', function($query2){
                    return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                });
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area', function ($query) use ($areaIds){
                        return $query->whereIn('areas.id', $areaIds);
                    })
                    // ->whereHas('stores', function ($query) use ($dedicates){
                    //     return $query->whereIn('dedicate', $dedicates);
                    // })
                    ->get();

            $demoStoreIds = SpvDemo::whereHas('store.district.area', function ($query) use ($areaIds){
                                return $query->whereIn('areas.id', $areaIds);
                           })->pluck('user_id');
            $spvdemo = User::with('spvDemos.store.district.area.region')->whereIn('id', $demoStoreIds)->get();

            $result = $this->getSupervisorCollection($supervisor, $spvdemo);

            foreach ($result as $item) {

                // $target = 0;
                // $actual = 0;
                // $promoDemo = "Promoter";

                // $stores = Store::where('user_id', $item['id'])->get();
                // $spvDemoIds = SpvDemo::where('user_id', $item['id'])->pluck('store_id');

                // if(count($spvDemoIds) > 0){
                //     $promoDemo = "Demonstrator";
                //     $stores = Store::whereIn('id', $spvDemoIds)->get();
                // }

                // foreach ($stores as $data) {

                //     $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
                //                 ->where('user_role', $promoDemo)
                //                 ->where('sell_type', 'Sell In')->first();

                //     if($sell_param == 2){
                //         $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
                //                     ->where('user_role', $promoDemo)
                //                     ->where('sell_type', 'Sell Out')->first();
                //     }

                //     if($summary){
                //         if($promoDemo == 'Promoter'){
                //             $target += $summary->sum_target_store_promo;
                //             $actual += $summary->sum_actual_store_promo;
                //         }else{
                //             $target += $summary->sum_target_store_demo;
                //             $actual += $summary->sum_actual_store_demo;
                //         }
                //     }

                // }

                // $item['target'] = $target;
                // $item['actual'] = $actual;

                $item['target'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[0];
                $item['actual'] = $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id'])[1];

                // $item['tes'] =  $this->getAchievementForSupervisorWithParamAlt($sell_param, $item['id']);
                // $item['actual'] = 'ddd';


            }

            return response()->json($result);

        }

    }

    public function getSupervisorCollection($supervisor, $spvdemo = null){

        $result = new Collection();
        $resultDemo = new Collection();

        foreach ($supervisor as $data) {

            $arr_area = [];
            $arr_region = [];
            $collection = new Collection();

            foreach ($data->stores as $detail) {
                if (!in_array($detail->district->area->name, $arr_area)) {
                    array_push($arr_area, $detail->district->area->name);
                }

                if (!in_array($detail->district->area->region->name, $arr_region)) {
                    array_push($arr_region, $detail->district->area->region->name);
                }
            }

            if(count($data->stores) == 0){
                $spvDemoIds = SpvDemo::where('user_id', $data->id)->pluck('store_id');

                if(count($spvDemoIds) > 0){
                    $stores = Store::whereIn('id', $spvDemoIds)->get();

                    foreach ($stores as $detail){
                        if (!in_array($detail->district->area->name, $arr_area)) {
                            array_push($arr_area, $detail->district->area->name);
                        }

                        if (!in_array($detail->district->area->region->name, $arr_region)) {
                            array_push($arr_region, $detail->district->area->region->name);
                        }
                    }
                }
            }

            for ($i = 0; $i < count($arr_area); $i++) {
                $data['area'] .= $arr_area[$i];

                if ($i != count($arr_area) - 1) {
                    $data['area'] .= ', ';
                }
            }

            for ($i = 0; $i < count($arr_region); $i++) {
                $data['region'] .= $arr_region[$i];

                if ($i != count($arr_region) - 1) {
                    $data['region'] .= ', ';
                }
            }

            $collection['id'] = $data['id'];
            $collection['nik'] = $data['nik'];
            $collection['name'] = $data['name'];
            $collection['area'] = $data['area'];
            $collection['region'] = $data['region'];

            $result->push($collection);

        }

        if($spvdemo != null) {

            foreach ($spvdemo as $data) {

                $arr_area = [];
                $arr_region = [];
                $collection = new Collection();

                foreach ($data->spvDemos as $detail) {
                    if (!in_array($detail->store->district->area->name, $arr_area)) {
                        array_push($arr_area, $detail->store->district->area->name);
                    }

                    if (!in_array($detail->store->district->area->region->name, $arr_region)) {
                        array_push($arr_region, $detail->store->district->area->region->name);
                    }
                }

                for ($i = 0; $i < count($arr_area); $i++) {
                    $data['area'] .= $arr_area[$i];

                    if ($i != count($arr_area) - 1) {
                        $data['area'] .= ', ';
                    }
                }

                for ($i = 0; $i < count($arr_region); $i++) {
                    $data['region'] .= $arr_region[$i];

                    if ($i != count($arr_region) - 1) {
                        $data['region'] .= ', ';
                    }
                }

                $collection['id'] = $data['id'];
                $collection['nik'] = $data['nik'];
                $collection['name'] = $data['name'];
                $collection['area'] = $data['area'];
                $collection['region'] = $data['region'];

                $resultDemo->push($collection);

            }

            $result = $result->merge($resultDemo);

        }

        return $result;
    }

    public function getTotalAchievementSupervisor($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // WITHOUT SUM T/A

        return response()->json([
            'total_target' => $this->getAchievementForSupervisorWithParamAlt($param, $user->id)[0],
            'total_actual' => $this->getAchievementForSupervisorWithParamAlt($param, $user->id)[1],
            'total_pf_target' => $this->getAchievementForSupervisorWithParamAlt($param, $user->id)[2],
            'total_pf_actual' => $this->getAchievementForSupervisorWithParamAlt($param, $user->id)[3],
            'working_days' => $this->getTotalHK($user->id)
        ]);

        // WITH SUM T/A

        // $storeIds = Store::where('user_id', $user->id)->pluck('id');
        // $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');
        // $promoDemo = "Promoter";

        // if(count($spvDemoIds) > 0){
        //     $promoDemo = "Demonstrator";
        //     $storeIds = Store::whereIn('id', $spvDemoIds)->pluck('id');
        // }

        // $store = Store::whereIn('id', $storeIds)->get();

        // $totalTarget = 0;
        // $totalActual = 0;
        // $totalTargetPF = 0;
        // $totalActualPF = 0;

        // foreach ($store as $data) {

        //     $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
        //                         ->where('user_role', $promoDemo)
        //                         ->where('sell_type', 'Sell In')->first();

        //             if($param == 2){
        //                 $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])
        //                             ->where('user_role', $promoDemo)
        //                             ->where('sell_type', 'Sell Out')->first();
        //             }

        //             if($summary){
        //                 if($promoDemo == 'Promoter'){
        //                     $totalTarget += $summary->sum_target_store_promo;
        //                     $totalActual += $summary->sum_actual_store_promo;
        //                     $totalTargetPF += $summary->sum_pf_target_store_promo;
        //                     $totalActualPF += $summary->sum_pf_actual_store_promo;
        //                 }else{
        //                     $totalTarget += $summary->sum_target_store_demo;
        //                     $totalActual += $summary->sum_actual_store_demo;
        //                     $totalTargetPF += $summary->sum_pf_target_store_demo;
        //                     $totalActualPF += $summary->sum_pf_actual_store_demo;
        //                 }
        //             }
        // }

        // return response()->json([
        //     'total_target' => $totalTarget,
        //     'total_actual' => $totalActual,
        //     'total_pf_target' => $totalTargetPF,
        //     'total_pf_actual' => $totalActualPF,
        //     'working_days' => $this->getTotalHK($user->id)
        // ]);
    }

    public function getTotalAchievementArea($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');
//         $dedicates = DmArea::where('user_id', $user->id)->pluck('dedicate')->toArray();
// //        $area = Area::whereIn('id', $areaIds)->get();
//         if(in_array("HYBRID", $dedicates)){
//             array_push($dedicates, "DA", "PC");
//         }

        $stores = Store::whereHas('district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })
                    // ->whereIn('dedicate', $dedicates)
                    ->get();
        $area = Area::whereIn('id', $areaIds)->get();

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        foreach ($area as $data) {

            $summary = SummaryTargetActual::where('area_id', $data['id'])
                        ->where('partner', 0)
                        ->where('sell_type', 'Sell In')->get();

            if($param == 2){
                $summary = SummaryTargetActual::where('area_id', $data['id'])
                            ->where('partner', 0)
                            ->where('sell_type', 'Sell Out')->get();
            }

            if($summary) {

                foreach ($summary as $detail) {
                    
                    $totalTarget += $detail->target_da;
                    $totalTarget += $detail->target_pc;
                    $totalTarget += $detail->target_mcc;

                    $totalActual += $detail->actual_da;
                    $totalActual += $detail->actual_pc;
                    $totalActual += $detail->actual_mcc;

                    $totalTargetPF += $detail->target_pf_da;
                    $totalTargetPF += $detail->target_pf_pc;
                    $totalTargetPF += $detail->target_pf_mcc;

                    $totalActualPF += $detail->actual_pf_da;
                    $totalActualPF += $detail->actual_pf_pc;
                    $totalActualPF += $detail->actual_pf_mcc;

                }                
            }
        }

        return response()->json([
            'total_target' => $totalTarget,
            'total_actual' => $totalActual,
            'total_pf_target' => $totalTargetPF,
            'total_pf_actual' => $totalActualPF,
        ]);
    }

    public function getTotalAchievementRegion($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');
        $region = Region::whereIn('id', $regionIds)->get();

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])
                        ->where('partner', 0)
                        ->where('sell_type', 'Sell In')->get();

            if($param == 2){
                $summary = SummaryTargetActual::where('region_id', $data['id'])
                            ->where('partner', 0)
                            ->where('sell_type', 'Sell Out')->get();
            }

            if($summary) {

                foreach ($summary as $detail){

                    $totalTarget += $detail->target_da;
                    $totalTarget += $detail->target_pc;
                    $totalTarget += $detail->target_mcc;

                    $totalActual += $detail->actual_da;
                    $totalActual += $detail->actual_pc;
                    $totalActual += $detail->actual_mcc;

                    $totalTargetPF += $detail->target_pf_da;
                    $totalTargetPF += $detail->target_pf_pc;
                    $totalTargetPF += $detail->target_pf_mcc;

                    $totalActualPF += $detail->actual_pf_da;
                    $totalActualPF += $detail->actual_pf_pc;
                    $totalActualPF += $detail->actual_pf_mcc;

                }                
            }
        }

        return response()->json([
            'total_target' => $totalTarget,
            'total_actual' => $totalActual,
            'total_pf_target' => $totalTargetPF,
            'total_pf_actual' => $totalActualPF,
        ]);
    }

    public function getTotalAchievementNational($param)
    {
        // $region = Region::whereIn('id', [1, 2, 3, 4])->get();

        $region = Region::all();

        $totalTarget = 0;
        $totalActual = 0;
        $totalTargetPF = 0;
        $totalActualPF = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])
                        ->where('partner', 0)
                        ->where('sell_type', 'Sell In')->get();

            if($param == 2){
                $summary = SummaryTargetActual::where('region_id', $data['id'])
                            ->where('partner', 0)
                            ->where('sell_type', 'Sell Out')->get();
            }

            if($summary) {

                foreach ($summary as $detail) {
                    
                    $totalTarget += $detail->target_da;
                    $totalTarget += $detail->target_pc;
                    $totalTarget += $detail->target_mcc;

                    $totalActual += $detail->actual_da;
                    $totalActual += $detail->actual_pc;
                    $totalActual += $detail->actual_mcc;

                    $totalTargetPF += $detail->target_pf_da;
                    $totalTargetPF += $detail->target_pf_pc;
                    $totalTargetPF += $detail->target_pf_mcc;

                    $totalActualPF += $detail->actual_pf_da;
                    $totalActualPF += $detail->actual_pf_pc;
                    $totalActualPF += $detail->actual_pf_mcc;

                }

                // $totalTarget += $summary->sum_target_region;
                // $totalActual += $summary->sum_actual_region;
                // $totalTargetPF += $summary->sum_pf_target_region;
                // $totalActualPF += $summary->sum_pf_actual_region;
            }
        }

        return response()->json([
            'total_target' => $totalTarget,
            'total_actual' => $totalActual,
            'total_pf_target' => $totalTargetPF,
            'total_pf_actual' => $totalActualPF,
        ]);
    }

    public function getAchievementByStore($param){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
        $stores = Store::whereIn('id', $storeIds)->get();

        $perStore = new Collection();

        if($stores){

            foreach ($stores as $data){

                $detailsData = ([
                    'id_store' => $data->id,
                    'store_name' => $data->store_name_1,
                    'store_id' => $data->store_id . ' - ' . $data->dedicate,
                    'target' => $this->achievementByStore($user->id, $data->id, $param)[0],
                    'actual' => $this->achievementByStore($user->id, $data->id, $param)[1],
                    'target_da' => $this->achievementByStore($user->id, $data->id, $param)[2],
                    'actual_da' => $this->achievementByStore($user->id, $data->id, $param)[3],
                    'target_pc' => $this->achievementByStore($user->id, $data->id, $param)[4],
                    'actual_pc' => $this->achievementByStore($user->id, $data->id, $param)[5],
                    'target_mcc' => $this->achievementByStore($user->id, $data->id, $param)[6],
                    'actual_mcc' => $this->achievementByStore($user->id, $data->id, $param)[7],
                    'target_pf_da' => $this->achievementByStore($user->id, $data->id, $param)[8],
                    'actual_pf_da' => $this->achievementByStore($user->id, $data->id, $param)[9],
                    'target_pf_pc' => $this->achievementByStore($user->id, $data->id, $param)[10],
                    'actual_pf_pc' => $this->achievementByStore($user->id, $data->id, $param)[11],
                    'target_pf_mcc' => $this->achievementByStore($user->id, $data->id, $param)[12],
                    'actual_pf_mcc' => $this->achievementByStore($user->id, $data->id, $param)[13]
                ]);
                $perStore->push($detailsData);
            }
        }

        return response()->json($perStore);

    }

    public function getAchievementByStoreWithParam($param, $id){

        $user = User::where('id', $id)->first();

        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
        $stores = Store::whereIn('id', $storeIds)->get();

        $perStore = new Collection();

        if($stores){

            foreach ($stores as $data){

                $detailsData = ([
                    'id_store' => $data->id,
                    'store_name' => $data->store_name_1,
                    'store_id' => $data->store_id . ' - ' . $data->dedicate,
                    'target' => $this->achievementByStore($user->id, $data->id, $param)[0],
                    'actual' => $this->achievementByStore($user->id, $data->id, $param)[1],
                    'target_da' => $this->achievementByStore($user->id, $data->id, $param)[2],
                    'actual_da' => $this->achievementByStore($user->id, $data->id, $param)[3],
                    'target_pc' => $this->achievementByStore($user->id, $data->id, $param)[4],
                    'actual_pc' => $this->achievementByStore($user->id, $data->id, $param)[5],
                    'target_mcc' => $this->achievementByStore($user->id, $data->id, $param)[6],
                    'actual_mcc' => $this->achievementByStore($user->id, $data->id, $param)[7],
                    'target_pf_da' => $this->achievementByStore($user->id, $data->id, $param)[8],
                    'actual_pf_da' => $this->achievementByStore($user->id, $data->id, $param)[9],
                    'target_pf_pc' => $this->achievementByStore($user->id, $data->id, $param)[10],
                    'actual_pf_pc' => $this->achievementByStore($user->id, $data->id, $param)[11],
                    'target_pf_mcc' => $this->achievementByStore($user->id, $data->id, $param)[12],
                    'actual_pf_mcc' => $this->achievementByStore($user->id, $data->id, $param)[13]
                ]);
                $perStore->push($detailsData);
            }
        }

        return response()->json($perStore);


    }

    public function salesmanAchievement($param){

        $user = JWTAuth::parseToken()->authenticate();

        $ta = SalesmanSummaryTargetActual::where('user_id', $user->id)->first();

        if($param == 1){ // Sales & Sales PF

            return response()->json([
                'id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'area' => (@$ta->area) ? $ta->area : '',
                'target_sales' => (@$ta->target_sales) ? $ta->target_sales : 0,
                'actual_sales' => (@$ta->actual_sales) ? $ta->actual_sales : 0,
                'target_sales_pf' => (@$ta->target_sales_pf) ? $ta->target_sales_pf : 0,
                'actual_sales_pf' => (@$ta->actual_sales_pf) ? $ta->actual_sales_pf : 0,
            ]);

        }else if($param == 2){ // CALL

            return response()->json([
                'id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'area' => (@$ta->area) ? $ta->area : '',
                'target_call' => (@$ta->target_call) ? $ta->target_call : 0,
                'actual_call' => (@$ta->actual_call) ? $ta->actual_call : 0,
            ]);

        }else if($param == 3){ // ACTIVE OUTLET

            return response()->json([
                'id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'area' => (@$ta->area) ? $ta->area : '',
                'target_active_outlet' => (@$ta->target_active_outlet) ? $ta->target_active_outlet : 0,
                'actual_active_outlet' => (@$ta->actual_active_outlet) ? $ta->actual_active_outlet : 0,
            ]);

        }else if($param == 4){ // EFFECTIVE CALL

            return response()->json([
                'id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'area' => (@$ta->area) ? $ta->area : '',
                'target_effective_call' => (@$ta->target_effective_call) ? $ta->target_effective_call : 0,
                'actual_effective_call' => (@$ta->actual_effective_call) ? $ta->actual_effective_call : 0,
            ]);

        }

    }

    public function salesmanAchievementList(){

        $user = User::whereHas('role', function($query){
            return $query->where('role_group', 'Salesman Explorer');
        })->get();

        $result = new Collection();

        if($user){

            foreach ($user as $data){

                $ta = SalesmanSummaryTargetActual::where('user_id', $data->id)->first();

                $detailsData = ([
                    'id' => $data->id,
                    'nik' => $data->nik,
                    'name' => $data->name,
                    'area' => (@$ta->area) ? $ta->area : '',
                    'target_call' => (@$ta->target_call) ? $ta->target_call : 0,
                    'actual_call' => (@$ta->actual_call) ? $ta->actual_call : 0,
                    'target_active_outlet' => (@$ta->target_active_outlet) ? $ta->target_active_outlet : 0,
                    'actual_active_outlet' => (@$ta->actual_active_outlet) ? $ta->actual_active_outlet : 0,
                    'target_effective_call' => (@$ta->target_effective_call) ? $ta->target_effective_call : 0,
                    'actual_effective_call' => (@$ta->actual_effective_call) ? $ta->actual_effective_call : 0,
                    'target_sales' => (@$ta->target_sales) ? $ta->target_sales : 0,
                    'actual_sales' => (@$ta->actual_sales) ? $ta->actual_sales : 0,
                    'target_sales_pf' => (@$ta->target_sales_pf) ? $ta->target_sales_pf : 0,
                    'actual_sales_pf' => (@$ta->actual_sales_pf) ? $ta->actual_sales_pf : 0,
                ]);
                $result->push($detailsData);

            }

        }

        return response()->json($result);

    }

    public function salesmanAchievementByNational(){

        $ta = SalesmanSummaryTargetActual::first();

        return response()->json([
            'sum_national_target_call' => (@$ta->sum_national_target_call) ? $ta->sum_national_target_call : 0,
            'sum_national_actual_call' => (@$ta->sum_national_actual_call) ? $ta->sum_national_actual_call : 0,
            'sum_national_target_active_outlet' => (@$ta->sum_national_target_active_outlet) ? $ta->sum_national_target_active_outlet : 0,
            'sum_national_actual_active_outlet' => (@$ta->sum_national_actual_active_outlet) ? $ta->sum_national_actual_active_outlet : 0,
            'sum_national_target_effective_call' => (@$ta->sum_national_target_effective_call) ? $ta->sum_national_target_effective_call : 0,
            'sum_national_actual_effective_call' => (@$ta->sum_national_actual_effective_call) ? $ta->sum_national_actual_effective_call : 0,
            'sum_national_target_sales' => (@$ta->sum_national_target_sales) ? $ta->sum_national_target_sales : 0,
            'sum_national_actual_sales' => (@$ta->sum_national_actual_sales) ? $ta->sum_national_actual_sales : 0,
            'sum_national_target_sales_pf' => (@$ta->sum_national_target_sales_pf) ? $ta->sum_national_target_sales_pf : 0,
            'sum_national_actual_sales_pf' => (@$ta->sum_national_actual_sales_pf) ? $ta->sum_national_actual_sales_pf : 0,
        ]);

    }

}
