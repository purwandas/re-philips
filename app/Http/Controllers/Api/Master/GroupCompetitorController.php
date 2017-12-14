<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\GroupCompetitorFilters;
use App\Traits\StringTrait;
use DB;
use App\GroupCompetitor;

class GroupCompetitorController extends Controller
{
    public function all($param)
    {
//    	$data = GroupCompetitor::join('group_products', 'group_competitors.groupproduct_id', '=', 'group_products.id')
//    				->where('group_products.id', $param)
//    				->select('group_competitors.id', 'group_competitors.name')
//    				->get();

        $data = GroupCompetitor::join('groupcompetitor_groups', 'groupcompetitor_groups.groupcompetitor_id', '=', 'group_competitors.id')
    				->where('groupcompetitor_groups.group_id', $param)
    				->select('group_competitors.id', 'group_competitors.name')
    				->get();

    	return response()->json($data);
    }

    public function allCategory($param, $param2)
    {
    	$category = 'Male Grooming';
    	if($param2 == 2){
			$category = 'Beauty';    		
    	}

    	$data = GroupCompetitor::join('group_products', 'group_competitors.groupproduct_id', '=', 'group_products.id')
    				->where('group_products.id', $param)
    				->where('group_competitors.kategori', $category)
    				->select('group_competitors.id', 'group_competitors.name')
    				->get();

    	return response()->json($data);
    }

    public function allNoParam()
    {
    	$data = GroupCompetitor::select('group_competitors.id', 'group_competitors.name')->get();

    	return response()->json($data);
    }
}
