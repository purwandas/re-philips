<?php

namespace App\Traits;

use App\GroupProduct;
use App\Reports\SummaryTargetActual;
use App\Store;
use App\TrainerArea;
use App\User;

trait TargetTrait {

    use PromoterTrait;

    public function changePromoterTitle($userId, $storeId){

        $target = SummaryTargetActual::where('user_id',$userId)->where('storeId', $storeId)->first();
        $target->update(['title_of_promoter' => $this->getPromoterTitle($userId, $storeId)]);

    }

    public function changeTarget($data, $change){

        /* Check header exist or not, if doesn't, make it */
        $target = SummaryTargetActual::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])->first();

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
                            'title_of_promoter' => $this->getPromoterTitle($user->id, $store->id),
                            'classification_store' => $store->classification,
                            'account' => $store->subChannel->name,
                            'store_id' => $store->store_id,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $store->store_name_2,
                            'spv_name' => $store->user->name,
                            'trainer' => $trainer_name,
                        ]);

        }else{

            /* Override title of promoter */
//            $target->update(['title_of_promoter' => $this->getPromoterTitle($user->id, $store->id)]);
            $this->changePromoterTitle($user->id, $store->id);

        }

        /* Target Add and/or Sum */
        $targetAfter = SummaryTargetActual::where('id', $target->id)->first();
        $sumStore = SummaryTargetActual::where('storeId',$target->storeId);
        $sumArea = SummaryTargetActual::where('area_id', $target->area_id);
        $sumRegion = SummaryTargetActual::where('region_id', $target->region_id);
        $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->first()->sum_target_store;
        $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->first()->sum_target_area;
        $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->first()->sum_target_region;

//        return $data['targetOld'];

        if($data['type'] == 'Total Dedicate'){ // If Type was Total Dedicate

            /* Add / Sum total dedicate */
            $group = GroupProduct::where('id', $data['groupproduct_id'])->first();

            /* Add to per total dedicate target */
            if($group->name == 'DA'){

                if($change == 'change') {

                    if (isset($data['targetOld'])) { // Reduce sum total if target update
                        $targetAfter->update([
                            'target_da' => $targetAfter->target_da - $data['targetOld'],
                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
                        ]);

                        // Sum Target
                        $targetAfter->update([
                            'target_da' => $targetAfter->target_da + $data['target'],
                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
                        ]);

                    }else {

                        // Sum Target
                        $targetAfter->update([
                            'target_da' => $targetAfter->target_da + $data['target'],
                            'sum_target_store' => $sumTargetStore + $data['target'],
                            'sum_target_area' => $sumTargetArea + $data['target'],
                            'sum_target_region' => $sumTargetRegion + $data['target'],
                        ]);

                    }

                }else{

                    // Delete Target
                    $targetAfter->update([
                        'target_da' => $targetAfter->target_da - $data['target'],
                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
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

            }else if($group->name == 'PC'){

                if($change == 'change') {

                    if (isset($data['targetOld'])) { // Reduce sum total if target update
                        $targetAfter->update([
                            'target_pc' => $targetAfter->target_pc - $data['targetOld'],
                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
                        ]);

                        // Sum Target
                        $targetAfter->update([
                            'target_pc' => $targetAfter->target_pc + $data['target'],
                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
                        ]);

                    }else {

                        // Sum Target
                        $targetAfter->update([
                            'target_pc' => $targetAfter->target_pc + $data['target'],
                            'sum_target_store' => $sumTargetStore + $data['target'],
                            'sum_target_area' => $sumTargetArea + $data['target'],
                            'sum_target_region' => $sumTargetRegion + $data['target'],
                        ]);

                    }

                }else{

                    // Delete Target
                    $targetAfter->update([
                        'target_pc' => $targetAfter->target_pc - $data['target'],
                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
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

            }else if($group->name == 'MCC'){

//                return $data['targetOld'];

                if($change == 'change') {

                    if (isset($data['targetOld'])) { // Reduce sum total if target update

                        $targetAfter->update([
                            'target_mcc' => $targetAfter->target_mcc - $data['targetOld'],
                            'sum_target_store' => $sumTargetStore - $data['targetOld'],
                            'sum_target_area' => $sumTargetArea - $data['targetOld'],
                            'sum_target_region' => $sumTargetRegion - $data['targetOld'],
                        ]);

                        // Sum Target
                        $targetAfter->update([
                            'target_mcc' => $targetAfter->target_mcc + $data['target'],
                            'sum_target_store' => $targetAfter->sum_target_store + $data['target'],
                            'sum_target_area' => $targetAfter->sum_target_area + $data['target'],
                            'sum_target_region' => $targetAfter->sum_target_region + $data['target'],
                        ]);

                    }else {

                        // Sum Target
                        $targetAfter->update([
                            'target_mcc' => $targetAfter->target_mcc + $data['target'],
                            'sum_target_store' => $sumTargetStore + $data['target'],
                            'sum_target_area' => $sumTargetArea + $data['target'],
                            'sum_target_region' => $sumTargetRegion + $data['target'],
                        ]);

                    }

                }else{

                    // Delete Target
                    $targetAfter->update([
                        'target_mcc' => $targetAfter->target_mcc - $data['target'],
                        'sum_target_store' => $targetAfter->sum_target_store - $data['target'],
                        'sum_target_area' => $targetAfter->sum_target_area - $data['target'],
                        'sum_target_region' => $targetAfter->sum_target_region - $data['target'],
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

            }

            /* Check if Promoter was hybrid or not */
            if($targetAfter->title_of_promoter == 'Hybrid'){

                $targetAfter->update([
                    'target_dapc' => $targetAfter->target_da + $targetAfter->target_pc
                ]);

//                if($change == 'change') {
//
//                    if (isset($data['targetOld'])) { // Reduce sum total if target update
//                        $targetAfter->update([
//                            'target_dapc' => $targetAfter->target_dapc - $data['targetOld'],
//                        ]);
//                    }
//
//                    // Sum Target
//                    $targetAfter->update([
//                        'target_dapc' => $targetAfter->target_dapc + $data['target'],
//                    ]);
//
//                }else{
//                    // Delete Target
//                    $targetAfter->update([
//                        'target_dapc' => $targetAfter->target_dapc - $data['target'],
//                    ]);
//                }

            }


        }else if($data['type'] == 'Product Focus') { // If Type was Product Focus

            //

        }

    }


}