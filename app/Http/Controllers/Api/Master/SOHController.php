<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\SOH;
use App\SOHDetail;
use App\Reports\SummarySoh;
use App\Price;
use App\Product;
use App\Store;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\DmArea;
use App\User;
use App\SpvDemo;
use App\TrainerArea;
use App\Traits\PromoterTrait;
use DB;
use App\SalesmanDedicate;

class SOHController extends Controller
{
    use PromoterTrait;
    
    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $user = JWTAuth::parseToken()->authenticate();

        if($this->getReject($user->id)){
            return response()->json(['status' => false, 'message' => 'Tidak bisa melakukan transaksi karena absen anda di reject oleh supervisor. '], 200);
        }

        // Check SOH header
        $sohHeader = SOH::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($sohHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $sohHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $sohDetail = SOHDetail::where('soh_id', $sohHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($sohDetail) { // If data exist -> update

                                $sohDetail->update([
                                    // 'quantity' => $sohDetail->quantity + $data['quantity']
                                    'quantity' => $data['quantity'],
                                ]);

                                /** Update Summary **/

                                $summary = SummarySOH::where('soh_detail_id', $sohDetail->id)->first();

                                $value = $data['quantity'] * $summary->unit_price;

                                $summary->update([
                                    // 'quantity' => $summary->quantity + $data['quantity'],
                                    'quantity' => $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = SOHDetail::create([
                                    'soh_id' => $sohHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($sohHeader->user->role->role_group == 'Salesman Explorer' || $sohHeader->user->role->role_group == 'SMD'){

                                    if($sohHeader->store->globalChannelId == ''){

                                        if($sohHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($sohHeader->user->dedicate, 'Sell In', $sohHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($sohHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($sohHeader->store->globalChannelId, 'Sell In', $sohHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE soh_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sohHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                // $realPrice = 0;
                                // $price = Price::where('product_id', $product->id)
                                //             ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                //             ->where('sell_type', 'Sell In')->first();

                                // if($price){
                                //     $realPrice = $price->price;
                                // }

                                /* Price */
                                $realPrice = 0;
                                if($user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'SMD') {
                                    if (isset($store->subChannel->channel->globalChannel->id)) {
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $dedicate = SalesmanDedicate::where('user_id',$user->id)->first();

                                        $newDedicate = '';

                                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $sohHeader->store_id)->pluck('distributor_id');
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

                                if (isset($store->subChannel->channel->name)){
                                    $channel = $store->subChannel->channel->name;
                                }else{
                                    $channel = '';
                                }

                                if (isset($store->subChannel->name)){
                                    $subChannel = $store->subChannel->name;
                                }else{
                                    $subChannel = '';
                                }


                                SummarySOH::create([
                                    'soh_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $sohHeader->store_id,
                                    'user_id' => $sohHeader->user_id,
                                    'week' => $sohHeader->week,
                                    'distributor_code' => $distributor_code,
                                    'distributor_name' => $distributor_name,
                                    'region' => $store->district->area->region->name,
                                    'channel' => $channel,
                                    'sub_channel' => $subChannel,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $customerCode,
                                    'store_id' => $store->store_id,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sohHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
                                    'role' => $user->role->role,
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

                return response()->json(['status' => true, 'id_transaksi' => $sohHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = SOH::create([
                                            'user_id' => $user->id,
                                            'store_id' => $content['id'],
                                            'week' => Carbon::now()->weekOfMonth,
                                            'date' => Carbon::now()
                                        ]);

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $sohDetail = SOHDetail::where('soh_id', $transaction->id)->where('product_id', $data['product_id'])->first();


                            if ($sohDetail) { // If data exist -> update

                                $sohDetail->update([
                                    // 'quantity' => $sohDetail->quantity + $data['quantity']
                                    'quantity' => $data['quantity'],
                                ]);

                                /** Update Summary **/

                                $summary = SummarySOH::where('soh_detail_id', $sohDetail->id)->first();

                                $value = $data['quantity'] * $summary->unit_price;

                                $summary->update([
                                    // 'quantity' => $summary->quantity + $data['quantity'],
                                    'quantity' => $data['quantity'],
                                    'value' => $value,
                                ]);

                            }else{ /** Insert Summary **/

                                // DETAILS
                                $detail = SOHDetail::create([
                                    'soh_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($transaction->user->role->role_group == 'Salesman Explorer' || $transaction->user->role->role_group == 'SMD'){

                                    if($transaction->store->globalChannelId == ''){

                                        if($transaction->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($transaction->user->dedicate, 'Sell In', $transaction->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($transaction->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($transaction->store->globalChannelId, 'Sell In', $transaction->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE soh_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $transaction->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                    if(count($spvDemoName) > 0){
                                        $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                    }

                                    $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

                                /* Price */
                                // $realPrice = 0;
                                // $price = Price::where('product_id', $product->id)
                                //             ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                //             ->where('sell_type', 'Sell In')->first();

                                // if($price){
                                //     $realPrice = $price->price;
                                // }

                                /* Price */
                                $realPrice = 0;
                                if($user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'SMD') {
                                    if (isset($store->subChannel->channel->globalChannel->id)) {
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $dedicate = SalesmanDedicate::where('user_id',$user->id)->first();

                                        $newDedicate = '';

                                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

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

                                    if (isset($store->subChannel->channel->name)){
                                        $channel = $store->subChannel->channel->name;
                                    }else{
                                        $channel = '';
                                    }

                                    if (isset($store->subChannel->name)){
                                        $subChannel = $store->subChannel->name;
                                    }else{
                                        $subChannel = '';
                                    }

                                SummarySOH::create([
                                    'soh_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $transaction->store_id,
                                    'user_id' => $transaction->user_id,
                                    'week' => $transaction->week,
                                    'distributor_code' => $distributor_code,
                                    'distributor_name' => $distributor_name,
                                    'region' => $store->district->area->region->name,
                                    'channel' => $channel,
                                    'sub_channel' => $subChannel,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $customerCode,
                                    'store_id' => $store->store_id,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
                                    'role' => $user->role->role,
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

                // Check sell in(Sell Thru) header after insert
                $sohHeaderAfter = SOH::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sohHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

    }

	public function tes(Request $request)
	{
		return 'tes';
	}

	/*
    public function store2(Request $request)
    {
    	try
    	{
    		$content = json_decode($request->getContent(),true);
    		$user = JWTAuth::parseToken()->authenticate();

    		//Transaction Header SOH
    		$transaction = Soh::create
    		([
    			'user_id'	=>$user->id,
    			'store_id'	=>$content['id'],
                'week' => Carbon::now()->weekOfMonth,
    			'date'		=>Carbon::now()
    		]);

    		// Transaction Details
    		foreach ($content['data'] as $data) {
    			SohDetail::create
    			([
    				'soh_id'	=> $transaction->id,
    				'product_id'	=> $data['product_id'],
    				'quantity'	=> $data['quantity']
    			]);
    		}
    	} 
    	catch (\Exception $e)
    	{
    		// Delete Inserted data
    		if (isset($transaction)) {
    			// Delete Detail first
    			$details= SohDetail::where('soh_id',$transaction->id);
    			$details->forceDelete();

    			// Delete Header then
    			Soh::find($transaction->id)->forceDelete();
    		}
    		return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi']);
    	}

    	return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);
    }
	*/
}
