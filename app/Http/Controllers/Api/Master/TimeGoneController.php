<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TimeGone;

class TimeGoneController extends Controller
{
    public function getTimeGone($param){
    	$timeGone = TimeGone::where('day', $param)->first();

    	if($timeGone){
			return response()->json(['timegone' => $timeGone->percent]);    		
    	}

    	return response()->json(['timegone' => 0]);
    }
}
