<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\GroupProductFilters;
use App\Traits\StringTrait;
use DB;
use App\GroupProduct;

class GroupProductController extends Controller
{
    // Data for select2 with Filters
    public function getDataWithFilters(GroupProductFilters $filters){        
        $data = GroupProduct::filter($filters)->get();

        return $data;
    }
}
