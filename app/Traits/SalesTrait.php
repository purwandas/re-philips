<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummaryRetDistributor;
use App\Reports\SummaryRetConsument;
use App\Reports\SummaryFreeProduct;
use App\Reports\SummaryTbat;
use App\Reports\SummarySoh;
use App\Reports\SummaryDisplayShare;
use App\User;
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
use App\Soh;
use App\SohDetail;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\PosmActivity;
use App\PosmActivityDetail;

trait SalesTrait {

    public function deleteSellIn($detailId){

        // Find Detail then delete
        $sellInDetail = SellInDetail::where('id',$detailId)->first();

            $sellIn_id = $sellInDetail->sellin_id;
            
        $sellInDetail->delete();
        $summarySellInDetail = SummarySellIn::where('sellin_detail_id',$detailId)->delete();

            // Check if no detail exist delete header
            $sellIn = SellIn::where('id',$sellIn_id)->first();
            $sellInDetail = SellInDetail::where('sellin_id',$sellIn->id)->get();

                if($sellInDetail->count() == 0){
                    $sellIn->delete();
                }

        if ($sellInDetail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateSellIn($id, $qty){

        $sellInDetail = SellInDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summarySellInDetail = SummarySellIn::where('sellin_detail_id',$id)
            ->first();
            $value = $summarySellInDetail->unit_price * $qty;
            
            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summarySellInDetail->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summarySellInDetail->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summarySellInDetail->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summarySellInDetail->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($sellInDetail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteSellOut($detailId){

        // Find Detail then delete
        $sellOutDetail = SellOutDetail::where('id',$detailId)->first();

            $sellOut_id = $sellOutDetail->sellout_id;
            
        $sellOutDetail->delete();
        $summarySellOutDetail = SummarySellOut::where('sellout_detail_id',$detailId)->delete();

            // Check if no detail exist delete header
            $sellOut = SellOut::where('id',$sellOut_id)->first();
            $sellOutDetail = SellOutDetail::where('sellout_id',$sellOut->id)->get();

                if($sellOutDetail->count() == 0){
                    $sellOut->delete();
                }

        if ($sellOutDetail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateSellOut($id, $qty){

        $sellOutDetail = SellOutDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summarySellOutDetail = SummarySellOut::where('sellout_detail_id',$id)
            ->first();
            $value = $summarySellOutDetail->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summarySellOutDetail->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summarySellOutDetail->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summarySellOutDetail->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summarySellOutDetail->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($sellOutDetail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteRetDistributor($detailId){

        // Find Detail then delete
        $retDistributorDetail = RetDistributorDetail::where('id',$detailId)->first();

            $retDistributor_id = $retDistributorDetail->retdistributor_id;
            
        $retDistributorDetail->delete();
        $summaryRetDistributorDetail = SummaryRetDistributor::where('retdistributor_detail_id',$detailId)->delete();

            // Check if no detail exist delete header
            $retDistributor = RetDistributor::where('id',$retDistributor_id)->first();
            $distributorDetail = RetDistributorDetail::where('retdistributor_id',$retDistributor->id)->get();

                if($distributorDetail->count() == 0){
                    $retDistributor->delete();
                }

        if ($retDistributorDetail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateRetDistributor($id, $qty){

        $retDistributorDetail = RetDistributorDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summaryRetDistributorDetail = SummaryRetDistributor::where('retdistributor_detail_id',$id)
            ->first();
            $value = $summaryRetDistributorDetail->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summaryRetDistributorDetail->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summaryRetDistributorDetail->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summaryRetDistributorDetail->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summaryRetDistributorDetail->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($retDistributorDetail) {
            return true;
        }else{
            return false;
        }
    }


    public function deleteRetConsument($id){

        // Find Detail then delete
        $detail = RetConsumentDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->retconsument_id;
            
        $detail->delete();
        $summary = SummaryRetConsument::where('retconsument_detail_id',$id)->delete();

            // Check if no detail exist delete header
            $header = RetConsument::where('id',$headerId)->first();
            $details = RetConsumentDetail::where('retconsument_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateRetConsument($id, $qty){

        $detail = RetConsumentDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summary = SummaryRetConsument::where('retconsument_detail_id',$id)
            ->first();
            $value = $summary->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summary->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summary->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summary->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summary->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteFreeProduct($id){

        // Find Detail then delete
        $detail = FreeProductDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->freeproduct_id;
            
        $detail->delete();
        $summary = SummaryFreeProduct::where('freeproduct_detail_id',$id)->delete();

            // Check if no detail exist delete header
            $header = FreeProduct::where('id',$headerId)->first();
            $details = FreeProductDetail::where('freeproduct_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateFreeProduct($id, $qty){

        $detail = FreeProductDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summary = SummaryFreeProduct::where('freeproduct_detail_id',$id)
            ->first();
            $value = $summary->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summary->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summary->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summary->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summary->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteTbat($id){

        // Find Detail then delete
        $detail = TbatDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->tbat_id;
            
        $detail->delete();
        $summary = SummaryTbat::where('tbat_detail_id',$id)->delete();

            // Check if no detail exist delete header
            $header = Tbat::where('id',$headerId)->first();
            $details = TbatDetail::where('tbat_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateTbat($id, $qty){

        $detail = TbatDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summary = SummaryTbat::where('tbat_detail_id',$id)
            ->first();
            $value = $summary->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summary->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summary->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summary->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summary->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteSoh($id){

        // Find Detail then delete
        $detail = SohDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->soh_id;
            
        $detail->delete();
        $summary = SummarySoh::where('soh_detail_id',$id)->delete();

            // Check if no detail exist delete header
            $header = Soh::where('id',$headerId)->first();
            $details = SohDetail::where('soh_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateSoh($id, $qty){

        $detail = SohDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summary = SummarySoh::where('soh_detail_id',$id)
            ->first();
            $value = $summary->unit_price * $qty;

            $pf_mr = 0;
            $pf_tr = 0;
            $pf_ppe = 0;
            if ($summary->value_pf_mr > 0) {
                $pf_mr = $value;
            }
            if ($summary->value_pf_tr > 0) {
                $pf_tr = $value;
            }
            if ($summary->value_pf_ppe > 0) {
                $pf_ppe = $value;
            }

            $summary->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteDisplayShare($id){

        // Find Detail then delete
        $detail = DisplayShareDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->display_share_id;
            
        $detail->delete();
        $summary = SummaryDisplayShare::where('displayshare_detail_id',$id)->delete();

            // Check if no detail exist delete header
            $header = DisplayShare::where('id',$headerId)->first();
            $details = DisplayShareDetail::where('display_share_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateDisplayShare($id, $philips, $all){

        $detail = DisplayShareDetail::where('id',$id)->update(['philips'=> $philips, 'all'=> $all]);

        $summary = SummaryDisplayShare::where('displayshare_detail_id',$id)
            ->first();

            $summary->update([
                        'philips'=> $philips,
                        'all'=> $all,
                        'percentage'=> ($philips/$all)*100,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

    public function deletePosmActivity($id){

        // Find Detail then delete
        $detail = PosmActivityDetail::where('id',$id)->first();
        // return response()->json($detail);
            $headerId = $detail->posmactivity_id;
            
        $detail->delete();

            // Check if no detail exist delete header
            $header = PosmActivity::where('id',$headerId)->first();
            $details = PosmActivityDetail::where('posmactivity_id',$header->id)->get();

                if($details->count() == 0){
                    $header->delete();
                }

        if ($detail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updatePosmActivity($id, $quantity){

        $detail = PosmActivityDetail::where('id',$id)->update(['quantity'=> $quantity]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

}