<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use DB;
use App\Store;

class StoreController extends Controller
{
    public function all()
    {
    	$data = Store::select('id', 'store_name_1 as name')->get();
    	
    	return response()->json(compact('data'));
    }
}
