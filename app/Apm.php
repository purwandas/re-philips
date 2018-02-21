<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Apm extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'store_id', 'product_id', 'month_minus_3_value', 'month_minus_2_value', 'month_minus_1_value'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */
}
