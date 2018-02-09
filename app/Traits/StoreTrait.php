<?php

namespace App\Traits;

use Carbon\Carbon;
use DB;
use App\Store;

trait StoreTrait {

    public function traitGetStoreId(){

        $store = Store::select('stores.*', DB::raw("(( substr(stores.store_id, 3, (length(stores.store_id)-2)) )*1) as counting"))
                    ->orderBy('counting', 'DESC')->first();
        if(!$store){
            return 'RE0001';
        }else{
            $data = "";

//            return $store->counting;

            for($i=2;$i<strlen($store->store_id);$i++){
                $data .= $store->store_id[$i];
            }

            $increment = (integer)$data + 1;
            $countLength = strlen((string)$increment);
            $result = "";

            if($countLength == 1){
                $result .= 'RE' . '000' . $increment;
            } else if ($countLength == 2){
                $result .= 'RE' . '00' . $increment;
            } else if ($countLength == 3){
                $result .= 'RE' . '0' . $increment;
            } else {
                $result .= 'RE' . $increment;
            }

            return $result;
        }
    }

}