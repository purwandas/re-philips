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

class SOSController extends Controller
{
    public function store(Request $request)
    {   
        try {

            $content = json_decode($request->getContent(), true);
            $user = JWTAuth::parseToken()->authenticate();   

            // TRANSACTION HEADER SOS
            $transaction = Sos::create([
	            'user_id' => $user->id,
	            'store_id' => $content['id'],
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
            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi ('.$e.')']);
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
}
