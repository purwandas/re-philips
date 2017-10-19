<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\StringTrait;
use DB;
use App\Place;

class PlaceController extends Controller
{
    public function all()
    {
    	$data = Place::select('id', 'name')->get();

    	return response()->json($data);
    }

    public function nearby(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $distance = 250;

    	$data = Place::where('latitude', '!=', null)
                    ->where('longitude', '!=', null)
                    ->select('id', 'name', 'latitude', 'longitude');

        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $haversine = '( 6371 * acos( cos( radians('.$content['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$content['longitude'].') ) + sin( radians('.$content['latitude'].') ) * sin( radians( latitude ) ) ) ) * 1000';
        $data = $data->selectRaw("{$haversine} AS distance")->orderBy('distance', 'asc')->whereRaw("{$haversine} <= ?", [$distance]);

        return response()->json($data->get());
    }
}
