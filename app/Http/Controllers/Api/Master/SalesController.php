<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\RetConsument;
use App\RetConsumentDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Tbat;
use App\TbatDetail;

class SalesController extends Controller
{
    //
    public function store(Request $request, $param)
    {   
        try {

            // Decode buat inputan raw body
            $content = json_decode($request->getContent(), true);
            $user = JWTAuth::parseToken()->authenticate();   

            // TRANSACTION HEADER
            if($param == 1){

                // SELL IN
                $transaction = SellIn::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);

            }else if($param == 2){

                // SELL OUT
                $transaction = SellOut::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);

            }else if($param == 3){

                // RETURN DISTRIBUTOR
                $transaction = RetDistributor::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 4){

                // RETURN CONSUMENT
                $transaction = RetConsument::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 5){

                // FREE PRODUCT
                $transaction = FreeProduct::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 6){

                // TBAT
                $transaction = Tbat::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'week' => Carbon::now()->weekOfMonth,
                            'date' => Carbon::now()
                            ]);
                
            }


            // TRANSACTION DETAILS
            foreach ($content['data'] as $data) {                

                if($param == 1){

                    // SELL IN
                    SellInDetail::create([
                        'sellin_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);

                }else if($param == 2){

                    // SELL OUT
                    SellOutDetail::create([
                        'sellout_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);

                }else if($param == 3){

                    // RETURN DISTRIBUTOR
                    RetDistributorDetail::create([
                        'retdistributor_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);
                    
                }else if($param == 4){

                    // RETURN CONSUMENT
                    RetConsumentDetail::create([
                        'retconsument_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);
                    
                }else if($param == 5){

                    // FREE PRODUCT
                    FreeProductDetail::create([
                        'freeproduct_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);
                    
                }else if($param == 6){

                    // TBAT
                    TbatDetail::create([
                        'tbat_id' => $transaction->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity']
                        ]);
                    
                }

            }

        } catch (\Exception $e) {

            /* Delete data that have been inserted before */
            if(isset($transaction)){
                if($param == 1){
                    /* Delete Details */
                    $details = SellInDetail::where('sellin_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    SellIn::find($transaction->id)->forceDelete();
                }else if($param == 2){
                    /* Delete Details */
                    $details = SellOutDetail::where('sellout_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    SellOut::find($transaction->id)->forceDelete();
                }else if($param == 3){
                    /* Delete Details */
                    $details = RetDistributorDetail::where('retdistributor_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    RetDistributor::find($transaction->id)->forceDelete();
                }else if($param == 4){
                    /* Delete Details */
                    $details = RetConsumentDetail::where('retconsument_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    RetConsument::find($transaction->id)->forceDelete();
                }else if($param == 5){
                    /* Delete Details */
                    $details = FreeProductDetail::where('freeproduct_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    FreeProduct::find($transaction->id)->forceDelete();
                }else if($param == 6){
                    /* Delete Details */
                    $details = TbatDetail::where('tbat_id', $transaction->id);
                    $details->forceDelete();

                    /* Delete Header */
                    Tbat::find($transaction->id)->forceDelete();
                }
            }

            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
        }
    	
    	return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);
    }
}
