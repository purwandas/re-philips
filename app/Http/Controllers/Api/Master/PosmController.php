<?php

namespace App\Http\Controllers\Api\Master;

use App\PosmActivityDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\PosmActivity;
use File;

class PosmController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        if(!isset($request->photo)){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        try{

            $dataLength = count($request->quantity);

            /* Check if image not match with other input count number */
            if(count($request->photo) != $dataLength){
                return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
            }

            /* Create POSM Activity */
            $transaction = PosmActivity::create([
                                    'user_id' => $user->id,
                                    'store_id' => $request->store_id,
                                    'date' => Carbon::now(),
                                ]);


            /* Create POSM Activity Detail */
            for($i=0;$i<$dataLength;$i++){

                $photo_url = "";

                if($request->photo[$i]){
                    $photo_url = $this->imageUpload($request->photo[$i], "posm/".$this->getRandomPath());
                }

                PosmActivityDetail::create([
                    'posmactivity_id' => $transaction->id,
                    'posm_id' => $request->posm_id[$i],
                    'quantity' => $request->quantity[$i],
                    'photo' => $photo_url
                ]);

            }

        }catch (\Exception $e){

            if(isset($transaction)) {
                /*
                 * Delete data that have been inserted before
                 */

                /* Delete Details */
                $details = PosmActivityDetail::where('posmactivity_id', $transaction->id);

                /* Delete Image (Include Folder) */
                foreach ($details->get() as $detail) {
                    $imagePath = explode('/', $detail->photo);
                    $count = count($imagePath);
                    $folderpath = $imagePath[$count - 2];

                    File::deleteDirectory(public_path() . "/image/posm/" . $folderpath);
                }

                $details->forceDelete();

                /* Delete Header */
                PosmActivity::find($transaction->id)->forceDelete();
            }

            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
        }

        return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);

    }
}
