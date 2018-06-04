<?php

namespace App\Traits;

use App\Distributor;
use App\StoreDistributor;

trait ReportTrait {

    public function getDistributorCode($storeId){

        // return $storeId . '~~OK';

        // $data = Distributor::whereHas('storeDistributors', function($query) use ($storeId){
        //     return $query->whereIn('store_id', $storeId);
        // })->pluck('code')->toArray();

        $distId = StoreDistributor::where('store_id', $storeId)->pluck('distributor_id');
        $data = Distributor::whereIn('id', $distId)->pluck('code')->toArray();

        return implode(', ', $data);
        // return 'ok';

    }

}