<?php

namespace App\Traits;

use Carbon\Carbon;
use App\User;
use App\Attendance;
use App\EmployeeStore;
use App\Region;
use App\Reports\SummaryTargetActual;
use Auth;
use App\Store;
use App\DmArea;
use App\RsmRegion;

trait AchievementTrait {

    public function dataNational(){

        $region = Region::whereIn('id', [1, 2, 3, 4])->get();

        $totalTargetSellIn = 0;
        $totalActualSellIn = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell In')->first();

            if($summary) {
                $totalTargetSellIn += $summary->sum_target_region;
                $totalActualSellIn += $summary->sum_actual_region;
            }
        }

        $totalTargetSellOut = 0;
        $totalActualSellOut = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell Out')->first();

            if($summary) {
                $totalTargetSellOut += $summary->sum_target_region;
                $totalActualSellOut += $summary->sum_actual_region;
            }
        }

        return ([
                    'sell_in_target' => $totalTargetSellIn,
                    'sell_in_actual' => $totalActualSellIn,
                    'sell_in_at' => ($totalTargetSellIn == 0) ? 0 : ($totalActualSellIn/$totalTargetSellIn) * 100,
                    'sell_in_gap' => $totalTargetSellIn - $totalActualSellIn,
                    'sell_out_target' => $totalTargetSellOut,
                    'sell_out_actual' => $totalActualSellOut,
                    'sell_out_at' => ($totalTargetSellOut == 0) ? 0 : ($totalActualSellOut/$totalTargetSellOut) * 100,
                    'sell_out_gap' => $totalTargetSellOut - $totalActualSellOut,
                ]);

    }

    public function dataRegion(){

        $user = Auth::user();

        $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');
        $region = Region::whereIn('id', $regionIds)->get();

        $totalTargetSellIn = 0;
        $totalActualSellIn = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell In')->first();

            if($summary) {
                $totalTargetSellIn += $summary->sum_target_region;
                $totalActualSellIn += $summary->sum_actual_region;
            }
        }

        $totalTargetSellOut = 0;
        $totalActualSellOut = 0;

        foreach ($region as $data) {

            $summary = SummaryTargetActual::where('region_id', $data['id'])->where('sell_type', 'Sell Out')->first();

            if($summary) {
                $totalTargetSellOut += $summary->sum_target_region;
                $totalActualSellOut += $summary->sum_actual_region;
            }
        }

        return ([
                    'sell_in_target' => $totalTargetSellIn,
                    'sell_in_actual' => $totalActualSellIn,
                    'sell_in_at' => ($totalTargetSellIn == 0) ? 0 : ($totalActualSellIn/$totalTargetSellIn) * 100,
                    'sell_in_gap' => $totalTargetSellIn - $totalActualSellIn,
                    'sell_out_target' => $totalTargetSellOut,
                    'sell_out_actual' => $totalActualSellOut,
                    'sell_out_at' => ($totalTargetSellOut == 0) ? 0 : ($totalActualSellOut/$totalTargetSellOut) * 100,
                    'sell_out_gap' => $totalTargetSellOut - $totalActualSellOut,
                ]);

    }

    public function dataArea(){

        $user = Auth::user();

        $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');
        $dedicates = DmArea::where('user_id', $user->id)->pluck('dedicate')->toArray();
//        $area = Area::whereIn('id', $areaIds)->get();
        if(in_array("HYBRID", $dedicates)){
            array_push($dedicates, "DA", "PC");
        }

        $stores = Store::whereHas('district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })->whereIn('dedicate', $dedicates)->get();

        $totalTargetSellIn = 0;
        $totalActualSellIn = 0;

        foreach ($stores as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell In')->first();

            if($summary) {
                $totalTargetSellIn += $summary->sum_target_store;
                $totalActualSellIn += $summary->sum_actual_store;
            }
        }

        $totalTargetSellOut = 0;
        $totalActualSellOut = 0;

        foreach ($stores as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell Out')->first();

            if($summary) {
                $totalTargetSellOut += $summary->sum_target_store;
                $totalActualSellOut += $summary->sum_actual_store;
            }
        }

        return ([
                    'sell_in_target' => $totalTargetSellIn,
                    'sell_in_actual' => $totalActualSellIn,
                    'sell_in_at' => ($totalTargetSellIn == 0) ? 0 : ($totalActualSellIn/$totalTargetSellIn) * 100,
                    'sell_in_gap' => $totalTargetSellIn - $totalActualSellIn,
                    'sell_out_target' => $totalTargetSellOut,
                    'sell_out_actual' => $totalActualSellOut,
                    'sell_out_at' => ($totalTargetSellOut == 0) ? 0 : ($totalActualSellOut/$totalTargetSellOut) * 100,
                    'sell_out_gap' => $totalTargetSellOut - $totalActualSellOut,
                ]);

    }

    public function dataSupervisor(){

        $user = Auth::user();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $store = Store::whereIn('id', $storeIds)->get();

        $totalTargetSellIn = 0;
        $totalActualSellIn = 0;

        foreach ($store as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell In')->first();

            if($summary) {
                $totalTargetSellIn += $summary->sum_target_store;
                $totalActualSellIn += $summary->sum_actual_store;
            }
        }

        $totalTargetSellOut = 0;
        $totalActualSellOut = 0;

        foreach ($store as $data) {

            $summary = SummaryTargetActual::where('storeId', $data['id'])->where('sell_type', 'Sell Out')->first();

            if($summary) {
                $totalTargetSellOut += $summary->sum_target_store;
                $totalActualSellOut += $summary->sum_actual_store;
            }
        }

        return ([
                    'sell_in_target' => $totalTargetSellIn,
                    'sell_in_actual' => $totalActualSellIn,
                    'sell_in_at' => ($totalTargetSellIn == 0) ? 0 : ($totalActualSellIn/$totalTargetSellIn) * 100,
                    'sell_in_gap' => $totalTargetSellIn - $totalActualSellIn,
                    'sell_out_target' => $totalTargetSellOut,
                    'sell_out_actual' => $totalActualSellOut,
                    'sell_out_at' => ($totalTargetSellOut == 0) ? 0 : ($totalActualSellOut/$totalTargetSellOut) * 100,
                    'sell_out_gap' => $totalTargetSellOut - $totalActualSellOut,
                ]);

    }

}