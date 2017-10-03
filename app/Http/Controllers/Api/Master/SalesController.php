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

            $content = json_decode($request->getContent(), true);
            $user = JWTAuth::parseToken()->authenticate();   

            // TRANSACTION HEADER
            if($param == 1){

                // SELL IN
                $transaction = SellIn::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'date' => Carbon::now()
                            ]);

            }else if($param == 2){

                // SELL OUT
                $transaction = SellOut::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'date' => Carbon::now()
                            ]);

            }else if($param == 3){

                // RETURN DISTRIBUTOR
                $transaction = RetDistributor::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 4){

                // RETURN CONSUMENT
                $transaction = RetConsument::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 5){

                // FREE PRODUCT
                $transaction = FreeProduct::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
                            'date' => Carbon::now()
                            ]);
                
            }else if($param == 6){

                // TBAT
                $transaction = Tbat::create([
                            'user_id' => $user->id,
                            'store_id' => $content['id'],
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
            return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi']);
        }
    	
    	return response()->json(['status' => true, 'id_transaksi' => $transaction->id, 'message' => 'Data berhasil di input']);
    }
}
