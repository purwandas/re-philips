<?php

namespace App\Http\Controllers\Api\Master;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function all()
    {
    	$data = Category::select('id', 'name')->get();

    	return response()->json($data);
    }

    public function allWithParam($param)
    {
    	$data = Category::where('categories.group_id', $param)
                ->select('id', 'name')->get();

    	return response()->json($data);
    }
}
