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
use App\CompetitorActivity;
use App\CompetitorActivityDetail;
use File;
use DB;

class CompetitorActivityController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function store(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        if(!isset($request->sku) || $request->sku == null){
            return response()->json(['status' => false, 'message' => 'SKU tidak boleh kosong'], 500);
        }

        if(!isset($request->promo_type) || $request->promo_type == null){
            return response()->json(['status' => false, 'message' => 'Jenis Promo tidak boleh kosong'], 500);
        }

        if(!isset($request->information) || $request->information == null){
            return response()->json(['status' => false, 'message' => 'Keterangan tidak boleh kosong'], 500);
        }

        if(!isset($request->groupcompetitor_id)){
            return response()->json(['status' => false, 'message' => 'Harus memilih salah satu item atau lebih'], 500);
        }

        if(!isset($request->photo)){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        // Check promo activity header
        $competitorActivityHeader = CompetitorActivity::where('user_id', $user->id)
                                ->where('store_id', $request->store_id)
                                ->where('date', date('Y-m-d'))
                                ->where('sku', $request->sku)
                                ->where('groupcompetitor_id', $request->groupcompetitor_id)
                                ->where('promo_type', $request->promo_type)
                                ->where('information', $request->information)
                                ->where('start_period', Carbon::parse($request->start_period)->format('Y-m-d'))
                                ->where('end_period', Carbon::parse($request->end_period)->format('Y-m-d'))
                                ->first();

        $startPeriod = Carbon::parse($request->start_period);//->format('d F Y'); Year - Month - Day
        $endPeriod = Carbon::parse($request->end_period);//->format('d F Y');


        if($competitorActivityHeader){

//            try {
//
//                DB::transaction(function () use ($request, $user, $startPeriod, $endPeriod, $competitorActivityHeader) {
//
//                    /* Create Promo Activity Detail */
//                    $competitorActivityDetail = CompetitorActivityDetail::where('competitoractivity_id', $competitorActivityHeader->id)->where('groupcompetitor_id', $request->groupcompetitor_id[$i])->first();
//
//                    if(!$competitorActivityDetail){ // Create
//
//                        CompetitorActivityDetail::create([
//                            'competitoractivity_id' => $competitorActivityHeader->id,
//                            'groupcompetitor_id' => $request->groupcompetitor_id,
//                        ]);
//
//                    }
//
//
//
//                    /* Delete Image (Include Folder) */
//                    $imagePath = explode('/', $competitorActivityHeader->photo);
//                    $count = count($imagePath);
//                    $folderpath = $imagePath[$count - 2];
//                    File::deleteDirectory(public_path() . "/image/competitor/" . $folderpath);
//
//                    /* Upload image again, anda again, and again~ */
//                    $photo_url = "";
//
//                    if($request->photo){
//                        $photo_url = $this->getUploadPathName($request->photo, "competitor/".$this->getRandomPath(), 'COMPETITOR');
//                    }
//
//                    // Update photo
//                    $competitorActivityHeader->update([
//                        'photo' => $photo_url
//                    ]);
//
//                });
//
//            }catch (\Exception $e){
//                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
//            }
//
//            // Upload image process
//            $imagePath = explode('/', $competitorActivityHeader->photo);
//            $count = count($imagePath);
//            $imageFolder = "competitor/" . $imagePath[$count - 2];
//            $imageName = $imagePath[$count - 1];
//
//            $this->upload($request->photo, $imageFolder, $imageName);
//
//            return response()->json(['status' => true, 'id_transaksi' => $competitorActivityHeader->id, 'message' => 'Data berhasil di input']);

        } else {

            try {

                DB::transaction(function () use ($request, $user, $startPeriod, $endPeriod, $competitorActivityHeader) {

                    $photo_url = "";

                    if ($request->photo) {
                        $photo_url = $this->getUploadPathName($request->photo, "competitor/" . $this->getRandomPath(), 'COMPETITOR');
                    }

                    /* Create Competitor Activity */
                    $transaction = CompetitorActivity::create([
                        'user_id' => $user->id,
                        'store_id' => $request->store_id,
                        'week' => Carbon::now()->weekOfMonth,
                        'date' => Carbon::now(),
                        'sku' => $request->sku,
                        'groupcompetitor_id' => $request->groupcompetitor_id,
                        'promo_type' => $request->promo_type,
                        'information' => $request->information,
                        'start_period' => $startPeriod,
                        'end_period' => $endPeriod,
                        'photo' => $photo_url
                    ]);

//                    /* Create Competitor Activity Detail */
//                    CompetitorActivityDetail::create([
//                        'competitoractivity_id' => $transaction->id,
//                        'groupcompetitor_id' => $request->groupcompetitor_id,
//                    ]);

                });

            }catch (\Exception $e){
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check competitor activity header after insert
            $competitorActivityHeaderAfter = CompetitorActivity::where('user_id', $user->id)
                                    ->where('store_id', $request->store_id)
                                    ->where('date', date('Y-m-d'))
                                    ->where('sku', $request->sku)
                                    ->where('groupcompetitor_id', $request->groupcompetitor_id)
                                    ->where('promo_type', $request->promo_type)
                                    ->where('information', $request->information)
                                    ->where('start_period', Carbon::parse($request->start_period)->format('Y-m-d'))
                                    ->where('end_period', Carbon::parse($request->end_period)->format('Y-m-d'))
                                    ->first();

            // Upload image process
            $imagePath = explode('/', $competitorActivityHeaderAfter->photo);
            $count = count($imagePath);
            $imageFolder = "competitor/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo, $imageFolder, $imageName);

            return response()->json(['status' => true, 'id_transaksi' => $competitorActivityHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

    public function store2(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        if(!isset($request->sku) || $request->sku == null){
            return response()->json(['status' => false, 'message' => 'SKU tidak boleh kosong'], 500);
        }

        if(!isset($request->promo_type) || $request->promo_type == null){
            return response()->json(['status' => false, 'message' => 'Jenis Promo tidak boleh kosong'], 500);
        }

        if(!isset($request->information) || $request->information == null){
            return response()->json(['status' => false, 'message' => 'Keterangan tidak boleh kosong'], 500);
        }

        if(!isset($request->groupcompetitor_id)){
            return response()->json(['status' => false, 'message' => 'Harus memilih salah satu item atau lebih'], 500);
        }

        if(!isset($request->photo)){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        // Check promo activity header
        $competitorActivityHeader = CompetitorActivity::where('user_id', $user->id)
                                ->where('store_id', $request->store_id)
                                ->where('date', date('Y-m-d'))
                                ->where('sku', $request->sku)
                                ->where('promo_type', $request->promo_type)
                                ->where('information', $request->information)
                                ->where('start_period', Carbon::parse($request->start_period)->format('Y-m-d'))
                                ->where('end_period', Carbon::parse($request->end_period)->format('Y-m-d'))
                                ->first();

        $dataLength = count($request->groupcompetitor_id);

        $startPeriod = Carbon::parse($request->start_period);//->format('d F Y'); Year - Month - Day
        $endPeriod = Carbon::parse($request->end_period);//->format('d F Y');


        if($competitorActivityHeader){

            try {

                DB::transaction(function () use ($request, $user, $dataLength, $startPeriod, $endPeriod, $competitorActivityHeader) {

                    /* Create Promo Activity Detail */
                    for ($i = 0; $i < $dataLength; $i++) {

                        $competitorActivityDetail = CompetitorActivityDetail::where('competitoractivity_id', $competitorActivityHeader->id)->where('groupcompetitor_id', $request->groupcompetitor_id[$i])->first();

                        if(!$competitorActivityDetail){ // Create

                            CompetitorActivityDetail::create([
                                'competitoractivity_id' => $competitorActivityHeader->id,
                                'groupcompetitor_id' => $request->groupcompetitor_id[$i],
                            ]);

                        }

                    }

                    /* Delete Image (Include Folder) */
                    $imagePath = explode('/', $competitorActivityHeader->photo);
                    $count = count($imagePath);
                    $folderpath = $imagePath[$count - 2];
                    File::deleteDirectory(public_path() . "/image/competitor/" . $folderpath);

                    /* Upload image again, anda again, and again~ */
                    $photo_url = "";

                    if($request->photo){
                        $photo_url = $this->getUploadPathName($request->photo, "competitor/".$this->getRandomPath(), 'COMPETITOR');
                    }

                    // Update photo
                    $competitorActivityHeader->update([
                        'photo' => $photo_url
                    ]);

                });

            }catch (\Exception $e){
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Upload image process
            $imagePath = explode('/', $competitorActivityHeader->photo);
            $count = count($imagePath);
            $imageFolder = "competitor/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo, $imageFolder, $imageName);

            return response()->json(['status' => true, 'id_transaksi' => $competitorActivityHeader->id, 'message' => 'Data berhasil di input']);

        } else {

            try {

                DB::transaction(function () use ($request, $user, $dataLength, $startPeriod, $endPeriod, $competitorActivityHeader) {

                    $photo_url = "";

                    if ($request->photo) {
                        $photo_url = $this->getUploadPathName($request->photo, "competitor/" . $this->getRandomPath(), 'COMPETITOR');
                    }

                    /* Create Competitor Activity */
                    $transaction = CompetitorActivity::create([
                        'user_id' => $user->id,
                        'store_id' => $request->store_id,
                        'week' => Carbon::now()->weekOfMonth,
                        'date' => Carbon::now(),
                        'sku' => $request->sku,
                        'promo_type' => $request->promo_type,
                        'information' => $request->information,
                        'start_period' => $startPeriod,
                        'end_period' => $endPeriod,
                        'photo' => $photo_url
                    ]);

                    /* Create Competitor Activity Detail */
                    for ($i = 0; $i < $dataLength; $i++) {

                        CompetitorActivityDetail::create([
                            'competitoractivity_id' => $transaction->id,
                            'groupcompetitor_id' => $request->groupcompetitor_id[$i],
                        ]);

                    }

                });

            }catch (\Exception $e){
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check competitor activity header after insert
            $competitorActivityHeaderAfter = CompetitorActivity::where('user_id', $user->id)
                                    ->where('store_id', $request->store_id)
                                    ->where('date', date('Y-m-d'))
                                    ->where('sku', $request->sku)
                                    ->where('promo_type', $request->promo_type)
                                    ->where('information', $request->information)
                                    ->where('start_period', Carbon::parse($request->start_period)->format('Y-m-d'))
                                    ->where('end_period', Carbon::parse($request->end_period)->format('Y-m-d'))
                                    ->first();

            // Upload image process
            $imagePath = explode('/', $competitorActivityHeaderAfter->photo);
            $count = count($imagePath);
            $imageFolder = "competitor/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo, $imageFolder, $imageName);

            return response()->json(['status' => true, 'id_transaksi' => $competitorActivityHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

}
