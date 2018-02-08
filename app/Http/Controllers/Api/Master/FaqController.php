<?php

namespace App\Http\Controllers\Api\Master;

use App\Faq;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Reports\HistorySalesmanSales;
use App\Reports\HistorySalesmanTargetActual;
use App\Reports\HistoryTargetActual;
use App\Reports\SalesmanSummarySales;
use App\Reports\SalesmanSummaryTargetActual;
use App\Reports\SummaryTargetActual;
use App\SalesmanProductFocuses;
use App\SellIn;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use App\SellOut;
use App\SellOutDetail;
use App\Reports\SummarySellOut;
use App\RetConsument;
use App\RetConsumentDetail;
use App\Reports\SummaryRetConsument;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\Reports\SummaryRetDistributor;
use App\Tbat;
use App\TbatDetail;
use App\Reports\SummaryTbat;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\Reports\SummaryDisplayShare;
use App\FreeProduct;
use App\FreeProductDetail;
use App\Reports\SummaryFreeProduct;
use App\SOH;
use App\SOHDetail;
use App\Reports\SummarySOH;
use App\SOS;
use App\SOSDetail;
use App\Reports\SummarySOS;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Store;
use App\StoreDistributor;
use App\Distributor;
use App\DmArea;
use App\TrainerArea;

use App\Price;
use App\Product;
use App\Category;
use App\ProductFocuses;
use App\Reports\HistorySellIn;
use App\Reports\HistorySellOut;
use App\Reports\HistoryRetConsument;
use App\Reports\HistoryRetDistributor;
use App\Reports\HistoryTbat;
use App\Reports\HistoryFreeProduct;
use App\Reports\HistorySoh;
use App\Reports\HistorySos;
use App\Reports\HistoryDisplayShare;

class FaqController extends Controller
{
    public function getFaq(){

        $faq = Faq::whereNull('deleted_at')->select('question', 'answer')->get();

        if ($faq->count() < 1) {
            return response()->json(
                [
                'status' => false,
                'message' => 'No Data Found',
                ],
                500
            );
        }
        return response()->json($faq);

    }

    public function sellin(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();

                // return response()->json($users);

        foreach ($users as $user){

            $dateByUser = SellIn::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('sell_ins.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('sell_ins.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))
                ->orderBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))
                ->select('sell_ins.user_id', DB::raw("YEAR(sell_ins.date) as year"), DB::raw("MONTH(sell_ins.date) as month"))
                ->get();

        // if (isset($dateByUser)) {
        //     return response()->json($dateByUser);
        // }

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = SellIn::where('user_id', $user->id)
                                ->whereMonth('sell_ins.date', '=', $dateUser->month)
                                ->whereYear('sell_ins.date', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    $transactionDetails = new Collection();

                    /*
                     * Fetch data from some models
                     */

                    /* District, Area, Region */
                    $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')->where('id', $detail->store_id)->first();
                    $spvName = (isset($store->user->name)) ? $store->user->name : '';

                    /* Distributor */
                    $distIds = StoreDistributor::where('store_id', $store->id)->pluck('distributor_id');
                    $dist = Distributor::whereIn('id', $distIds)->get();

                    $distributorCode = '';
                    $distributorName = '';
                    foreach ($dist as $distDetail) {
                        $distributorCode .= $distDetail->code;
                        $distributorName .= $distDetail->name;

                        if ($distDetail->id != $dist->last()->id) {
                            $distributorCode .= ', ';
                            $distributorName .= ', ';
                        }
                    }

                    /* DM */
                    $dmIds = DmArea::where('area_id', $store->district->area->id)->pluck('user_id');
                    $dm = User::whereIn('id', $dmIds)->get();

                    $dm_name = '';
                    foreach ($dm as $dmDetail) {
                        $dm_name .= $dmDetail->name;

                        if ($dmDetail->id != $dm->last()->id) {
                            $dm_name .= ', ';
                        }
                    }

                    /* Trainer */
                    $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
                    $tr = User::whereIn('id', $trIds)->get();

                    $trainer_name = '';
                    foreach ($tr as $trDetail) {
                        $trainer_name .= $trDetail->name;

                        if ($trDetail->id != $tr->last()->id) {
                            $trainer_name .= ', ';
                        }
                    }

                    /*
                     * Transaction Details
                     */

                    $transaction = SellInDetail::where('sellin_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail){

                        $product = Product::with('category.group.groupProduct')->where('id', $transactionDetail->product_id)->first();

                        /* Price */
                        $realPrice = 0;
                        $price = Price::where('product_id', $product->id)
                                    ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                    ->where('sell_type', 'Sell In')->first();

                        if($price){
                            $realPrice = $price->price;
                        }

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $realPrice*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $realPrice*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $realPrice*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'product_id' => $product->id,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $realPrice,
                            'value' => $realPrice*$transactionDetail->quantity,
                            'value_pf_mr' => $value_pf_mr,
                            'value_pf_tr' => $value_pf_tr,
                            'value_pf_ppe' => $value_pf_ppe,
                        ]);
                        $transactionDetails->push($transactionDetailsData);

                    }

                    /* Header Details */
                    $headerDetailsData = ([
                        'week' => $detail->week,
                        'distributor_code' => $distributorCode,
                        'distributor_name' => $distributorName,
                        'region' => $store->district->area->region->name,
                        'region_id' => $store->district->area->region->id,
                        'channel' => $store->subChannel->channel->name,
                        'sub_channel' => $store->subChannel->name,
                        'area' => $store->district->area->name,
                        'area_id' => $store->district->area->id,
                        'district' => $store->district->name,
                        'district_id' => $store->district->id,
                        'store_name_1' => $store->store_name_1,
                        'store_name_2' => $store->store_name_2,
                        'store_id' => $store->store_id,
                        'storeId' => $store->id,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        
                        'role_id' => $user->role_id,
                        'role' => $user->role,
                        'role_group' => $user->role_group,
                        
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                        'transaction' => $transactionDetails,
                    ]);
                    $headerDetails->push($headerDetailsData);


                    /* Delete data that has been inserted to history */
                    $sellIn = SellIn::where('id', $detail->id);
                    $sellInDetails =  SellInDetail::where('sellin_id', $sellIn->first()->id);

                    /* Delete summary table */
                    $summary = SummarySellIn::where('sellin_detail_id', $sellInDetails->first()->id);
                    $summary->delete();

                    /* Get Data again*/
                    SellInDetail::where('sellin_id', $sellIn->first()->id)->delete();
                    SellIn::where('id', $detail->id)->delete();

                }

                /* Header */
                $headerData = ([
                    'id' => $user->id,
                    'name' => $user->name,
                    'month' => $dateUser->month,
                    'year' => $dateUser->year,
                    'details' => $headerDetails,
                ]);
                $header->push($headerData);

                if($dateByUser->count() > 0){

                    /* Insert Data */
                    $h = new HistorySellIn();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        return response()->json($h);

    }
}
