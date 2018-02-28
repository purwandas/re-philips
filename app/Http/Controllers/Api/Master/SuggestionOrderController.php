<?php

namespace App\Http\Controllers\Api\Master;

use App\Apm;
use App\EmployeeStore;
use App\Leadtime;
use App\SpvDemo;
use App\Product;
use App\Soh;
use App\Store;
use App\Traits\ApmTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Reports\SummarySellOut;

class SuggestionOrderController extends Controller
{
    use ApmTrait;

    public function checkNeededPO($store_id, $param = 1){

        $products = new Collection();
        $countNeeded = 0;

        // Foreach product by store at APM table use (store_id)
        $apmProduct = Apm::where('store_id', $store_id)
                        ->groupBy('product_id')->select('product_id')->pluck('product_id')->toArray();

        $summary = SummarySellOut::where('storeId', $store_id)
                        ->groupBy('product_id')->select('product_id')->pluck('product_id')->toArray();

        $result = array_merge($apmProduct, $summary);
        $result = array_unique($result);

//        return response()->json($result);

        if(count($result) > 0) {

            foreach ($result as $key => $value){

//            for($i=0;$i<=count($result);$i++){

//                $apm = Apm::where('store_id', $store_id)->where('product_id', $product['product_id'])->first();

                $apm = $this->sumMonthProductValue($store_id, $value);

//                $test = ([
//                    'product_id' => $value,
//                    'apm' => $apm,
//                    'sell in' => $this->checkSellIn($store_id, $value),
//                ]);
//
//                $products->push($test);
//
//                continue;

                if($apm > 0){

                    if($this->checkStock($store_id, $value) > 0 || $this->checkSellIn($store_id, $value) > 0) {

                        $apmPerDay = $apm / 26;                        
                        $percentProduct = (($apm * 100) / $this->sumMonthValue($store_id));
                        $totalTarget = $this->getTotalTarget($store_id);
                        $contribution = ($percentProduct * $totalTarget) / 100;

                        $leadtime = $this->getLeadtime($store_id) * $apmPerDay;
                        $stock = $this->getStockValueCurrent($store_id, $value);
                        $sellIn = $this->getSellInValueCurrent($store_id, $value);
                        $sellOut = $this->getSellOutValueCurrent($store_id, $value);
                        $totalStock = (($stock + $sellIn) - $sellOut) - $leadtime;

                        $poNeededValue = $totalStock - $contribution;
                        if ($poNeededValue > 0) {
                            $poNeededValue = 0;
                        } else {
                            $poNeededValue = abs($poNeededValue);
                        }
                        $poNeededQty = floor($poNeededValue / $this->getPriceCurrent($store_id, $value));

                       // $test = ([
                       //     'apm' => $apm,
                       //     'apm per day' => round($apmPerDay,2),
                       //     'percent kontribusi produk' => $percentProduct,
                       //     'total target toko' => $totalTarget,
                       //     '%kontribusi * total target' => $contribution,
                       //     'leadtime value' => $leadtime,
                       //     'stock value' => $stock,
                       //     'sell in value' => $sellIn,
                       //     'sell out value' => $sellOut,
                       //     'total stock' => $totalStock,
                       //     'po needed value' => $poNeededValue,
                       //     'po needed qty' => $poNeededQty,
                       // ]);

                       // $products->push($test);

                       // continue;

                        if ($poNeededQty > 0) {

                            $dataProduct = Product::where('id', $value)->first();

                            $product = ([
                                'id' => $dataProduct->id,
                                'model' => $dataProduct->model,
                                'variants' => $dataProduct->variants,
                                'name' => $dataProduct->name,
                                'po_needed_value' => $poNeededValue,
                                'po_needed_qty' => $poNeededQty,
                            ]);

                            if ($param == 1) { // LIST PRODUCTS
                                $products->push($product);
                            } else { // COUNT NEEDED
                                $countNeeded += 1;
                            }

                        }

                    }



                }else{ // KALO GA PUNYA APM (MASIH HARUS DIKONFIRMASI)
                    // FOR WHILE, DO NOTHING
                }

            }
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

        if($user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid'){

            $empStoreIds = Store::where('user_id', $user->id)->pluck('id');

            $spvDemo = SpvDemo::where('user_id', $user->id)->first();
            if($spvDemo){
                $empStoreIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');
            }

        }

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

        // $store2 = ([
        //     'id' => '2000',
        //     'store_id' => 'TEST0001',
        //     'store_name_1' => 'TOKO TEST',
        //     'store_name_2' => 'TOKO TEST',
        //     'po_needed' => 0
        // ]);

        // $stores->push($store2);

        return response()->json($stores);

    }
}
