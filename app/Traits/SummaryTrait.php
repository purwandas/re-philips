<?php

namespace App\Traits;

use App\Filters\ProductFocusSalesmanFilters;
use App\Reports\SalesmanSummarySales;
use App\SalesmanProductFocuses;
use Carbon\Carbon;
use App\Price;
use App\SellIn;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use App\SellOut;
use App\SellOutDetail;
use App\Reports\SummarySellOut;
use App\RetConsument;
use App\RetConsumentDetail;
use App\Reports\SummaryRetConsument;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\Reports\SummaryRetDistributor;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Reports\SummaryFreeProduct;
use App\Tbat;
use App\TbatDetail;
use App\Reports\SummaryTbat;
use App\SOH;
use App\SOHDetail;
use App\Reports\SummarySOH;
use App\SOS;
use App\SOSDetail;
use App\Reports\SummarySOS;
use App\ProductFocuses;

trait SummaryTrait {

    use ActualTrait;

    public function changeSummary($data, $change){

        if($data['sell_type'] == 'Sell In'){
            $this->changeSummarySellIn($data, $change);
            $this->changeSummaryRetConsument($data, $change);
            $this->changeSummaryRetDistributor($data, $change);
            $this->changeSummaryFreeProduct($data, $change);
            $this->changeSummaryTbat($data, $change);
            $this->changeSummarySoh($data, $change);
            $this->changeSummarySos($data, $change);
        }elseif ($data['sell_type'] == 'Sell Out'){
            $this->changeSummarySellOut($data, $change);
        }

    }

    public function changeSummarySellIn($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $sellInIds = SellIn::whereMonth('sell_ins.date', '=', Carbon::now()->format('m'))
                         ->whereYear('sell_ins.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sellInDetail = SellInDetail::whereIn('sellin_id', $sellInIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sellInDetail as $detail){

                $summary = SummarySellIn::where('sellin_detail_id', $detail->id)->first();

                if($summary) {

                    if ($data['type'] == 'Modern Retail') {

                        if ($change == 'change') {
                            $summary->update([
                                'value_pf_mr' => $summary->value
                            ]);
                        } else if ($change == 'delete') {
                            $summary->update([
                                'value_pf_mr' => 0
                            ]);
                        }

                    } else if ($data['type'] == 'Traditional Retail') {

                        if ($change == 'change') {
                            $summary->update([
                                'value_pf_tr' => $summary->value
                            ]);
                        } else if ($change == 'delete') {
                            $summary->update([
                                'value_pf_tr' => 0
                            ]);
                        }

                    } else if ($data['type'] == 'PPE') {

                        if ($change == 'change') {
                            $summary->update([
                                'value_pf_ppe' => $summary->value
                            ]);
                        } else if ($change == 'delete') {
                            $summary->update([
                                'value_pf_ppe' => 0
                            ]);
                        }

                    }

                    /* Reset Actual */
                    $this->resetActual($summary->user_id, $summary->storeId, 'Sell In');

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

                if($detail->sellIn->user->role->role_group != 'Salesman Explorer') {

                    $summary = SummarySellIn::where('sellin_detail_id', $detail->id)->first();

                    if($summary) {

                        if ($change == 'change') {

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

                            foreach ($productFocus as $focus) {

                                if ($focus->type == 'Modern Retail') {
                                    $summary->update([
                                        'value_pf_mr' => $summary->quantity * $data['price']
                                    ]);
                                } else if ($focus->type == 'Traditional Retail') {
                                    $summary->update([
                                        'value_pf_tr' => $summary->quantity * $data['price']
                                    ]);
                                } else if ($focus->type == 'PPE') {
                                    $summary->update([
                                        'value_pf_ppe' => $summary->quantity * $data['price']
                                    ]);
                                }

                            }

                        } else if ($change == 'delete') {
                            $summary->update([
                                'unit_price' => 0,
                                'value' => 0,
                                'value_pf_mr' => 0,
                                'value_pf_tr' => 0,
                                'value_pf_ppe' => 0,
                            ]);
                        }

                        /* Reset Actual */
                        $this->resetActual($summary->user_id, $summary->storeId, 'Sell In');

                    }

                }else { // SEE (Salesman Explorer)

                    $summary = SalesmanSummarySales::where('sellin_detail_id', $detail->id)->first();

                    if ($summary) {

                        if ($change == 'change') {

                            $summary->update([
                                'unit_price' => $data['price'],
                                'value' => $summary->quantity * $data['price']
                            ]);

                            /* Product Focus */
                            $productFocus = SalesmanProductFocuses::where('product_id', $data['product_id'])->first();

                            $summary->update([
                                'value_pf' => 0,
                            ]);

                            if ($productFocus) { // Jika ada product focus
                                $summary->update([
                                    'value_pf' => $summary->quantity * $data['price']
                                ]);
                            }


                        } else if ($change == 'delete') {
                            $summary->update([
                                'unit_price' => 0,
                                'value' => 0,
                                'value_pf' => 0,
                            ]);
                        }

                        /* Reset Actual */
                        $this->resetActualSalesman($summary->user_id);

                    }

                }

            }

        }

    }

    public function changeSummarySellOut($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $sellOutIds = SellOut::whereMonth('sell_outs.date', '=', Carbon::now()->format('m'))
                         ->whereYear('sell_outs.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sellOutDetail = SellOutDetail::whereIn('sellout_id', $sellOutIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sellOutDetail as $detail){

                $summary = SummarySellOut::where('sellout_detail_id', $detail->id)->first();

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

                /* Reset Actual */
                $this->resetActual($summary->user_id, $summary->storeId, 'Sell Out');

            }

        }else if(isset($data['product_id']) && isset($data['globalchannel_id']) && isset($data['price'])) { /* Price Change */

            $sellOutIds = SellOut::join('stores', 'stores.id', '=', 'sell_outs.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('sell_outs.date', '=', Carbon::now()->format('m'))
                        ->whereYear('sell_outs.date', '=', Carbon::now()->format('Y'))->pluck('sell_outs.id');

            $sellOutDetail = SellOutDetail::whereIn('sellout_id', $sellOutIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sellOutDetail as $detail){

                $summary = SummarySellOut::where('sellout_detail_id', $detail->id)->first();

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

                /* Reset Actual */
                $this->resetActual($summary->user_id, $summary->storeId, 'Sell Out');

            }

        }

    }

    public function changeSummaryRetConsument($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $retConsumentIds = RetConsument::whereMonth('ret_consuments.date', '=', Carbon::now()->format('m'))
                         ->whereYear('ret_consuments.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $retConsumentDetail = RetConsumentDetail::whereIn('retconsument_id', $retConsumentIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($retConsumentDetail as $detail){

                $summary = SummaryRetConsument::where('retconsument_detail_id', $detail->id)->first();

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

            $retConsumentIds = RetConsument::join('stores', 'stores.id', '=', 'ret_consuments.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('ret_consuments.date', '=', Carbon::now()->format('m'))
                        ->whereYear('ret_consuments.date', '=', Carbon::now()->format('Y'))->pluck('ret_consuments.id');

            $retConsumentDetail = RetConsumentDetail::whereIn('retconsument_id', $retConsumentIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($retConsumentDetail as $detail){

                $summary = SummaryRetConsument::where('retconsument_detail_id', $detail->id)->first();

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

    public function changeSummaryRetDistributor($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $retDistributorIds = RetDistributor::whereMonth('ret_distributors.date', '=', Carbon::now()->format('m'))
                         ->whereYear('ret_distributors.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $retDistributorDetail = RetDistributorDetail::whereIn('retdistributor_id', $retDistributorIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($retDistributorDetail as $detail){

                $summary = SummaryRetDistributor::where('retdistributor_detail_id', $detail->id)->first();

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

            $retDistributorIds = RetDistributor::join('stores', 'stores.id', '=', 'ret_distributors.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('ret_distributors.date', '=', Carbon::now()->format('m'))
                        ->whereYear('ret_distributors.date', '=', Carbon::now()->format('Y'))->pluck('ret_distributors.id');

            $retDistributorDetail = RetDistributorDetail::whereIn('retdistributor_id', $retDistributorIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($retDistributorDetail as $detail){

                $summary = SummaryRetDistributor::where('retdistributor_detail_id', $detail->id)->first();

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

    public function changeSummaryFreeProduct($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $freeProductIds = FreeProduct::whereMonth('free_products.date', '=', Carbon::now()->format('m'))
                         ->whereYear('free_products.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $freeProductDetail = FreeProductDetail::whereIn('freeproduct_id', $freeProductIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($freeProductDetail as $detail){

                $summary = SummaryFreeProduct::where('freeproduct_detail_id', $detail->id)->first();

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

            $freeProductIds = FreeProduct::join('stores', 'stores.id', '=', 'free_products.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('free_products.date', '=', Carbon::now()->format('m'))
                        ->whereYear('free_products.date', '=', Carbon::now()->format('Y'))->pluck('free_products.id');

            $freeProductDetail = FreeProductDetail::whereIn('freeproduct_id', $freeProductIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($freeProductDetail as $detail){

                $summary = SummaryFreeProduct::where('freeproduct_detail_id', $detail->id)->first();

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

    public function changeSummaryTbat($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $tbatIds = Tbat::whereMonth('tbats.date', '=', Carbon::now()->format('m'))
                         ->whereYear('tbats.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $tbatDetail = TbatDetail::whereIn('tbat_id', $tbatIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($tbatDetail as $detail){

                $summary = SummaryTbat::where('tbat_detail_id', $detail->id)->first();

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

            $tbatIds = Tbat::join('stores', 'stores.id', '=', 'tbats.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('tbats.date', '=', Carbon::now()->format('m'))
                        ->whereYear('tbats.date', '=', Carbon::now()->format('Y'))->pluck('tbats.id');

            $tbatDetail = TbatDetail::whereIn('tbat_id', $tbatIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($tbatDetail as $detail){

                $summary = SummaryTbat::where('tbat_detail_id', $detail->id)->first();

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

    public function changeSummarySoh($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $sohIds = SOH::whereMonth('sohs.date', '=', Carbon::now()->format('m'))
                         ->whereYear('sohs.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sohDetail = SOHDetail::whereIn('soh_id', $sohIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sohDetail as $detail){

                $summary = SummarySOH::where('soh_detail_id', $detail->id)->first();

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

            $sohIds = SOH::join('stores', 'stores.id', '=', 'sohs.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('sohs.date', '=', Carbon::now()->format('m'))
                        ->whereYear('sohs.date', '=', Carbon::now()->format('Y'))->pluck('sohs.id');

            $sohDetail = SOHDetail::whereIn('soh_id', $sohIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sohDetail as $detail){

                $summary = SummarySOH::where('soh_detail_id', $detail->id)->first();

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

    public function changeSummarySos($data, $change){

        if(isset($data['product_id']) && isset($data['type'])){ /* Product Focus Change */

            $sosIds = SOS::whereMonth('sos.date', '=', Carbon::now()->format('m'))
                         ->whereYear('sos.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sosDetail = SOSDetail::whereIn('sos_id', $sosIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sosDetail as $detail){

                $summary = SummarySOS::where('sos_detail_id', $detail->id)->first();

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

            $sosIds = SOS::join('stores', 'stores.id', '=', 'sos.store_id')
                        ->join('sub_channels', 'sub_channels.id', '=', 'stores.subchannel_id')
                        ->join('channels', 'channels.id', '=', 'sub_channels.channel_id')
                        ->join('global_channels', 'global_channels.id', '=', 'channels.globalchannel_id')
                        ->where('global_channels.id', $data['globalchannel_id'])
                        ->whereMonth('sos.date', '=', Carbon::now()->format('m'))
                        ->whereYear('sos.date', '=', Carbon::now()->format('Y'))->pluck('sos.id');

            $sosDetail = SOSDetail::whereIn('sos_id', $sosIds)
                                ->where('product_id', $data['product_id'])->get();

            foreach ($sosDetail as $detail){

                $summary = SummarySOS::where('sos_detail_id', $detail->id)->first();

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

    public function changeSummarySellInSalesman($data, $change){

        if(isset($data['product_id'])) { /* Product Focus Change */

            $sellInIds = SellIn::whereMonth('sell_ins.date', '=', Carbon::now()->format('m'))
                ->whereYear('sell_ins.date', '=', Carbon::now()->format('Y'))->pluck('id');

            $sellInDetail = SellInDetail::whereIn('sellin_id', $sellInIds)
                ->where('product_id', $data['product_id'])->get();

            foreach ($sellInDetail as $detail) {

                $summary = SalesmanSummarySales::where('sellin_detail_id', $detail->id)->first();

                if($summary) {

                    if ($change == 'change') {
                        $summary->update([
                            'value_pf' => $summary->value
                        ]);
                    } else if ($change == 'delete') {
                        $summary->update([
                            'value_pf' => 0
                        ]);
                    }

                    /* Reset Actual */
                    $this->resetActualSalesman($summary->user_id);

                }

            }

        }

    }

}