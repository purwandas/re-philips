<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesmanSummaryTargetActual extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'nik', 'salesman_name', 'area', 'target_call', 'actual_call', 'target_active_outlet', 'actual_active_outlet',
        'target_effective_call', 'actual_effective_call', 'target_sales', 'actual_sales', 'target_sales_pf', 'actual_sales_pf',
        'sum_national_target_call', 'sum_national_actual_call', 'sum_national_target_active_outlet', 'sum_national_actual_active_outlet',
        'sum_national_target_effective_call', 'sum_national_actual_effective_call', 'sum_national_target_sales', 'sum_national_actual_sales',
        'sum_national_target_sales_pf', 'sum_national_actual_sales_pf'
    ];
}
