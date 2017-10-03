<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\GroupFilters;
use App\Traits\StringTrait;
use DB;
use App\Group;

class GroupController extends Controller
{
    public function all($param)
    {
    	$data = Group::where('groupproduct_id', $param)
    				->select('groups.id', 'groups.name')
    				->get();

    	return response()->json(compact('data'));
    }
}
