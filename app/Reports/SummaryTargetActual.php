<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Model;

class SummaryTargetActual extends Model
{
    protected $fillable = [
        'region_id', 'area_id', 'district_id', 'storeId', 'user_id', 'region', 'area', 'district', 'nik',
        'promoter_name', 'account_type', 'title_of_promoter', 'classification_store', 'account', 'store_id', 'store_name_1',
        'store_name_2', 'spv_name', 'trainer', 'sell_type', 'target_dapc', 'actual_dapc', 'target_da', 'actual_da', 'target_pc', 'actual_pc',
        'target_mcc', 'actual_mcc', 'target_pf_da', 'actual_pf_da', 'target_pf_pc', 'actual_pf_pc', 'target_pf_mcc', 'actual_pf_mcc',
        'sum_target_store', 'sum_actual_store', 'sum_target_area', 'sum_actual_area', 'sum_target_region', 'sum_actual_region'

    ];
}
