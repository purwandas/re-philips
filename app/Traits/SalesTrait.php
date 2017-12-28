<?php

namespace App\Traits;

use Carbon\Carbon;
use App\User;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\Reports\SummaryRetDistributor;
use App\Reports\SummaryRetConsument;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\RetConsument;
use App\RetConsumentDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Tbat;
use App\TbatDetail;

trait SalesTrait {

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
            $summaryRetDistributorDetail->update([
                        'quantity'=> $qty,
                        'value'=> $value,
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

            $summary->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                    ]);

        if ($detail) {
            return true;
        }else{
            return false;
        }
    }

}