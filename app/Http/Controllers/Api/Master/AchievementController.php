<?php

namespace App\Http\Controllers\Api\Master;

use App\Area;
use App\Region;
use App\Reports\SummaryTargetActual;
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
        }

        return array($totalTarget, $totalActual,
            $totalTargetDA, $totalActualDA, $totalTargetPC, $totalActualPC, $totalTargetMCC, $totalActualMCC,
            $totalTargetPFDA, $totalActualPFDA, $totalTargetPFPC, $totalActualPFPC, $totalTargetPFMCC, $totalActualPFMCC,
            $totalTargetW1, $totalActualW1, $totalTargetW2, $totalActualW2, $totalTargetW3, $totalActualW3,
            $totalTargetW4, $totalActualW4, $totalTargetW5, $totalActualW5);

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

        $data = SummaryTargetActual::where('user_id', $id)->where('storeId', $storeId)->where('sell_type', 'Sell In')->get();

        if($param == 2){
            $data = SummaryTargetActual::where('user_id', $id)->where('storeId', $storeId)->where('sell_type', 'Sell Out')->get();
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
        }

        return array($totalTarget, $totalActual,
            $totalTargetDA, $totalActualDA, $totalTargetPC, $totalActualPC, $totalTargetMCC, $totalActualMCC,
            $totalTargetPFDA, $totalActualPFDA, $totalTargetPFPC, $totalActualPFPC, $totalTargetPFMCC, $totalActualPFMCC,
            $totalTargetW1, $totalActualW1, $totalTargetW2, $totalActualW2, $totalTargetW3, $totalActualW3,
            $totalTargetW4, $totalActualW4, $totalTargetW5, $totalActualW5);

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

        if($countHK > 26){
            $countHK = 26;
        }

        return $countHK;

    }

    public function getAchievementForSupervisor($param){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
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
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)->get();

        foreach($promoters as $promoter){

            $promoter['target'] =  $this->achievement($promoter['id'], $param)[0];
            $promoter['actual'] =  $this->achievement($promoter['id'], $param)[1];

        }

        return response()->json($promoters);
    }

    public function getSupervisorAchievement($param, $sell_param){

        $user = JWTAuth::parseToken()->authenticate();

        if($param == 1) { // BY NATIONAL

            $supervisor = User::where(function ($query) {
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
            })->with('stores.district.area.region')->get();

            $result = $this->getSupervisorCollection($supervisor);

            foreach ($result as $item) {

                $target = 0;
                $actual = 0;

                $stores = Store::where('user_id', $item['id'])->get();

                foreach ($stores as $data) {

                    $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell In')->first();

                    if($sell_param == 2){
                        $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell Out')->first();
                    }

                    if($summary){
                        $target += $summary->sum_target_store;
                        $actual += $summary->sum_actual_store;
                    }

                }

                $item['target'] = $target;
                $item['actual'] = $actual;

            }

            return response()->json($result);

        }else if($param == 2) { // BY REGION

            $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');

            $supervisor = User::where(function ($query) {
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area.region', function ($query) use ($regionIds){
                        return $query->whereIn('id', $regionIds);
                    })->get();

            $result = $this->getSupervisorCollection($supervisor);

            foreach ($result as $item) {

                $target = 0;
                $actual = 0;

                $stores = Store::where('user_id', $item['id'])->get();

                foreach ($stores as $data) {

                    $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell In')->first();

                    if($sell_param == 2){
                        $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell Out')->first();
                    }

                    if($summary){
                        $target += $summary->sum_target_store;
                        $actual += $summary->sum_actual_store;
                    }

                }

                $item['target'] = $target;
                $item['actual'] = $actual;

            }

            return response()->json($result);

        }else if($param == 3) { // BY AREA

            $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');
            $dedicates = DmArea::where('user_id', $user->id)->pluck('dedicate')->toArray();
            if(in_array("HYBRID", $dedicates)){
                array_push($dedicates, "DA", "PC");
            }

            $supervisor = User::where(function ($query) {
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })
                    ->whereHas('stores', function ($query) use ($dedicates){
                        return $query->whereIn('dedicate', $dedicates);
                    })
                    ->get();

            $result = $this->getSupervisorCollection($supervisor);

            foreach ($result as $item) {

                $target = 0;
                $actual = 0;

                $stores = Store::where('user_id', $item['id'])->get();

                foreach ($stores as $data) {

                    $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell In')->first();

                    if($sell_param == 2){
                        $summary = SummaryTargetActual::whereIn('storeId', [$data['id']])->where('sell_type', 'Sell Out')->first();
                    }

                    if($summary){
                        $target += $summary->sum_target_store;
                        $actual += $summary->sum_actual_store;
                    }

                }

                $item['target'] = $target;
                $item['actual'] = $actual;

            }

            return response()->json($result);

        }

    }

    public function getSupervisorCollection($supervisor){

        $result = new Collection();

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

        return $result;
    }

    public function getTotalAchievementSupervisor($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $store = Store::whereIn('id', $storeIds)->get();

        $totalTarget = 0;
        $totalActual = 0;

        foreach ($store as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell In')->first();

            if($param == 2){
                $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell Out')->first();
            }

            if($summary) {
                $totalTarget += $summary->sum_target_store;
                $totalActual += $summary->sum_actual_store;
            }
        }

        return response()->json(['total_target' => $totalTarget, 'total_actual' => $totalActual]);
    }

    public function getTotalAchievementArea($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');
        $dedicates = DmArea::where('user_id', $user->id)->pluck('dedicate')->toArray();
//        $area = Area::whereIn('id', $areaIds)->get();
        if(in_array("HYBRID", $dedicates)){
            array_push($dedicates, "DA", "PC");
        }

        $stores = Store::whereHas('district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })->whereIn('dedicate', $dedicates)->get();

        $totalTarget = 0;
        $totalActual = 0;

        foreach ($stores as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell In')->first();

            if($param == 2){
                    $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell Out')->first();
            }

            if($summary) {
                $totalTarget += $summary->sum_target_store;
                $totalActual += $summary->sum_actual_store;
            }
        }

        return response()->json(['total_target' => $totalTarget, 'total_actual' => $totalActual]);
    }

    public function getTotalAchievementRegion($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');
        $region = Region::whereIn('id', $regionIds)->get();

        $totalTarget = 0;
        $totalActual = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell In')->first();

            if($param == 2){
                $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell Out')->first();
            }

            if($summary) {
                $totalTarget += $summary->sum_target_region;
                $totalActual += $summary->sum_actual_region;
            }
        }

        return response()->json(['total_target' => $totalTarget, 'total_actual' => $totalActual]);
    }

    public function getTotalAchievementNational($param)
    {
        $region = Region::whereIn('id', [1, 2, 3, 4])->get();

        $totalTarget = 0;
        $totalActual = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell In')->first();

            if($param == 2){
                $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell Out')->first();
            }

            if($summary) {
                $totalTarget += $summary->sum_target_region;
                $totalActual += $summary->sum_actual_region;
            }
        }

        return response()->json(['total_target' => $totalTarget, 'total_actual' => $totalActual]);
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
}
