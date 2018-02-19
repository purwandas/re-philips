<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\Reports\SummaryDisplayShare;
use App\Price;
use App\Category;
use App\Store;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\DmArea;
use App\User;
use App\SpvDemo;
use App\TrainerArea;
use DB;

class DisplayShareController extends Controller
{
    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $user = JWTAuth::parseToken()->authenticate();

        if($this->getReject($user->id)){
            return response()->json(['status' => false, 'message' => 'Tidak bisa melakukan transaksi karena absen anda di reject oleh supervisor. '], 200);
        }

        // Check Display Share header
        $displayShareHeader = DisplayShare::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($displayShareHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $displayShareHeader, $user) {

                        foreach ($content['data'] as $data) {

                            $displayShareDetail = DisplayShareDetail::where('display_share_id', $displayShareHeader->id)->where('category_id', $data['category_id'])->first();

                            if ($displayShareDetail) { // If data exist -> update

                                $displayShareDetail->update([
                                    'philips' => $displayShareDetail->quantity + $data['philips'],
                                    'all' => $displayShareDetail->quantity + $data['all']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryDisplayShare::where('displayshare_detail_id', $displayShareDetail->id)->first();

                                $summary->update([
                                    'philips' => $data['philips'],
                                    'all' => $data['all'],
                                ]);

                                // Update percentage
                                $summary->update([
                                    'percentage' => ($summary->philips / $summary->all ) * 100,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = DisplayShareDetail::create([
                                    'display_share_id' => $displayShareHeader->id,
                                    'category_id' => $data['category_id'],
                                    'philips' => $data['philips'],
                                    'all' => $data['all']
                                ]);

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $displayShareHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Category */
                                $category = Category::where('id', $data['category_id'])->first();


                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $displayShareHeader->store_id)->pluck('distributor_id');
                                $dist = Distributor::whereIn('id', $distIds)->get();

                                $distributor_code = '';
                                $distributor_name = '';
                                foreach ($dist as $distDetail) {
                                    $distributor_code .= $distDetail->code;
                                    $distributor_name .= $distDetail->name;

                                    if ($distDetail->id != $dist->last()->id) {
                                        $distributor_code .= ', ';
                                        $distributor_name .= ', ';
                                    }
                                }


                                /* DM */
                                $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                                $dm = User::whereIn('id', $dmIds)->get();

                                $dm_name = '';
                                foreach ($dm as $dmDetail) {
                                    $dm_name .= $dmDetail->name;

                                    if ($dmDetail->id != $dm->last()->id) {
                                        $dm_name .= ', ';
                                    }
                                }

                                /* Trainer */
                                $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                                $tr = User::whereIn('id', $trIds)->get();

                                $trainer_name = '';
                                foreach ($tr as $trDetail) {
                                    $trainer_name .= $trDetail->name;

                                    if ($trDetail->id != $tr->last()->id) {
                                        $trainer_name .= ', ';
                                    }
                                }

                                SummaryDisplayShare::create([
                                    'displayshare_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $displayShareHeader->store_id,
                                    'user_id' => $displayShareHeader->user_id,
                                    'week' => $displayShareHeader->week,
                                    'distributor_code' => $distributor_code,
                                    'distributor_name' => $distributor_name,
                                    'region' => $store->district->area->region->name,
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $customerCode,
                                    'store_id' => $store->store_id,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $displayShareHeader->date,
                                    'category' => $category->name,
                                    'philips' => $data['philips'],
                                    'all' => $data['all'],
                                    'percentage' => (($data['philips'] / $data['all']) * 100),
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                            }

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                return response()->json(['status' => true, 'id_transaksi' => $displayShareHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = DisplayShare::create([
                                            'user_id' => $user->id,
                                            'store_id' => $content['id'],
                                            'week' => Carbon::now()->weekOfMonth,
                                            'date' => Carbon::now()
                                        ]);

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = DisplayShareDetail::create([
                                    'display_share_id' => $transaction->id,
                                    'category_id' => $data['category_id'],
                                    'philips' => $data['philips'],
                                    'all' => $data['all']
                                ]);


                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                            /* Category */
                            $category = Category::where('id', $detail->category_id)->first();

                            /* Distributor */
                            $distIds = StoreDistributor::where('store_id', $transaction->store_id)->pluck('distributor_id');
                            $dist = Distributor::whereIn('id', $distIds)->get();

                            $distributor_code = '';
                            $distributor_name = '';
                            foreach ($dist as $distDetail) {
                                $distributor_code .= $distDetail->code;
                                $distributor_name .= $distDetail->name;

                                if ($distDetail->id != $dist->last()->id) {
                                    $distributor_code .= ', ';
                                    $distributor_name .= ', ';
                                }
                            }

                            /* DM */
                            $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                            $dm = User::whereIn('id', $dmIds)->get();

                            $dm_name = '';
                            foreach ($dm as $dmDetail) {
                                $dm_name .= $dmDetail->name;

                                if ($dmDetail->id != $dm->last()->id) {
                                    $dm_name .= ', ';
                                }
                            }

                            /* Trainer */
                            $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                            $tr = User::whereIn('id', $trIds)->get();

                            $trainer_name = '';
                            foreach ($tr as $trDetail) {
                                $trainer_name .= $trDetail->name;

                                if ($trDetail->id != $tr->last()->id) {
                                    $trainer_name .= ', ';
                                }
                            }

                            SummaryDisplayShare::create([
                                'displayshare_detail_id' => $detail->id,
                                'region_id' => $store->district->area->region->id,
                                'area_id' => $store->district->area->id,
                                'district_id' => $store->district->id,
                                'storeId' => $transaction->store_id,
                                'user_id' => $transaction->user_id,
                                'week' => $transaction->week,
                                'distributor_code' => $distributor_code,
                                'distributor_name' => $distributor_name,
                                'region' => $store->district->area->region->name,
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $customerCode,
                                'store_id' => $store->store_id,
                                'nik' => $user->nik,
                                'promoter_name' => $user->name,
                                'date' => $transaction->date,
                                'category' => $category->name,
                                'philips' => $data['philips'],
                                'all' => $data['all'],
                                'percentage' => (($data['philips'] / $data['all']) * 100),
                                'role' => $user->role->role,
                                'role_id' => $user->role->id,
                                'role_group' => $user->role->role_group,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in(Sell Thru) header after insert
                $displayShareHeaderAfter = DisplayShare::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $displayShareHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

    }

}
