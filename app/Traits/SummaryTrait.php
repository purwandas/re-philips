<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Price;
use App\SellIn;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use App\ProductFocuses;

trait SummaryTrait {

    public function changeSummary($data, $change){

        $this->changeSummarySellIn($data, $change);

    }

    public function changeSummarySellIn($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $sellInIds = SellIn::whereMonth('sell_ins.date', '=', Carbon::now()->format('m'))
                         ->whereYear('sell_ins.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sellInDetail = SellInDetail::whereIn('sellin_id', $sellInIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sellInDetail as $detail){

                $summary = SummarySellIn::where('sellin_detail_id', $detail->id)->first();

                if($data['type'] == 'Modern Retail'){

                    if($change == 'change'){
                        $summary->update([
                            'value_pf_mr' => $summary->value
                        ]);
                    }else if($change == 'delete'){
                        $summary->update([
                            'value_pf_mr' => 0
                        ]);
                    }

                }else if($data['type'] == 'Traditional Retail'){

                    if($change == 'change'){
                        $summary->update([
                            'value_pf_tr' => $summary->value
                        ]);
                    }else if($change == 'delete'){
                        $summary->update([
                            'value_pf_tr' => 0
                        ]);
                    }

                }else if($data['type'] == 'PPE'){

                    if($change == 'change'){
                        $summary->update([
                            'value_pf_ppe' => $summary->value
                        ]);
                    }else if($change == 'delete'){
                        $summary->update([
                            'value_pf_ppe' => 0
                        ]);
                    }

                }

            }

        }else if(isset($data['product_id']) && isset($data['globalchannel_id']) && isset($data['price'])) { /* Price Change */

            $sellInIds = SellIn::join('stores', 'stores.id', '=', 'sell_ins.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('sell_ins.date', '=', Carbon::now()->format('m'))
                        ->whereYear('sell_ins.date', '=', Carbon::now()->format('Y'))->pluck('sell_ins.id');

            $sellInDetail = SellInDetail::whereIn('sellin_id', $sellInIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sellInDetail as $detail){

                $summary = SummarySellIn::where('sellin_detail_id', $detail->id)->first();

                if($change == 'change'){

                    $summary->update([
                        'unit_price' => $data['price'],
                        'value' => $summary->quantity * $data['price']
                    ]);

                    /* Product Focus */
                    $productFocus = ProductFocuses::where('product_id', $data['product_id'])->get();

                    $summary->update([
                        'value_pf_mr' => 0,
                        'value_pf_tr' => 0,
                        'value_pf_ppe' => 0,
                    ]);

                    foreach ($productFocus as $focus){

                        if($focus->type == 'Modern Retail'){
                            $summary->update([
                                'value_pf_mr' => $summary->quantity * $data['price']
                            ]);
                        }else if($focus->type == 'Traditional Retail'){
                            $summary->update([
                                'value_pf_tr' => $summary->quantity * $data['price']
                            ]);
                        }else if($focus->type == 'PPE'){
                            $summary->update([
                                'value_pf_ppe' => $summary->quantity * $data['price']
                            ]);
                        }

                    }

                }else if($change == 'delete'){
                    $summary->update([
                        'unit_price' => 0,
                        'value' => 0,
                        'value_pf_mr' => 0,
                        'value_pf_tr' => 0,
                        'value_pf_ppe' => 0,
                    ]);
                }

            }

        }

    }

}