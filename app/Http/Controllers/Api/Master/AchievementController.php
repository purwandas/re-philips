<?php

namespace App\Http\Controllers\Api\Master;

use App\Reports\SummaryTargetActual;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class AchievementController extends Controller
{
    // Default summarize for total achievement
    public function achievement($id){

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

        $data = SummaryTargetActual::where('user_id', $id)->get();

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
        }

        return array($totalTarget, $totalActual,
            $totalTargetDA, $totalActualDA, $totalTargetPC, $totalActualPC, $totalTargetMCC, $totalActualMCC,
            $totalTargetPFDA, $totalActualPFDA, $totalTargetPFPC, $totalActualPFPC, $totalTargetPFMCC, $totalActualPFMCC,
            $totalTargetW1, $totalActualW1, $totalTargetW2, $totalActualW2, $totalTargetW3, $totalActualW3,
            $totalTargetW4, $totalActualW4, $totalTargetW5, $totalActualW5);

    }

    public function getAchievement(){

        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'target' => $this->achievement($user->id)[0],
            'actual' => $this->achievement($user->id)[1],
            'target_da' => $this->achievement($user->id)[2],
            'actual_da' => $this->achievement($user->id)[3],
            'target_pc' => $this->achievement($user->id)[4],
            'actual_pc' => $this->achievement($user->id)[5],
            'target_mcc' => $this->achievement($user->id)[6],
            'actual_mcc' => $this->achievement($user->id)[7],
            'target_pf_da' => $this->achievement($user->id)[8],
            'actual_pf_da' => $this->achievement($user->id)[9],
            'target_pf_pc' => $this->achievement($user->id)[10],
            'actual_pf_pc' => $this->achievement($user->id)[11],
            'target_pf_mcc' => $this->achievement($user->id)[12],
            'actual_pf_mcc' => $this->achievement($user->id)[13],
            'target_week1' => $this->achievement($user->id)[14],
            'actual_week1' => $this->achievement($user->id)[15],
            'target_week2' => $this->achievement($user->id)[16],
            'actual_week2' => $this->achievement($user->id)[17],
            'target_week3' => $this->achievement($user->id)[18],
            'actual_week3' => $this->achievement($user->id)[19],
            'target_week4' => $this->achievement($user->id)[20],
            'actual_week4' => $this->achievement($user->id)[21],
            'target_week5' => $this->achievement($user->id)[22],
            'actual_week5' => $this->achievement($user->id)[23],
        ]);
    }

    public function getAchievementWithParam($id){

        $user = User::where('id', $id)->first();

        return response()->json([
            'target' => $this->achievement($user->id)[0],
            'actual' => $this->achievement($user->id)[1],
            'target_da' => $this->achievement($user->id)[2],
            'actual_da' => $this->achievement($user->id)[3],
            'target_pc' => $this->achievement($user->id)[4],
            'actual_pc' => $this->achievement($user->id)[5],
            'target_mcc' => $this->achievement($user->id)[6],
            'actual_mcc' => $this->achievement($user->id)[7],
            'target_pf_da' => $this->achievement($user->id)[8],
            'actual_pf_da' => $this->achievement($user->id)[9],
            'target_pf_pc' => $this->achievement($user->id)[10],
            'actual_pf_pc' => $this->achievement($user->id)[11],
            'target_pf_mcc' => $this->achievement($user->id)[12],
            'actual_pf_mcc' => $this->achievement($user->id)[13],
            'target_week1' => $this->achievement($user->id)[14],
            'actual_week1' => $this->achievement($user->id)[15],
            'target_week2' => $this->achievement($user->id)[16],
            'actual_week2' => $this->achievement($user->id)[17],
            'target_week3' => $this->achievement($user->id)[18],
            'actual_week3' => $this->achievement($user->id)[19],
            'target_week4' => $this->achievement($user->id)[20],
            'actual_week4' => $this->achievement($user->id)[21],
            'target_week5' => $this->achievement($user->id)[22],
            'actual_week5' => $this->achievement($user->id)[23],
        ]);
    }

    public function getAchievementForSupervisor(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)->get();

//        foreach($promoters as $promoter){
//
//            $detail = AttendanceDetail::where('attendance_id', $attendance->attendance_id)
//                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
//                    ->select('attendance_details.check_in', 'attendance_details.check_in_longitude', 'attendance_details.check_in_latitude', 'attendance_details.check_in_location',
//                        'attendance_details.check_out', 'attendance_details.check_out_longitude', 'attendance_details.check_out_latitude', 'attendance_details.check_out_location', 'attendance_details.detail as keterangan',
//                        'stores.store_id', 'stores.store_name_1', 'stores.store_name_2')
//                    ->get();
//
//            if($attendance->status == 'Masuk'){
//                $attendance['detail'] = $detail;
//            }else{
//                if($attendance->reject == '1'){
//                    $attendance['detail'] = $detail;
//                }else {
//                    $attendance['detail'] = [];
//                }
//            }
//
//        }

//        return response()->json($attendances);
    }
}
