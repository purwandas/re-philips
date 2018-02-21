<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\MaintenanceRequest;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Traits\PromoterTrait;
use DB;
use File;

class MaintenanceRequestController extends Controller
{
    use UploadTrait;
    use StringTrait;
    use PromoterTrait;

    public function store(Request $content){

        $user = JWTAuth::parseToken()->authenticate();

        if($this->getReject($user->id)){
            return response()->json(['status' => false, 'message' => 'Tidak bisa melakukan transaksi karena absen anda di reject oleh supervisor. '], 200);
        }

        // Get how many photo
        $photoLength = count($content->photo);

        // SET IMAGES FOLDER
        $folderPath = asset('image').'/maintenancerequest/'.$this->getRandomPath();

        try {
            DB::transaction(function () use ($content, $user, $folderPath) {

                $category = str_replace_array('"', explode(" ", $content->category), '');
                $channel = str_replace_array('"', explode(" ",$content->channel),'');
                $type = str_replace_array('"', explode(" ",$content->type),'');

                /** Insert Maintenance Request **/

                $maintenanceRequest = MaintenanceRequest::create([
                    'user_id' => $user->id,
                    'region_id' => $content->region_id,
                    'area_id' => $content->area_id,
                    'store_id' => $content->id,
                    'category' => $category,
                    'channel' => $channel,
                    'type' => $type,
                    'report' => $content->report,
                    // 'month' => (integer)Carbon::now()->format('m'),
                    // 'year' => (integer)Carbon::now()->format('Y'),
                    'date' => Carbon::now(),
                    'photo' => $folderPath
                ]);

            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
        }

        // Check Maintenance Request header after insert
        $maintenanceRequestAfter = MaintenanceRequest::where('user_id', $user->id)
            ->where('store_id', $content->id)
            ->where('report', $content->report)
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->first();
            // return response()->json($maintenanceRequestAfter);

       // Upload image process
       $imagePath = explode('/', $maintenanceRequestAfter->photo);
       $count = count($imagePath);
       $imageFolder = "maintenancerequest/" . $imagePath[$count - 2];
       $imageName = $imagePath[$count - 1];

       // Get just folder name
            $folderArray = explode('/', $maintenanceRequestAfter->photo );
            $folderName = "maintenancerequest/" . $folderArray[count($folderArray) - 1];

       // Finally upload image
        for($i=0;$i<$photoLength;$i++){

            $this->posmUploadName($content->photo[$i], $folderName, 'MaintenanceRequest');

        }


        return response()->json(['status' => true, 'id_transaksi' => $maintenanceRequestAfter->id, 'message' => 'Data berhasil di input']);

    }

}
