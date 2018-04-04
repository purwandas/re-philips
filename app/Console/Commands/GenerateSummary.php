<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use App\Filters\AchievementFilters;
use App\Reports\SummaryTargetActual;
use App\Reports\HistoryTargetActual;
use Carbon\Carbon;
use App\Reports\HistorySalesmanTargetActual;
use App\Reports\SalesmanSummaryTargetActual;
use Illuminate\Support\Collection;
use App\SellInDetail;
use App\Reports\SummarySellIn;
use App\Reports\SalesmanSummarySales;
use App\SellOutDetail;
use App\Reports\SummarySellOut;
use App\SOHDetail;
use App\Reports\SummarySoh;
use App\SellIn;
use App\SellOut;
use App\Soh;
use App\Store;
use App\SpvDemo;
use App\TrainerArea;
use App\DmArea;
use App\Product;
use App\Price;
use App\User;
use App\SalesmanDedicate;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;
use App\SalesmanProductFocuses;
use App\Traits\ActualTrait;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;

class GenerateSummary extends Command
{
    use UploadTrait;
    use StringTrait;
    use ActualTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:sum {param}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Data For All Summary';

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
        //
        if($this->argument('param') === null){
            //$this->info($this->argument('param'));    
            return false;
        }

        // PROCESS
        $this->generateSellInMonth($this->argument('param'));
        $this->info('Summary Sell In / Thru has been generated :*');

        $this->generateSellOutMonth($this->argument('param'));
        $this->info('Summary Sell Out has been generated :D');

        $this->generateSOHMonth($this->argument('param'));
        $this->info('Summary SOH has been generated :3');
        
    }

    public function generateSOHMonth($param){

        // SOH
        if($param == 100){
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))
                        ->whereDay('sohs.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $soh = Soh::whereYear('sohs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sohs.date', Carbon::now()->format('m'))
                        ->whereDay('sohs.date', $param)
                        ->get();
        }

        foreach ($soh as $data) {

            $sohDetails = SohDetail::where('soh_id', $data['id'])->get();

            foreach ($sohDetails as $detail) {

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell In')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
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

                if (isset($store->subChannel->channel->name)){
                    $channel = $store->subChannel->channel->name;
                }else{
                    $channel = '';
                }

                if (isset($store->subChannel->name)){
                    $subChannel = $store->subChannel->name;
                }else{
                    $subChannel = '';
                }

                $summary = SummarySoh::where('soh_detail_id', $detail['id'])->first();

                if($summary){ // UPDATE

                    $value = $detail['quantity'] * $realPrice;

                    $summary->update([
                        // 'quantity' => $summary->quantity + $data['quantity'],
                        'unit_price' => $realPrice,
                        'quantity' => $detail['quantity'],
                        'value' => $value,
                    ]);

                }else{

                    SummarySOH::create([
                        'soh_detail_id' => $detail['id'],
                        'region_id' => $store->district->area->region->id,
                        'area_id' => $store->district->area->id,
                        'district_id' => $store->district->id,
                        'storeId' => $data['store_id'],
                        'user_id' => $data['user_id'],
                        'week' => $data['week'],
                        'distributor_code' => $distributor_code,
                        'distributor_name' => $distributor_name,
                        'region' => $store->district->area->region->name,
                        'channel' => $store->subChannel->channel->name,
                        'sub_channel' => $store->subChannel->name,
                        'area' => $store->district->area->name,
                        'district' => $store->district->name,
                        'store_name_1' => $store->store_name_1,
                        'store_name_2' => $customerCode,
                        'store_id' => $store->store_id,
                        'nik' => $data->user->nik,
                        'promoter_name' => $data->user->name,
                        'date' => $data['date'],
                        'model' => $product->model . '/' . $product->variants,
                        'group' => $product->category->group->groupProduct->name,
                        'category' => $product->category->name,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $detail['quantity'],
                        'unit_price' => $realPrice,
                        'value' => $realPrice * $detail['quantity'],
                        'value_pf_mr' => 0,
                        'value_pf_tr' => 0,
                        'value_pf_ppe' => 0,
                        'role' => $data->user->role->role,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                    ]);

                }

            }

        }

    }


    public function generateSellOutMonth($param){

        // SELL OUT
        // $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
        //                 ->whereMonth('sell_outs.date', Carbon::now()->format('m'))->get();

        if($param == 100){
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))
                        ->whereDay('sell_outs.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $sellOut = SellOut::whereYear('sell_outs.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_outs.date', Carbon::now()->format('m'))
                        ->whereDay('sell_outs.date', $param)
                        ->get();
        }

        foreach ($sellOut as $data) {

            $sellOutDetails = SellOutDetail::where('sellout_id', $data['id'])->get();

            foreach ($sellOutDetails as $detail) {

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell Out')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell Out')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell Out')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
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

                if (isset($store->subChannel->channel->name)){
                    $channel = $store->subChannel->channel->name;
                }else{
                    $channel = '';
                }

                if (isset($store->subChannel->name)){
                    $subChannel = $store->subChannel->name;
                }else{
                    $subChannel = '';
                }

                $summary = SummarySellOut::where('sellout_detail_id', $detail['id'])->first();

                if($summary){ // UPDATE

                    $value = $detail['quantity'] * $realPrice;

                    ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                    ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                    ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                    $summary->update([
                        'unit_price' => $realPrice,
                        'quantity' => $detail['quantity'],
                        'value' => $value,
                        'value_pf_mr' => $value_pf_mr,
                        'value_pf_tr' => $value_pf_tr,
                        'value_pf_ppe' => $value_pf_ppe,
                    ]);

                }else{ // CREATE

                    $summary = SummarySellOut::create([
                        'sellout_detail_id' => $detail['id'],
                        'region_id' => $store->district->area->region->id,
                        'area_id' => $store->district->area->id,
                        'district_id' => $store->district->id,
                        'storeId' => $data['store_id'],
                        'user_id' => $data['user_id'],
                        'week' => $data['week'],
                        'distributor_code' => $distributor_code,
                        'distributor_name' => $distributor_name,
                        'region' => $store->district->area->region->name,
                        'channel' => $channel,
                        'sub_channel' => $subChannel,
                        'area' => $store->district->area->name,
                        'district' => $store->district->name,
                        'store_name_1' => $store->store_name_1,
                        'store_name_2' => $customerCode,
                        'store_id' => $store->store_id,
                        'dedicate' => $store->dedicate,
                        'nik' => $data->user->nik,
                        'promoter_name' => $data->user->name,
                        'date' => $data['date'],
                        'model' => $product->model . '/' . $product->variants,
                        'group' => $product->category->group->groupProduct->name,
                        'category' => $product->category->name,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $detail['quantity'],
                        'irisan' => $detail['irisan'],
                        'unit_price' => $realPrice,
                        'value' => $realPrice * $detail['quantity'],
                        'value_pf_mr' => $value_pf_mr,
                        'value_pf_tr' => $value_pf_tr,
                        'value_pf_ppe' => $value_pf_ppe,
                        'role' => $data->user->role->role,
                        'role_id' => $data->user->role->id,
                        'role_group' => $data->user->role->role_group,
                        'spv_name' => $spvName,
                        'dm_name' => $dm_name,
                        'trainer_name' => $trainer_name,
                    ]);

                }                

            }


            /* Reset Actual */
            $this->resetActual($data['user_id'], $data['store_id'], 'Sell Out');

        }

    }

    public function generateSellInMonth($param){

        // SELL THRU
        // $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
        //                 ->whereMonth('sell_ins.date', Carbon::now()->format('m'))->get();

        if($param == 100){
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))->get();
        }else if($param == 0){
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))
                        ->whereDay('sell_ins.date', Carbon::now()->format('d'))
                        ->get();
        }else{
            $sellIn = SellIn::whereYear('sell_ins.date', Carbon::now()->format('Y'))
                        ->whereMonth('sell_ins.date', Carbon::now()->format('m'))
                        ->whereDay('sell_ins.date', $param)
                        ->get();
        }

        foreach ($sellIn as $data) {
            
            $sellInDetails = SellInDetail::where('sellin_id', $data['id'])->get();

            foreach ($sellInDetails as $detail) {

                // return $detail;

                /* DATA */
                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                            ->where('id', $data['store_id'])->first();
                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                $spvDemoName = SpvDemo::where('user_id', $data->user->id)->first();
                if(count($spvDemoName) > 0){
                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                }

                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                /* Product */
                $product = Product::with('category.group.groupProduct')
                            ->where('id', $detail['product_id'])->first();

                /* Price */
                $realPrice = 0;
                if($data->user->role->role_group == 'Salesman Explorer') {
                    if (isset($store->subChannel->channel->globalChannel->id)) {
                        $price = Price::where('product_id', $product->id)
                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }else{
                        $dedicate = SalesmanDedicate::where('user_id',$data->user->id)->first();

                        $newDedicate = '';

                        if($dedicate->dedicate == 'Traditional Retail') $newDedicate = 'TR';
                        if($dedicate->dedicate == 'Mother Care & Child') $newDedicate = 'MCC';

                        $price = Price::where('product_id', $product->id)
                            ->join('global_channels','global_channels.id','prices.globalchannel_id')
                            ->where('global_channels.name',$newDedicate)
                            ->where('sell_type', 'Sell In')
                            ->first();
                    }

                }else{
                    $price = Price::where('product_id', $product->id)
                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                        ->where('sell_type', 'Sell In')
                        ->first();
                }

                if($price){
                    $realPrice = $price->price;
                }

                /* Distributor */
                $distIds = StoreDistributor::where('store_id', $data['store_id'])->pluck('distributor_id');
                $dist = Distributor::whereIn('id', $distIds)->get();

                $distributor_code = '';
                $distributor_name = '';
                foreach ($dist as $distDetail) {
                    $distributor_code .= $distDetail->code;
                    $distributor_name .= $distDetail->name;

                    if ($distDetail->id != $dist->last()->id) {
                        $distributor_code .= ', ';
                        $distributor_name .= ', ';
                    }
                }

                /* Value - Product Focus */
                $value_pf_mr = 0;
                $value_pf_tr = 0;
                $value_pf_ppe = 0;

                $productFocus = ProductFocuses::where('product_id', $product->id)->get();
                foreach ($productFocus as $productFocusDetail) {
                    if ($productFocusDetail->type == 'Modern Retail') {
                        $value_pf_mr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'Traditional Retail') {
                        $value_pf_tr = $realPrice * $data['quantity'];
                    } else if ($productFocusDetail->type == 'PPE') {
                        $value_pf_ppe = $realPrice * $data['quantity'];
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

                // PROCESSING DATA
                if($data->user->role->role_group != 'Salesman Explorer'){ // PROMOTER

                    $summary = SummarySellIn::where('sellin_detail_id', $detail['id'])->first();

                    if($summary){ // UPDATE

                        $value = $detail['quantity'] * $realPrice;

                        ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                        ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                        ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                        $summary->update([
                            'unit_price' => $realPrice,
                            'quantity' => $detail['quantity'],
                            'value' => $value,
                            'value_pf_mr' => $value_pf_mr,
                            'value_pf_tr' => $value_pf_tr,
                            'value_pf_ppe' => $value_pf_ppe,
                        ]);

                    }else{ // CREATE

                        if (isset($store->subChannel->channel->name)){
                            $channel = $store->subChannel->channel->name;
                        }else{
                            $channel = '';
                        }

                        if (isset($store->subChannel->name)){
                            $subChannel = $store->subChannel->name;
                        }else{
                            $subChannel = '';
                        }

                        $summary = SummarySellIn::create([
                            'sellin_detail_id' => $detail['id'],
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $data['store_id'],
                            'user_id' => $data['user_id'],
                            'week' => $data['week'],
                            'distributor_code' => $distributor_code,
                            'distributor_name' => $distributor_name,
                            'region' => $store->district->area->region->name,
                            'channel' => $channel,
                            'sub_channel' => $subChannel,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $customerCode,
                            'store_id' => $store->store_id,
                            'dedicate' => $store->dedicate,
                            'nik' => $data->user->nik,
                            'promoter_name' => $data->user->name,
                            'date' => $data['date'],
                            'model' => $product->model . '/' . $product->variants,
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $detail['quantity'],
                            'irisan' => $detail['irisan'],
                            'unit_price' => $realPrice,
                            'value' => $realPrice * $detail['quantity'],
                            'value_pf_mr' => $value_pf_mr,
                            'value_pf_tr' => $value_pf_tr,
                            'value_pf_ppe' => $value_pf_ppe,
                            'role' => $data->user->role->role,
                            'role_id' => $data->user->role->id,
                            'role_group' => $data->user->role->role_group,
                            'spv_name' => $spvName,
                            'dm_name' => $dm_name,
                            'trainer_name' => $trainer_name,
                        ]);

                    }

                }else{ // SEE

                    $summary = SalesmanSummarySales::where('sellin_detail_id', $detail['id'])->first();

                    if($summary){ // UPDATE

                        $value = $detail['quantity'] * $realPrice;

                        ($summary->value_pf > 0) ? $value_pf = $value : $value_pf = 0;

                        $summary->update([
                            'unit_price' => $realPrice,
                            'quantity' => $detail['quantity'],
                            'value' => $value,
                            'value_pf' => $value_pf
                        ]);

                    }else{ // CREATE

                        $value_pf = 0;

                        $productFocus = SalesmanProductFocuses::where('product_id', $product->id)->first();

                        if($productFocus){
                            $value_pf = $realPrice * $detail->quantity;
                        }

                        if (isset($store->subChannel->channel->name)){
                            $channel = $store->subChannel->channel->name;
                        }else{
                            $channel = '';
                        }

                        if (isset($store->subChannel->name)){
                            $subChannel = $store->subChannel->name;
                        }else{
                            $subChannel = '';
                        }


                        $summary = SalesmanSummarySales::create([
                            'sellin_detail_id' => $detail['id'],
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $data['store_id'],
                            'user_id' => $data['user_id'],
                            'week' => $data['week'],
                            'distributor_code' => $distributor_code,
                            'distributor_name' => $distributor_name,
                            'region' => $store->district->area->region->name,
                            'channel' => $channel,
                            'sub_channel' => $subChannel,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $customerCode,
                            'store_id' => $store->store_id,
                            'dedicate' => $store->dedicate,
                            'nik' => $data->user->nik,
                            'promoter_name' => $data->user->name,
                            'date' => $data['date'],
                            'model' => $product->model . '/' . $product->variants,
                            'group' => $product->category->group->groupProduct->name,
                            'category' => $product->category->name,
                            'product_name' => $product->name,
                            'quantity' => $detail['quantity'],
                            'unit_price' => $realPrice,
                            'value' => $realPrice * $detail['quantity'],
                            'value_pf' => $value_pf,
                            'role' => $data->user->role->role,
                            'role_id' => $data->user->role->id,
                            'role_group' => $data->user->role->role_group,
                        ]);

                    }

                }
                
            }

            if($data->user->role->role_group != 'Salesman Explorer'){ // PROMOTER
                /* Reset Actual */
                $this->resetActual($data['user_id'], $data['store_id'], 'Sell In');
            }else{ // SEE
                /* Reset Actual */
                $this->resetActualSalesman($data['user_id']);
            }

        }

        // $sellInDetails = SellInDetail::whereHas('sellIn', function($query){
        //                     return $query->whereYear('sell_ins.date', Carbon::now()->format('Y'))
        //                                  ->whereMonth('sell_ins.date', Carbon::now()->format('m'));
        //                  })->get();

        //                     // return $sellInDetails;

        // foreach ($sellInDetails as $data) {

        //     return $data;
            
        //     if($data->sellIn->user->role->role_group != 'Salesman Explorer'){
        //         return 'No SEE';

        //         $summary = SummarySellIn::where('sellin_detail_id', $data['id'])->first();

        //         if($summary){
        //             // $
        //         }

        //     }else{
        //         return 'SEE';
        //     }

        // }

    }
}
