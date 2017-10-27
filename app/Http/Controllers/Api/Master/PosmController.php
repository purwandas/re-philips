<?php

namespace App\Http\Controllers\Api\Master;

use App\Posm;
use App\PosmActivityDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Mockery\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\PosmActivity;
use File;
use DB;

class PosmController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        if(!isset($request->photo)){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        // Check posm header
        $posmHeader = PosmActivity::where('user_id', $user->id)->where('store_id', $request->store_id)->where('date', date('Y-m-d'))->first();

        // Get how many data
        $dataLength = count($request->posm_id);

        /* Check if image not match with other input count number */
        if(count($request->photo) != $dataLength) {
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh ada yang kosong'], 500);
        }

        if ($posmHeader) { // If header exist (update and/or create detail)

            try {

                DB::transaction(function () use ($request, $posmHeader, $user, $dataLength) {

                    $arrayUpdate = [];

                    for($i=0;$i<$dataLength;$i++){

                        $posmActivityDetail = PosmActivityDetail::where('posmactivity_id', $posmHeader->id)->where('posm_id', $request->posm_id[$i])->first();

                        if ($posmActivityDetail) { // If data exist -> update

                            /*
                             *  Check request quantity is integer
                             *  In update function if quantity + $request->quantity,
                             *  $request->quantity not count as string but
                             *  exception that to 0 value.
                             */
                            if(!is_numeric($request->quantity[$i])){
                                throw new Exception();
                            }

                            /* Delete Image (Just get path, Delete later) */
                            $imagePath = explode('/', $posmActivityDetail->photo);
                            $count = count($imagePath);
                            $folderpath = $imagePath[$count - 2];
//                            File::deleteDirectory(public_path() . "/image/posm/" . $folderpath);
//                            array_push($arrayUpdate, { "id" : $posmActivityDetail->id, "path" : $folderpath });
                            $newVal = array('id' => $posmActivityDetail->id, 'path' => $folderpath);
                            array_push($arrayUpdate, $newVal);

                            /* Upload image again, anda again, and again~ */
                            $photo_url = "";

                            if($request->photo[$i]){
                                $photo_url = $this->getUploadPath($request->photo[$i], "posm/".$this->getRandomPath());
                            }

                            $posmActivityDetail->update([
                                'quantity' => $posmActivityDetail->quantity + $request->quantity[$i],
                                'photo' => $photo_url
                            ]);

                        } else { // If data didn't exist -> create

                            $photo_url = "";

                            if($request->photo[$i]){
                                $photo_url = $this->getUploadPath($request->photo[$i], "posm/".$this->getRandomPath());
                            }

                            PosmActivityDetail::create([
                                'posmactivity_id' => $posmHeader->id,
                                'posm_id' => $request->posm_id[$i],
                                'quantity' => $request->quantity[$i],
                                'photo' => $photo_url
                            ]);

                        }

                    }

                    /* Delete image all updated photo */
                    foreach ($arrayUpdate as $data){
                        File::deleteDirectory(public_path() . "/image/posm/" . $data['path']);
                    }


                });

            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Finally upload image for every data in details
            for($i=0;$i<$dataLength;$i++){

                $posmActivityDetail = PosmActivityDetail::where('posmactivity_id', $posmHeader->id)->where('posm_id', $request->posm_id[$i])->first();

                // Get folder and file name
                $imagePath = explode('/', $posmActivityDetail->photo);
                $count = count($imagePath);
                $imageFolder = "posm/" . $imagePath[$count - 2];
                $imageName = $imagePath[$count - 1];

                $this->upload($request->photo[$i], $imageFolder, $imageName);

            }

            return response()->json(['status' => true, 'id_transaksi' => $posmHeader->id, 'message' => 'Data berhasil di input']);

        }else { // If header didn't exist (create header & detail)

            try {
                DB::transaction(function () use ($request, $user, $dataLength) {

                    // HEADER
                    $transaction = PosmActivity::create([
                                        'user_id' => $user->id,
                                        'store_id' => $request->store_id,
                                        'week' => Carbon::now()->weekOfMonth,
                                        'date' => Carbon::now()
                                    ]);

                    // DETAILS
                    for($i=0;$i<$dataLength;$i++){

                        $photo_url = "";

//                        if($request->photo[$i]){
//                            $photo_url = $this->imageUpload($request->photo[$i], "posm/".$this->getRandomPath());
//                        }

                        if($request->photo[$i]){
                            $photo_url = $this->getUploadPath($request->photo[$i], "posm/".$this->getRandomPath());
                        }

                        PosmActivityDetail::create([
                            'posmactivity_id' => $transaction->id,
                            'posm_id' => $request->posm_id[$i],
                            'quantity' => $request->quantity[$i],
                            'photo' => $photo_url
                        ]);

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check posm activity header after insert
            $posmActivityHeaderAfter = PosmActivity::where('user_id', $user->id)->where('store_id', $request->store_id)->where('date', date('Y-m-d'))->first();

            // Finally upload image for every data in details
            for($i=0;$i<$dataLength;$i++){

                $posmActivityDetail = PosmActivityDetail::where('posmactivity_id', $posmActivityHeaderAfter->id)->where('posm_id', $request->posm_id[$i])->first();

                // Get folder and file name
                $imagePath = explode('/', $posmActivityDetail->photo);
                $count = count($imagePath);
                $imageFolder = "posm/" . $imagePath[$count - 2];
                $imageName = $imagePath[$count - 1];

                $this->upload($request->photo[$i], $imageFolder, $imageName);

            }

            return response()->json(['status' => true, 'id_transaksi' => $posmActivityHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

    public function all($param)
    {
    	$data = Posm::join('group_products', 'posms.groupproduct_id', '=', 'group_products.id')
    				   ->where('group_products.id', $param)
    				   ->select('posms.id', 'posms.name')
    				   ->get();

    	return response()->json($data);
    }
}
