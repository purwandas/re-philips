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

class SOHController extends Controller
{
	public function tes(Request $request)
	{
		return 'tes';
	}
    public function store(Request $request)
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
}
