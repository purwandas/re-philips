<?php

namespace App\Http\Controllers\Api\Master;

use App\Area;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    public function getRegion(){

        $region = Region::select('id', 'name')->get();

        return response()->json($region);

    }

    public function getAreaByRegion($param){

        $area = Area::join('regions', 'regions.id', '=', 'areas.region_id')
                ->where('region_id', $param)
                ->select('areas.id', 'areas.name', 'regions.name as region')->get();

        return response()->json($area);

    }
}
