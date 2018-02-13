<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Leadtime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'area_id', 'leadtime'
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

	public function area()
    {
        return $this->belongsTo('App\Area', 'area_id');
    }
}
