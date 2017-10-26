<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class AttendanceDetail extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'attendance_id', 'store_id', 'is_store', 'check_in', 'check_out', 'check_in_longitude', 'check_in_latitude', 'check_out_longitude', 'check_out_latitude', 'check_in_location', 'check_out_location', 'detail'
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

	public function attendance()
    {
        return $this->belongsTo('App\Attendance', 'attendance_id');
    }

	/**
     * Filtering Berdasarakan Request User
     * @param $query
     * @param QueryFilters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
