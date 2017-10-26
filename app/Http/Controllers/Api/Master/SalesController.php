<?php

namespace App\Http\Controllers\Api\Master;

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

                            } else { // If data didn't exist -> create

                                SellInDetail::create([
                                    'sellin_id' => $sellInHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
                            SellInDetail::create([
                                    'sellin_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity']
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
