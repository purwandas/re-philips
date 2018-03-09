<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class ApmMonth extends Model
{
    protected $fillable = [
        'previous_month', 'selected'
    ];
}
