<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class SummaryTargetActual extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'region_id', 'area_id', 'district_id', 'storeId', 'user_id', 'region', 'area', 'district', 'nik',
        'promoter_name', 'account_type', 'title_of_promoter', 'classification_store', 'account', 'store_id', 'store_name_1',
        'store_name_2', 'spv_name', 'trainer', 'sell_type', 'target_dapc', 'actual_dapc', 'target_da', 'actual_da', 'target_pc', 'actual_pc',
        'target_mcc', 'actual_mcc', 'target_pf_da', 'actual_pf_da', 'target_pf_pc', 'actual_pf_pc', 'target_pf_mcc', 'actual_pf_mcc',
        'target_da_w1', 'actual_da_w1', 'target_da_w2', 'actual_da_w2', 'target_da_w3', 'actual_da_w3', 'target_da_w4', 'actual_da_w4', 'target_da_w5', 'actual_da_w5',
        'target_pc_w1', 'actual_pc_w1', 'target_pc_w2', 'actual_pc_w2', 'target_pc_w3', 'actual_pc_w3', 'target_pc_w4', 'actual_pc_w4', 'target_pc_w5', 'actual_pc_w5',
        'target_mcc_w1', 'actual_mcc_w1', 'target_mcc_w2', 'actual_mcc_w2', 'target_mcc_w3', 'actual_mcc_w3', 'target_mcc_w4', 'actual_mcc_w4', 'target_mcc_w5', 'actual_mcc_w5',
        'sum_target_store', 'sum_actual_store', 'sum_target_area', 'sum_actual_area', 'sum_target_region', 'sum_actual_region', 'sum_pf_target_store',
        'sum_pf_actual_store', 'sum_pf_target_area', 'sum_pf_actual_area', 'sum_pf_target_region', 'sum_pf_actual_store_demo', 'sum_pf_target_store_demo',
        'sum_actual_store_demo', 'sum_target_store_demo', 'sum_pf_actual_store_promo', 'sum_pf_target_store_promo', 'sum_actual_store_promo', 'sum_target_store_promo',
        'user_role', 'partner'
    ];
}
