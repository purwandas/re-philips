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
use DB;
use File;

class MaintenanceReportController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $content){

        $user = JWTAuth::parseToken()->authenticate();

        // Get how many photo
        $photoLength = count($content->photo);

        // SET IMAGES FOLDER
        $folderPath = asset('image').'/maintenancerequest/'.$this->getRandomPath();

        try {
            DB::transaction(function () use ($content, $user, $folderPath) {

                /** Insert Maintenance Request **/

                $maintenanceRequest = MaintenanceRequest::create([
                    'user_id' => $user->id,
                    'region_id' => $content->region_id,
                    'area_id' => $content->area_id,
                    'store_id' => $content->id,
                    'category' => $content->category,
                    'channel' => $content->channel,
                    'type' => $content->type,
                    'report' => $content->report,
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
