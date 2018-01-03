<?php

namespace App\Http\Controllers\Api\Master;

use App\Price;
use App\Product;
use App\Reports\SalesmanSummarySales;
use App\SalesmanProductFocuses;
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
use App\TrainerArea;

class SalesController extends Controller
{
    use ActualTrait;

    public function store(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        if($param == 1) { /* SELL IN */

//            return response()->json($this->getPromoterTitle($user->id, $content['id']));

            // Check sell in header
            $sellInHeader = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($sellInHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $sellInHeader, $user) {

                        foreach ($content['data'] as $data) {

                            $sellInDetail = SellInDetail::where('sellin_id', $sellInHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($sellInDetail) { // If data exist -> update

                                $sellInDetail->update([
                                    'quantity' => $sellInDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                if($user->role != 'Salesman Explorer') {

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

                                }

                            } else { // If data didn't exist -> create

                                $detail = SellInDetail::create([
                                    'sellin_id' => $sellInHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sellInHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';
                                

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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

                                if($user->role != 'Salesman Explorer') {

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
                                        'channel' => $store->subChannel->channel->name,
                                        'sub_channel' => $store->subChannel->name,
                                        'area' => $store->district->area->name,
                                        'district' => $store->district->name,
                                        'store_name_1' => $store->store_name_1,
                                        'store_name_2' => $store->store_name_2,
                                        'store_id' => $store->store_id,
                                        'dedicate' => $store->dedicate,
                                        'nik' => $user->nik,
                                        'promoter_name' => $user->name,
                                        'date' => $sellInHeader->date,
                                        'model' => $product->model . '/' . $product->variants,
                                        'group' => $product->category->group->groupProduct->name,
                                        'category' => $product->category->name,
                                        'product_name' => $product->name,
                                        'quantity' => $data['quantity'],
                                        'unit_price' => $realPrice,
                                        'value' => $realPrice * $data['quantity'],
                                        'value_pf_mr' => $value_pf_mr,
                                        'value_pf_tr' => $value_pf_tr,
                                        'value_pf_ppe' => $value_pf_ppe,
                                        'role' => $user->role,
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

                                    $this->changeActual($summary_ta, 'change');

                                }else{ // Buat SEE (Salesman Explorer)

                                    $value_pf = 0;

                                    $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                    if($productFocus){
                                        $value_pf = $realPrice * $detail->quantity;
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
                                        'channel' => $store->subChannel->channel->name,
                                        'sub_channel' => $store->subChannel->name,
                                        'area' => $store->district->area->name,
                                        'district' => $store->district->name,
                                        'store_name_1' => $store->store_name_1,
                                        'store_name_2' => $store->store_name_2,
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
                                        'role' => $user->role,
                                    ]);

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

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = SellInDetail::create([
                                    'sellin_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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

                            if($user->role != 'Salesman Explorer') {

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
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
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role,
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

                                $this->changeActual($summary_ta, 'change');

                            }else{ // Buat SEE (Salesman Explorer)

                                $value_pf = 0;

                                $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                                if($productFocus){
                                    $value_pf = $realPrice * $detail->quantity;
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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
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
                                    'role' => $user->role,
                                ]);

                            }

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $sellInHeaderAfter = SellIn::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sellInHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 2) { /* SELL OUT */

            // return response()->json($this->getPromoterTitle($user->id, $content['id']));

            // Check sell out header
            $sellOutHeader = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($sellOutHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $sellOutHeader, $user) {

                        foreach ($content['data'] as $data) {

                            $sellOutDetail = SellOutDetail::where('sellout_id', $sellOutHeader->id)->where('product_id', $data['product_id'])->first();

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

                                $this->changeActual($summary_ta, 'change');

                            } else { // If data didn't exist -> create

                                $detail = SellOutDetail::create([
                                    'sellout_id' => $sellOutHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sellOutHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';


                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
                                    'store_id' => $store->store_id,
                                    'dedicate' => $store->dedicate,
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sellOutHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role,
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

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = SellOutDetail::create([
                                    'sellout_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $store->store_name_2,
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
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                                'role' => $user->role,
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

                            $this->changeActual($summary_ta, 'change');

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $sellOutHeaderAfter = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sellOutHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 3) { /* RETURN DISTRIBUTOR */

            // Check ret distributor header
            $retDistributorHeader = RetDistributor::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($retDistributorHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $retDistributorHeader, $user) {

                        foreach ($content['data'] as $data) {

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

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $retDistributorHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
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
                                    'role' => $user->role,
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

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = RetDistributorDetail::create([
                                    'retdistributor_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $store->store_name_2,
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
                                'role' => $user->role,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $retDistributorHeaderAfter = RetDistributor::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $retDistributorHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 4) { /* RETURN CONSUMENT */

            // Check ret consument header
            $retConsumentHeader = RetConsument::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($retConsumentHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $retConsumentHeader, $user) {

                        foreach ($content['data'] as $data) {

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

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $retConsumentHeader->store_id)->first();

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
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
                                    'role' => $user->role,
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

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = RetConsumentDetail::create([
                                    'retconsument_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $store->store_name_2,
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
                                'role' => $user->role,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $retConsumentHeaderAfter = RetConsument::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $retConsumentHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 5) { /* FREE PRODUCT */

            // Check free product header
            $freeProductHeader = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($freeProductHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $freeProductHeader, $user) {

                        foreach ($content['data'] as $data) {

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

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $freeProductHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
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
                                    'role' => $user->role,
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

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = FreeProductDetail::create([
                                    'freeproduct_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $store->store_name_2,
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
                                'role' => $user->role,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $freeProductHeaderAfter = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $freeProductHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 6) { /* TBAT */

            // Check tbat header
            $tbatHeader = Tbat::where('user_id', $user->id)->where('store_id', $content['id'])->where('store_destination_id', $content['destination_id'])->where('date', date('Y-m-d'))->first();

            if ($tbatHeader) { // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $tbatHeader, $user) {

                        foreach ($content['data'] as $data) {

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

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $tbatHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                /* Store Destination */
                                $storeDestination = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $tbatHeader->store_destination_id)->first();

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                    'channel' => $store->subChannel->channel->name,
                                    'sub_channel' => $store->subChannel->name,
                                    'area' => $store->district->area->name,
                                    'district' => $store->district->name,
                                    'store_name_1' => $store->store_name_1,
                                    'store_name_2' => $store->store_name_2,
                                    'store_id' => $store->store_id,
                                    'dedicate' => $store->dedicate,
                                    'store_destination_name_1' => $storeDestination->store_name_1,
                                    'store_destination_name_2' => $storeDestination->store_name_2,
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
                                    'role' => $user->role,
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
                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = TbatDetail::create([
                                    'tbat_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                                $spvName='';
                                if (isset($store->user->name)) {
                                    $spvName=$store->user->name;
                                }

                            /* Store Destination */
                            $storeDestination = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_destination_id)->first();

                            // /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)->first();

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
                                'channel' => $store->subChannel->channel->name,
                                'sub_channel' => $store->subChannel->name,
                                'area' => $store->district->area->name,
                                'district' => $store->district->name,
                                'store_name_1' => $store->store_name_1,
                                'store_name_2' => $store->store_name_2,
                                'store_id' => $store->store_id,
                                'dedicate' => $store->dedicate,
                                'store_destination_name_1' => $storeDestination->store_name_1,
                                'store_destination_name_2' => $storeDestination->store_name_2,
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
                                'role' => $user->role,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in header after insert
                $tbatHeaderAfter = Tbat::where('user_id', $user->id)->where('store_id', $content['id'])->where('store_destination_id', $content['destination_id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $tbatHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        }

    }

}
