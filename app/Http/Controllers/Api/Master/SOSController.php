<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Sos;
use App\SosDetail;
use DB;

class SOSController extends Controller
{
    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $user = JWTAuth::parseToken()->authenticate();

        // Check Sos header
        $sosHeader = Sos::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

        if ($sosHeader) { // If header exist (update and/or create detail)

            try {
                DB::transaction(function () use ($content, $sosHeader, $user) {

                    foreach ($content['data'] as $data) {

                        $sosDetail = SosDetail::where('sos_id', $sosHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($sosDetail) { // If data exist -> update

                            $sosDetail->update([
                                'quantity' => $sosDetail->quantity + $data['quantity']
                            ]);

                        } else { // If data didn't exist -> create

                            SosDetail::create([
                                'sos_id' => $sosHeader->id,
                                'product_id' => $data['product_id'],
                                'quantity' => $data['quantity']
                            ]);

                        }

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            return response()->json(['status' => true, 'id_transaksi' => $sosHeader->id, 'message' => 'Data berhasil di input']);

        } else { // If header didn't exist (create header & detail)

            try {
                DB::transaction(function () use ($content, $user) {

                    // HEADER
                    $transaction = Sos::create([
                                        'user_id' => $user->id,
                                        'store_id' => $content['id'],
                                        'week' => Carbon::now()->weekOfMonth,
                                        'date' => Carbon::now()
                                    ]);

                    foreach ($content['data'] as $data) {

                        // DETAILS
                        SosDetail::create([
                                'sos_id' => $transaction->id,
                                'product_id' => $data['product_id'],
                                'quantity' => $data['quantity']
                            ]);

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check sos header after insert
            $sosHeaderAfter = Sos::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            return response()->json(['status' => true, 'id_transaksi' => $sosHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

    /*
    public function store2(Request $request)
    {
       try {

            $content = json_decode($request->getContent(), true);
            $user = JWTAuth::parseToken()->authenticate();

            // TRANSACTION HEADER SOS
            $transaction = Sos::create([
	            'user_id' => $user->id,
	            'store_id' => $content['id'],
                'week' => Carbon::now()->weekOfMonth,
	            'date' => Carbon::now()
            ]);


            // TRANSACTION DETAILS
            foreach ($content['data'] as $data) {
                SosDetail::create([
                    'sos_id' => $transaction->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity']
                ]);
	        }

       } catch (\Exception $e) {
            // Delete Inserted data
            if (isset($transaction)) {
                // Delete Detail first
                $details= SosDetail::where('sos_id',$transaction->id);
                $details->forceDelete();

                // Delete Header then
                Sos::find($transaction->id)->forceDelete();
            }
           return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi']);
       }

    	return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);

    	// $user = JWTAuth::parseToken()->authenticate();
    	// $content = json_decode($request->getContent(), true);
    	// $hasil='';
    	// foreach ($content['data'] as $data) {
    	// 	$hasil.=$data['product_id']."->".$data['quantity'];
    	// }
    	// return $hasil." #".$user->id;
    }
    */
}
