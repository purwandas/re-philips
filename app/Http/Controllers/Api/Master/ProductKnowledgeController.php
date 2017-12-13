<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductKnowledge;
use App\ProductKnowledgeRead;
use DB;
use JWTAuth;
use Auth;
use App\Store;

class ProductKnowledgeController extends Controller
{
    public function get($param)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $type = "";
        if($param == 1){
            $type = "Product Knowledge";
        }else if($param == 2){
            $type = "Planogram";
        }else{
            $type = "POSM";
        }

        // Check Promoter Group
		$isPromoter = 0;
		if($user->role == 'Promoter' || $user->role == 'Promoter Additional' || $user->role == 'Promoter Event' || $user->role == 'Demonstrator MCC' || $user->role == 'Demonstrator DA' || $user->role == 'ACT'  || $user->role == 'PPE' || $user->role == 'BDT' || $user->role == 'Salesman Explorer' || $user->role == 'SMD' || $user->role == 'SMD Coordinator' || $user->role == 'HIC' || $user->role == 'HIE' || $user->role == 'SMD Additional' || $user->role == 'ASC'){
			$isPromoter = 1;
		}

        if($isPromoter == 1){

		    $storeIds = $user->employeeStores()->pluck('store_id'); // Get Store ID
		    $areaIds = Store::whereIn('id', $storeIds)->pluck('district_id'); // Get District ID

        }

        $data = ProductKnowledge::where('target_type', 'All')->where('type', $type)
    				->select('product_knowledges.id', 'product_knowledges.date', 'product_knowledges.from', 'product_knowledges.subject', 'product_knowledges.filename', 'product_knowledges.file')
    				->get();

        // If user was in promoter group
        if($isPromoter == 1) {

            /* INIT Data Area to be filtered */
            $dataArea = ProductKnowledge::where('target_type', 'Area')->get();
            $areaArray = [];
            foreach ($dataArea as $area) {
                $target = explode(',', $area['target_detail']);
                foreach($areaIds as $areaId) {
                    if (in_array($areaId, $target)) {
                        array_push($areaArray, $area['id']);
                    }
                }
            }

            /* MERGER Data All dan Data Area */
            $dataAreaSelect = ProductKnowledge::whereIn('id', $areaArray)->where('type', $type)
                                ->select('product_knowledges.id', 'product_knowledges.date', 'product_knowledges.from', 'product_knowledges.subject', 'product_knowledges.filename', 'product_knowledges.file')
                ->get();

            $data = $data->merge($dataAreaSelect);

            /* INIT Data Store to be filtered */
            $dataStore = ProductKnowledge::where('target_type', 'Store')->get();
            $storeArray = [];
            foreach ($dataStore as $store) {
                $target = explode(',', $store['target_detail']);
                foreach($storeIds as $storeId) {
                    if (in_array($storeId, $target)) {
                        array_push($storeArray, $store['id']);
                    }
                }
            }

            /* MERGER Data All dan Data Store */
            $dataStoreSelect = ProductKnowledge::whereIn('id', $storeArray)->where('type', $type)
                                ->select('product_knowledges.id', 'product_knowledges.date', 'product_knowledges.from', 'product_knowledges.subject', 'product_knowledges.filename', 'product_knowledges.file')
                ->get();

            $data = $data->merge($dataStoreSelect);

            /* INIT Data Store to be filtered */
            $dataPromoter = ProductKnowledge::where('target_type', 'Promoter')->get();
            $promoterArray = [];
            foreach ($dataPromoter as $promoter) {
                $target = explode(',', $promoter['target_detail']);
                if (in_array($user->id, $target)) {
                    array_push($promoterArray, $promoter['id']);
                }
            }

            /* MERGER Data All dan Data Promoter */
            $dataPromoterSelect = ProductKnowledge::whereIn('id', $promoterArray)->where('type', $type)
                                ->select('product_knowledges.id', 'product_knowledges.date', 'product_knowledges.from', 'product_knowledges.subject', 'product_knowledges.filename' , 'product_knowledges.file')
                ->get();

            $data = $data->merge($dataPromoterSelect);



        }

        // Set has read
        $data->map(function ($detail) use ($user) {
            $productRead = ProductKnowledgeRead::where('productknowledge_id', $detail['id'])->where('user_id', $user->id)->first();

            if($productRead) {
                $detail['hasRead'] = 1;
            }else{
                $detail['hasRead'] = 0;
            }

            return $detail;
        });

        return response()->json($data);
    }

    public function read($param)
    {
    	$user = JWTAuth::parseToken()->authenticate();

        $productKnowledgeRead = ProductKnowledgeRead::where('productknowledge_id', $param)->where('user_id', $user->id)->count();
        if($productKnowledgeRead == 0){
            $productKnowledge = ProductKnowledge::find($param);
            $productKnowledge->update([ 'total_read' => $productKnowledge->total_read+1 ]);

            ProductKnowledgeRead::create([
                'productknowledge_id' => $param,
                'user_id' => $user->id
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Info Readed']);
    }
}
