<?php

namespace App\Http\Controllers\Api\Master;

use App\EmployeeStore;
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class SuggestionOrderController extends Controller
{
    public function checkNeededPO($store_id, $param = 1){

        // Foreach product by store at APM table use (store_id)
        $products = new Collection();

        $countNeeded = 0;

        $product1 = ([
            'id' => '1',
            'model' => 'MODEL A',
            'variants' => '11',
            'name' => 'MODEL A/11',
            'po_needed_value' => '0',
            'po_needed_qty' => '0',
        ]);

        if($product1['po_needed_qty'] > 0){
            $products->push($product1);
            $countNeeded += 1;
        }

        $product2 = ([
            'id' => '2',
            'model' => 'MODEL B',
            'variants' => '12',
            'name' => 'MODEL B/12',
            'po_needed_value' => '12500000',
            'po_needed_qty' => '5',
        ]);

        if($product2['po_needed_qty'] > 0){
            $products->push($product2);
            $countNeeded += 1;
        }

        if($param == 1){ // RETURN LIST PRODUCTS
            return $products;
        }else{ // RETURN COUNT NEEDED
            return $countNeeded;
        }
    }

    public function getStorePO(){

        $user = JWTAuth::parseToken()->authenticate();

        $empStoreIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

        $stores = Store::whereIn('id', $empStoreIds)
                    ->select('id', 'store_id', 'store_name_1', 'store_name_2')
                    ->get();

        if($stores){

            foreach ($stores as $store){

                if($this->checkNeededPO($store->id, 2) > 0){
                    $store['po_needed'] = 1;
                }else{
                    $store['po_needed'] = 0;
                }

            }

        }

        $store2 = ([
            'id' => '2000',
            'store_id' => 'TEST0001',
            'store_name_1' => 'TOKO TEST',
            'store_name_2' => 'TOKO TEST',
            'po_needed' => 0
        ]);

        $stores->push($store2);

        return response()->json($stores);

    }
}
