<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\PromoActivity;
use App\PromoActivityDetail;
use File;

class PromoActivityController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $request){

//        return response()->json($request->all());

        $user = JWTAuth::parseToken()->authenticate();

        if(!isset($request->promo_type) || $request->promo_type == null){
            return response()->json(['status' => false, 'message' => 'Jenis Promo tidak boleh kosong'], 500);
        }

        if(!isset($request->information) || $request->information == null){
            return response()->json(['status' => false, 'message' => 'Keterangan tidak boleh kosong'], 500);
        }

        if(!isset($request->product_id)){
            return response()->json(['status' => false, 'message' => 'Harus memilih salah satu item atau lebih'], 500);
        }

        if(!isset($request->photo) || $request->photo == null){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        try{

            $dataLength = count($request->product_id);

            $startPeriod = Carbon::parse($request->start_period);//->format('d F Y'); Year - Month - Day
            $endPeriod = Carbon::parse($request->end_period);//->format('d F Y');

            $photo_url = "";

            if($request->photo){
                $photo_url = $this->imageUpload($request->photo, "promo/".$this->getRandomPath());
            }

            /* Create Promo Activity */
            $transaction = PromoActivity::create([
                                    'user_id' => $user->id,
                                    'store_id' => $request->store_id,
                                    'week' => Carbon::now()->weekOfMonth,
                                    'date' => Carbon::now(),
                                    'promo_type' => $request->promo_type,
                                    'information' => $request->information,
                                    'start_period' => $startPeriod,
                                    'end_period' => $endPeriod,
                                    'photo' => $photo_url
                                ]);


            /* Create Promo Activity Detail */
            for($i=0;$i<$dataLength;$i++){

                PromoActivityDetail::create([
                    'promoactivity_id' => $transaction->id,
                    'product_id' => $request->product_id[$i],
                ]);

            }

        }catch (\Exception $e){

            if(isset($transaction)) {
                /*
                 * Delete data that have been inserted before
                 */

                /* Delete Details */
                $details = PromoActivityDetail::where('promoactivity_id', $transaction->id);
                $details->forceDelete();

                /* Delete Image (Include Folder) */
                $imagePath = explode('/', $transaction->photo);
                $count = count($imagePath);
                $folderpath = $imagePath[$count - 2];
                File::deleteDirectory(public_path() . "/image/promo/" . $folderpath);

                /* Delete Header */
                PromoActivity::find($transaction->id)->forceDelete();
            }

            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
        }

        return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);

    }
}
