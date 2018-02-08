<?php

namespace App\Http\Controllers\Api\Master;

use App\DmArea;
use App\District;
use App\EmployeeStore;
use App\RsmRegion;
use App\SpvDemo;
use App\Store;
use App\Target;
use App\Reports\StoreLocationActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use App\Traits\StoreTrait;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class StoreController extends Controller
{
    use StoreTrait;

    public function all(){
    	$data = Store::join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();
    	
    	return response()->json($data);
    }

    public function nearby(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $distance = 250;

        $user = JWTAuth::parseToken()->authenticate();
        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

        // Check Target
        // $storeIdTarget = Target::where('user_id', $user->id)->pluck('store_id');

    	$data = Store::join('districts', 'stores.district_id', '=', 'districts.id')
                    ->where('latitude', '!=', null)
                    ->where('longitude', '!=', null)
                    ->whereNotIn('stores.id', $storeIds)
                    // ->whereIn('stores.id', $storeIdTarget)
                    ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name');
//                    ->select('id', 'store_name_1 as nama', 'latitude', 'longitude');

        // return response()->json($storeIdTarget);

        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $haversine = '( 6371 * acos( cos( radians('.$content['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$content['longitude'].') ) + sin( radians('.$content['latitude'].') ) * sin( radians( latitude ) ) ) ) * 1000';
        $data = $data->selectRaw("{$haversine} AS distance")->orderBy('distance', 'asc')->whereRaw("{$haversine} <= ?", [$distance])
            ->groupBy('store_id');
            
        return response()->json($data->get());
    }

    public function bySupervisor(){

        $user = JWTAuth::parseToken()->authenticate();

        $data = Store::where('user_id', $user->id)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                    'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

        // CHECK IN DEMO SPV
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        if(count($spvDemoIds) > 0){

            $data = Store::whereIn('stores.id', $spvDemoIds)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                    'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

        }

    	return response()->json($data);

    }

    public function byPromoter(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

//        $arinaId = Store::where('store_id', 'AR0001')->first()->id;
//
//        $storeIds->push($arinaId);

        $data = Store::whereIn('stores.id', $storeIds)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function updateStore(Request $request){

        $user = JWTAuth::parseToken()->authenticate();
        // return response()->json($request->id);
        $store = Store::find($request->id);

        /* Case kalo ga bisa di update setelah first update */
//        if($store->longitude != null && $store->latitude != null){
//            return response()->json(['status' => false, 'message' => 'Longitude dan latitude untuk store ini telah diinput'], 500);
//        }

        // Begin Store Activities Here!

        
        // ($var > 2 ? true : false);
        if (isset($store->subchannel_id)) {
            $subchannel = $store->subchannel->name;
            $channel_id = $store->subchannel->channel->id;
            $channel = $store->subchannel->channel->name;
            $globalchannel_id = $store->subchannel->channel->globalchannel->id;
            $globalchannel = $store->subchannel->channel->globalchannel->name;
        }else{
            $subchannel = '';
            $channel_id = '';
            $channel = '';
            $globalchannel_id = '';
            $globalchannel = '';
        }

        if (isset($store->user_id)) {
            $user_name = $store->user->name;
            $nik = $store->user->nik;
            $role_id = $store->user->role->id;
            $role = $store->user->role->role;
            $role_group = $store->user->role->role_group;
            $grading = '';
            $grading_id = '';
            if (isset($store->user->grading->grading)) {
                $grading = $store->user->grading->grading;
            }
            if (isset($store->user->grading->id)) {
                $grading_id = $store->user->grading->id;
            }
        }else{
            $user_name = '';
            $nik = '';
            $role_id = '';
            $role = '';
            $role_group = '';
            $grading_id = '';
            $grading = '';
        }

        $details = new Collection();

            $data = ([
                'storeId' => $store->id,
                'store_id' => $store->store_id,
                'store_name_1' => $store->store_name_1,
                'store_name_2' => $store->store_name_2,
                'longitude' => $store->longitude,
                'latitude' => $store->latitude,
                'address' => $store->address,

                'subchannel_id' => $store->subchannel_id,
                'subchannel' => $subchannel,
                'channel_id' => $channel_id,
                'channel' => $channel,
                'globalchannel_id' => $globalchannel_id,
                'globalchannel' => $globalchannel,


                'no_telp_toko' => $store->no_telp_toko,
                'no_telp_pemilik_toko' => $store->no_telp_pemilik_toko,
                'kepemilikan_toko' => $store->kepemilikan_toko,
                
                'district_id' => $store->district_id,
                'district' => $store->district->name,
                'area_id' => $store->district->area->id,
                'area' => $store->district->area->name,
                'region_id' => $store->district->area->region->id,
                'region' => $store->district->area->region->name,

                'user_id' => $store->user_id,
                'user' => $user_name,
                'nik' => $nik,
                'role_id' => $role_id,
                'role' => $role,
                'role_group' => $role_group,
                'grading_id' => $grading_id,
                'grading' => $grading,

                'lokasi_toko' => $store->lokasi_toko,
                'tipe_transaksi_2' => $store->tipe_transaksi_2,
                'tipe_transaksi' => $store->tipe_transaksi,
                'kondisi_toko' => $store->kondisi_toko,
                'new_longitude' => $request->longitude,
                'new_latitude' => $request->latitude,
                'new_address' => $request->address,
            ]);

            $details->push($data);

            // return response()->json($details);
        $dt = Carbon::now();
        $date = $dt->toDateString(); 
        StoreLocationActivity::create([
            'user_id' => $user->id,
            'date' => $date,
            'details' => $details,
        ]);
        // End Store Activities

        $store->update(['longitude' => $request->longitude, 'latitude' => $request->latitude, 'address' => $request->address]);

        return response()->json(['status' => true, 'message' => 'Update longitude dan latitude store berhasil']);

    }

    public function byArea(Request $request){

        $data = Store::whereHas('district.area', function ($query) use ($request){
                    return $query->where('id', $request->area_id);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byDm(){

        $user = JWTAuth::parseToken()->authenticate();

        $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');

        $data = Store::whereHas('district.area', function ($query) use ($areaIds){
                    return $query->whereIn('id', $areaIds);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byRsm(){

        $user = JWTAuth::parseToken()->authenticate();

        $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');

        $data = Store::whereHas('district.area.region', function ($query) use ($regionIds){
                    return $query->whereIn('id', $regionIds);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function create(Request $request){
        $content = json_decode($request->getContent(),true);
        $user = JWTAuth::parseToken()->authenticate();

                try {
                    $storeID = $this->traitGetStoreId();
                    DB::transaction(function () use ($request, $storeID, $user) {
                        $store = Store::create(
                            [
                                'store_id' => $storeID,
                                'store_name_1' => $request['store_name_1'],
                                'store_name_2' => $request['store_name_2'],
                                'longitude' => $request['longitude'],
                                'latitude' => $request['latitude'],
                                'address' => $request['address'],

                                'no_telp_toko' => $request['no_telp_toko'],
                                'no_telp_pemilik_toko' => $request['no_telp_pemilik_toko'],
                                'kepemilikan_toko' => $request['kepemilikan_toko'],

                                'district_id' => $request['district_id'],

                                'lokasi_toko' => $request['lokasi_toko'],
                                'tipe_transaksi_2' => $request['tipe_transaksi_2'],
                                'tipe_transaksi' => $request['tipe_transaksi'],
                                'kondisi_toko' => $request['kondisi_toko'],
                            ]
                        );
                        $EmployeeStore = EmployeeStore::create(
                            [
                                'user_id' => $user->id,
                                'store_id' => $store->id,
                            ]
                        );
                    });

                    // Check store after insert
                    $storeData = Store::where('store_id', $storeID)
                    ->where("store_name_1", $request['store_name_1'])
                    ->where("store_name_2", $request['store_name_2'])
                    ->first();
                    return response()->json(['status' => true, 'id_store' => $storeData->id, 'message' => 'Data berhasil di input']);
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Data gagal di input']);
                }


    }

    public function getStoreId(){
        $result['store_id'] = $this->traitGetStoreId();
            return $result;
    }

    public function getDistrict(){
        $district = District::select('districts.id', 'districts.area_id','districts.name')->get();

        return $district;
    }

    public function getArina(){

        $data = Store::where('store_id', 'AR001')->get();

        return response()->json($data);

    }
}
