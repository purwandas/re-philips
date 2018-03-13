<?php

namespace App\Traits;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummaryRetDistributor;
use App\Reports\SummaryRetConsument;
use App\Reports\SummaryFreeProduct;
use App\Reports\SummaryTbat;
use App\Reports\SummarySoh;
use App\Reports\SummaryDisplayShare;
use App\Reports\SalesActivity;
use App\Reports\SalesmanSalesActivity;
use App\Reports\SalesmanSummarySales;
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

    use ActualTrait;

    public function deleteSellIn($detailId){
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        // Find Detail then delete
        $sellInDetail = SellInDetail::where('id',$detailId)->first();

        $sellIn_id = $sellInDetail->sellin_id;
            
        $sellInDetail->forceDelete();
        $summarySellInDetail = SummarySellIn::where('sellin_detail_id',$detailId)->first();

            /* begin insert sales activity */
            $data = new Collection();

            /* Header Details */
            $dataSummary = ([
                'activity' => 'Delete',
                'type' => 'Sell In',
                'action_from' => 'Web',
                'detail_id'=> $summarySellInDetail->sellin_detail_id,
                'week' => $summarySellInDetail->week,
                'distributor_code' => $summarySellInDetail->distributor_code,
                'distributor_name' => $summarySellInDetail->distributor_name,
                'region' => $summarySellInDetail->region,
                'region_id' => $summarySellInDetail->region_id,
                'channel' => $summarySellInDetail->channel,
                'sub_channel' => $summarySellInDetail->sub_channel,
                'area' => $summarySellInDetail->area,
                'area_id' => $summarySellInDetail->area_id,
                'district' => $summarySellInDetail->district,
                'district_id' => $summarySellInDetail->district_id,
                'store_name_1' => $summarySellInDetail->store_name_1,
                'store_name_2' => $summarySellInDetail->store_name_2,
                'store_id' => $summarySellInDetail->store_id,
                'storeId' => $summarySellInDetail->storeId,
                'dedicate' => $summarySellInDetail->dedicate,
                'nik' => $summarySellInDetail->nik,
                'promoter_name' => $summarySellInDetail->promoter_name,
                'user_id' => $summarySellInDetail->user_id,
                'date' => $summarySellInDetail->date,
                'role' => $summarySellInDetail->role,
                'spv_name' => $summarySellInDetail->spv_name,
                'dm_name' => $summarySellInDetail->dm_name,
                'trainer_name' => $summarySellInDetail->trainer_name,
                'model' => $summarySellInDetail->model,
                'group' => $summarySellInDetail->group,
                'category' => $summarySellInDetail->category,
                'product_name' => $summarySellInDetail->product_name,
                'unit_price' => $summarySellInDetail->unit_price,
                'quantity' => $summarySellInDetail->quantity,
                'value' => $summarySellInDetail->value,
                'value_pf_mr' => $summarySellInDetail->value_pf_mr,
                'value_pf_tr' => $summarySellInDetail->value_pf_tr,
                'value_pf_ppe' => $summarySellInDetail->value_pf_ppe,
                'new_quantity' => '',
                'new_value' => '',
                'new_value_pf_mr' => '',
                'new_value_pf_tr' => '',
                'new_value_pf_ppe' => '',
            ]);

            $data->push($dataSummary);

            $dt = Carbon::now();
            $date = $dt->toDateString();               // 2015-12-19
            SalesActivity::create([
                'user_id' => $userId,
                'date' => $date,
                'details' => $data,
            ]);
            /* end insert sales activity */
            
        // Update Target Actuals
        $summary_ta['user_id'] = $summarySellInDetail->user_id;
        $summary_ta['store_id'] = $summarySellInDetail->storeId;
        $summary_ta['week'] = $summarySellInDetail->week;
        $summary_ta['pf'] = $summarySellInDetail->value_pf_mr + $summarySellInDetail->value_pf_tr + $summarySellInDetail->value_pf_ppe;
        $summary_ta['value'] = $summarySellInDetail->value;
        $summary_ta['group'] = $summarySellInDetail->group;
        $summary_ta['sell_type'] = 'Sell In';
        $summary_ta['irisan'] = $summarySellInDetail->irisan;

        $this->changeActual($summary_ta, 'delete');

        $summarySellInDetail->forceDelete();

            // Check if no detail exist delete header
            $sellIn = SellIn::where('id',$sellIn_id)->first();
            $sellInDetail = SellInDetail::where('sellin_id',$sellIn->id)->get();

                if($sellInDetail->count() == 0){
                    $sellIn->forceDelete();
                }

        if ($sellInDetail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateSellIn($id, $qty){
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $sellInDetail = SellInDetail::where('id',$id);

        $roleGroup = SellIn::where('id', $sellInDetail->first()->sellin_id)->first()->user->role->role_group;

        $sellInDetail->update(['quantity'=> $qty]);

        if($roleGroup != 'Salesman Explorer'){ // Promoter

            $summarySellInDetail = SummarySellIn::where('sellin_detail_id',$id)
                ->first();

                $value_old = $summarySellInDetail->value;

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

                    /* begin insert sales activity */
                    $data = new Collection();

                    /* Header Details */
                    $dataSummary = ([
                        'activity' => 'Update',
                        'type' => 'Sell In',
                        'action_from' => 'Web',
                        'detail_id'=> $summarySellInDetail->sellin_detail_id,
                        'week' => $summarySellInDetail->week,
                        'distributor_code' => $summarySellInDetail->distributor_code,
                        'distributor_name' => $summarySellInDetail->distributor_name,
                        'region' => $summarySellInDetail->region,
                        'region_id' => $summarySellInDetail->region_id,
                        'channel' => $summarySellInDetail->channel,
                        'sub_channel' => $summarySellInDetail->sub_channel,
                        'area' => $summarySellInDetail->area,
                        'area_id' => $summarySellInDetail->area_id,
                        'district' => $summarySellInDetail->district,
                        'district_id' => $summarySellInDetail->district_id,
                        'store_name_1' => $summarySellInDetail->store_name_1,
                        'store_name_2' => $summarySellInDetail->store_name_2,
                        'store_id' => $summarySellInDetail->store_id,
                        'storeId' => $summarySellInDetail->storeId,
                        'dedicate' => $summarySellInDetail->dedicate,
                        'nik' => $summarySellInDetail->nik,
                        'promoter_name' => $summarySellInDetail->promoter_name,
                        'user_id' => $summarySellInDetail->user_id,
                        'date' => $summarySellInDetail->date,
                        'role' => $summarySellInDetail->role,
                        'spv_name' => $summarySellInDetail->spv_name,
                        'dm_name' => $summarySellInDetail->dm_name,
                        'trainer_name' => $summarySellInDetail->trainer_name,
                        'model' => $summarySellInDetail->model,
                        'group' => $summarySellInDetail->group,
                        'category' => $summarySellInDetail->category,
                        'product_name' => $summarySellInDetail->product_name,
                        'unit_price' => $summarySellInDetail->unit_price,
                        'quantity' => $summarySellInDetail->quantity,
                        'value' => $summarySellInDetail->value,
                        'value_pf_mr' => $summarySellInDetail->value_pf_mr,
                        'value_pf_tr' => $summarySellInDetail->value_pf_tr,
                        'value_pf_ppe' => $summarySellInDetail->value_pf_ppe,
                        'new_quantity' => $qty,
                        'new_value' => $value,
                        'new_value_pf_mr' => $pf_mr,
                        'new_value_pf_tr' => $pf_tr,
                        'new_value_pf_ppe' => $pf_ppe,
                    ]);

                    $data->push($dataSummary);

                    $dt = Carbon::now();
                    $date = $dt->toDateString();
                    SalesActivity::create([
                        'user_id' => $userId,
                        'date' => $date,
                        'details' => $data,
                    ]);
                    /* end insert sales activity */

                $summarySellInDetail->update([
                            'quantity'=> $qty,
                            'value'=> $value,
                            'value_pf_mr' => $pf_mr,
                            'value_pf_te' => $pf_tr,
                            'value_pf_ppe' => $pf_ppe,
                        ]);

                // Actual Summary
                $summary_ta['user_id'] = $summarySellInDetail->user_id;
                $summary_ta['store_id'] = $summarySellInDetail->storeId;
                $summary_ta['week'] = $summarySellInDetail->week;
                $summary_ta['pf'] = $summarySellInDetail->value_pf_mr + $summarySellInDetail->value_pf_tr + $summarySellInDetail->value_pf_ppe;
                $summary_ta['value_old'] = $value_old;
                $summary_ta['value'] = $summarySellInDetail->value;
                $summary_ta['group'] = $summarySellInDetail->group;
                $summary_ta['sell_type'] = 'Sell In';
                $summary_ta['irisan'] = $summarySellInDetail->irisan;

                $this->changeActual($summary_ta, 'change');

        }else{ // SEE

            $summary = SalesmanSummarySales::where('sellin_detail_id', $id)->first();

                $value_old = $summary->value; // Buat reset actual salesman

                $value = $qty * $summary->unit_price;

                ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                /* begin insert sales activity */
                    $data = new Collection();

                    /* Header Details */
                    $dataSummary = ([
                        'activity' => 'Update',
                        'type' => 'Sell In',
                        'action_from' => 'Web',
                        'detail_id'=> $summary->sellin_detail_id,
                        'week' => $summary->week,
                        'distributor_code' => $summary->distributor_code,
                        'distributor_name' => $summary->distributor_name,
                        'region' => $summary->region,
                        'region_id' => $summary->region_id,
                        'channel' => $summary->channel,
                        'sub_channel' => $summary->sub_channel,
                        'area' => $summary->area,
                        'area_id' => $summary->area_id,
                        'district' => $summary->district,
                        'district_id' => $summary->district_id,
                        'store_name_1' => $summary->store_name_1,
                        'store_name_2' => $summary->store_name_2,
                        'store_id' => $summary->store_id,
                        'storeId' => $summary->storeId,
                        // 'dedicate' => $summary->dedicate,
                        'nik' => $summary->nik,
                        'promoter_name' => $summary->promoter_name,
                        'user_id' => $summary->user_id,
                        'date' => $summary->date,
                        'role' => $summary->role,
                        // 'spv_name' => $summarySellInDetail->spv_name,
                        // 'dm_name' => $summarySellInDetail->dm_name,
                        // 'trainer_name' => $summarySellInDetail->trainer_name,
                        'model' => $summary->model,
                        'group' => $summary->group,
                        'category' => $summary->category,
                        'product_name' => $summary->product_name,
                        'unit_price' => $summary->unit_price,
                        'quantity' => $summary->quantity,
                        'value' => $summary->value,
                        'value_pf' => $summary->value_pf,
                        'new_quantity' => $qty,
                        'new_value' => $value,
                        'new_value_pf' => $value_pf,
                        // 'new_value_pf_tr' => $pf_tr,
                        // 'new_value_pf_ppe' => $pf_ppe,
                    ]);

                    $data->push($dataSummary);

                    $dt = Carbon::now();
                    $date = $dt->toDateString();
                    SalesmanSalesActivity::create([
                        'user_id' => $userId,
                        'date' => $date,
                        'details' => $data,
                    ]);
                    /* end insert sales activity */ 

                $summary->update([
                    'quantity' => $qty,
                    'value' => $value,
                    'value_pf' => $value_pf
                ]);

                // Actual Summary
                $summary_ta['user_id'] = $summary->user_id;
                $summary_ta['store_id'] = $summary->store_id;
                $summary_ta['pf'] = $summary->value_pf;
                $summary_ta['value_old'] = $value_old;
                $summary_ta['value'] = $summary->value;

                $this->changeActualSalesman($summary_ta, 'change');

        }

        if ($sellInDetail) {
                return true;
            }else{
                return false;
            }
    }

    public function deleteSellOut($detailId){
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        // Find Detail then delete
        $sellOutDetail = SellOutDetail::where('id',$detailId)->first();

            $sellOut_id = $sellOutDetail->sellout_id;
            
        $sellOutDetail->forceDelete();
        $summarySellOutDetail = SummarySellOut::where('sellout_detail_id',$detailId)->first();

            /* begin insert sales activity */
            $data = new Collection();

            /* Header Details */
            $dataSummary = ([
                'activity' => 'Delete',
                'type' => 'Sell Out',
                'action_from' => 'Web',
                'detail_id'=> $summarySellOutDetail->sellout_detail_id,
                'week' => $summarySellOutDetail->week,
                'distributor_code' => $summarySellOutDetail->distributor_code,
                'distributor_name' => $summarySellOutDetail->distributor_name,
                'region' => $summarySellOutDetail->region,
                'region_id' => $summarySellOutDetail->region_id,
                'channel' => $summarySellOutDetail->channel,
                'sub_channel' => $summarySellOutDetail->sub_channel,
                'area' => $summarySellOutDetail->area,
                'area_id' => $summarySellOutDetail->area_id,
                'district' => $summarySellOutDetail->district,
                'district_id' => $summarySellOutDetail->district_id,
                'store_name_1' => $summarySellOutDetail->store_name_1,
                'store_name_2' => $summarySellOutDetail->store_name_2,
                'store_id' => $summarySellOutDetail->store_id,
                'storeId' => $summarySellOutDetail->storeId,
                'dedicate' => $summarySellOutDetail->dedicate,
                'nik' => $summarySellOutDetail->nik,
                'promoter_name' => $summarySellOutDetail->promoter_name,
                'user_id' => $summarySellOutDetail->user_id,
                'date' => $summarySellOutDetail->date,
                'role' => $summarySellOutDetail->role,
                'spv_name' => $summarySellOutDetail->spv_name,
                'dm_name' => $summarySellOutDetail->dm_name,
                'trainer_name' => $summarySellOutDetail->trainer_name,
                'model' => $summarySellOutDetail->model,
                'group' => $summarySellOutDetail->group,
                'category' => $summarySellOutDetail->category,
                'product_name' => $summarySellOutDetail->product_name,
                'unit_price' => $summarySellOutDetail->unit_price,
                'quantity' => $summarySellOutDetail->quantity,
                'value' => $summarySellOutDetail->value,
                'value_pf_mr' => $summarySellOutDetail->value_pf_mr,
                'value_pf_tr' => $summarySellOutDetail->value_pf_tr,
                'value_pf_ppe' => $summarySellOutDetail->value_pf_ppe,
                'new_quantity' => '',
                'new_value' => '',
                'new_value_pf_mr' => '',
                'new_value_pf_tr' => '',
                'new_value_pf_ppe' => '',
            ]);

            $data->push($dataSummary);

            $dt = Carbon::now();
            $date = $dt->toDateString();               // 2015-12-19
            SalesActivity::create([
                'user_id' => $userId,
                'date' => $date,
                'details' => $data,
            ]);
            /* end insert sales activity */

        // Update Target Actuals
        $summary_ta['user_id'] = $summarySellOutDetail->user_id;
        $summary_ta['store_id'] = $summarySellOutDetail->storeId;
        $summary_ta['week'] = $summarySellOutDetail->week;
        $summary_ta['pf'] = $summarySellOutDetail->value_pf_mr + $summarySellOutDetail->value_pf_tr + $summarySellOutDetail->value_pf_ppe;
        $summary_ta['value'] = $summarySellOutDetail->value;
        $summary_ta['group'] = $summarySellOutDetail->group;
        $summary_ta['sell_type'] = 'Sell Out';
        $summary_ta['irisan'] = $summarySellOutDetail->irisan;

        $this->changeActual($summary_ta, 'delete');

        $summarySellOutDetail->forceDelete();

            // Check if no detail exist delete header
            $sellOut = SellOut::where('id',$sellOut_id)->first();
            $sellOutDetail = SellOutDetail::where('sellout_id',$sellOut->id)->get();

                if($sellOutDetail->count() == 0){
                    $sellOut->forceDelete();
                }

        if ($sellOutDetail) {
            return true;
        }else{
            return false;
        }
        
    }

    public function updateSellOut($id, $qty){
        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $sellOutDetail = SellOutDetail::where('id',$id)->update(['quantity'=> $qty]);

        $summarySellOutDetail = SummarySellOut::where('sellout_detail_id',$id)
            ->first();

            $value_old = $summarySellOutDetail->value;

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
                
                /* begin insert sales activity */
                $data = new Collection();

                /* Header Details */
                $dataSummary = ([
                    'activity' => 'Update',
                    'type' => 'Sell Out',
                    'action_from' => 'Web',
                    'detail_id'=> $summarySellOutDetail->sellout_detail_id,
                    'week' => $summarySellOutDetail->week,
                    'distributor_code' => $summarySellOutDetail->distributor_code,
                    'distributor_name' => $summarySellOutDetail->distributor_name,
                    'region' => $summarySellOutDetail->region,
                    'region_id' => $summarySellOutDetail->region_id,
                    'channel' => $summarySellOutDetail->channel,
                    'sub_channel' => $summarySellOutDetail->sub_channel,
                    'area' => $summarySellOutDetail->area,
                    'area_id' => $summarySellOutDetail->area_id,
                    'district' => $summarySellOutDetail->district,
                    'district_id' => $summarySellOutDetail->district_id,
                    'store_name_1' => $summarySellOutDetail->store_name_1,
                    'store_name_2' => $summarySellOutDetail->store_name_2,
                    'store_id' => $summarySellOutDetail->store_id,
                    'storeId' => $summarySellOutDetail->storeId,
                    'dedicate' => $summarySellOutDetail->dedicate,
                    'nik' => $summarySellOutDetail->nik,
                    'promoter_name' => $summarySellOutDetail->promoter_name,
                    'user_id' => $summarySellOutDetail->user_id,
                    'date' => $summarySellOutDetail->date,
                    'role' => $summarySellOutDetail->role,
                    'spv_name' => $summarySellOutDetail->spv_name,
                    'dm_name' => $summarySellOutDetail->dm_name,
                    'trainer_name' => $summarySellOutDetail->trainer_name,
                    'model' => $summarySellOutDetail->model,
                    'group' => $summarySellOutDetail->group,
                    'category' => $summarySellOutDetail->category,
                    'product_name' => $summarySellOutDetail->product_name,
                    'unit_price' => $summarySellOutDetail->unit_price,
                    'quantity' => $summarySellOutDetail->quantity,
                    'irisan' => $summarySellOutDetail->irisan,
                    'value' => $summarySellOutDetail->value,
                    'value_pf_mr' => $summarySellOutDetail->value_pf_mr,
                    'value_pf_tr' => $summarySellOutDetail->value_pf_tr,
                    'value_pf_ppe' => $summarySellOutDetail->value_pf_ppe,
                    'new_quantity' => $qty,
                    'new_value' => $value,
                    'new_value_pf_mr' => $pf_mr,
                    'new_value_pf_tr' => $pf_tr,
                    'new_value_pf_ppe' => $pf_ppe,
                ]);

                $data->push($dataSummary);

                $dt = Carbon::now();
                $date = $dt->toDateString();
                SalesActivity::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'details' => $data,
                ]);
                /* end insert sales activity */

            $summarySellOutDetail->update([
                        'quantity'=> $qty,
                        'value'=> $value,
                        'value_pf_mr' => $pf_mr,
                        'value_pf_te' => $pf_tr,
                        'value_pf_ppe' => $pf_ppe,
                    ]);

            // Actual Summary
            $summary_ta['user_id'] = $summarySellOutDetail->user_id;
            $summary_ta['store_id'] = $summarySellOutDetail->storeId;
            $summary_ta['week'] = $summarySellOutDetail->week;
            $summary_ta['pf'] = $summarySellOutDetail->value_pf_mr + $summarySellOutDetail->value_pf_tr + $summarySellOutDetail->value_pf_ppe;
            $summary_ta['value_old'] = $value_old;
            $summary_ta['value'] = $summarySellOutDetail->value;
            $summary_ta['group'] = $summarySellOutDetail->group;
            $summary_ta['sell_type'] = 'Sell Out';
            $summary_ta['irisan'] = $summarySellOutDetail->irisan;

            $this->changeActual($summary_ta, 'change');

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
            
        $retDistributorDetail->forceDelete();
        $summaryRetDistributorDetail = SummaryRetDistributor::where('retdistributor_detail_id',$detailId)->forceDelete();

            // Check if no detail exist delete header
            $retDistributor = RetDistributor::where('id',$retDistributor_id)->first();
            $distributorDetail = RetDistributorDetail::where('retdistributor_id',$retDistributor->id)->get();

                if($distributorDetail->count() == 0){
                    $retDistributor->forceDelete();
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
            
        $detail->forceDelete();
        $summary = SummaryRetConsument::where('retconsument_detail_id',$id)->forceDelete();

            // Check if no detail exist delete header
            $header = RetConsument::where('id',$headerId)->first();
            $details = RetConsumentDetail::where('retconsument_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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
            
        $detail->forceDelete();
        $summary = SummaryFreeProduct::where('freeproduct_detail_id',$id)->forceDelete();

            // Check if no detail exist delete header
            $header = FreeProduct::where('id',$headerId)->first();
            $details = FreeProductDetail::where('freeproduct_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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
            
        $detail->forceDelete();
        $summary = SummaryTbat::where('tbat_detail_id',$id)->forceDelete();

            // Check if no detail exist delete header
            $header = Tbat::where('id',$headerId)->first();
            $details = TbatDetail::where('tbat_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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
            
        $detail->forceDelete();
        $summary = SummarySoh::where('soh_detail_id',$id)->forceDelete();

            // Check if no detail exist delete header
            $header = Soh::where('id',$headerId)->first();
            $details = SohDetail::where('soh_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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
            
        $detail->forceDelete();
        $summary = SummaryDisplayShare::where('displayshare_detail_id',$id)->forceDelete();

            // Check if no detail exist delete header
            $header = DisplayShare::where('id',$headerId)->first();
            $details = DisplayShareDetail::where('display_share_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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
            
        $detail->forceDelete();

            // Check if no detail exist delete header
            $header = PosmActivity::where('id',$headerId)->first();
            $details = PosmActivityDetail::where('posmactivity_id',$header->id)->get();

                if($details->count() == 0){
                    $header->forceDelete();
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