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
use DB;

class PromoActivityController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $request){

        $start_period = str_replace('"', '', $request->start_period);
        $end_period = str_replace('"', '', $request->end_period);

        // // $date = Carbon::parse($request->);

        // return response()->json(['status' => true, 'message' => $start_period]);

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

        // Check promo activity header
        $promoActivityHeader = PromoActivity::where('user_id', $user->id)
                                ->where('store_id', $request->store_id)
                                ->where('date', date('Y-m-d'))
                                ->where('promo_type', $request->promo_type)
                                ->where('information', $request->information)
                                ->where('start_period', Carbon::parse($start_period)->format('Y-m-d'))
                                ->where('end_period', Carbon::parse($end_period)->format('Y-m-d'))
                                ->first();

        // Get how many data
        $dataLength = count($request->product_id);

        $startPeriod = Carbon::parse($start_period);//->format('d F Y'); Year - Month - Day
        $endPeriod = Carbon::parse($end_period);//->format('d F Y');

        if($promoActivityHeader){

            try{

                DB::transaction(function () use ($request, $user, $dataLength, $startPeriod, $endPeriod, $promoActivityHeader) {

                    /* Create Promo Activity Detail */
                    for ($i = 0; $i < $dataLength; $i++) {

                        $promoActivityDetail = PromoActivityDetail::where('promoactivity_id', $promoActivityHeader->id)->where('product_id', $request->product_id[$i])->first();

                        if(!$promoActivityDetail){ // Create

                            PromoActivityDetail::create([
                                'promoactivity_id' => $promoActivityHeader->id,
                                'product_id' => $request->product_id[$i],
                            ]);

                        }

                    }

                    /* Delete Image (Include Folder) */
                    $imagePath = explode('/', $promoActivityHeader->photo);
                    $count = count($imagePath);
                    $folderpath = $imagePath[$count - 2];
                    File::deleteDirectory(public_path() . "/image/promo/" . $folderpath);

                    /* Upload image again, anda again, and again~ */
                    $photo_url = "";

                    if($request->photo){
                        $photo_url = $this->getUploadPathName($request->photo, "promo/".$this->getRandomPath(), 'PROMO');
                    }

                    // Update photo to null
                    $promoActivityHeader->update([
                        'photo' => $photo_url
                    ]);

                });

            }catch (\Exception $e){
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Upload image process
            $imagePath = explode('/', $promoActivityHeader->photo);
            $count = count($imagePath);
            $imageFolder = "promo/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo, $imageFolder, $imageName);

            return response()->json(['status' => true, 'id_transaksi' => $promoActivityHeader->id, 'message' => 'Data berhasil di input']);

        } else {

            try {

                DB::transaction(function () use ($request, $user, $dataLength, $startPeriod, $endPeriod, $promoActivityHeader) {

                    $photo_url = "";

                    if ($request->photo) {
                        $photo_url = $this->getUploadPathName($request->photo, "promo/" . $this->getRandomPath(), 'PROMO');
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
                    for ($i = 0; $i < $dataLength; $i++) {

                        PromoActivityDetail::create([
                            'promoactivity_id' => $transaction->id,
                            'product_id' => $request->product_id[$i],
                        ]);

                    }

                });

            }catch (\Exception $e){
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check posm activity header after insert
            $promoActivityHeaderAfter = PromoActivity::where('user_id', $user->id)
                                    ->where('store_id', $request->store_id)
                                    ->where('date', date('Y-m-d'))
                                    ->where('promo_type', $request->promo_type)
                                    ->where('information', $request->information)
                                    ->where('start_period', Carbon::parse($start_period)->format('Y-m-d'))
                                    ->where('end_period', Carbon::parse($end_period)->format('Y-m-d'))
                                    ->first();

            // Upload image process
            $imagePath = explode('/', $promoActivityHeaderAfter->photo);
            $count = count($imagePath);
            $imageFolder = "promo/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo, $imageFolder, $imageName);

            return response()->json(['status' => true, 'id_transaksi' => $promoActivityHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }
}
