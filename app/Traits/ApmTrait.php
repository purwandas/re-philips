<?php

namespace App\Traits;

use App\Apm;
use App\ApmMonth;
use App\Leadtime;
use App\Price;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummarySoh;
use App\SellOutDetail;
use App\Soh;
use App\Store;
use App\Target;
use Carbon\Carbon;

trait ApmTrait {

    public function getProductTotalCurrent($storeId, $productId){

        $totalValue = SummarySellOut::where('storeId', $storeId)
                        ->where('product_id', $productId)
                        ->where(function($query){
                            return $query->where('irisan', 0)->orWhere('irisan', null);
                        })
                        ->sum('value');

        return $totalValue;

    }

    public function getStockValueCurrent($storeId, $productId){

        $stock = SummarySoh::where('storeId', $storeId)->where('product_id', $productId)->orderBy('created_at', 'DESC')->first();

        if($stock){

            return $stock->value;

        }

    }

    public function getSellInValueCurrent($storeId, $productId){

        $sellInValue = 0;

        $sellIn = SummarySellIn::where('storeId', $storeId)->where('product_id', $productId)
                    ->where('created_at', '>', $this->getLastStock($storeId, $productId))->get();

        if($sellIn){

            foreach ($sellIn as $data){
                $sellInValue += $data->value;
            }

        }

        return $sellInValue;

    }

    public function getSellOutValueCurrent($storeId, $productId){

        $sellOutValue = 0;

        $sellOut = SummarySellOut::where('storeId', $storeId)->where('product_id', $productId)
                    ->where('created_at', '>', $this->getLastStock($storeId, $productId))->get();

        if($sellOut){

            foreach ($sellOut as $data){
                $sellOutValue += $data->value;
            }

        }

        return $sellOutValue;

    }

    public function checkStock($storeId, $productId){

        $checkStock = SummarySoh::whereMonth('summary_sohs.date', '=', Carbon::now()->format('m'))
                        ->whereYear('summary_sohs.date', '=', Carbon::now()->format('Y'))
                        ->where('summary_sohs.storeId', $storeId)
                        ->where('summary_sohs.product_id', $productId)
                        ->count();

        return $checkStock;

    }

    public function checkSellIn($storeId, $productId){

        $checkSellIn = SummarySellIn::whereMonth('summary_sell_ins.date', '=', Carbon::now()->format('m'))
                        ->whereYear('summary_sell_ins.date', '=', Carbon::now()->format('Y'))
                        ->where('summary_sell_ins.storeId', $storeId)
                        ->where('summary_sell_ins.product_id', $productId)
                        ->count();

        return $checkSellIn;

    }

    public function sumMonthValue($storeId){

        $apm = Apm::where('store_id', $storeId)->get();

        $apmMonth = ApmMonth::all();

        $totalValue = 0;

        /* Same Dividing = 3 */

        if($apm){

            foreach ($apm as $data){

                $valueSelected = 0;

                if($apmMonth->first()->selected == 1) $valueSelected += $data->month_minus_1_value;
                if($apmMonth->get(1)->selected == 1) $valueSelected += $data->month_minus_2_value;
                if($apmMonth->get(2)->selected == 1) $valueSelected += $data->month_minus_3_value;
                if($apmMonth->get(3)->selected == 1) $valueSelected += $data->month_minus_4_value;
                if($apmMonth->get(4)->selected == 1) $valueSelected += $data->month_minus_5_value;
                if($apmMonth->get(5)->selected == 1) $valueSelected += $data->month_minus_6_value;
                
                $totalValue += $valueSelected / 3;

            }

        }

        return $totalValue;

        /* Different Dividing */

        if($apm){

            foreach ($apm as $data){

                $apmDividing = 0;
                if($data->month_minus_3_value > 0) $apmDividing += 1;
                if($data->month_minus_2_value > 0) $apmDividing += 1;
                if($data->month_minus_1_value > 0) $apmDividing += 1;

                if($apmDividing > 0){

                    $totalValue += ($data->month_minus_3_value + $data->month_minus_2_value + $data->month_minus_1_value) / $apmDividing;

                }

            }

        }

        return $totalValue;

    }

    public function sumMonthProductValue($storeId, $productId){

        $apm = Apm::where('store_id', $storeId)->where('product_id', $productId)->first();

        $apmMonth = ApmMonth::all();

        $totalValue = 0;

        /* Same Dividing = 3 */

        if($apm) {

            // $totalValue = ($apm->month_minus_3_value + $apm->month_minus_2_value + $apm->month_minus_1_value) / 3;

            $valueSelected = 0;

            if($apmMonth->first()->selected == 1) $valueSelected += $apm->month_minus_1_value;
            if($apmMonth->get(1)->selected == 1) $valueSelected += $apm->month_minus_2_value;
            if($apmMonth->get(2)->selected == 1) $valueSelected += $apm->month_minus_3_value;
            if($apmMonth->get(3)->selected == 1) $valueSelected += $apm->month_minus_4_value;
            if($apmMonth->get(4)->selected == 1) $valueSelected += $apm->month_minus_5_value;
            if($apmMonth->get(5)->selected == 1) $valueSelected += $apm->month_minus_6_value;
            
            $totalValue += $valueSelected / 3;

        }

        return $totalValue;

        /* Different Dividing */

        if($apm){

            $apmDividing = 0;
            if($apm->month_minus_3_value > 0) $apmDividing += 1;
            if($apm->month_minus_2_value > 0) $apmDividing += 1;
            if($apm->month_minus_1_value > 0) $apmDividing += 1;

            if($apmDividing > 0){

                $totalValue = ($apm->month_minus_3_value + $apm->month_minus_2_value + $apm->month_minus_1_value) / $apmDividing;

            }

        }

        return $totalValue;

    }

    public function getTotalTarget($storeId){

        $totalTarget = 0;

        $sellType = 'Sell In';

        $globalChannel = Store::where('id', $storeId)->first();
        if($globalChannel){
            if($globalChannel->subChannel->channel->globalChannel->name == 'MR' || $globalChannel->subChannel->channel->globalChannel->name == 'Modern Retail'){
                $sellType = 'Sell Out';                
            }
        }

        $targets = Target::where('store_id', $storeId)->where('sell_type', $sellType)->where('partner', 0)->get();

        foreach ($targets as $target){

            $totalTarget += $target['target_da'] + $target['target_pc'] + $target['target_mcc'];

        }

        return $totalTarget;

    }

    public function getLastStock($storeId, $productId){

        $lastStock = SummarySoh::where('storeId', $storeId)->where('product_id', $productId)->orderBy('created_at', 'DESC')->first();

        if($lastStock){

            return $lastStock->updated_at;

        }

        return "No Stock";

    }

    public function getLeadtime($storeId){

        $area_id = Store::where('id', $storeId)->first()->district->area->id;

        $leadtime = Leadtime::where('area_id', $area_id)->first();

        if($leadtime){
            return $leadtime->leadtime;
        }

        return 0;

    }

    public function getPriceCurrent($storeId, $productId){

        $globalchannel_id = Store::where('id', $storeId)->first()->subChannel->channel->globalChannel->id;
        $globalchannel_name = Store::where('id', $storeId)->first()->subChannel->channel->globalChannel->name;

        $sell_type = 'Sell In';

        if($globalchannel_name == 'MR' || $globalchannel_name == 'Modern Retail'){
            $sell_type = 'Sell Out';
        }

        $price = Price::where('product_id', $productId)->where('globalchannel_id', $globalchannel_id)
                    ->where('sell_type', $sell_type)->first();

        if($price){
            return $price->price;
        }

        return 0;

    }

}