<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\SOH;
use App\SOHDetail;
use DB;

class SOHController extends Controller
{
    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $user = JWTAuth::parseToken()->authenticate();

        // Check SOH header
        $sohHeader = SOH::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

        if ($sohHeader) { // If header exist (update and/or create detail)

            try {
                DB::transaction(function () use ($content, $sohHeader, $user) {

                    foreach ($content['data'] as $data) {

                        $sohDetail = SOHDetail::where('soh_id', $sohHeader->id)->where('product_id', $data['product_id'])->first();

                        if ($sohDetail) { // If data exist -> update

                            $sohDetail->update([
                                'quantity' => $sohDetail->quantity + $data['quantity']
                            ]);

                        } else { // If data didn't exist -> create

                            SOHDetail::create([
                                'soh_id' => $sohHeader->id,
                                'product_id' => $data['product_id'],
                                'quantity' => $data['quantity']
                            ]);

                        }

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            return response()->json(['status' => true, 'id_transaksi' => $sohHeader->id, 'message' => 'Data berhasil di input']);

        } else { // If header didn't exist (create header & detail)

            try {
                DB::transaction(function () use ($content, $user) {

                    // HEADER
                    $transaction = SOH::create([
                                        'user_id' => $user->id,
                                        'store_id' => $content['id'],
                                        'week' => Carbon::now()->weekOfMonth,
                                        'date' => Carbon::now()
                                    ]);

                    foreach ($content['data'] as $data) {

                        // DETAILS
                        SOHDetail::create([
                                'soh_id' => $transaction->id,
                                'product_id' => $data['product_id'],
                                'quantity' => $data['quantity']
                            ]);

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
            }

            // Check soh header after insert
            $sohHeaderAfter = SOH::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', date('Y-m-d'))->first();

            return response()->json(['status' => true, 'id_transaksi' => $sohHeaderAfter->id, 'message' => 'Data berhasil di input']);

        }

    }

	public function tes(Request $request)
	{
		return 'tes';
	}

	/*
    public function store2(Request $request)
    {
    	try
    	{
    		$content = json_decode($request->getContent(),true);
    		$user = JWTAuth::parseToken()->authenticate();

    		//Transaction Header SOH
    		$transaction = Soh::create
    		([
    			'user_id'	=>$user->id,
    			'store_id'	=>$content['id'],
                'week' => Carbon::now()->weekOfMonth,
    			'date'		=>Carbon::now()
    		]);

    		// Transaction Details
    		foreach ($content['data'] as $data) {
    			SohDetail::create
    			([
    				'soh_id'	=> $transaction->id,
    				'product_id'	=> $data['product_id'],
    				'quantity'	=> $data['quantity']
    			]);
    		}
    	} 
    	catch (\Exception $e)
    	{
    		// Delete Inserted data
    		if (isset($transaction)) {
    			// Delete Detail first
    			$details= SohDetail::where('soh_id',$transaction->id);
    			$details->forceDelete();

    			// Delete Header then
    			Soh::find($transaction->id)->forceDelete();
    		}
    		return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi']);
    	}

    	return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);
    }
	*/
}
