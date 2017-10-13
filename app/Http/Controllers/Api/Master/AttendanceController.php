<?php

namespace App\Http\Controllers\Api\Master;

use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Geotools;

class AttendanceController extends Controller
{
    public function attendance(Request $request, $param){



        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        /*
         * Checking mode of attendance input
         * 1 => Check In
         * 2 => Check Out
         * 3 => Sakit
         * 4 => Izin
         * 5 => Off
         */


        if($param == 1){
//            return response()->json('OK');

            /* CHECK IN */

            $store = Store::find($content['store_id']);
            $coordStore = Geotools::coordinate([$store->latitude, $store->longitude]);
            $coordNow = Geotools::coordinate([$content['latitude'], $content['longitude']]);
            $distance = Geotools::distance()->setFrom($coordStore)->setTo($coordNow)->flat();

            // Check distance if above 250 meter(s)
            if($distance > 250){
                return response()->json(['status' => false, 'message' => 'Jarak anda terlalu jauh dari store'], 500);
            }

            return response()->json($distance);

        }elseif ($param == 2){

        }elseif ($param == 3){

        }elseif ($param == 4){

        }elseif ($param == 5){

        }

    }

    /* GET DISTANCE METHOD */
    public function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {
        $earth_radius = 6371;

        $dLat = deg2rad( $latitude2 - $latitude1 );
        $dLon = deg2rad( $longitude2 - $longitude1 );

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

}
