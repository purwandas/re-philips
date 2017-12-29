<?php

namespace App\Console\Commands;

use App\Reports\HistoryTargetActual;
use App\Reports\SummaryTargetActual;
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
        
        // $this->sos();
    }

    /**
     * History Sales : Sell In
     *
     */
    public function sellin(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = SellIn::where('user_id', $user->id)
                ->whereMonth('sell_ins.date', '<', Carbon::now()->format('m'))
                ->whereYear('sell_ins.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
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
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $sellInDetails->delete();
                    $sellIn->delete();


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

        $this->info('History Sell In berhasil dibuat');

    }

    /**
     * History Sales : Sell Out
     *
     */
    public function sellout(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = SellOut::where('user_id', $user->id)
                ->whereMonth('sell_outs.date', '<', Carbon::now()->format('m'))
                ->whereYear('sell_outs.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $sellOutDetails->delete();
                    $sellOut->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = RetConsument::where('user_id', $user->id)
                ->whereMonth('ret_consuments.date', '<', Carbon::now()->format('m'))
                ->whereYear('ret_consuments.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $retConsumentDetails->delete();
                    $retConsument->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = RetDistributor::where('user_id', $user->id)
                ->whereMonth('ret_distributors.date', '<', Carbon::now()->format('m'))
                ->whereYear('ret_distributors.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $retDistributorDetails->delete();
                    $retDistributor->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = FreeProduct::where('user_id', $user->id)
                ->whereMonth('free_products.date', '<', Carbon::now()->format('m'))
                ->whereYear('free_products.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $freeProductDetails->delete();
                    $freeProduct->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = Tbat::where('user_id', $user->id)
                ->whereMonth('tbats.date', '<', Carbon::now()->format('m'))
                ->whereYear('tbats.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
                        'spv_name' => $spvName,
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

                    $tbatDetails->delete();
                    $tbat->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = DisplayShare::where('user_id', $user->id)
                ->whereMonth('display_shares.date', '<', Carbon::now()->format('m'))
                ->whereYear('display_shares.date', '<', Carbon::now()->format('Y'), 'or')
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
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
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

                    $displayShareDetails->delete();
                    $displayShare->delete();


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
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = SOH::where('user_id', $user->id)
                ->whereMonth('sohs.date', '<', Carbon::now()->format('m'))
                ->whereYear('sohs.date', '<', Carbon::now()->format('Y'), 'or')
                ->groupBy(DB::raw("YEAR(sohs.date)"), DB::raw("MONTH(sohs.date)"))->orderBy(DB::raw("YEAR(sohs.date)"), DB::raw("MONTH(sohs.date)"))
                ->select('sohs.user_id', DB::raw("YEAR(sohs.date) as year"), DB::raw("MONTH(sohs.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = SOH::where('user_id', $user->id)
                                ->whereMonth('sohs.date', '=', $dateUser->month)
                                ->whereYear('sohs.date', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    $transactionDetails = new Collection();

                    /*
                     * Fetch data from some models
                     */

                    /* District, Area, Region */
                    $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')->where('id', $detail->store_id)->first();
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                    $transaction = SOHDetail::where('soh_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail)
                    {

                        $product = Product::with('category.group.groupProduct')->where('id', $transactionDetail->product_id)->first();

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                        'transaction' => $transactionDetails,
                    ]);
                    $headerDetails->push($headerDetailsData);


                    /* Delete data that has been inserted to history */
                    $soh = SOH::where('id', $detail->id);
                    $sohDetails =  SOHDetail::where('soh_id', $soh->first()->id);

                    /* Delete summary table */
                    $summary = SummarySOH::where('soh_detail_id', $sohDetails->first()->id);
                    $summary->delete();

                    $sohDetails->delete();
                    $soh->delete();


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
     * History Sales : SOS
     *
     */
    public function sos(){

        $header = new Collection();

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = SOS::where('user_id', $user->id)
                ->whereMonth('sos.date', '<', Carbon::now()->format('m'))
                ->whereYear('sos.date', '<', Carbon::now()->format('Y'), 'or')
                ->groupBy(DB::raw("YEAR(sos.date)"), DB::raw("MONTH(sos.date)"))->orderBy(DB::raw("YEAR(sos.date)"), DB::raw("MONTH(sos.date)"))
                ->select('sos.user_id', DB::raw("YEAR(sos.date) as year"), DB::raw("MONTH(sos.date) as month"))->get();

            foreach ($dateByUser as $dateUser) {

                $headerDetails = new Collection();

                $data = SOS::where('user_id', $user->id)
                                ->whereMonth('sos.date', '=', $dateUser->month)
                                ->whereYear('sos.date', '=', $dateUser->year)->get();

                foreach ($data as $detail) {

                    $transactionDetails = new Collection();

                    /*
                     * Fetch data from some models
                     */

                    /* District, Area, Region */
                    $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')->where('id', $detail->store_id)->first();
                    $spvName = ($store->user->name != '' ) ? $store->user->name : '';

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

                    $transaction = SOSDetail::where('sos_id', $detail->id)->get();

                    foreach ($transaction as $transactionDetail){

                        $product = Product::with('category.group.groupProduct')->where('id', $transactionDetail->product_id)->first();

                        $price = Price::where('product_id', $product->id)->first();

                        /* Value - Product Focus */
                        $value_pf_mr = 0;
                        $value_pf_tr = 0;
                        $value_pf_ppe = 0;

                        $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                        foreach ($productFocus as $productFocusDetail) {
                            if ($productFocusDetail->type == 'Modern Retail') {
                                $value_pf_mr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'Traditional Retail') {
                                $value_pf_tr = $price['price']*$transactionDetail->quantity;
                            } else if ($productFocusDetail->type == 'PPE') {
                                $value_pf_ppe = $price['price']*$transactionDetail->quantity;
                            }
                        }

                        /* Transaction Details */
                        $transactionDetailsData = ([
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'model' => $product->model . '/' . $product->variants,
                            'product_name' => $product->name,
                            'quantity' => $transactionDetail->quantity,
                            'unit_price' => $price['price'],
                            'value' => $price['price']*$transactionDetail->quantity,
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
                        'dedicate' => $store->dedicate,
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                        'transaction' => $transactionDetails,
                    ]);
                    $headerDetails->push($headerDetailsData);


                    /* Delete data that has been inserted to history */
                    $sos = SOS::where('id', $detail->id);
                    $sosDetails =  SOSDetail::where('sos_id', $sos->first()->id);

                    /* Delete summary table */
                    $summary = SummarySOS::where('sos_detail_id', $sosDetails->first()->id);
                    $summary->delete();

                    $sosDetails->delete();
                    $sos->delete();


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
                    $h = new HistorySos();
                    $h->user_id = $user->id;
                    $h->month = $dateUser->month;
                    $h->year = $dateUser->year;
                    $h->details = $headerDetails;
                    $h->save();

                }

            }

        }

        $this->info('History SOS berhasil dibuat');

    }

    /**
     * History Sales : Target Actual
     *
     */
    public function targetActual(){

        /* Init */
        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $users = User::whereIn('role', $role)->get();

        foreach ($users as $user){

            $dateByUser = SummaryTargetActual::where('user_id', $user->id)
                ->whereMonth('summary_target_actuals.created_at', '<', Carbon::now()->format('m'))
                ->whereYear('summary_target_actuals.created_at', '<', Carbon::now()->format('Y'), 'or')
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
                        'sum_target_area' => $detail->sum_target_area,
                        'sum_actual_area' => $detail->sum_actual_area,
                        'sum_target_region' => $detail->sum_target_region,
                        'sum_actual_region' => $detail->sum_actual_region,
                    ]);
                    $details->push($detailsData);

                    /* Delete summary table */
                    $summary = SummaryTargetActual::where('id', $detail->id);
                    $summary->delete();

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

}
