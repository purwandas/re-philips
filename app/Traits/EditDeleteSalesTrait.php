<?php

namespace App\Traits;

use App\EmployeeStore;
use App\SellIn;
use App\Store;
use App\Target;

trait EditDeleteSalesTrait {

    public function editData($data, $param){

        return $data;

        if($param == 1) {

            $sellInHeader = SellIn::where('id', $data['id'])->first();

            try {

                DB::transaction(function () use ($sellInHeader, $data) {

                    foreach ($data['data'] as $detail) {

                        $sellInDetail = SellInDetail::where('sellin_id', $sellInHeader->id)->where('product_id', $detail['product_id'])->first();

                        if ($sellInDetail) { // Kalo ada data, edit

                            $sellInDetail->update([
                                'quantity' => $sellInDetail->quantity + $data['quantity']
                            ]);

                            // Update Summary

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

                        }

                    }

                });

            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);

            }

        }

    }

}