<?php

namespace App\Http\Controllers\Api\Master;

use App\Price;
use App\Product;
use App\Reports\SalesmanSummarySales;
use App\SalesmanProductFocuses;
use App\SalesmanDedicate;
use App\Store;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\DmArea;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummaryRetConsument;
use App\Reports\SummaryRetDistributor;
use App\Reports\SummaryFreeProduct;
use App\Reports\SummaryTbat;
use App\Traits\ActualTrait;
use App\Traits\PromoterTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\RetConsument;
use App\RetConsumentDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Tbat;
use App\TbatDetail;
use DB;
use App\User;
use App\SpvDemo;
use App\TrainerArea;

class SalesController extends Controller
{
    use ActualTrait;

    public function store(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        if($this->getReject($user->id)){
            return response()->json(['status' => false, 'message' => 'Tidak bisa melakukan transaksi karena absen anda di reject oleh supervisor. '], 200);
        }

        if(!isset($content['irisan'])) { // Set Default Irisan if doesn't exist
            $content['irisan'] = 0;
        }else{
            if($content['irisan'] == null){
                $content['irisan'] = 0;
            }
        }

        // return response()->json($content);

        if($param == 1) { /* SELL IN(SELL THRU) */

         // return response()->json($this->getPromoterTitle($user->id, $content['id']));

            // Check sell in(Sell Thru) header
            $sellInHeader = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($sellInHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $sellInHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $sellInDetail = SellInDetail::where('sellin_id', $sellInHeader->id)
                                            ->where('product_id', $data['product_id'])
                                            ->where('irisan', $content['irisan'])
                                            ->first();

                            if ($sellInDetail) { // If data exist -> update

                                $sellInDetail->update([
                                    'quantity' => $sellInDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                if($user->role->role_group != 'Salesman Explorer') {

                                    $summary = SummarySellIn::where('sellin_detail_id', $sellInDetail->id)->first();

                                    $value_old = $summary->value;

                                    $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                    ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                    ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                    ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                    $summary->update([
                                        'quantity' => $summary->quantity + $data['quantity'],
                                        'value' => $value,
                                        'value_pf_mr' => $value_pf_mr,
                                        'value_pf_tr' => $value_pf_tr,
                                        'value_pf_ppe' => $value_pf_ppe,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $sellInHeader->user_id;
                                    $summary_ta['store_id'] = $sellInHeader->store_id;
                                    $summary_ta['week'] = $sellInHeader->week;
                                    $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                    $summary_ta['value_old'] = $value_old;
                                    $summary_ta['value'] = $summary->value;
                                    $summary_ta['group'] = $summary->group;
                                    $summary_ta['sell_type'] = 'Sell In';
                                    $summary_ta['irisan'] = $summary->irisan;

                                    $this->changeActual($summary_ta, 'change');

                                }else{ // SEE (Salesman Explorer)

                                    $summary = SalesmanSummarySales::where('sellin_detail_id', $sellInDetail->id)->first();

                                    $value_old = $summary->value; // Buat reset actual salesman

                                    $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                    ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                                    $summary->update([
                                        'quantity' => $summary->quantity + $data['quantity'],
                                        'value' => $value,
                                        'value_pf' => $value_pf
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $sellInHeader->user_id;
                                    $summary_ta['store_id'] = $sellInHeader->store_id;
                                    $summary_ta['pf'] = $summary->value_pf;
                                    $summary_ta['value_old'] = $value_old;
                                    $summary_ta['value'] = $summary->value;

                                    $this->changeActualSalesman($summary_ta, 'change');

                                }

                            } else { // If data didn't exist -> create

                                $detail = SellInDetail::create([
                                    'sellin_id' => $sellInHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($sellInHeader->user->role->role_group == 'Salesman Explorer' || $sellInHeader->user->role->role_group == 'SMD'){

                                    if($sellInHeader->store->globalChannelId == ''){

                                        if($sellInHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($sellInHeader->user->dedicate, 'Sell In', $sellInHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($sellInHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($sellInHeader->store->globalChannelId, 'Sell In', $sellInHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // SellInDetail::where('id', $detail->id)->first()->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE sell_in_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sellInHeader->store_id)->first();
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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $sellInHeader->store_id)->pluck('distributor_id');
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

                                /* Value - Product Focus */
                                $value_pf_mr = 0;
                                $value_pf_tr = 0;
                                $value_pf_ppe = 0;

                                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                                foreach ($productFocus as $productFocusDetail) {
                                    if ($productFocusDetail->type == 'Modern Retail') {
                                        $value_pf_mr = $realPrice * $data['quantity'];
                                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                                        $value_pf_tr = $realPrice * $data['quantity'];
                                    } else if ($productFocusDetail->type == 'PPE') {
                                        $value_pf_ppe = $realPrice * $data['quantity'];
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

                                if($user->role->role_group != 'Salesman Explorer') {
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

                                    $summary = SummarySellIn::create([
                                        'sellin_detail_id' => $detail->id,
                                        'region_id' => $store->district->area->region->id,
                                        'area_id' => $store->district->area->id,
                                        'district_id' => $store->district->id,
                                        'storeId' => $sellInHeader->store_id,
                                        'user_id' => $sellInHeader->user_id,
                                        'week' => $sellInHeader->week,
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
                                        'dedicate' => $store->dedicate,
                                        'nik' => $user->nik,
                                        'promoter_name' => $user->name,
                                        'date' => $sellInHeader->date,
                                        'model' => $product->model . '/' . $product->variants,
                                        'group' => $product->category->group->groupProduct->name,
                                        'category' => $product->category->name,
                                        'product_id' => $product->id,
                                        'product_name' => $product->name,
                                        'quantity' => $data['quantity'],
                                        'irisan' => $content['irisan'],
                                        'unit_price' => $realPrice,
                                        'value' => $realPrice * $data['quantity'],
                                        'value_pf_mr' => $value_pf_mr,
                                        'value_pf_tr' => $value_pf_tr,
                                        'value_pf_ppe' => $value_pf_ppe,
                                        'role' => $user->role->role,
                                        'role_id' => $user->role->id,
                                        'role_group' => $user->role->role_group,
                                        'spv_name' => $spvName,
                                        'dm_name' => $dm_name,
                                        'trainer_name' => $trainer_name,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $sellInHeader->user_id;
                                    $summary_ta['store_id'] = $sellInHeader->store_id;
                                    $summary_ta['week'] = $sellInHeader->week;
                                    $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                    $summary_ta['value'] = $summary->value;
                                    $summary_ta['group'] = $summary->group;
                                    $summary_ta['sell_type'] = 'Sell In';
                                    $summary_ta['irisan'] = $summary->irisan;

                                    $this->changeActual($summary_ta, 'change');

                                }else{ // Buat SEE (Salesman Explorer)

                                    $value_pf = 0;

                                    $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                    if($productFocus){
                                        $value_pf = $realPrice * $detail->quantity;
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


                                    $summary = SalesmanSummarySales::create([
                                        'sellin_detail_id' => $detail->id,
                                        'region_id' => $store->district->area->region->id,
                                        'area_id' => $store->district->area->id,
                                        'district_id' => $store->district->id,
                                        'storeId' => $sellInHeader->store_id,
                                        'user_id' => $sellInHeader->user_id,
                                        'week' => $sellInHeader->week,
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
                                        'dedicate' => $store->dedicate,
                                        'nik' => $user->nik,
                                        'promoter_name' => $user->name,
                                        'date' => $sellInHeader->date,
                                        'model' => $product->model . '/' . $product->variants,
                                        'group' => $product->category->group->groupProduct->name,
                                        'category' => $product->category->name,
                                        'product_name' => $product->name,
                                        'quantity' => $detail->quantity,
                                        'unit_price' => $realPrice,
                                        'value' => $realPrice * $detail->quantity,
                                        'value_pf' => $value_pf,
                                        'role' => $user->role->role,
                                        'role_id' => $user->role->id,
                                        'role_group' => $user->role->role_group,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $sellInHeader->user_id;
                                    $summary_ta['store_id'] = $sellInHeader->store_id;
                                    $summary_ta['pf'] = $summary->value_pf;
                                    $summary_ta['value'] = $summary->value;

                                    $this->changeActualSalesman($summary_ta, 'change');

                                }

                            }

                        }

                       });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                return response()->json(['status' => true, 'id_transaksi' => $sellInHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = SellIn::create([
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

                            $sellInDetail = SellInDetail::where('sellin_id', $transaction->id)
                                            ->where('product_id', $data['product_id'])
                                            ->where('irisan', $content['irisan'])
                                            ->first();

                            if ($sellInDetail) { // If data exist -> update

                                $sellInDetail->update([
                                    'quantity' => $sellInDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                if($user->role->role_group != 'Salesman Explorer') {

                                    $summary = SummarySellIn::where('sellin_detail_id', $sellInDetail->id)->first();

                                    $value_old = $summary->value;

                                    $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                    ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                    ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                    ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                    $summary->update([
                                        'quantity' => $summary->quantity + $data['quantity'],
                                        'value' => $value,
                                        'value_pf_mr' => $value_pf_mr,
                                        'value_pf_tr' => $value_pf_tr,
                                        'value_pf_ppe' => $value_pf_ppe,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $transaction->user_id;
                                    $summary_ta['store_id'] = $transaction->store_id;
                                    $summary_ta['week'] = $transaction->week;
                                    $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                    $summary_ta['value_old'] = $value_old;
                                    $summary_ta['value'] = $summary->value;
                                    $summary_ta['group'] = $summary->group;
                                    $summary_ta['sell_type'] = 'Sell In';
                                    $summary_ta['irisan'] = $summary->irisan;

                                    $this->changeActual($summary_ta, 'change');

                                }else{ // SEE (Salesman Explorer)

                                    $summary = SalesmanSummarySales::where('sellin_detail_id', $sellInDetail->id)->first();

                                    $value_old = $summary->value; // Buat reset actual salesman

                                    $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                    ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                                    $summary->update([
                                        'quantity' => $summary->quantity + $data['quantity'],
                                        'value' => $value,
                                        'value_pf' => $value_pf
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $transaction->user_id;
                                    $summary_ta['store_id'] = $transaction->store_id;
                                    $summary_ta['pf'] = $summary->value_pf;
                                    $summary_ta['value_old'] = $value_old;
                                    $summary_ta['value'] = $summary->value;

                                    $this->changeActualSalesman($summary_ta, 'change');

                                }

                            } else { // If data didn't exist -> create                                

                                // DETAILS
                                $detail = SellInDetail::create([
                                        'sellin_id' => $transaction->id,
                                        'product_id' => $data['product_id'],
                                        'quantity' => $data['quantity'],
                                        'irisan' => $content['irisan']
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

                                // SellInDetail::where('id', $detail->id)->first()->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE sell_in_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                // $product = $detail->product->name;
                                // $g_channel_store = $transaction->store->globalChannelId;
                                // $g_channel_dedicate = $transaction->user->dedicate;

                                // $detail->forceDelete();
                                // $transaction->forceDelete();

                                // return response()->json($transaction->store->globalChannelId);

                                // return response()->json($detail->product->getPriceAttribute($transaction->store->globalChannelId, 'Sell In', $transaction->date));

                                // return response()->json($detail->product->getPriceAttribute($transaction->date)->where('globalchannel_id', $transaction->store->globalChannelId)->where('sell_type', 'Sell In'));

                                // return response()->json(['type' => 'SELL IN', 'product' => $product, 'g_channel_store' => $g_channel_store, 'g_channel_dedicate' => $g_channel_dedicate,'price' => $priceForDetail]);

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

                                // $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                //     if(count($spvDemoName) > 0){
                                //         $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                //     }

                                // $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

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
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
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

                                /* Value - Product Focus */
                                $value_pf_mr = 0;
                                $value_pf_tr = 0;
                                $value_pf_ppe = 0;

                                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                                foreach ($productFocus as $productFocusDetail) {
                                    if ($productFocusDetail->type == 'Modern Retail') {
                                        $value_pf_mr = $realPrice * $detail->quantity;
                                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                                        $value_pf_tr = $realPrice * $detail->quantity;
                                    } else if ($productFocusDetail->type == 'PPE') {
                                        $value_pf_ppe = $realPrice * $detail->quantity;
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

                                if($user->role->role_group != 'Salesman Explorer') {
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

                                    $summary = SummarySellIn::create([
                                        'sellin_detail_id' => $detail->id,
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
                                        'dedicate' => $store->dedicate,
                                        'nik' => $user->nik,
                                        'promoter_name' => $user->name,
                                        'date' => $transaction->date,
                                        'model' => $product->model . '/' . $product->variants,
                                        'group' => $product->category->group->groupProduct->name,
                                        'category' => $product->category->name,
                                        'product_id' => $product->id,
                                        'product_name' => $product->name,
                                        'quantity' => $detail->quantity,
                                        'irisan' => $content['irisan'],
                                        'unit_price' => $realPrice,
                                        'value' => $realPrice * $detail->quantity,
                                        'value_pf_mr' => $value_pf_mr,
                                        'value_pf_tr' => $value_pf_tr,
                                        'value_pf_ppe' => $value_pf_ppe,
                                        'role' => $user->role->role,
                                        'role_id' => $user->role->id,
                                        'role_group' => $user->role->role_group,
                                        'spv_name' => $spvName,
                                        'dm_name' => $dm_name,
                                        'trainer_name' => $trainer_name,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $transaction->user_id;
                                    $summary_ta['store_id'] = $transaction->store_id;
                                    $summary_ta['week'] = $transaction->week;
                                    $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                    $summary_ta['value'] = $summary->value;
                                    $summary_ta['group'] = $summary->group;
                                    $summary_ta['sell_type'] = 'Sell In';
                                    $summary_ta['irisan'] = $summary->irisan;

                                    // return $summary_ta;

                                    $this->changeActual($summary_ta, 'change');

                                }else{ // Buat SEE (Salesman Explorer)

                                    $value_pf = 0;

                                    $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                    if($productFocus){
                                        $value_pf = $realPrice * $detail->quantity;
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

                                    $summary = SalesmanSummarySales::create([
                                        'sellin_detail_id' => $detail->id,
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
                                        'dedicate' => $store->dedicate,
                                        'nik' => $user->nik,
                                        'promoter_name' => $user->name,
                                        'date' => $transaction->date,
                                        'model' => $product->model . '/' . $product->variants,
                                        'group' => $product->category->group->groupProduct->name,
                                        'category' => $product->category->name,
                                        'product_name' => $product->name,
                                        'quantity' => $detail->quantity,
                                        'unit_price' => $realPrice,
                                        'value' => $realPrice * $detail->quantity,
                                        'value_pf' => $value_pf,
                                        'role' => $user->role->role,
                                        'role_id' => $user->role->id,
                                        'role_group' => $user->role->role_group,
                                    ]);

                                    // Actual Summary
                                    $summary_ta['user_id'] = $transaction->user_id;
                                    $summary_ta['store_id'] = $transaction->store_id;
                                    $summary_ta['pf'] = $summary->value_pf;
                                    $summary_ta['value'] = $summary->value;

                                    $this->changeActualSalesman($summary_ta, 'change');

                                }

                            }

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in(Sell Thru) header after insert
                $sellInHeaderAfter = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sellInHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 2) { /* SELL OUT */

            // return response()->json($this->getPromoterTitle($user->id, $content['id']));

            // Check sell out header
            $sellOutHeader = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();
            // return response()->json($sellOutHeader);

            if ($sellOutHeader) { // If header exist (update and/or create detail)

               try {
                   DB::transaction(function () use ($content, $sellOutHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $sellOutDetail = SellOutDetail::where('sellout_id', $sellOutHeader->id)
                                                ->where('product_id', $data['product_id'])
                                                ->where('irisan', $content['irisan'])
                                                ->first();

                            if ($sellOutDetail) { // If data exist -> update

                                $sellOutDetail->update([
                                    'quantity' => $sellOutDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummarySellOut::where('sellout_detail_id', $sellOutDetail->id)->first();

                                $value_old = $summary->value;

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellOutHeader->user_id;
                                $summary_ta['store_id'] = $sellOutHeader->store_id;
                                $summary_ta['week'] = $sellOutHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            } else { // If data didn't exist -> create

                                $detail = SellOutDetail::create([
                                    'sellout_id' => $sellOutHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($sellOutHeader->user->role->role_group == 'Salesman Explorer' || $sellOutHeader->user->role->role_group == 'SMD'){

                                    if($sellOutHeader->store->globalChannelId == ''){

                                        if($sellOutHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($sellOutHeader->user->dedicate, 'Sell Out', $sellOutHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($sellOutHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($sellOutHeader->store->globalChannelId, 'Sell Out', $sellOutHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE sell_out_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sellOutHeader->store_id)->first();
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
                                $realPrice = 0;
                                if($user->role->role_group == 'SMD' || $user->role->role_group == 'Salesman Explorer') {
                                    if (isset($store->subChannel->channel->globalChannel->id)) {
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }else{
                                        $dedicate = SalesmanDedicate::where('user_id',$user->id)->first();

                                        $newDedicate = '';

                                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $sellOutHeader->store_id)->pluck('distributor_id');
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

                                /* Value - Product Focus */
                                $value_pf_mr = 0;
                                $value_pf_tr = 0;
                                $value_pf_ppe = 0;

                                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                                foreach ($productFocus as $productFocusDetail) {
                                    if ($productFocusDetail->type == 'Modern Retail') {
                                        $value_pf_mr = $realPrice * $data['quantity'];
                                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                                        $value_pf_tr = $realPrice * $data['quantity'];
                                    } else if ($productFocusDetail->type == 'PPE') {
                                        $value_pf_ppe = $realPrice * $data['quantity'];
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

                                $summary = SummarySellOut::create([
                                    'sellout_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $sellOutHeader->store_id,
                                    'user_id' => $sellOutHeader->user_id,
                                    'week' => $sellOutHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sellOutHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellOutHeader->user_id;
                                $summary_ta['store_id'] = $sellOutHeader->store_id;
                                $summary_ta['week'] = $sellOutHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            }

                        }

                   });
               } catch (\Exception $e) {
                   return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
               }

                return response()->json(['status' => true, 'id_transaksi' => $sellOutHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

               try {
                   DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = SellOut::create([
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

                            $sellOutDetail = SellOutDetail::where('sellout_id', $transaction->id)
                                                ->where('product_id', $data['product_id'])
                                                ->where('irisan', $content['irisan'])
                                                ->first();

                            if ($sellOutDetail) { // If data exist -> update

                                $sellOutDetail->update([
                                    'quantity' => $sellOutDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummarySellOut::where('sellout_detail_id', $sellOutDetail->id)->first();

                                $value_old = $summary->value;

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['week'] = $transaction->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            } else { // If data didn't exist -> create

                                // DETAILS
                                $detail = SellOutDetail::create([
                                        'sellout_id' => $transaction->id,
                                        'product_id' => $data['product_id'],
                                        'quantity' => $data['quantity'],
                                        'irisan' => $content['irisan'],
                                    ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($transaction->user->role->role_group == 'Salesman Explorer' || $transaction->user->role->role_group == 'SMD'){

                                    if($transaction->store->globalChannelId == ''){

                                        if($transaction->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($transaction->user->dedicate, 'Sell Out', $transaction->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($transaction->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($transaction->store->globalChannelId, 'Sell Out', $transaction->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE sell_out_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------


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

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

                                /* Price */
                                $realPrice = 0;
                                if($user->role->role_group == 'SMD' || $user->role->role_group == 'Salesman Explorer') {
                                    if (isset($store->subChannel->channel->globalChannel->id)) {
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }else{
                                        $dedicate = SalesmanDedicate::where('user_id',$user->id)->first();

                                        $newDedicate = '';

                                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell Out')
                                            ->first();
                                    }
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

                                /* Value - Product Focus */
                                $value_pf_mr = 0;
                                $value_pf_tr = 0;
                                $value_pf_ppe = 0;

                                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                                foreach ($productFocus as $productFocusDetail) {
                                    if ($productFocusDetail->type == 'Modern Retail') {
                                        $value_pf_mr = $realPrice * $detail->quantity;
                                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                                        $value_pf_tr = $realPrice * $detail->quantity;
                                    } else if ($productFocusDetail->type == 'PPE') {
                                        $value_pf_ppe = $realPrice * $detail->quantity;
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

                                $summary = SummarySellOut::create([
                                    'sellout_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'irisan' => $content['irisan'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['week'] = $transaction->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                // return response()->json($this->changeActual($summary_ta, 'change'));
                                $this->changeActual($summary_ta, 'change');

                            }
                        }

                   });
               } catch (\Exception $e) {
                   return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
               }

                // Check sell in(Sell Thru) header after insert
                $sellOutHeaderAfter = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sellOutHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 3) { /* RETURN DISTRIBUTOR */

            // Check ret distributor header
            $retDistributorHeader = RetDistributor::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($retDistributorHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $retDistributorHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $retDistributorDetail = RetDistributorDetail::where('retdistributor_id', $retDistributorHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($retDistributorDetail) { // If data exist -> update

                                $retDistributorDetail->update([
                                    'quantity' => $retDistributorDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryRetDistributor::where('retdistributor_detail_id', $retDistributorDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = RetDistributorDetail::create([
                                    'retdistributor_id' => $retDistributorHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($retDistributorHeader->user->role->role_group == 'Salesman Explorer' || $retDistributorHeader->user->role->role_group == 'SMD'){

                                    if($retDistributorHeader->store->globalChannelId == ''){

                                        if($retDistributorHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($retDistributorHeader->user->dedicate, 'Sell In', $retDistributorHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($retDistributorHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($retDistributorHeader->store->globalChannelId, 'Sell In', $retDistributorHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE ret_distributor_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $retDistributorHeader->store_id)->first();
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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $retDistributorHeader->store_id)->pluck('distributor_id');
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

                                SummaryRetDistributor::create([
                                    'retdistributor_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $retDistributorHeader->store_id,
                                    'user_id' => $retDistributorHeader->user_id,
                                    'week' => $retDistributorHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $retDistributorHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                return response()->json(['status' => true, 'id_transaksi' => $retDistributorHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = RetDistributor::create([
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

                            $retDistributorDetail = RetDistributorDetail::where('retdistributor_id', $transaction->id)->where('product_id', $data['product_id'])->first();

                            if ($retDistributorDetail) { // If data exist -> update

                                $retDistributorDetail->update([
                                    'quantity' => $retDistributorDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryRetDistributor::where('retdistributor_detail_id', $retDistributorDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                // DETAILS
                                $detail = RetDistributorDetail::create([
                                        'retdistributor_id' => $transaction->id,
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
                                DB::statement('UPDATE ret_distributor_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $transaction->store_id)->first();

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                    $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                    if(count($spvDemoName) > 0){
                                        $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                    }

                                    $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
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

                                SummaryRetDistributor::create([
                                    'retdistributor_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                // Check sell in(Sell Thru) header after insert
                $retDistributorHeaderAfter = RetDistributor::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $retDistributorHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 4) { /* RETURN CONSUMENT */

            // Check ret consument header
            $retConsumentHeader = RetConsument::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($retConsumentHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $retConsumentHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $retConsumentDetail = RetConsumentDetail::where('retconsument_id', $retConsumentHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($retConsumentDetail) { // If data exist -> update

                                $retConsumentDetail->update([
                                    'quantity' => $retConsumentDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryRetConsument::where('retconsument_detail_id', $retConsumentDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = RetConsumentDetail::create([
                                    'retconsument_id' => $retConsumentHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($retConsumentHeader->user->role->role_group == 'Salesman Explorer' || $retConsumentHeader->user->role->role_group == 'SMD'){

                                    if($retConsumentHeader->store->globalChannelId == ''){

                                        if($retConsumentHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($retConsumentHeader->user->dedicate, 'Sell In', $retConsumentHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($retConsumentHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($retConsumentHeader->store->globalChannelId, 'Sell In', $retConsumentHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE ret_consument_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $retConsumentHeader->store_id)->first();

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $retConsumentHeader->store_id)->pluck('distributor_id');
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

                                SummaryRetConsument::create([
                                    'retconsument_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $retConsumentHeader->store_id,
                                    'user_id' => $retConsumentHeader->user_id,
                                    'week' => $retConsumentHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $retConsumentHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                return response()->json(['status' => true, 'id_transaksi' => $retConsumentHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = RetConsument::create([
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

                            $retConsumentDetail = RetConsumentDetail::where('retconsument_id', $transaction->id)->where('product_id', $data['product_id'])->first();

                            if ($retConsumentDetail) { // If data exist -> update

                                $retConsumentDetail->update([
                                    'quantity' => $retConsumentDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryRetConsument::where('retconsument_detail_id', $retConsumentDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                // DETAILS
                                $detail = RetConsumentDetail::create([
                                        'retconsument_id' => $transaction->id,
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
                                DB::statement('UPDATE ret_consument_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

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

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
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

                                SummaryRetConsument::create([
                                    'retconsument_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                // Check sell in(Sell Thru) header after insert
                $retConsumentHeaderAfter = RetConsument::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $retConsumentHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 5) { /* FREE PRODUCT */

            // Check free product header
            $freeProductHeader = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($freeProductHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $freeProductHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $freeProductDetail = FreeProductDetail::where('freeproduct_id', $freeProductHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($freeProductDetail) { // If data exist -> update

                                $freeProductDetail->update([
                                    'quantity' => $freeProductDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryFreeProduct::where('freeproduct_detail_id', $freeProductDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = FreeProductDetail::create([
                                    'freeproduct_id' => $freeProductHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($freeProductHeader->user->role->role_group == 'Salesman Explorer' || $freeProductHeader->user->role->role_group == 'SMD'){

                                    if($freeProductHeader->store->globalChannelId == ''){

                                        if($freeProductHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($freeProductHeader->user->dedicate, 'Sell In', $freeProductHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($freeProductHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($freeProductHeader->store->globalChannelId, 'Sell In', $freeProductHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE free_product_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $freeProductHeader->store_id)->first();
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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $freeProductHeader->store_id)->pluck('distributor_id');
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

                                SummaryFreeProduct::create([
                                    'freeproduct_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $freeProductHeader->store_id,
                                    'user_id' => $freeProductHeader->user_id,
                                    'week' => $freeProductHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $freeProductHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                return response()->json(['status' => true, 'id_transaksi' => $freeProductHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = FreeProduct::create([
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

                            $freeProductDetail = FreeProductDetail::where('freeproduct_id', $transaction->id)->where('product_id', $data['product_id'])->first();

                            if ($freeProductDetail) { // If data exist -> update

                                $freeProductDetail->update([
                                    'quantity' => $freeProductDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryFreeProduct::where('freeproduct_detail_id', $freeProductDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                // DETAILS
                                $detail = FreeProductDetail::create([
                                        'freeproduct_id' => $transaction->id,
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
                                DB::statement('UPDATE free_product_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

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

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
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

                                SummaryFreeProduct::create([
                                    'freeproduct_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
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

                // Check sell in(Sell Thru) header after insert
                $freeProductHeaderAfter = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $freeProductHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 6) { /* TBAT */

            // Check tbat header
            $tbatHeader = Tbat::where('user_id', $user->id)->where('store_id', $content['id'])->where('store_destination_id', $content['destination_id'])->where('date', date('Y-m-d'))->first();

            if ($tbatHeader) { // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $tbatHeader, $user) {

                        $arInsert = [];

                        foreach ($content['data'] as $data) {

                            if(in_array($data['product_id'], $arInsert)){
                                continue;
                            }else{
                                array_push($arInsert, $data['product_id']);
                            }

                            $tbatDetail = TbatDetail::where('tbat_id', $tbatHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($tbatDetail) { // If data exist -> update

                                $tbatDetail->update([
                                    'quantity' => $tbatDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryTbat::where('tbat_detail_id', $tbatDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                $detail = TbatDetail::create([
                                    'tbat_id' => $tbatHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                // UPDATE PRICE NEW METHOD

                                $priceForDetail = 0;                                

                                // BY DEDICATE - GLOBAL CHANNEL
                                if($tbatHeader->user->role->role_group == 'Salesman Explorer' || $tbatHeader->user->role->role_group == 'SMD'){

                                    if($tbatHeader->store->globalChannelId == ''){

                                        if($tbatHeader->user->dedicate != ''){

                                            $cekPrice = $detail->product->getPriceAttribute($tbatHeader->user->dedicate, 'Sell In', $tbatHeader->date);

                                            if($cekPrice){
                                                $priceForDetail = $cekPrice->price;
                                            }    

                                        }

                                    }

                                }

                                // BY STORE - GLOBAL CHANNEL

                                if($tbatHeader->store->globalChannelId != ''){

                                    $cekPrice = $detail->product->getPriceAttribute($tbatHeader->store->globalChannelId, 'Sell In', $tbatHeader->date);

                                    if($cekPrice){
                                        $priceForDetail = $cekPrice->price;
                                    }

                                }

                                // UPDATE SALES PRICE IN DETAIL

                                // $detail->update(['price' => $priceForDetail]);
                                DB::statement('UPDATE tbat_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $tbatHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Store Destination */
                                $storeDestination = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $tbatHeader->store_destination_id)->first();
                                $spvName2='';
                                if (isset($storeDestination->user->name)) {
                                    $spvName2=$storeDestination->user->name;
                                }
                                
                                $spvDemoName2 = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName2) > 0){
                                    $spvName2 = (isset($spvDemoName2->user->name)) ? $spvDemoName2->user->name : '';
                                }

                                $customerCode2 = (isset($storeDestination->store_name_2)) ? $storeDestination->store_name_2 : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
                                }

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $tbatHeader->store_id)->pluck('distributor_id');
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

                                SummaryTbat::create([
                                    'tbat_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $tbatHeader->store_id,
                                    'storeDestinationId' => $tbatHeader->store_destination_id,
                                    'user_id' => $tbatHeader->user_id,
                                    'week' => $tbatHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'store_destination_name_1' => $storeDestination->store_name_1,
                                    'store_destination_name_2' => $customerCode2,
                                    'store_destination_id' => $storeDestination->store_id,
                                    'destination_dedicate' => $storeDestination->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $tbatHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'spv_name2' => $spvName2,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                            }

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                return response()->json(['status' => true, 'id_transaksi' => $tbatHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = Tbat::create([
                                            'user_id' => $user->id,
                                            'store_id' => $content['id'],
                                            'store_destination_id' => $content['destination_id'],
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

                            $tbatDetail = TbatDetail::where('tbat_id', $transaction->id)->where('product_id', $data['product_id'])->first();

                            if ($tbatDetail) { // If data exist -> update

                                $tbatDetail->update([
                                    'quantity' => $tbatDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummaryTbat::where('tbat_detail_id', $tbatDetail->id)->first();

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                ]);

                            } else { // If data didn't exist -> create

                                /** Insert Summary **/

                                // DETAILS
                                $detail = TbatDetail::create([
                                        'tbat_id' => $transaction->id,
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
                                DB::statement('UPDATE tbat_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                                // --------------------------------------------

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $transaction->store_id)->first();
                                    $spvName='';
                                    if (isset($store->user->name)) {
                                        $spvName=$store->user->name;
                                    }

                                    $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                    if(count($spvDemoName) > 0){
                                        $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                    }

                                    $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Store Destination */
                                $storeDestination = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $transaction->store_destination_id)->first();
                                    $spvName2='';
                                    if (isset($storeDestination->user->name)) {
                                        $spvName2=$storeDestination->user->name;
                                    }
                                    
                                    $spvDemoName2 = SpvDemo::where('user_id', $user->id)->first();
                                    if(count($spvDemoName2) > 0){
                                        $spvName2 = (isset($spvDemoName2->user->name)) ? $spvDemoName2->user->name : '';
                                    }

                                    $customerCode2 = (isset($storeDestination->store_name_2)) ? $storeDestination->store_name_2 : '';

                                // /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $detail->product_id)->first();

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
                                        if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                        $price = Price::where('product_id', $product->id)
                                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                            ->where('global_channels.name',$newDedicate)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }

                                }else{
                                    if($store->subchannel_id != null || $store->subchannel_id != ''){
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }else{
                                        $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', '')
                                            ->where('sell_type', 'Sell In')
                                            ->first();
                                    }
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

                                SummaryTbat::create([
                                    'tbat_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $transaction->store_id,
                                    'storeDestinationId' => $transaction->store_destination_id,
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
                                    'dedicate' => $store->dedicate,
                                    'store_destination_name_1' => $storeDestination->store_name_1,
                                    'store_destination_name_2' => $customerCode2,
                                    'store_destination_id' => $storeDestination->store_id,
                                    'destination_dedicate' => $storeDestination->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => 0,
                                    'value_pf_tr' => 0,
                                    'value_pf_ppe' => 0,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'spv_name2' => $spvName2,
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
                $tbatHeaderAfter = Tbat::where('user_id', $user->id)->where('store_id', $content['id'])->where('store_destination_id', $content['destination_id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $tbatHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        }

    }

    public function sellInWithDate(Request $request){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        if($this->getReject($user->id)){
            return response()->json(['status' => false, 'message' => 'Tidak bisa melakukan transaksi karena absen anda di reject oleh supervisor. '], 200);
        }

        if(!isset($content['irisan'])) { // Set Default Irisan if doesn't exist
            $content['irisan'] = 0;
        }else{
            if($content['irisan'] == null){
                $content['irisan'] = 0;
            }
        }

        $date = Carbon::parse($content['date']);

        // MAIN METHOD

        // Check sell in(Sell Thru) header
        $sellInHeader = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', $date)->first();

        if ($sellInHeader) { // If header exist (update and/or create detail)

            try {
                DB::transaction(function () use ($content, $sellInHeader, $user, $date) {

                    $arInsert = [];

                    foreach ($content['data'] as $data) {

                        if(in_array($data['product_id'], $arInsert)){
                            continue;
                        }else{
                            array_push($arInsert, $data['product_id']);
                        }

                        $sellInDetail = SellInDetail::where('sellin_id', $sellInHeader->id)
                                        ->where('product_id', $data['product_id'])
                                        ->where('irisan', $content['irisan'])
                                        ->first();

                        if ($sellInDetail) { // If data exist -> update

                            $sellInDetail->update([
                                'quantity' => $sellInDetail->quantity + $data['quantity']
                            ]);

                            /** Update Summary **/

                            if($user->role->role_group != 'Salesman Explorer') {

                                $summary = SummarySellIn::where('sellin_detail_id', $sellInDetail->id)->first();

                                $value_old = $summary->value;

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellInHeader->user_id;
                                $summary_ta['store_id'] = $sellInHeader->store_id;
                                $summary_ta['week'] = $sellInHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell In';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            }else{ // SEE (Salesman Explorer)

                                $summary = SalesmanSummarySales::where('sellin_detail_id', $sellInDetail->id)->first();

                                $value_old = $summary->value; // Buat reset actual salesman

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf' => $value_pf
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellInHeader->user_id;
                                $summary_ta['store_id'] = $sellInHeader->store_id;
                                $summary_ta['pf'] = $summary->value_pf;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;

                                $this->changeActualSalesman($summary_ta, 'change');

                            }

                        } else { // If data didn't exist -> create

                            $detail = SellInDetail::create([
                                'sellin_id' => $sellInHeader->id,
                                'product_id' => $data['product_id'],
                                'quantity' => $data['quantity'],
                                'irisan' => $content['irisan']
                            ]);

                            // UPDATE PRICE NEW METHOD

                            $priceForDetail = 0;                                

                            // BY DEDICATE - GLOBAL CHANNEL
                            if($sellInHeader->user->role->role_group == 'Salesman Explorer' || $sellInHeader->user->role->role_group == 'SMD'){

                                if($sellInHeader->store->globalChannelId == ''){

                                    if($sellInHeader->user->dedicate != ''){

                                        $cekPrice = $detail->product->getPriceAttribute($sellInHeader->user->dedicate, 'Sell In', $sellInHeader->date);

                                        if($cekPrice){
                                            $priceForDetail = $cekPrice->price;
                                        }    

                                    }

                                }

                            }

                            // BY STORE - GLOBAL CHANNEL

                            if($sellInHeader->store->globalChannelId != ''){

                                $cekPrice = $detail->product->getPriceAttribute($sellInHeader->store->globalChannelId, 'Sell In', $sellInHeader->date);

                                if($cekPrice){
                                    $priceForDetail = $cekPrice->price;
                                }

                            }

                            // UPDATE SALES PRICE IN DETAIL

                            // SellInDetail::where('id', $detail->id)->first()->update(['price' => $priceForDetail]);
                            DB::statement('UPDATE sell_in_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                            // --------------------------------------------

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $sellInHeader->store_id)->first();
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
                                    if($dedicate->dedicate == 'Modern Retail') $newDedicate = 'MR';

                                    $price = Price::where('product_id', $product->id)
                                        ->join('global_channels','global_channels.id','prices.globalchannel_id')
                                        ->where('global_channels.name',$newDedicate)
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }

                            }else{
                                if($store->subchannel_id != null || $store->subchannel_id != ''){
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }else{
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', '')
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }
                            }

                            if($price){
                                $realPrice = $price->price;
                            }

                            /* Distributor */
                            $distIds = StoreDistributor::where('store_id', $sellInHeader->store_id)->pluck('distributor_id');
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

                            /* Value - Product Focus */
                            $value_pf_mr = 0;
                            $value_pf_tr = 0;
                            $value_pf_ppe = 0;

                            $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                            foreach ($productFocus as $productFocusDetail) {
                                if ($productFocusDetail->type == 'Modern Retail') {
                                    $value_pf_mr = $realPrice * $data['quantity'];
                                } else if ($productFocusDetail->type == 'Traditional Retail') {
                                    $value_pf_tr = $realPrice * $data['quantity'];
                                } else if ($productFocusDetail->type == 'PPE') {
                                    $value_pf_ppe = $realPrice * $data['quantity'];
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

                            if($user->role->role_group != 'Salesman Explorer') {
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

                                $summary = SummarySellIn::create([
                                    'sellin_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $sellInHeader->store_id,
                                    'user_id' => $sellInHeader->user_id,
                                    'week' => $sellInHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sellInHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellInHeader->user_id;
                                $summary_ta['store_id'] = $sellInHeader->store_id;
                                $summary_ta['week'] = $sellInHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell In';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            }else{ // Buat SEE (Salesman Explorer)

                                $value_pf = 0;

                                $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                if($productFocus){
                                    $value_pf = $realPrice * $detail->quantity;
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


                                $summary = SalesmanSummarySales::create([
                                    'sellin_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $sellInHeader->store_id,
                                    'user_id' => $sellInHeader->user_id,
                                    'week' => $sellInHeader->week,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sellInHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf' => $value_pf,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellInHeader->user_id;
                                $summary_ta['store_id'] = $sellInHeader->store_id;
                                $summary_ta['pf'] = $summary->value_pf;
                                $summary_ta['value'] = $summary->value;

                                $this->changeActualSalesman($summary_ta, 'change');

                            }

                        }

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            return response()->json(['status' => true, 'id_transaksi' => $sellInHeader->id, 'message' => 'Data berhasil di input']);

        } else { // If header didn't exist (create header & detail)

            try {
                DB::transaction(function () use ($content, $user, $date) {

                    // HEADER
                    $transaction = SellIn::create([
                                        'user_id' => $user->id,
                                        'store_id' => $content['id'],
                                        'week' => $date->weekOfMonth,
                                        'date' => $date
                                    ]);

                    $arInsert = [];

                    foreach ($content['data'] as $data) {

                        if(in_array($data['product_id'], $arInsert)){
                            continue;
                        }else{
                            array_push($arInsert, $data['product_id']);
                        }

                        $sellInDetail = SellInDetail::where('sellin_id', $transaction->id)
                                        ->where('product_id', $data['product_id'])
                                        ->where('irisan', $content['irisan'])
                                        ->first();

                        if ($sellInDetail) { // If data exist -> update

                            $sellInDetail->update([
                                'quantity' => $sellInDetail->quantity + $data['quantity']
                            ]);

                            /** Update Summary **/

                            if($user->role->role_group != 'Salesman Explorer') {

                                $summary = SummarySellIn::where('sellin_detail_id', $sellInDetail->id)->first();

                                $value_old = $summary->value;

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['week'] = $transaction->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell In';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            }else{ // SEE (Salesman Explorer)

                                $summary = SalesmanSummarySales::where('sellin_detail_id', $sellInDetail->id)->first();

                                $value_old = $summary->value; // Buat reset actual salesman

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf' => $value_pf
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['pf'] = $summary->value_pf;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;

                                $this->changeActualSalesman($summary_ta, 'change');

                            }

                        } else { // If data didn't exist -> create                                

                            // DETAILS
                            $detail = SellInDetail::create([
                                    'sellin_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan']
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

                            // SellInDetail::where('id', $detail->id)->first()->update(['price' => $priceForDetail]);
                            DB::statement('UPDATE sell_in_details SET price = ? WHERE id = ?', [$priceForDetail, $detail->id]);

                            // --------------------------------------------

                            // $product = $detail->product->name;
                            // $g_channel_store = $transaction->store->globalChannelId;
                            // $g_channel_dedicate = $transaction->user->dedicate;

                            // $detail->forceDelete();
                            // $transaction->forceDelete();

                            // return response()->json($transaction->store->globalChannelId);

                            // return response()->json($detail->product->getPriceAttribute($transaction->store->globalChannelId, 'Sell In', $transaction->date));

                            // return response()->json($detail->product->getPriceAttribute($transaction->date)->where('globalchannel_id', $transaction->store->globalChannelId)->where('sell_type', 'Sell In'));

                            // return response()->json(['type' => 'SELL IN', 'product' => $product, 'g_channel_store' => $g_channel_store, 'g_channel_dedicate' => $g_channel_dedicate,'price' => $priceForDetail]);

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

                            // $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                            //     if(count($spvDemoName) > 0){
                            //         $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                            //     }

                            // $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

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
                                if($store->subchannel_id != null || $store->subchannel_id != ''){
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }else{
                                    $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', '')
                                        ->where('sell_type', 'Sell In')
                                        ->first();
                                }
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

                            /* Value - Product Focus */
                            $value_pf_mr = 0;
                            $value_pf_tr = 0;
                            $value_pf_ppe = 0;

                            $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                            foreach ($productFocus as $productFocusDetail) {
                                if ($productFocusDetail->type == 'Modern Retail') {
                                    $value_pf_mr = $realPrice * $detail->quantity;
                                } else if ($productFocusDetail->type == 'Traditional Retail') {
                                    $value_pf_tr = $realPrice * $detail->quantity;
                                } else if ($productFocusDetail->type == 'PPE') {
                                    $value_pf_ppe = $realPrice * $detail->quantity;
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

                            if($user->role->role_group != 'Salesman Explorer') {
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

                                $summary = SummarySellIn::create([
                                    'sellin_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'irisan' => $content['irisan'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['week'] = $transaction->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell In';
                                $summary_ta['irisan'] = $summary->irisan;

                                // return $summary_ta;

                                $this->changeActual($summary_ta, 'change');

                            }else{ // Buat SEE (Salesman Explorer)

                                $value_pf = 0;

                                $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                if($productFocus){
                                    $value_pf = $realPrice * $detail->quantity;
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

                                $summary = SalesmanSummarySales::create([
                                    'sellin_detail_id' => $detail->id,
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
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $transaction->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $detail->quantity,
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $detail->quantity,
                                    'value_pf' => $value_pf,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $transaction->user_id;
                                $summary_ta['store_id'] = $transaction->store_id;
                                $summary_ta['pf'] = $summary->value_pf;
                                $summary_ta['value'] = $summary->value;

                                $this->changeActualSalesman($summary_ta, 'change');

                            }

                        }

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check sell in(Sell Thru) header after insert
            $sellInHeaderAfter = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', $date)->first();

            return response()->json(['status' => true, 'id_transaksi' => $sellInHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

}
