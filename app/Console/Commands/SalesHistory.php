<?php

namespace App\Console\Commands;

use App\EmployeeStore;
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
use App\Target;
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
use DB;
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

class SalesHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all sales data from past month and insert into history sales';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sellin();
        $this->sellout();
        $this->retconsument();
        $this->retdistributor();
        $this->freeproduct();
        $this->tbat();
        $this->displayshare();
        $this->soh();
        $this->targetActual();
        $this->salesmanSales();
        $this->salesmanTargetActual();

    }

    /**
     * History Sales : Sell In (Sell Through)
     *
     */
    public function sellin(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = SellIn::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('sell_ins.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('sell_ins.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))->orderBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))
                ->select('sell_ins.user_id', DB::raw("YEAR(sell_ins.date) as year"), DB::raw("MONTH(sell_ins.date) as month"))->get();

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
                            'irisan' => $transactionDetail->irisan,
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

        $this->info('History Sell Through berhasil dibuat');

    }

    /**
     * History Sales : Sell Out
     *
     */
    public function sellout(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = SellOut::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('sell_outs.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('sell_outs.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(sell_outs.date)"), DB::raw("MONTH(sell_outs.date)"))->orderBy(DB::raw("YEAR(sell_outs.date)"), DB::raw("MONTH(sell_outs.date)"))
                ->select('sell_outs.user_id', DB::raw("YEAR(sell_outs.date) as year"), DB::raw("MONTH(sell_outs.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = SellOut::where('user_id', $user->id)
                                ->whereMonth('sell_outs.date', '=', $dateUser->month)
                                ->whereYear('sell_outs.date', '=', $dateUser->year)->get();

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

                    $transaction = SellOutDetail::where('sellout_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail){

                        $product = Product::with('category.group.groupProduct')->where('id', $transactionDetail->product_id)->first();

                        /* Price */
                        $realPrice = 0;
                        $price = Price::where('product_id',
                            $product->id)
                                    ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                    ->where('sell_type', 'Sell Out')->first();

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
                    $sellOut = SellOut::where('id', $detail->id);
                    $sellOutDetails =  SellOutDetail::where('sellout_id', $sellOut->first()->id);

                    /* Delete summary table */
                    $summary = SummarySellOut::where('sellout_detail_id', $sellOutDetails->first()->id);
                    $summary->delete();

                    SellOutDetail::where('sellout_id', $sellOut->first()->id)->delete();
                    SellOut::where('id', $detail->id)->delete();

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
                    $h = new HistorySellOut();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History Sell Out berhasil dibuat');

    }

    /**
     * History Sales : Ret. Consument
     *
     */
    public function retconsument(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = RetConsument::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('ret_consuments.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('ret_consuments.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(ret_consuments.date)"), DB::raw("MONTH(ret_consuments.date)"))->orderBy(DB::raw("YEAR(ret_consuments.date)"), DB::raw("MONTH(ret_consuments.date)"))
                ->select('ret_consuments.user_id', DB::raw("YEAR(ret_consuments.date) as year"), DB::raw("MONTH(ret_consuments.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = RetConsument::where('user_id', $user->id)
                                ->whereMonth('ret_consuments.date', '=', $dateUser->month)
                                ->whereYear('ret_consuments.date', '=', $dateUser->year)->get();

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

                    $transaction = RetConsumentDetail::where('retconsument_id', $detail->id)->get();

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
                    $retConsument = RetConsument::where('id', $detail->id);
                    $retConsumentDetails =  RetConsumentDetail::where('retconsument_id', $retConsument->first()->id);

                    /* Delete summary table */
                    $summary = SummaryRetConsument::where('retconsument_detail_id', $retConsumentDetails->first()->id);
                    $summary->delete();

                    RetConsumentDetail::where('retconsument_id', $retConsument->first()->id)->delete();-
                    RetConsument::where('id', $detail->id)->delete();

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
                    $h = new HistoryRetConsument();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History Ret. Consument berhasil dibuat');

    }

    /**
     * History Sales : Ret. Distributor
     *
     */
    public function retdistributor(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();

        foreach ($users as $user){

            $dateByUser = RetDistributor::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('ret_distributors.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('ret_distributors.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(ret_distributors.date)"), DB::raw("MONTH(ret_distributors.date)"))->orderBy(DB::raw("YEAR(ret_distributors.date)"), DB::raw("MONTH(ret_distributors.date)"))
                ->select('ret_distributors.user_id', DB::raw("YEAR(ret_distributors.date) as year"), DB::raw("MONTH(ret_distributors.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = RetDistributor::where('user_id', $user->id)
                                ->whereMonth('ret_distributors.date', '=', $dateUser->month)
                                ->whereYear('ret_distributors.date', '=', $dateUser->year)->get();

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

                    $transaction = RetDistributorDetail::where('retdistributor_id', $detail->id)->get();

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
                    $retDistributor = RetDistributor::where('id', $detail->id);
                    $retDistributorDetails =  RetDistributorDetail::where('retdistributor_id', $retDistributor->first()->id);

                    /* Delete summary table */
                    $summary = SummaryRetDistributor::where('retdistributor_detail_id', $retDistributorDetails->first()->id);
                    $summary->delete();

                    RetDistributorDetail::where('retdistributor_id', $retDistributor->first()->id)->delete();
                    RetDistributor::where('id', $detail->id)->delete();

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
                    $h = new HistoryRetDistributor();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History Ret. Distributor berhasil dibuat');

    }

    /**
     * History Sales : Free Product
     *
     */
    public function freeproduct(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = FreeProduct::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('free_products.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('free_products.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(free_products.date)"), DB::raw("MONTH(free_products.date)"))->orderBy(DB::raw("YEAR(free_products.date)"), DB::raw("MONTH(free_products.date)"))
                ->select('free_products.user_id', DB::raw("YEAR(free_products.date) as year"), DB::raw("MONTH(free_products.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = FreeProduct::where('user_id', $user->id)
                                ->whereMonth('free_products.date', '=', $dateUser->month)
                                ->whereYear('free_products.date', '=', $dateUser->year)->get();

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

                    $transaction = FreeProductDetail::where('freeproduct_id', $detail->id)->get();

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
                    $freeProduct = FreeProduct::where('id', $detail->id);
                    $freeProductDetails =  FreeProductDetail::where('freeproduct_id', $freeProduct->first()->id);

                    /* Delete summary table */
                    $summary = SummaryFreeProduct::where('freeproduct_detail_id', $freeProductDetails->first()->id);
                    $summary->delete();

                    FreeProductDetail::where('freeproduct_id', $freeProduct->first()->id)->delete();
                    FreeProduct::where('id', $detail->id)->delete();

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
                    $h = new HistoryFreeProduct();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History Free Product berhasil dibuat');

    }

    /**
     * History Sales : TBAT
     *
     */
    public function tbat(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = Tbat::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('tbats.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('tbats.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(tbats.date)"), DB::raw("MONTH(tbats.date)"))->orderBy(DB::raw("YEAR(tbats.date)"), DB::raw("MONTH(tbats.date)"))
                ->select('tbats.user_id', DB::raw("YEAR(tbats.date) as year"), DB::raw("MONTH(tbats.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = Tbat::where('user_id', $user->id)
                                ->whereMonth('tbats.date', '=', $dateUser->month)
                                ->whereYear('tbats.date', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    $transactionDetails = new Collection();

                    /*
                     * Fetch data from some models
                     */

                    /* District, Area, Region */
                    $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')->where('id', $detail->store_id)->first();
                    $spvName = (isset($store->user->name)) ? $store->user->name : '';

                    $storeDestination = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')->where('id', $detail->store_destination_id)->first();
                    $spvNameDestination = (isset($storeDestination->user->name)) ? $storeDestination->user->name : '';

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

                    $transaction = TbatDetail::where('tbat_id', $detail->id)->get();

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
                        'store_destination_name_1' => $storeDestination->store_name_1,
                        'store_destination_name_2' => $storeDestination->store_name_2,
                        'store_destination_id' => $storeDestination->store_id,
                        'store_destinationId' => $storeDestination->id,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role_id' => $user->role_id,
                        'role' => $user->role,
                        'role_group' => $user->role_group,
                        'spv_name' => $spvName,
                        'spv_destination_name' => $spvNameDestination,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                        'transaction' => $transactionDetails,
                    ]);
                    $headerDetails->push($headerDetailsData);


                    /* Delete data that has been inserted to history */
                    $tbat = Tbat::where('id', $detail->id);
                    $tbatDetails =  TbatDetail::where('tbat_id', $tbat->first()->id);

                    /* Delete summary table */
                    $summary = SummaryTbat::where('tbat_detail_id', $tbatDetails->first()->id);
                    $summary->delete();

                    TbatDetail::where('tbat_id', $tbat->first()->id);
                    Tbat::where('id', $detail->id)->delete();

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
                    $h = new HistoryTbat();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History TBAT berhasil dibuat');

    }

    /**
     * History Sales : Display Share
     *
     */
    public function displayshare(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = DisplayShare::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('display_shares.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('display_shares.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(display_shares.date)"), DB::raw("MONTH(display_shares.date)"))
                ->orderBy(DB::raw("YEAR(display_shares.date)"), DB::raw("MONTH(display_shares.date)"))
                ->select('display_shares.user_id', DB::raw("YEAR(display_shares.date) as year"), DB::raw("MONTH(display_shares.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = DisplayShare::where('user_id', $user->id)
                                ->whereMonth('display_shares.date', '=', $dateUser->month)
                                ->whereYear('display_shares.date', '=', $dateUser->year)->get();

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

                    $transaction = DisplayShareDetail::where('display_share_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail){

                        $category = Category::where('id', $transactionDetail->category_id)->first();

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            
                            'category'   => $category->name,
                            'philips'    => $transactionDetail->philips,
                            'all'        => $transactionDetail->all,
                            'percentage' => ($transactionDetail->philips / $transactionDetail->all * 100)
                            
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
                    $displayShare = DisplayShare::where('id', $detail->id);
                    $displayShareDetails =  DisplayShareDetail::where('display_share_id', $displayShare->first()->id);

                    /* Delete summary table */
                    $summary = SummaryDisplayShare::where('displayshare_detail_id', $displayShareDetails->first()->id);
                    $summary->delete();

                    DisplayShareDetail::where('display_share_id', $displayShare->first()->id)->delete();
                    DisplayShare::where('id', $detail->id)->delete();

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
                    $h = new HistoryDisplayShare();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History Display Share berhasil dibuat');

    }

    /**
     * History Sales : SOH
     *
     */
    public function soh(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = Soh::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('sohs.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('sohs.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(sohs.date)"), DB::raw("MONTH(sohs.date)"))->orderBy(DB::raw("YEAR(sohs.date)"), DB::raw("MONTH(sohs.date)"))
                ->select('sohs.user_id', DB::raw("YEAR(sohs.date) as year"), DB::raw("MONTH(sohs.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = Soh::where('user_id', $user->id)
                                ->whereMonth('sohs.date', '=', $dateUser->month)
                                ->whereYear('sohs.date', '=', $dateUser->year)->get();

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

                    $transaction = SohDetail::where('soh_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail)
                    {

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
                    $soh = Soh::where('id', $detail->id);
                    $sohDetails =  SohDetail::where('soh_id', $soh->first()->id);

                    /* Delete summary table */
                    $summary = SummarySoh::where('soh_detail_id', $sohDetails->first()->id);
                    $summary->delete();

                    /* Gett data again */
                    SohDetail::where('soh_id', $soh->first()->id)->delete();
                    Soh::where('id', $detail->id)->delete();

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
                    $h = new HistorySoh();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History SOH berhasil dibuat');

    }

    /**
     * History Sales : Target Actual
     *
     */
    public function targetActual(){

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = SummaryTargetActual::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('summary_target_actuals.created_at', '<', Carbon::now()->format('m'))
                                 ->whereYear('summary_target_actuals.created_at', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(summary_target_actuals.created_at)"), DB::raw("MONTH(summary_target_actuals.created_at)"))->orderBy(DB::raw("YEAR(summary_target_actuals.created_at)"), DB::raw("MONTH(summary_target_actuals.created_at)"))
                ->select('summary_target_actuals.user_id', DB::raw("YEAR(summary_target_actuals.created_at) as year"), DB::raw("MONTH(summary_target_actuals.created_at) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $details = new Collection();

                $data = SummaryTargetActual::where('user_id', $user->id)
                                ->whereMonth('summary_target_actuals.created_at', '=', $dateUser->month)
                                ->whereYear('summary_target_actuals.created_at', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    /* Details */
                    $detailsData = ([
                        'region_id' => $detail->region_id,
                        'area_id' => $detail->area_id,
                        'district_id' => $detail->district_id,
                        'storeId' => $detail->storeId,
                        'user_id' => $detail->user_id,
                        'user_role' => $detail->user_role,
                        'partner' => $detail->partner,
                        'region' => $detail->region,
                        'area' => $detail->area,
                        'district' => $detail->district,
                        'nik' => $detail->nik,
                        'promoter_name' => $detail->promoter_name,
                        'account_type' => $detail->account_type,
                        'title_of_promoter' => $detail->title_of_promoter,
                        'classification_store' => $detail->classification_store,
                        'account' => $detail->account,
                        'store_id' => $detail->store_id,
                        'store_name_1' => $detail->store_name_1,
                        'store_name_2' => $detail->store_name_2,
                        'spv_name' => $detail->spv_name,
                        'trainer' => $detail->trainer,
                        'sell_type' => $detail->sell_type,
                        'target_dapc' => $detail->target_dapc,
                        'actual_dapc' => $detail->actual_dapc,
                        'target_da' => $detail->target_da,
                        'actual_da' => $detail->actual_da,
                        'target_pc' => $detail->target_pc,
                        'actual_pc' => $detail->actual_pc,
                        'target_mcc' => $detail->target_mcc,
                        'actual_mcc' => $detail->actual_mcc,
                        'target_pf_da' => $detail->target_pf_da,
                        'actual_pf_da' => $detail->actual_pf_da,
                        'target_pf_pc' => $detail->target_pf_pc,
                        'actual_pf_pc' => $detail->actual_pf_pc,
                        'target_pf_mcc' => $detail->target_pf_mcc,
                        'actual_pf_mcc' => $detail->actual_pf_mcc,
                        'target_da_w1' => $detail->target_da_w1,
                        'actual_da_w1' => $detail->actual_da_w1,
                        'target_da_w2' => $detail->target_da_w2,
                        'actual_da_w2' => $detail->actual_da_w2,
                        'target_da_w3' => $detail->target_da_w3,
                        'actual_da_w3' => $detail->actual_da_w3,
                        'target_da_w4' => $detail->target_da_w4,
                        'actual_da_w4' => $detail->actual_da_w4,
                        'target_da_w5' => $detail->target_da_w5,
                        'actual_da_w5' => $detail->actual_da_w5,
                        'target_pc_w1' => $detail->target_pc_w1,
                        'actual_pc_w1' => $detail->actual_pc_w1,
                        'target_pc_w2' => $detail->target_pc_w2,
                        'actual_pc_w2' => $detail->actual_pc_w2,
                        'target_pc_w3' => $detail->target_pc_w3,
                        'actual_pc_w3' => $detail->actual_pc_w3,
                        'target_pc_w4' => $detail->target_pc_w4,
                        'actual_pc_w4' => $detail->actual_pc_w4,
                        'target_pc_w5' => $detail->target_pc_w5,
                        'actual_pc_w5' => $detail->actual_pc_w5,
                        'target_mcc_w1' => $detail->target_mcc_w1,
                        'actual_mcc_w1' => $detail->actual_mcc_w1,
                        'target_mcc_w2' => $detail->target_mcc_w2,
                        'actual_mcc_w2' => $detail->actual_mcc_w2,
                        'target_mcc_w3' => $detail->target_mcc_w3,
                        'actual_mcc_w3' => $detail->actual_mcc_w3,
                        'target_mcc_w4' => $detail->target_mcc_w4,
                        'actual_mcc_w4' => $detail->actual_mcc_w4,
                        'target_mcc_w5' => $detail->target_mcc_w5,
                        'actual_mcc_w5' => $detail->actual_mcc_w5,
                        'sum_target_store' => $detail->sum_target_store,
                        'sum_actual_store' => $detail->sum_actual_store,
                        'sum_pf_target_store' => $detail->sum_pf_target_store,
                        'sum_pf_actual_store' => $detail->sum_pf_actual_store,
                        'sum_target_store_promo' => $detail->sum_target_store_promo,
                        'sum_actual_store_promo' => $detail->sum_actual_store_promo,
                        'sum_pf_target_store_promo' => $detail->sum_pf_target_store_promo,
                        'sum_pf_actual_store_promo' => $detail->sum_pf_actual_store_promo,
                        'sum_target_store_demo' => $detail->sum_target_store_demo,
                        'sum_actual_store_demo' => $detail->sum_actual_store_demo,
                        'sum_pf_target_store_demo' => $detail->sum_pf_target_store_demo,
                        'sum_pf_actual_store_demo' => $detail->sum_pf_actual_store_demo,
                        'sum_target_area' => $detail->sum_target_area,
                        'sum_actual_area' => $detail->sum_actual_area,
                        'sum_pf_target_area' => $detail->sum_pf_target_area,
                        'sum_pf_actual_area' => $detail->sum_pf_actual_area,
                        'sum_target_region' => $detail->sum_target_region,
                        'sum_actual_region' => $detail->sum_actual_region,
                        'sum_pf_target_region' => $detail->sum_pf_target_region,
                        'sum_pf_actual_region' => $detail->sum_pf_actual_region,
                    ]);
                    $details->push($detailsData);

                    /* Truncate actual in summary table */
                    $summary = SummaryTargetActual::where('id', $detail->id);

                    $emp = EmployeeStore::where('user_id', $detail->user_id)
                            ->where('store_id', $detail->storeId)->first();

                    // GET ALL VALUE, AND UPDATE ALL STORE
                    $totalValue = $summary->actual_da + $summary->actual_pc + $summary->actual_mcc;
                    $totalValuePF = $summary->actual_pf_da + $summary->actual_pf_pc + $summary->actual_pf_mcc;

                    $sumStore = SummaryTargetActual::where('storeId', $summary->storeId)->where('sell_type', $summary->sell_type);
                    $sumStorePromo = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Promoter');
                    $sumStoreDemo = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Demonstrator');
                    $sumArea = SummaryTargetActual::where('area_id', $summary->area_id)->where('sell_type', $summary->sell_type);
                    $sumRegion = SummaryTargetActual::where('region_id', $summary->region_id)->where('sell_type', $summary->sell_type);
                    $sumActualStore = SummaryTargetActual::where('storeId', $summary->storeId)->where('sell_type', $summary->sell_type)->first()->sum_actual_store;
                    $sumActualArea = SummaryTargetActual::where('area_id', $summary->area_id)->where('sell_type', $summary->sell_type)->first()->sum_actual_area;
                    $sumActualRegion = SummaryTargetActual::where('region_id', $summary->region_id)->where('sell_type', $summary->sell_type)->first()->sum_actual_region;
                    $sumActualStorePromo = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Promoter');
                    $sumActualStoreDemo = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Demonstrator');
                    // PF
                    $sumActualStorePF = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->first()->sum_pf_actual_store;
                    $sumActualAreaPF =  SummaryTargetActual::where('area_id', $summary->area_id)->where('sell_type', $summary->sell_type)->first()->sum_pf_actual_area;
                    $sumActualRegionPF = SummaryTargetActual::where('region_id', $summary->region_id)->where('sell_type', $summary->sell_type)->first()->sum_pf_actual_region;
                    $sumActualStorePromoPF = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Promoter');
                    $sumActualStoreDemoPF = SummaryTargetActual::where('storeId',$summary->storeId)->where('sell_type', $summary->sell_type)->where('user_role', 'Demonstrator');

                    // Handler
                    if($sumActualStorePromo->first()) $sumActualStorePromo = $sumActualStorePromo->first()->sum_actual_store_promo; else $sumActualStorePromo = 0;
                    if($sumActualStoreDemo->first()) $sumActualStoreDemo = $sumActualStoreDemo->first()->sum_actual_store_demo; else $sumActualStoreDemo = 0;

                    if($sumActualStorePromoPF->first()) $sumActualStorePromoPF = $sumActualStorePromoPF->first()->sum_pf_actual_store_promo; else $sumActualStorePromoPF = 0;
                    if($sumActualStoreDemoPF->first()) $sumActualStoreDemoPF = $sumActualStoreDemoPF->first()->sum_pf_actual_store_demo; else $sumActualStoreDemoPF = 0;

                    if($summary->irisan == 0){
                        if($summary->user_role == 'Promoter'){
                            $summary->update([
                                'sum_actual_store' => $sumActualStore - $totalValue,
                                'sum_actual_store_promo' => $sumActualStorePromo - $totalValue,
                                'sum_actual_area' => $sumActualArea - $totalValue,
                                'sum_actual_region' => $sumActualRegion - $totalValue,
                                'sum_pf_actual_store' => $sumActualStorePF - $totalValuePF,
                                'sum_pf_actual_store_promo' => $sumActualStorePromoPF - $totalValuePF,
                                'sum_pf_actual_area' => $sumActualAreaPF - $totalValuePF,
                                'sum_pf_actual_region' => $sumActualRegionPF - $totalValuePF,
                            ]);
                        }else{
                            $summary->update([
                                'sum_actual_store' => $sumActualStore - $totalValue,
                                'sum_actual_store_demo' => $sumActualStoreDemo - $totalValue,
                                'sum_actual_area' => $sumActualArea - $totalValue,
                                'sum_actual_region' => $sumActualRegion - $totalValue,
                                'sum_pf_actual_store' => $sumActualStorePF - $totalValuePF,
                                'sum_pf_actual_store_demo' => $sumActualStoreDemoPF - $totalValuePF,
                                'sum_pf_actual_area' => $sumActualAreaPF - $totalValuePF,
                                'sum_pf_actual_region' => $sumActualRegionPF - $totalValuePF,

                            ]);
                        }
                    }else{
                        $summary->update([
                            'sum_actual_store_demo' => $sumActualStoreDemo - $totalValue,
                            'sum_pf_actual_store_demo' => $sumActualStoreDemoPF - $totalValuePF,
                        ]);
                    }

                    // UPDATE DATA TO ALL STORE IN RANGE
                    if($summary->user_role == 'Demonstrator'){
                        $sumStoreDemo->update([
                            'sum_actual_store_demo' => $summary->sum_actual_store_demo,
                            'sum_pf_actual_store_demo' => $summary->sum_pf_actual_store_demo,
                        ]);
                    }else{
                        $sumStorePromo->update([
                            'sum_actual_store_promo' => $summary->sum_actual_store_promo,
                            'sum_pf_actual_store_promo' => $summary->sum_pf_actual_store_promo,
                        ]);
                    }

                    $sumStore->update([
                        'sum_actual_store' => $summary->sum_actual_store,
                        'sum_pf_actual_store' => $summary->sum_pf_actual_store,
                    ]);

                    $sumArea->update([
                        'sum_actual_area' => $summary->sum_actual_area,
                        'sum_pf_actual_area' => $summary->sum_pf_actual_area,
                    ]);

                    $sumRegion->update([
                        'sum_actual_region' => $summary->sum_actual_region,
                        'sum_pf_actual_region' => $summary->sum_pf_actual_region,
                    ]);

                    if($emp){ // JIKA MASIH ADA LINK STORE HANYA RESET ACTUAL DARI SUMMARY ACTUAL
//                        $summary->delete();
                        $summary->update([
                            'actual_dapc' => 0,
                            'actual_da' => 0,
                            'actual_pc' => 0,
                            'actual_mcc' => 0,
                            'actual_pf_da' => 0,
                            'actual_pf_pc' => 0,
                            'actual_pf_mcc' => 0,
                            'actual_da_w1' => 0,
                            'actual_da_w2' => 0,
                            'actual_da_w3' => 0,
                            'actual_da_w4' => 0,
                            'actual_da_w5' => 0,
                            'actual_pc_w1' => 0,
                            'actual_pc_w2' => 0,
                            'actual_pc_w3' => 0,
                            'actual_pc_w4' => 0,
                            'actual_pc_w5' => 0,
                            'actual_mcc_w1' => 0,
                            'actual_mcc_w2' => 0,
                            'actual_mcc_w3' => 0,
                            'actual_mcc_w4' => 0,
                            'actual_mcc_w5' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }else{ // JIKA TIDAK ADA STORE MAKA HAPUS TARGET DAN SUMMARY ACTUAL
                        $summary->delete();

                        // DELETE TARGET
                        $target = where('user_id', $detail->user_id)->where('store_id', $detail->storeId);
                        $target->forceDelete();
                    }


                }

                if($dateByUser->count() > 0){

                    /* Insert Data */
                    $h = new HistoryTargetActual();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $details;
                    $h->save();

                }

            }

        }

        $this->info('History Target Actual berhasil dibuat');

    }

    /**
     * History Sales : Salesman Sales
     *
     */
    public function salesmanSales(){

        $header = new Collection();

        /* Init */
        $role = ['Salesman Explorer'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = SellIn::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('sell_ins.date', '<', Carbon::now()->format('m'))
                                 ->whereYear('sell_ins.date', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))->orderBy(DB::raw("YEAR(sell_ins.date)"), DB::raw("MONTH(sell_ins.date)"))
                ->select('sell_ins.user_id', DB::raw("YEAR(sell_ins.date) as year"), DB::raw("MONTH(sell_ins.date) as month"))->get();

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
                        $value_pf = 0;

                        $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                        if($productFocus){
                            $value_pf = $realPrice * $transactionDetail->quantity;
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
                            'value_pf' => $value_pf,
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
                    $summary = SalesmanSummarySales::where('sellin_detail_id', $sellInDetails->first()->id);
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
                    $h = new HistorySalesmanSales();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History penjualan salesman berhasil dibuat');

    }

    /**
     * History Sales : Salesman Target Actual
     *
     */
    public function salesmanTargetActual(){

        /* Init */
        $role = ['Salesman Explorer'];

        $users = User::whereIn('role_group', $role)
                ->join('roles','roles.id','users.role_id')
                ->select('users.id','users.nik','users.name','users.role_id','roles.role','roles.role_group')
                ->get();


        foreach ($users as $user){

            $dateByUser = SalesmanSummaryTargetActual::where('user_id', $user->id)
                ->where(function ($query){
                    return $query->whereMonth('salesman_summary_target_actuals.created_at', '<', Carbon::now()->format('m'))
                                 ->whereYear('salesman_summary_target_actuals.created_at', '<', Carbon::now()->format('Y'), 'or');
                })
                ->groupBy(DB::raw("YEAR(salesman_summary_target_actuals.created_at)"), DB::raw("MONTH(salesman_summary_target_actuals.created_at)"))->orderBy(DB::raw("YEAR(salesman_summary_target_actuals.created_at)"), DB::raw("MONTH(salesman_summary_target_actuals.created_at)"))
                ->select('salesman_summary_target_actuals.user_id', DB::raw("YEAR(salesman_summary_target_actuals.created_at) as year"), DB::raw("MONTH(salesman_summary_target_actuals.created_at) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $details = new Collection();

                $data = SalesmanSummaryTargetActual::where('user_id', $user->id)
                                ->whereMonth('salesman_summary_target_actuals.created_at', '=', $dateUser->month)
                                ->whereYear('salesman_summary_target_actuals.created_at', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    /* Details */
                    $detailsData = ([
                        'user_id' => $detail->user_id,
                        'nik' => $detail->nik,
                        'salesman_name' => $detail->salesman_name,
                        'area' => $detail->area,
                        'target_call' => $detail->target_call,
                        'actual_call' => $detail->actual_call,
                        'target_active_outlet' => $detail->target_active_outlet,
                        'actual_active_outlet' => $detail->actual_active_outlet,
                        'target_effective_call' => $detail->target_effective_call,
                        'actual_effective_call' => $detail->actual_effective_call,
                        'target_sales' => $detail->target_sales,
                        'actual_sales' => $detail->actual_sales,
                        'target_sales_pf' => $detail->target_sales_pf,
                        'actual_sales_pf' => $detail->actual_sales_pf,
                        'sum_national_target_call' => $detail->sum_national_target_call,
                        'sum_national_actual_call' => $detail->sum_national_actual_call,
                        'sum_national_target_active_outlet' => $detail->sum_national_target_active_outlet,
                        'sum_national_actual_active_outlet' => $detail->sum_national_actual_active_outlet,
                        'sum_national_target_effective_call' => $detail->sum_national_target_effective_call,
                        'sum_national_actual_effective_call' => $detail->sum_national_actual_effective_call,
                        'sum_national_target_sales' => $detail->sum_national_target_sales,
                        'sum_national_actual_sales' => $detail->sum_national_actual_sales,
                        'sum_national_target_sales_pf' => $detail->sum_national_target_sales_pf,
                        'sum_national_actual_sales_pf' => $detail->sum_national_actual_sales_pf,
                    ]);
                    $details->push($detailsData);

                    /* Delete summary table */
                    $summary = SalesmanSummaryTargetActual::where('id', $detail->id);
                    $summary->delete();

                }

                if($dateByUser->count() > 0){

                    /* Insert Data */
                    $h = new HistorySalesmanTargetActual();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $details;
                    $h->save();

                }

            }

        }

        $this->info('History Salesman Target Actual berhasil dibuat');

    }

}
