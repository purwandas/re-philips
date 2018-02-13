<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\News;
use App\NewsRead;
use DB;
use JWTAuth;
use Auth;
use App\Store;

class NewsController extends Controller
{
    public function get()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Check Promoter Group
		$isPromoter = 0;
		if($user->role->role_group == 'Promoter' || $user->role->role_group == 'Promoter Additional' || $user->role->role_group == 'Promoter Event' || $user->role->role_group == 'Demonstrator MCC' || $user->role->role_group == 'Demonstrator DA' || $user->role->role_group == 'ACT'  || $user->role->role_group == 'PPE' || $user->role->role_group == 'BDT' || $user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'SMD' || $user->role->role_group == 'SMD Coordinator' || $user->role->role_group == 'HIC' || $user->role->role_group == 'HIE' || $user->role->role_group == 'SMD Additional' || $user->role->role_group == 'ASC'){
			$isPromoter = 1;
		}

        if($isPromoter == 1){

		    $storeIds = $user->employeeStores()->pluck('store_id'); // Get Store ID
		    $areaIds = Store::whereIn('id', $storeIds)->pluck('district_id'); // Get District ID

        }

        $data = News::where('target_type', 'All')
    				->select('news.id', 'news.date', 'news.from', 'news.subject', 'news.content')
    				->get();

        // If user was in promoter group
        if($isPromoter == 1) {

            /* INIT Data Area to be filtered */
            $dataArea = News::where('target_type', 'Area')->get();
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
            $dataAreaSelect = News::whereIn('id', $areaArray)
                                ->select('news.id', 'news.date', 'news.from', 'news.subject', 'news.content')
                ->get();

            $data = $data->merge($dataAreaSelect);

            /* INIT Data Store to be filtered */
            $dataStore = News::where('target_type', 'Store')->get();
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
            $dataStoreSelect = News::whereIn('id', $storeArray)
                                ->select('news.id', 'news.date', 'news.from', 'news.subject', 'news.content')
                ->get();

            $data = $data->merge($dataStoreSelect);

            /* INIT Data Store to be filtered */
            $dataPromoter = News::where('target_type', 'Promoter')->get();
            $promoterArray = [];
            foreach ($dataPromoter as $promoter) {
                $target = explode(',', $promoter['target_detail']);
                if (in_array($user->id, $target)) {
                    array_push($promoterArray, $promoter['id']);
                }
            }

            /* MERGER Data All dan Data Promoter */
            $dataPromoterSelect = News::whereIn('id', $promoterArray)
                                ->select('news.id', 'news.date', 'news.from', 'news.subject', 'news.content')
                ->get();

            $data = $data->merge($dataPromoterSelect);

        }

        // Set has read
        $data->map(function ($detail) use ($user) {
            $newsRead = NewsRead::where('news_id', $detail['id'])->where('user_id', $user->id)->first();

            if($newsRead) {
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

        $newsRead = NewsRead::where('news_id', $param)->where('user_id', $user->id)->count();
        if($newsRead == 0){
            $news = News::find($param);
            $news->update([ 'total_read' => $news->total_read+1 ]);

            NewsRead::create([
                'news_id' => $param,
                'user_id' => $user->id
            ]);
        }

        return response()->json(['status' => true, 'message' => 'News Readed']);
    }
}
