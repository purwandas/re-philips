<?php

namespace App\Traits;

use App\Attendance;
use App\GroupProduct;
use App\Reports\SummaryTargetActual;
use App\Store;
use App\TrainerArea;
use App\User;
use Carbon\Carbon;

trait TargetTrait {

    use ActualTrait;

    public function setHeader($data){

        /* Check header exist or not, if doesn't, make it */
        $target = SummaryTargetActual::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])
            ->where('sell_type', $data['sell_type'])->first();

        // Fetch some data
        $store = Store::with('district.area.region', 'subChannel.channel')->where('id', $data['store_id'])->first();
        $user = User::where('id', $data['user_id'])->first();

        if(!$target){

            /* Trainer */
            $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
            $tr = User::whereIn('id', $trIds)->get();

            $trainer_name = '';
            foreach ($tr as $trDetail) {
                $trainer_name .= $trDetail->name;

                if ($trDetail->id != $tr->last()->id) {
                    $trainer_name .= ', ';
                }
            }

            $target = SummaryTargetActual::create([
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $store->id,
                            'user_id' => $user->id,
                            'region' => $store->district->area->region->name,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'nik' => $user->nik,
                            'promoter_name' => $user->name,
                            'account_type' => $store->subChannel->channel->name,
                            'title_of_promoter' => $this->getPromoterTitle($user->id, $store->id, $data['sell_type']),
                            'classification_store' => $store->classification,
                            'account' => $store->subChannel->name,
                            'store_id' => $store->store_id,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $store->store_name_2,
                            'spv_name' => ($store->user->name != '') ? $store->user->name : '',
                            'trainer' => $trainer_name,
                            'sell_type' => $data['sell_type'],
                        ]);

        }else{

            /* Override title of promoter */
            $this->changePromoterTitle($user->id, $store->id, $data['sell_type']);

        }

        return $target;

    }

    public function changePromoterTitle($userId, $storeId, $sellType){

        $target = SummaryTargetActual::where('user_id',$userId)->where('storeId', $storeId)->where('sell_type', $sellType)->first();
        $target->update(['title_of_promoter' => $this->getPromoterTitle($userId, $storeId, $sellType)]);

    }

    public function changeWeekly($target, $total){

        $attendance = Attendance::where('user_id', $target->user_id)
                        ->whereMonth('attendances.date', '=', Carbon::now()->month)
                        ->whereYear('attendances.date', '=', Carbon::now()->year)->get();
//        $attendance = Attendance::where('user_id', $target)->get();

        if($attendance){ // JIKA ADA DATA ABSENSI NYA

            $hkWeek1 = 0;
            $hkWeek2 = 0;
            $hkWeek3 = 0;
            $hkWeek4 = 0;
            $hkWeek5 = 0;
            $hkLimit = 0;

            foreach ($attendance as $data){

                $week = Carbon::parse($data['date'])->weekOfMonth;

                if($week == 1){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek1 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 2){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek2 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 3){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek3 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 4){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek4 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 5){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek5 += 1;
                            $hkLimit += 1;
                        }
                    }
                }

            }

            /* DA */
            $targetPerWeek1 = ($total['da']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['da']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['da']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['da']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['da']/26) * $hkWeek5;

//            return $targetPerWeek1 . ' - ' . $targetPerWeek2 . ' - ' . $targetPerWeek3 . ' - ' . $targetPerWeek4 . ' - ' . $targetPerWeek5;

            $target->update([
                'target_da_w1' => $targetPerWeek1,
                'target_da_w2' => $targetPerWeek2,
                'target_da_w3' => $targetPerWeek3,
                'target_da_w4' => $targetPerWeek4,
                'target_da_w5' => $targetPerWeek5,
            ]);

            /* PC */
            $targetPerWeek1 = ($total['pc']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['pc']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['pc']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['pc']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['pc']/26) * $hkWeek5;

            $target->update([
                'target_pc_w1' => $targetPerWeek1,
                'target_pc_w2' => $targetPerWeek2,
                'target_pc_w3' => $targetPerWeek3,
                'target_pc_w4' => $targetPerWeek4,
                'target_pc_w5' => $targetPerWeek5,
            ]);

            /* MCC */
            $targetPerWeek1 = ($total['mcc']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['mcc']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['mcc']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['mcc']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['mcc']/26) * $hkWeek5;

            $target->update([
                'target_mcc_w1' => $targetPerWeek1,
                'target_mcc_w2' => $targetPerWeek2,
                'target_mcc_w3' => $targetPerWeek3,
                'target_mcc_w4' => $targetPerWeek4,
                'target_mcc_w5' => $targetPerWeek5,
            ]);

//            return $targetPerWeek1 . ' - ' . $targetPerWeek2 . ' - ' . $targetPerWeek3 . ' - ' . $targetPerWeek4 . ' - ' . $targetPerWeek5;

        }

    }

    public function changeTarget($data, $change){

        $target = $this->setHeader($data);

        /* Target Add and/or Sum */
        $targetAfter = SummaryTargetActual::where('id', $target->id)->first();
        $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
        $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
        $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
        $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
        $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
        $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

        if(!$sumTargetStore) $sumTargetStore = 0;
        if(!$sumTargetArea) $sumTargetArea = 0;
        if(!$sumTargetRegion) $sumTargetRegion = 0;

        /* Add / Sum All Target */
        if($change == 'change'){ // INSERT / UPDATE

            // DA
            if (isset($data['targetOldDa']) && $data['targetOldDa'] > 0) {
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da - $data['targetOldDa'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldDa'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldDa'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldDa'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da + $data['target_da'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_da'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_da'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_da'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da + $data['target_da'],
                    'sum_target_store' => $sumTargetStore + $data['target_da'],
                    'sum_target_area' => $sumTargetArea + $data['target_da'],
                    'sum_target_region' => $sumTargetRegion + $data['target_da'],
                ]);
            }

            // Product Focus DA
            if (isset($data['targetOldPfDa']) && $data['targetOldPfDa'] > 0) {
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da - $data['targetOldPfDa'],
                ]);

                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da + $data['target_pf_da'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da + $data['target_pf_da'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

            $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
            $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
            $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // PC
            if (isset($data['targetOldPc']) && $data['targetOldPc'] > 0) {
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc - $data['targetOldPc'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldPc'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldPc'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldPc'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc + $data['target_pc'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_pc'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_pc'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_pc'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc + $data['target_pc'],
                    'sum_target_store' => $sumTargetStore + $data['target_pc'],
                    'sum_target_area' => $sumTargetArea + $data['target_pc'],
                    'sum_target_region' => $sumTargetRegion + $data['target_pc'],
                ]);
            }

            // Product Focus PC
            if (isset($data['targetOldPfPc']) && $data['targetOldPfPc'] > 0) {
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc - $data['targetOldPfPc'],
                ]);

                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc + $data['target_pf_pc'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc + $data['target_pf_pc'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

            $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
            $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
            $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // MCC
            if (isset($data['targetOldMcc']) && $data['targetOldMcc'] > 0) {
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc - $data['targetOldMcc'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldMcc'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldMcc'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldMcc'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc + $data['target_mcc'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_mcc'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_mcc'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_mcc'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc + $data['target_mcc'],
                    'sum_target_store' => $sumTargetStore + $data['target_mcc'],
                    'sum_target_area' => $sumTargetArea + $data['target_mcc'],
                    'sum_target_region' => $sumTargetRegion + $data['target_mcc'],
                ]);
            }

            // Product Focus MCC
            if (isset($data['targetOldPfMcc']) && $data['targetOldPfMcc'] > 0) {
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc - $data['targetOldPfMcc'],
                ]);

                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc + $data['target_pf_mcc'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc + $data['target_pf_mcc'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

        }else{ // DELETE

            // DA
            if (isset($data['target_da']) && $data['target_da'] > 0) {
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da - $data['target_da'],
                    'sum_target_store' => $sumTargetStore - $data['target_da'],
                    'sum_target_area' => $sumTargetArea - $data['target_da'],
                    'sum_target_region' => $sumTargetRegion - $data['target_da'],
                ]);
            }

            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // PC
            if (isset($data['target_pc']) && $data['target_pc'] > 0) {
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc - $data['target_pc'],
                    'sum_target_store' => $sumTargetStore - $data['target_pc'],
                    'sum_target_area' => $sumTargetArea - $data['target_pc'],
                    'sum_target_region' => $sumTargetRegion - $data['target_pc'],
                ]);
            }

            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // MCC
            if (isset($data['target_mcc']) && $data['target_mcc'] > 0) {
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc - $data['target_mcc'],
                    'sum_target_store' => $sumTargetStore - $data['target_mcc'],
                    'sum_target_area' => $sumTargetArea - $data['target_mcc'],
                    'sum_target_region' => $sumTargetRegion - $data['target_mcc'],
                ]);
            }

            // Product Focus DA
            if (isset($data['target_pf_da']) && $data['target_pf_da'] > 0) {
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da - $data['target_pf_da'],
                ]);
            }

            // Product Focus PC
            if (isset($data['target_pf_pc']) && $data['target_pf_pc'] > 0) {
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc - $data['target_pf_pc'],
                ]);
            }

            // Product Focus MCC
            if (isset($data['target_pf_mcc']) && $data['target_pf_mcc'] > 0) {
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc - $data['target_pf_mcc'],
                ]);
            }

            $targetAfter = SummaryTargetActual::where('id', $target->id)->first();

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

        }

        /* Check if Promoter was hybrid or not */
        if($targetAfter->title_of_promoter == 'HYBRID') {

            $targetAfter->update([
                'target_dapc' => $targetAfter->target_da + $targetAfter->target_pc
            ]);
        }else{

            if($targetAfter->target_dapc > 0){
                $targetAfter->update([
                    'target_dapc' => 0
                ]);
            }

        }

        /* Create Weekly Target */
        $total['da'] = $targetAfter->target_da;
        $total['pc'] = $targetAfter->target_pc;
        $total['mcc'] = $targetAfter->target_mcc;

        $this->changeWeekly($targetAfter, $total);

        /* Reset Actual */
        $this->resetActual($data['user_id'], $data['store_id'], $data['sell_type']);
//
////                if($change == 'change') {
////
////                    if (isset($data['targetOld'])) { // Reduce sum total if target update
////                        $targetAfter->update([
////                            'target_dapc' => $targetAfter->target_dapc - $data['targetOld'],
////                        ]);
////                    }
////
////                    // Sum Target
////                    $targetAfter->update([
////                        'target_dapc' => $targetAfter->target_dapc + $data['target'],
////                    ]);
////
////                }else{
////                    // Delete Target
////                    $targetAfter->update([
////                        'target_dapc' => $targetAfter->target_dapc - $data['target'],
////                    ]);
////                }
//
//            }


        /* Target Add and/or Sum */
//        $targetAfter = SummaryTargetActual::where('id', $target->id)->first();
//        $sumStore = SummaryTargetActual::where('storeId',$target->storeId);
//        $sumArea = SummaryTargetActual::where('area_id', $target->area_id);
//        $sumRegion = SummaryTargetActual::where('region_id', $target->region_id);
//        $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->first()->sum_target_store;
//        $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->first()->sum_target_area;
//        $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->first()->sum_target_region;
//
////        return $data['targetOld'];
//
//        if($data['type'] == 'Total Dedicate'){ // If Type was Total Dedicate
//
//            /* Add / Sum total dedicate */
//            $group = GroupProduct::where('id', $data['groupproduct_id'])->first();
//
//            /* Add to per total dedicate target */
//            if($group->name == 'DA'){
//
//                if($change == 'change') {
//
//                    if (isset($data['targetOld'])) { // Reduce sum total if target update
//                        $targetAfter->update([
//                            'target_da' => $targetAfter->target_da - $data['targetOld'],
//                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
//                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
//                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
//                        ]);
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_da' => $targetAfter->target_da + $data['target'],
//                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
//                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
//                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
//                        ]);
//
//                    }else {
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_da' => $targetAfter->target_da + $data['target'],
//                            'sum_target_store' => $sumTargetStore + $data['target'],
//                            'sum_target_area' => $sumTargetArea + $data['target'],
//                            'sum_target_region' => $sumTargetRegion + $data['target'],
//                        ]);
//
//                    }
//
//                }else{
//
//                    // Delete Target
//                    $targetAfter->update([
//                        'target_da' => $targetAfter->target_da - $data['target'],
//                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
//                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
//                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
//                    ]);
//
//                }
//
//                // Update Sum Target Store to All Summary
//                $sumStore->update([
//                    'sum_target_store' => $targetAfter->sum_target_store,
//                ]);
//
//                $sumArea->update([
//                    'sum_target_area' => $targetAfter->sum_target_area,
//                ]);
//
//                $sumRegion->update([
//                    'sum_target_region' => $targetAfter->sum_target_region,
//                ]);
//
//            }else if($group->name == 'PC'){
//
//                if($change == 'change') {
//
//                    if (isset($data['targetOld'])) { // Reduce sum total if target update
//                        $targetAfter->update([
//                            'target_pc' => $targetAfter->target_pc - $data['targetOld'],
//                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
//                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
//                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
//                        ]);
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_pc' => $targetAfter->target_pc + $data['target'],
//                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
//                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
//                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
//                        ]);
//
//                    }else {
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_pc' => $targetAfter->target_pc + $data['target'],
//                            'sum_target_store' => $sumTargetStore + $data['target'],
//                            'sum_target_area' => $sumTargetArea + $data['target'],
//                            'sum_target_region' => $sumTargetRegion + $data['target'],
//                        ]);
//
//                    }
//
//                }else{
//
//                    // Delete Target
//                    $targetAfter->update([
//                        'target_pc' => $targetAfter->target_pc - $data['target'],
//                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
//                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
//                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
//                    ]);
//
//                }
//
//                // Update Sum Target Store to All Summary
//                $sumStore->update([
//                    'sum_target_store' => $targetAfter->sum_target_store,
//                ]);
//
//                $sumArea->update([
//                    'sum_target_area' => $targetAfter->sum_target_area,
//                ]);
//
//                $sumRegion->update([
//                    'sum_target_region' => $targetAfter->sum_target_region,
//                ]);
//
//            }else if($group->name == 'MCC'){
//
////                return $data['targetOld'];
//
//                if($change == 'change') {
//
//                    if (isset($data['targetOld'])) { // Reduce sum total if target update
//
//                        $targetAfter->update([
//                            'target_mcc' => $targetAfter->target_mcc - $data['targetOld'],
//                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
//                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
//                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
//                        ]);
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_mcc' => $targetAfter->target_mcc + $data['target'],
//                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
//                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
//                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
//                        ]);
//
//                    }else {
//
//                        // Sum Target
//                        $targetAfter->update([
//                            'target_mcc' => $targetAfter->target_mcc + $data['target'],
//                            'sum_target_store' => $sumTargetStore + $data['target'],
//                            'sum_target_area' => $sumTargetArea + $data['target'],
//                            'sum_target_region' => $sumTargetRegion + $data['target'],
//                        ]);
//
//                    }
//
//                }else{
//
//                    // Delete Target
//                    $targetAfter->update([
//                        'target_mcc' => $targetAfter->target_mcc - $data['target'],
//                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
//                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
//                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
//                    ]);
//
//                }
//
//                // Update Sum Target Store to All Summary
//                $sumStore->update([
//                    'sum_target_store' => $targetAfter->sum_target_store,
//                ]);
//
//                $sumArea->update([
//                    'sum_target_area' => $targetAfter->sum_target_area,
//                ]);
//
//                $sumRegion->update([
//                    'sum_target_region' => $targetAfter->sum_target_region,
//                ]);
//
//            }
//
//            /* Check if Promoter was hybrid or not */
//            if($targetAfter->title_of_promoter == 'Hybrid'){
//
//                $targetAfter->update([
//                    'target_dapc' => $targetAfter->target_da + $targetAfter->target_pc
//                ]);
//
////                if($change == 'change') {
////
////                    if (isset($data['targetOld'])) { // Reduce sum total if target update
////                        $targetAfter->update([
////                            'target_dapc' => $targetAfter->target_dapc - $data['targetOld'],
////                        ]);
////                    }
////
////                    // Sum Target
////                    $targetAfter->update([
////                        'target_dapc' => $targetAfter->target_dapc + $data['target'],
////                    ]);
////
////                }else{
////                    // Delete Target
////                    $targetAfter->update([
////                        'target_dapc' => $targetAfter->target_dapc - $data['target'],
////                    ]);
////                }
//
//            }
//
//
//        }else if($data['type'] == 'Product Focus') { // If Type was Product Focus
//
//            //
//
//        }

    }


}