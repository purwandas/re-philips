<?php

namespace App\Http\Controllers\Api\Master;

use App\EmployeeStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;
use App\Store;

class StoreController extends Controller
{
    public function all(){
    	$data = Store::select('id', 'store_name_1 as name')->get();
    	
    	return response()->json($data);
    }

    public function nearby(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $distance = 250;

    	$data = Store::where('latitude', '!=', null)
                    ->where('longitude', '!=', null)
                    ->select('id', 'store_name_1 as name', 'latitude', 'longitude');

        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $haversine = '( 6371 * acos( cos( radians('.$content['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$content['longitude'].') ) + sin( radians('.$content['latitude'].') ) * sin( radians( latitude ) ) ) ) * 1000';
        $data = $data->selectRaw("{$haversine} AS distance")->orderBy('distance', 'asc')->whereRaw("{$haversine} <= ?", [$distance]);

        return response()->json($data->get());
    }

    public function bySupervisor(){

        $user = JWTAuth::parseToken()->authenticate();

        $data = Store::where('user_id', $user->id)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byPromoter(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

        $data = Store::whereIn('stores.id', $storeIds)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function updateStore(Request $request){

        $store = Store::find($request->id);

        /* Case kalo ga bisa di update setelah first update */
//        if($store->longitude != null && $store->latitude != null){
//            return response()->json(['status' => false, 'message' => 'Longitude dan latitude untuk store ini telah diinput'], 500);
//        }

        $store->update(['longitude' => $request->longitude, 'latitude' => $request->latitude]);

        return response()->json(['status' => true, 'message' => 'Update longitude dan latitude store berhasil']);

    }

}
