<?php

namespace App\Console\Commands;

use App\SellIn;
use App\SellInDetail;
use App\SummarySellIn;
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
use App\ProductFocuses;
use App\Reports\HistorySellIn;

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
                ->whereYear('sell_ins.date', '<=', Carbon::now()->format('Y'))
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
                        $value_pf_mr = '';
                        $value_pf_tr = '';
                        $value_pf_ppe = '';

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
                        'nik' => $user->nik,
                        'promoter_name' => $user->name,
                        'user_id' => $user->id,
                        'date' => $detail->date,
                        'role' => $user->role,
                        'spv_name' => $store->user->name,
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

}
