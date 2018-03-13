<?php

namespace App\Http\Controllers\Api\Master;

use App\Area;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class AreaController extends Controller
{
    public function getRegion(){

        $user = JWTAuth::parseToken()->authenticate();

        $region = Region::select('id', 'name')->get();

        // if($user->role->role_group == 'Salesman Explorer'){

        //     $region = Region::where( function($query) {
        //                     return $query->where('name', 'NOT LIKE', '%2')
        //                                  ->where('name', 'NOT LIKE', '%3')
        //                                  ->where('name', 'NOT LIKE', '%4')
        //                                  ->where('name', 'NOT LIKE', '%MCC');
        //                 })->select('id', 'name')->get();

        // }

        return response()->json($region);

    }

    public function getAreaByRegion($param){

        $area = Area::join('regions', 'regions.id', '=', 'areas.region_id')
                ->where('region_id', $param)
                ->select('areas.id', 'areas.name', 'regions.name as region')->get();

        return response()->json($area);

    }
}
