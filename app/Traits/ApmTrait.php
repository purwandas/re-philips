<?php

namespace App\Traits;

use App\Apm;
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

        $stock = SummarySoh::where('storeId', $storeId)->where('product_id', $productId)->orderBy('updated_at', 'DESC')->first();

        if($stock){

            return $stock->value;

        }

    }

    public function getSellInValueCurrent($storeId, $productId){

        $sellInValue = 0;

        $sellIn = SummarySellIn::where('storeId', $storeId)->where('product_id', $productId)
                    ->where('updated_at', '>', $this->getLastStock($storeId, $productId))->get();

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
                    ->where('updated_at', '>', $this->getLastStock($storeId, $productId))->get();

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

        $totalValue = 0;

        /* Same Dividing = 3 */

        if($apm){

            foreach ($apm as $data){

                $totalValue += ($data->month_minus_3_value + $data->month_minus_2_value + $data->month_minus_1_value) / 3;

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

        $totalValue = 0;

        /* Same Dividing = 3 */

        if($apm) {

            $totalValue = ($apm->month_minus_3_value + $apm->month_minus_2_value + $apm->month_minus_1_value) / 3;

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
            if($globalChannel->subChannel()->channel()->globalChannel() == 'MR' || $globalChannel->subChannel()->channel()->globalChannel() == 'Modern Retail'){
                $sellType = 'Sell Out';                
            }
        }

        $targets = Target::where('store_id', $storeId)->where('sell_type', $sellType)->get();

        foreach ($targets as $target){

            $totalTarget += $target['target_da'] + $target['target_pc'] + $target['target_mcc'];

        }

        return $totalTarget;

    }

    public function getLastStock($storeId, $productId){

        $lastStock = SummarySoh::where('storeId', $storeId)->where('product_id', $productId)->orderBy('updated_at', 'DESC')->first();

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

        $price = Price::where('product_id', $productId)->where('globalchannel_id', $globalchannel_id)
                    ->where('sell_type', 'Sell Out')->first();

        if($price){
            return $price->price;
        }

        return 0;

    }

}