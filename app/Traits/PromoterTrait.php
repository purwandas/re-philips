<?php

namespace App\Traits;

use App\EmployeeStore;
use App\Store;
use App\Target;

trait PromoterTrait {

    public function getPromoterTitle($user_id, $store_id){

        $target = Target::where('user_id', $user_id)->where('store_id', $store_id)->get();

        $result = '';

        if(count($target) > 0){

            $group = [];

            foreach ($target as $data) {

                if (!in_array($data->groupProduct->name, $group)) {
                    array_push($group, $data->groupProduct->name);
                }

            }

            if(count($group) > 1){
                $result = 'Hybrid';
            }else if(count($group) == 1){
                $result = $group[0];
            }

        }else{
            $result = 'No Target';
        }

        return $result;

    }

}