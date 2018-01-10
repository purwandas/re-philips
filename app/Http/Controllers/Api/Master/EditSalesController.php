<?php

namespace App\Http\Controllers\Api\Master;

use App\DisplayShare;
use App\DisplayShareDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\PosmActivity;
use App\PosmActivityDetail;
use App\Reports\SummaryDisplayShare;
use App\Reports\SummaryFreeProduct;
use App\Reports\SummaryRetConsument;
use App\Reports\SummaryRetDistributor;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummarySoh;
use App\Reports\SummaryTbat;
use App\RetConsument;
use App\RetConsumentDetail;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\Soh;
use App\SohDetail;
use App\Tbat;
use App\TbatDetail;
use App\Traits\ActualTrait;
use App\Traits\EditDeleteSalesTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class EditSalesController extends Controller
{
    use ActualTrait;

    public function edit(Request $request, $param){

        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

//        return $content;

        if($param == 1) { // SELL IN

            $dataHeader = SellIn::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = SellInDetail::where('sellin_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummarySellIn::where('sellin_detail_id', $dataDetail->id)->first();

                            $value_old = $summary->value;

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                            // Actual Summary
                            $summary_ta['user_id'] = $dataHeader->user_id;
                            $summary_ta['store_id'] = $dataHeader->store_id;
                            $summary_ta['week'] = $dataHeader->week;
                            $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                            $summary_ta['value_old'] = $value_old;
                            $summary_ta['value'] = $summary->value;
                            $summary_ta['group'] = $summary->group;
                            $summary_ta['sell_type'] = 'Sell In';

                            $this->changeActual($summary_ta, 'change');

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 2) { // SELL OUT

            $dataHeader = Sellout::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = SellOutDetail::where('sellout_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummarySellOut::where('sellout_detail_id', $dataDetail->id)->first();

                            $value_old = $summary->value;

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                            // Actual Summary
                            $summary_ta['user_id'] = $dataHeader->user_id;
                            $summary_ta['store_id'] = $dataHeader->store_id;
                            $summary_ta['week'] = $dataHeader->week;
                            $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                            $summary_ta['value_old'] = $value_old;
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

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 3) { // RETURN DISTRIBUTOR

            $dataHeader = RetDistributor::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = RetDistributorDetail::where('retdistributor_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummaryRetDistributor::where('retdistributor_detail_id', $dataDetail->id)->first();

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 4) { // RETURN CONSUMENT

            $dataHeader = RetConsument::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = RetConsumentDetail::where('retconsument_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummaryRetConsument::where('retconsument_detail_id', $dataDetail->id)->first();

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 5) { // FREE PRODUCT

            $dataHeader = FreeProduct::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = FreeProductDetail::where('freeproduct_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummaryFreeProduct::where('freeproduct_detail_id', $dataDetail->id)->first();

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 6) { // TBAT

            $dataHeader = Tbat::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = TbatDetail::where('tbat_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummaryTbat::where('tbat_detail_id', $dataDetail->id)->first();

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 7) { // SOH

            $dataHeader = Soh::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = SohDetail::where('soh_id', $dataHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                            // Update Summary

                            $summary = SummarySoh::where('soh_detail_id', $dataDetail->id)->first();

                            $value = ($data['quantity']) * $summary->unit_price;

                            ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                            ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                            ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                            $summary->update([
                                'quantity' => $data['quantity'],
                                'value' => $value,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 8) { // Display Share

            $dataHeader = DisplayShare::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = DisplayShareDetail::where('display_share_id', $dataHeader->id)->where('category_id', $data['category_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'philips' => $data['philips'],
                                'all' => $data['all'],
                            ]);

                            // Update Summary

                            $summary = SummaryDisplayShare::where('displayshare_detail_id', $dataDetail->id)->first();

                            $summary->update([
                                'philips' => $data['philips'],
                                'all' => $data['all'],
                            ]);

                            // Update percentage
                            $summary->update([
                                'percentage' => ($summary->philips / $summary->all ) * 100,
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }else if($param == 9) { // POSM

            $dataHeader = PosmActivity::where('id', $content['id'])->first();

            try {

                DB::transaction(function () use ($dataHeader, $content) {

                    foreach ($content['data'] as $data) {

                        $dataDetail = PosmActivityDetail::where('posmactivity_id', $dataHeader->id)->where('posm_id', $data['posm_id'])->first();

                        if ($dataDetail) { // Kalo ada data, edit

                            $dataDetail->update([
                                'quantity' => $data['quantity']
                            ]);

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

            return response()->json(['status' => true, 'id_transaksi' => $dataHeader->id, 'message' => 'Data berhasil di update']);

        }



    }
}
