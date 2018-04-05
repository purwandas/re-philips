<?php

namespace App\Http\Controllers\Api\Master;

use App\Traits\ActualTrait;
use App\Traits\PromoterTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use JWTAuth;
use Auth;
use DB;
use App\Price;
use App\Product;
use App\Store;
use App\EmployeeStore;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\DmArea;
use App\User;
use App\TrainerArea;
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
use App\Soh;
use App\SohDetail;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\PosmActivity;
use App\PosmActivityDetail;

class SalesHistoryController extends Controller
{
    use ActualTrait;

    public function getData($param){

        $user = JWTAuth::parseToken()->authenticate();

            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $date1 = "$year-$month-01";
            $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

        if($param == 1) { /* SELL IN (SELL THRU) */

            $header = SellIn::whereNull('sell_ins.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('sell_ins.user_id',$user->id)
                    ->join('users','users.id','sell_ins.user_id')
                    ->join('stores','stores.id','sell_ins.store_id')
                    ->select('sell_ins.id','sell_ins.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellInDetail::where('sellin_id', $value->id)
                    ->join('products','products.id','sell_in_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_in_details.id as detail_id','products.name as product_name','sell_in_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_in_details.id as sell_in_detail_id','products.id as product_id', 'sell_in_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 2) { /* SELL OUT */

            $header = SellOut::whereNull('sell_outs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('sell_outs.user_id',$user->id)
                    ->join('users','users.id','sell_outs.user_id')
                    ->join('stores','stores.id','sell_outs.store_id')
                    ->select('sell_outs.id','sell_outs.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellOutDetail::where('sellout_id', $value->id)
                    ->join('products','products.id','sell_out_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_out_details.id as detail_id','products.name as product_name','sell_out_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_out_details.id as sell_out_details','products.id as product_id', 'sell_out_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 3) { /* RETURN DISTRIBUTOR */
            $header = RetDistributor::whereNull('ret_distributors.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('ret_distributors.user_id',$user->id)
                    ->join('users','users.id','ret_distributors.user_id')
                    ->join('stores','stores.id','ret_distributors.store_id')
                    ->select('ret_distributors.id','ret_distributors.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetDistributorDetail::where('retdistributor_id', $value->id)
                    ->join('products','products.id','ret_distributor_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_distributor_details.id as detail_id','products.name as product_name','ret_distributor_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_distributor_details.id as ret_distributor_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 4) { /* RETURN CONSUMENT */
            
            $header = RetConsument::whereNull('ret_consuments.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('ret_consuments.user_id',$user->id)
                    ->join('users','users.id','ret_consuments.user_id')
                    ->join('stores','stores.id','ret_consuments.store_id')
                    ->select('ret_consuments.id','ret_consuments.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetConsumentDetail::where('retconsument_id', $value->id)
                    ->join('products','products.id','ret_consument_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_consument_details.id as detail_id','products.name as product_name','ret_consument_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_consument_details.id as ret_consument_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 5) { /* FREE PRODUCT */

            $header = FreeProduct::whereNull('free_products.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('free_products.user_id',$user->id)
                    ->join('users','users.id','free_products.user_id')
                    ->join('stores','stores.id','free_products.store_id')
                    ->select('free_products.id','free_products.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = FreeProductDetail::where('freeproduct_id', $value->id)
                    ->join('products','products.id','free_product_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('free_product_details.id as detail_id','products.name as product_name','free_product_details.quantity','products.model','categories.name as category_name','groups.name as group_name','free_product_details.id as free_product_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 6) { /* TBAT */

            $header = Tbat::whereNull('tbats.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('tbats.user_id',$user->id)
                    ->join('users','users.id','tbats.user_id')
                    ->join('stores','stores.id','tbats.store_id')
                    ->join('stores as storeD','storeD.id','tbats.store_destination_id')
                    ->select('tbats.id','tbats.date as date','stores.store_name_1','stores.store_id','storeD.store_name_1 as storeD_name_1','storeD.store_id as storeD_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = TbatDetail::where('tbat_id', $value->id)
                    ->join('products','products.id','tbat_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('tbat_details.id as detail_id','products.name as product_name','tbat_details.quantity','products.model','categories.name as category_name','groups.name as group_name','tbat_details.id as tbat_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['store_destination_name_1'] = $value->storeD_name_1;
                        $result[$key]['store_destination_id'] = $value->storeD_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        } else if($param == 7) { /* Stock On Hand */

            $header = Soh::whereNull('sohs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('sohs.user_id',$user->id)
                    ->join('users','users.id','sohs.user_id')
                    ->join('stores','stores.id','sohs.store_id')
                    ->select('sohs.id','sohs.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SohDetail::where('soh_id', $value->id)
                    ->join('products','products.id','soh_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('soh_details.id as detail_id','products.name as product_name','soh_details.quantity','products.model','categories.name as category_name','groups.name as group_name','soh_details.id as soh_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        } else if($param == 8) { /* Display Share */

            $header = DisplayShare::whereNull('display_shares.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('display_shares.user_id',$user->id)
                    ->join('users','users.id','display_shares.user_id')
                    ->join('stores','stores.id','display_shares.store_id')
                    ->select('display_shares.id','display_shares.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = DisplayShareDetail::where('display_share_id', $value->id)
                    ->join('categories','categories.id','display_share_details.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('display_share_details.id as detail_id','display_share_details.philips','display_share_details.all','categories.name as category_name','groups.name as group_name','display_share_details.id as tbat_detail_id','categories.id as category_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        }  else if($param == 9) { /* POSM Activity */

            $header = PosmActivity::whereNull('posm_activities.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->where('posm_activities.user_id',$user->id)
                    ->join('users','users.id','posm_activities.user_id')
                    ->join('stores','stores.id','posm_activities.store_id')
                    ->select('posm_activities.id','posm_activities.date as date','stores.store_name_1','stores.store_id')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = PosmActivityDetail::where('posmactivity_id', $value->id)
                    ->join('posms','posms.id','posm_activity_details.posm_id')
                    ->join('groups','groups.id','posms.group_id')
                    ->select('posm_activity_details.id as detail_id','posm_activity_details.quantity','posms.name as posm_name','groups.name as group_name','posm_activity_details.id as tbat_detail_id','posms.id as posm_id','posm_activity_details.photo')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        }

    }

    public function getDataUser($param){
        

        $user = JWTAuth::parseToken()->authenticate();
        
        // get promoter ID
        $store = Store::where('user_id', $user->id)->get(); //->pluck('distributor_id');
        $employeeStore = '';
        // $index = 0;
        foreach ($store as $key => $value) {
            $employeeStore = EmployeeStore::where('store_id',$value->id)->get();
            foreach ($employeeStore as $key2 => $value2) {
                $users[] = $value2->user_id;
            }
        }
        if (!isset($users)) {
            return response()->json(['status' => false, 'message' => 'No data found'], 500);
        }
        $promoter = User::whereIn('id', $users)->pluck('id');

        // $newParam = explode('`', $param);
        // $param = $newParam[0];
        // $promoter = ["$newParam[1]"];

            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $date1 = "$year-$month-01";
            $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

        if($param == 1) { /* SELL IN (SELL THRU)*/

            $header = SellIn::whereNull('sell_ins.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sell_ins.user_id',$promoter)
                    ->join('users','users.id','sell_ins.user_id')
                    ->join('stores','stores.id','sell_ins.store_id')
                    ->select('sell_ins.id','sell_ins.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellInDetail::where('sellin_id', $value->id)
                    ->join('products','products.id','sell_in_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_in_details.id as detail_id','products.name as product_name','sell_in_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_in_details.id as sell_in_detail_id','products.id as product_id', 'sell_in_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 2) { /* SELL OUT */

            $header = SellOut::whereNull('sell_outs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sell_outs.user_id',$promoter)
                    ->join('users','users.id','sell_outs.user_id')
                    ->join('stores','stores.id','sell_outs.store_id')
                    ->select('sell_outs.id','sell_outs.date as date','stores.store_name_1','stores.store_id','users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellOutDetail::where('sellout_id', $value->id)
                    ->join('products','products.id','sell_out_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_out_details.id as detail_id','products.name as product_name','sell_out_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_out_details.id as sell_out_details','products.id as product_id', 'sell_out_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 3) { /* RETURN DISTRIBUTOR */
            $header = RetDistributor::whereNull('ret_distributors.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('ret_distributors.user_id',$promoter)
                    ->join('users','users.id','ret_distributors.user_id')
                    ->join('stores','stores.id','ret_distributors.store_id')
                    ->select('ret_distributors.id','ret_distributors.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetDistributorDetail::where('retdistributor_id', $value->id)
                    ->join('products','products.id','ret_distributor_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_distributor_details.id as detail_id','products.name as product_name','ret_distributor_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_distributor_details.id as ret_distributor_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 4) { /* RETURN CONSUMENT */
            
            $header = RetConsument::whereNull('ret_consuments.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('ret_consuments.user_id',$promoter)
                    ->join('users','users.id','ret_consuments.user_id')
                    ->join('stores','stores.id','ret_consuments.store_id')
                    ->select('ret_consuments.id','ret_consuments.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetConsumentDetail::where('retconsument_id', $value->id)
                    ->join('products','products.id','ret_consument_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_consument_details.id as detail_id','products.name as product_name','ret_consument_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_consument_details.id as ret_consument_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 5) { /* FREE PRODUCT */

            $header = FreeProduct::whereNull('free_products.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('free_products.user_id',$promoter)
                    ->join('users','users.id','free_products.user_id')
                    ->join('stores','stores.id','free_products.store_id')
                    ->select('free_products.id','free_products.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = FreeProductDetail::where('freeproduct_id', $value->id)
                    ->join('products','products.id','free_product_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('free_product_details.id as detail_id','products.name as product_name','free_product_details.quantity','products.model','categories.name as category_name','groups.name as group_name','free_product_details.id as free_product_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 6) { /* TBAT */

            $header = Tbat::whereNull('tbats.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('tbats.user_id',$promoter)
                    ->join('users','users.id','tbats.user_id')
                    ->join('stores','stores.id','tbats.store_id')
                    ->join('stores as storeD','storeD.id','tbats.store_destination_id')
                    ->select('tbats.id','tbats.date as date','stores.store_name_1','stores.store_id','storeD.store_name_1 as storeD_name_1','storeD.store_id as storeD_id','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = TbatDetail::where('tbat_id', $value->id)
                    ->join('products','products.id','tbat_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('tbat_details.id as detail_id','products.name as product_name','tbat_details.quantity','products.model','categories.name as category_name','groups.name as group_name','tbat_details.id as tbat_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['store_destination_name_1'] = $value->storeD_name_1;
                        $result[$key]['store_destination_id'] = $value->storeD_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        } else if($param == 7) { /* Stock On Hand */

            $header = Soh::whereNull('sohs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sohs.user_id',$promoter)
                    ->join('users','users.id','sohs.user_id')
                    ->join('stores','stores.id','sohs.store_id')
                    ->select('sohs.id','sohs.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SohDetail::where('soh_id', $value->id)
                    ->join('products','products.id','soh_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('soh_details.id as detail_id','products.name as product_name','soh_details.quantity','products.model','categories.name as category_name','groups.name as group_name','soh_details.id as tbat_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        }else{
            return response()->json(['status' => false, 'message' => 'No param found'], 500);
        }

    }

    public function getDataUser2($param){
        // perubahan param, jadi [parameter sales]`[id promoter]

        $user = JWTAuth::parseToken()->authenticate();

        // // get promoter ID
        // $store = Store::where('user_id', $user->id)->get(); //->pluck('distributor_id');
        // $employeeStore = '';
        // // $index = 0;
        // foreach ($store as $key => $value) {
        //     $employeeStore = EmployeeStore::where('store_id',$value->id)->get();
        //     foreach ($employeeStore as $key2 => $value2) {
        //         $users[] = $value2->user_id;
        //     }
        // }
        // if (!isset($users)) {
        //     return response()->json(['status' => false, 'message' => 'No data found'], 500);
        // }
        // $promoter = User::whereIn('id', $users)->pluck('id');

        $newParam = explode('`', $param);
        $param = $newParam[0];
        $promoter = ["$newParam[1]"];

            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $date1 = "$year-$month-01";
            $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

        if($param == 1) { /* SELL IN (SELL THRU)*/

            $header = SellIn::whereNull('sell_ins.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sell_ins.user_id',$promoter)
                    ->join('users','users.id','sell_ins.user_id')
                    ->join('stores','stores.id','sell_ins.store_id')
                    ->select('sell_ins.id','sell_ins.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellInDetail::where('sellin_id', $value->id)
                    ->join('products','products.id','sell_in_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_in_details.id as detail_id','products.name as product_name','sell_in_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_in_details.id as sell_in_detail_id','products.id as product_id', 'sell_in_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 2) { /* SELL OUT */

            $header = SellOut::whereNull('sell_outs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sell_outs.user_id',$promoter)
                    ->join('users','users.id','sell_outs.user_id')
                    ->join('stores','stores.id','sell_outs.store_id')
                    ->select('sell_outs.id','sell_outs.date as date','stores.store_name_1','stores.store_id','users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SellOutDetail::where('sellout_id', $value->id)
                    ->join('products','products.id','sell_out_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('sell_out_details.id as detail_id','products.name as product_name','sell_out_details.quantity','products.model','categories.name as category_name','groups.name as group_name','sell_out_details.id as sell_out_details','products.id as product_id', 'sell_out_details.irisan as irisan')
                    ->get();

                    foreach ($detail as $data){
                        if($data['irisan'] == 0 || $data['irisan'] == null){
                            unset($data->irisan);
                        }else{
                            $user = User::where('id', $data['irisan'])->first();

                            if($user){
                                $data['irisan'] = $user;
                            }else{
                                $data['irisan'] = 'Promoter not found';
                            }
                        }
                    }

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;

                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 3) { /* RETURN DISTRIBUTOR */
            $header = RetDistributor::whereNull('ret_distributors.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('ret_distributors.user_id',$promoter)
                    ->join('users','users.id','ret_distributors.user_id')
                    ->join('stores','stores.id','ret_distributors.store_id')
                    ->select('ret_distributors.id','ret_distributors.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetDistributorDetail::where('retdistributor_id', $value->id)
                    ->join('products','products.id','ret_distributor_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_distributor_details.id as detail_id','products.name as product_name','ret_distributor_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_distributor_details.id as ret_distributor_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 4) { /* RETURN CONSUMENT */
            
            $header = RetConsument::whereNull('ret_consuments.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('ret_consuments.user_id',$promoter)
                    ->join('users','users.id','ret_consuments.user_id')
                    ->join('stores','stores.id','ret_consuments.store_id')
                    ->select('ret_consuments.id','ret_consuments.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = RetConsumentDetail::where('retconsument_id', $value->id)
                    ->join('products','products.id','ret_consument_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('ret_consument_details.id as detail_id','products.name as product_name','ret_consument_details.quantity','products.model','categories.name as category_name','groups.name as group_name','ret_consument_details.id as ret_consument_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 5) { /* FREE PRODUCT */

            $header = FreeProduct::whereNull('free_products.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('free_products.user_id',$promoter)
                    ->join('users','users.id','free_products.user_id')
                    ->join('stores','stores.id','free_products.store_id')
                    ->select('free_products.id','free_products.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = FreeProductDetail::where('freeproduct_id', $value->id)
                    ->join('products','products.id','free_product_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('free_product_details.id as detail_id','products.name as product_name','free_product_details.quantity','products.model','categories.name as category_name','groups.name as group_name','free_product_details.id as free_product_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }

            return response()->json($result);

        } else if($param == 6) { /* TBAT */

            $header = Tbat::whereNull('tbats.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('tbats.user_id',$promoter)
                    ->join('users','users.id','tbats.user_id')
                    ->join('stores','stores.id','tbats.store_id')
                    ->join('stores as storeD','storeD.id','tbats.store_destination_id')
                    ->select('tbats.id','tbats.date as date','stores.store_name_1','stores.store_id','storeD.store_name_1 as storeD_name_1','storeD.store_id as storeD_id','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = TbatDetail::where('tbat_id', $value->id)
                    ->join('products','products.id','tbat_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('tbat_details.id as detail_id','products.name as product_name','tbat_details.quantity','products.model','categories.name as category_name','groups.name as group_name','tbat_details.id as tbat_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['store_destination_name_1'] = $value->storeD_name_1;
                        $result[$key]['store_destination_id'] = $value->storeD_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        } else if($param == 7) { /* Stock On Hand */

            $header = Soh::whereNull('sohs.deleted_at')
                    ->where('date','>=',$date1)
                    ->where('date','<=',$date2)
                    ->whereIn('sohs.user_id',$promoter)
                    ->join('users','users.id','sohs.user_id')
                    ->join('stores','stores.id','sohs.store_id')
                    ->select('sohs.id','sohs.date as date','stores.store_name_1','stores.store_id', 'users.name as user_name')
                    ->get();
                foreach ($header as $key => $value) {
                    $detail = SohDetail::where('soh_id', $value->id)
                    ->join('products','products.id','soh_details.product_id')
                    ->join('categories','categories.id','products.category_id')
                    ->join('groups','groups.id','categories.group_id')
                    ->select('soh_details.id as detail_id','products.name as product_name','soh_details.quantity','products.model','categories.name as category_name','groups.name as group_name','soh_details.id as tbat_detail_id','products.id as product_id')
                    ->get();

                        $result[$key]['id'] = $value->id;
                        $result[$key]['date'] = $value->date;
                        $result[$key]['user_name'] = $value->user_name;
                        $result[$key]['store_name_1'] = $value->store_name_1;
                        $result[$key]['store_id'] = $value->store_id;
                        $result[$key]['detail'] = $detail;
                }

            if (!isset($result)) {
                return response()->json(['status' => false, 'message' => 'No data found'], 500);
            }
            
            return response()->json($result);

        }else{
            return response()->json(['status' => false, 'message' => 'No param found'], 500);
        }

    }

}
