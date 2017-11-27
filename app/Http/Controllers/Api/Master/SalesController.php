<?php

namespace App\Http\Controllers\Api\Master;

use App\Price;
use App\Product;
use App\Store;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\DmArea;
use App\Reports\SummarySellIn;
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
    public function store(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        if($param == 1) { /* SELL IN */

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

                                $summary = SummarySellIn::where('sellin_detail_id', $sellInDetail->id)->first();

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

                                SummarySellIn::create([
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
                                    'spv_name' => $store->user->name,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

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

                            SummarySellIn::create([
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
                                'spv_name' => $store->user->name,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

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

                            } else { // If data didn't exist -> create

                                SellOutDetail::create([
                                    'sellout_id' => $sellOutHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

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
                            SellOutDetail::create([
                                    'sellout_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

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

                            } else { // If data didn't exist -> create

                                RetDistributorDetail::create([
                                    'retdistributor_id' => $retDistributorHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
                            RetDistributorDetail::create([
                                    'retdistributor_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check ret distributor header after insert
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

                            } else { // If data didn't exist -> create

                                RetConsumentDetail::create([
                                    'retconsument_id' => $retConsumentHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
                            RetConsumentDetail::create([
                                    'retconsument_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check ret consument header after insert
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

                            } else { // If data didn't exist -> create

                                FreeProductDetail::create([
                                    'freeproduct_id' => $freeProductHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
                            FreeProductDetail::create([
                                    'freeproduct_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check free product header after insert
                $freeProductHeaderAfter = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $freeProductHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        } else if($param == 6) { /* TBAT */

            // Check tbat header
            $tbatHeader = Tbat::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($tbatHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $tbatHeader, $user) {

                        foreach ($content['data'] as $data) {

                            $tbatDetail = TbatDetail::where('tbat_id', $tbatHeader->id)->where('product_id', $data['product_id'])->first();

                            if ($tbatDetail) { // If data exist -> update

                                $tbatDetail->update([
                                    'quantity' => $tbatDetail->quantity + $data['quantity']
                                ]);

                            } else { // If data didn't exist -> create

                                TbatDetail::create([
                                    'tbat_id' => $tbatHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
                                            'week' => Carbon::now()->weekOfMonth,
                                            'date' => Carbon::now()
                                        ]);

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            TbatDetail::create([
                                    'tbat_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
                                ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check tbat header after insert
                $tbatHeaderAfter = FreeProduct::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $tbatHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

        }

    }

}
