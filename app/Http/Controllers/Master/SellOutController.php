<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use DB;
use Auth;
use Carbon\Carbon;
use App\SellOut;
use App\SellOutDetail;
use App\Product;
use App\DmArea;
use App\Store;
use App\Region;
use App\RsmRegion;
use App\TrainerArea;
use App\Reports\SummarySellOut;
use App\Traits\ActualTrait;
use App\Traits\PromoterTrait;
use App\User;
use App\SpvDemo;
use App\Price;
use App\Reports\SalesmanSummarySales;
use App\SalesmanProductFocuses;
use App\SalesmanDedicate;
use App\StoreDistributor;
use App\Distributor;
use App\ProductFocuses;

class SellOutController extends Controller
{
    //
    use StringTrait;
    use ActualTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.form.sellout-form');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = SellOut::where('sell_outs.deleted_at', null)
                    ->join('stores', 'sell_outs.store_id', '=', 'stores.id')
                    ->join('sell_out_details', 'sell_outs.id', '=', 'sell_out_details.sellout_id')
                    ->join('products', 'sell_out_details.product_id', '=', 'products.id')
                    ->select('sell_outs.*', 'stores.store_name_1', 'stores.store_name_2', 'sell_out_details.quantity', 'products.name as product')
                    ->get();

        return $this->makeTable($data);
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.sellout-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = Carbon::parse($request->date);
    	// return $request->all();

        // $content = $request;//json_decode($request->getContent(), true);
        $user_id = explode('`', $request['user_id']);

        $user = User::where('users.id','=',$user_id[0])->first();

        $content['id'] = $request['store_id'];

        foreach ($request['product_id'] as $key => $value) {
            $content['data'][$key] = [ "product_id"=>$value, "quantity"=>$request['quantity'][$key] ];
        }

        if(!isset($request['irisan'])) { // Set Default Irisan if doesn't exist
            $content['irisan'] = 0;
        }else{
            if($request['irisan'] == null){
                $content['irisan'] = 0;
            }else{
                $content['irisan'] = $request['irisan'];
            }
        }

        $content['date'] = $date;

        // if($param == 2) { /* SELL OUT */

            // return response()->json($this->getPromoterTitle($user->id, $content['id']));

            // Check sell out header
            $sellOutHeader = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', $content['date']->format('Y-m-d'))->first();

            if ($sellOutHeader) { // If header exist (update and/or create detail)

               try {
                   DB::transaction(function () use ($content, $sellOutHeader, $user) {

                        foreach ($content['data'] as $data) {

                            $sellOutDetail = SellOutDetail::where('sellout_id', $sellOutHeader->id)
                                                ->where('product_id', $data['product_id'])
                                                ->where('irisan', $content['irisan'])
                                                ->first();

                            if ($sellOutDetail) { // If data exist -> update

                                $sellOutDetail->update([
                                    'quantity' => $sellOutDetail->quantity + $data['quantity']
                                ]);

                                /** Update Summary **/

                                $summary = SummarySellOut::where('sellout_detail_id', $sellOutDetail->id)->first();

                                $value_old = $summary->value;

                                $value = ($summary->quantity + $data['quantity']) * $summary->unit_price;

                                ($summary->value_pf_mr > 0) ? $value_pf_mr = $value : $value_pf_mr = 0;
                                ($summary->value_pf_tr > 0) ? $value_pf_tr = $value : $value_pf_tr = 0;
                                ($summary->value_pf_ppe > 0) ? $value_pf_ppe = $value : $value_pf_ppe = 0;

                                $summary->update([
                                    'quantity' => $summary->quantity + $data['quantity'],
                                    'value' => $value,
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellOutHeader->user_id;
                                $summary_ta['store_id'] = $sellOutHeader->store_id;
                                $summary_ta['week'] = $sellOutHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value_old'] = $value_old;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            } else { // If data didn't exist -> create

                                $detail = SellOutDetail::create([
                                    'sellout_id' => $sellOutHeader->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                ]);

                                /** Insert Summary **/

                                /* Store */
                                $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                            ->where('id', $sellOutHeader->store_id)->first();
                                $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                                /* Product */
                                $product = Product::with('category.group.groupProduct')
                                            ->where('id', $data['product_id'])->first();

                                /* Price */
                                $realPrice = 0;
                                $price = Price::where('product_id', $product->id)
                                            ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                            ->where('sell_type', 'Sell Out')
                                            ->first();

                                if($price){
                                    $realPrice = $price->price;
                                }

                                /* Distributor */
                                $distIds = StoreDistributor::where('store_id', $sellOutHeader->store_id)->pluck('distributor_id');
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

                                $summary = SummarySellOut::create([
                                    'sellout_detail_id' => $detail->id,
                                    'region_id' => $store->district->area->region->id,
                                    'area_id' => $store->district->area->id,
                                    'district_id' => $store->district->id,
                                    'storeId' => $sellOutHeader->store_id,
                                    'user_id' => $sellOutHeader->user_id,
                                    'week' => $sellOutHeader->week,
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
                                    'nik' => $user->nik,
                                    'promoter_name' => $user->name,
                                    'date' => $sellOutHeader->date,
                                    'model' => $product->model . '/' . $product->variants,
                                    'group' => $product->category->group->groupProduct->name,
                                    'category' => $product->category->name,
                                    'product_name' => $product->name,
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                    'unit_price' => $realPrice,
                                    'value' => $realPrice * $data['quantity'],
                                    'value_pf_mr' => $value_pf_mr,
                                    'value_pf_tr' => $value_pf_tr,
                                    'value_pf_ppe' => $value_pf_ppe,
                                    'role' => $user->role->role,
                                    'role_id' => $user->role->id,
                                    'role_group' => $user->role->role_group,
                                    'spv_name' => $spvName,
                                    'dm_name' => $dm_name,
                                    'trainer_name' => $trainer_name,
                                ]);

                                // Actual Summary
                                $summary_ta['user_id'] = $sellOutHeader->user_id;
                                $summary_ta['store_id'] = $sellOutHeader->store_id;
                                $summary_ta['week'] = $sellOutHeader->week;
                                $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                                $summary_ta['value'] = $summary->value;
                                $summary_ta['group'] = $summary->group;
                                $summary_ta['sell_type'] = 'Sell Out';
                                $summary_ta['irisan'] = $summary->irisan;

                                $this->changeActual($summary_ta, 'change');

                            }

                        }

                   });
               } catch (\Exception $e) {
                   return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                   // return redirect(route('sellout'))->with('status', 'Gagal melakukan transaksi');
               }

                return response()->json(['status' => true, 'id_transaksi' => $sellOutHeader->id, 'message' => 'Data berhasil di input']);
                // return redirect(route('sellout'))->with('status', 'Data berhasil di input');

            } else { // If header didn't exist (create header & detail)

               try {
                   DB::transaction(function () use ($content, $user) {

                        // HEADER
                        $transaction = SellOut::create([
                                            'user_id' => $user->id,
                                            'store_id' => $content['id'],
                                            'week' => $content['date']->weekOfMonth,
                                            'date' => $content['date']->format('Y-m-d')
                                        ]);

                        foreach ($content['data'] as $data) {

                            // DETAILS
                            $detail = SellOutDetail::create([
                                    'sellout_id' => $transaction->id,
                                    'product_id' => $data['product_id'],
                                    'quantity' => $data['quantity'],
                                    'irisan' => $content['irisan'],
                                ]);

                            /** Insert Summary **/

                            /* Store */
                            $store = Store::with('district.area.region', 'subChannel.channel.globalChannel', 'user')
                                        ->where('id', $transaction->store_id)->first();
                            $spvName = (isset($store->user->name)) ? $store->user->name : '';

                                $spvDemoName = SpvDemo::where('user_id', $user->id)->first();
                                if(count($spvDemoName) > 0){
                                    $spvName = (isset($spvDemoName->user->name)) ? $spvDemoName->user->name : '';
                                }

                                $customerCode = (isset($store->store_name_2)) ? $store->store_name_2 : '';

                            /* Product */
                            $product = Product::with('category.group.groupProduct')
                                        ->where('id', $detail->product_id)->first();

                            /* Price */
                            $realPrice = 0;
                            $price = Price::where('product_id', $product->id)
                                        ->where('globalchannel_id', $store->subChannel->channel->globalChannel->id)
                                        ->where('sell_type', 'Sell Out')
                                        ->first();

                            if($price){
                                $realPrice = $price->price;
                            }

                            /* Distributor */
                            $distIds = StoreDistributor::where('store_id', $transaction->store_id)->pluck('distributor_id');
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
                                    $value_pf_mr = $realPrice * $detail->quantity;
                                } else if ($productFocusDetail->type == 'Traditional Retail') {
                                    $value_pf_tr = $realPrice * $detail->quantity;
                                } else if ($productFocusDetail->type == 'PPE') {
                                    $value_pf_ppe = $realPrice * $detail->quantity;
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

                            $summary = SummarySellOut::create([
                                'sellout_detail_id' => $detail->id,
                                'region_id' => $store->district->area->region->id,
                                'area_id' => $store->district->area->id,
                                'district_id' => $store->district->id,
                                'storeId' => $transaction->store_id,
                                'user_id' => $transaction->user_id,
                                'week' => $transaction->week,
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
                                'nik' => $user->nik,
                                'promoter_name' => $user->name,
                                'date' => $transaction->date,
                                'model' => $product->model . '/' . $product->variants,
                                'group' => $product->category->group->groupProduct->name,
                                'category' => $product->category->name,
                                'product_name' => $product->name,
                                'quantity' => $detail->quantity,
                                'irisan' => $content['irisan'],
                                'unit_price' => $realPrice,
                                'value' => $realPrice * $detail->quantity,
                                'value_pf_mr' => $value_pf_mr,
                                'value_pf_tr' => $value_pf_tr,
                                'value_pf_ppe' => $value_pf_ppe,
                                'role' => $user->role->role,
                                'role_id' => $user->role->id,
                                'role_group' => $user->role->role_group,
                                'spv_name' => $spvName,
                                'dm_name' => $dm_name,
                                'trainer_name' => $trainer_name,
                            ]);

                            // Actual Summary
                            $summary_ta['user_id'] = $transaction->user_id;
                            $summary_ta['store_id'] = $transaction->store_id;
                            $summary_ta['week'] = $transaction->week;
                            $summary_ta['pf'] = $summary->value_pf_mr + $summary->value_pf_tr + $summary->value_pf_ppe;
                            $summary_ta['value'] = $summary->value;
                            $summary_ta['group'] = $summary->group;
                            $summary_ta['sell_type'] = 'Sell Out';
                            $summary_ta['irisan'] = $summary->irisan;

//                            return response()->json($this->changeActual($summary_ta, 'change'));
                            $this->changeActual($summary_ta, 'change');
                        }

                   });
               } catch (\Exception $e) {
                   return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
               }

                // Check sell in(Sell Through) header after insert
                $sellOutHeaderAfter = SellOut::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', $content['date']->format('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $sellOutHeaderAfter->id, 'message' => 'Data berhasil di input']);
                // return redirect(route('sellout'))->with('status', 'Data berhasil di input');

            }

        // }

        return response()->json(['url' => url('/sellout')]);
            // return redirect(route('sellout'))->with('status', 'Something went wrong.');
    }

}
