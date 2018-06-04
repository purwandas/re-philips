<?php

namespace App\Http\Controllers\Master;

use App\Filters\SellinFilters;
use App\Filters\SellOutFilters;
use App\Filters\StoreFilters;
use App\Filters\UserFilters;
use App\Filters\PriceFilters;
use App\Filters\TargetFilters;
use App\Filters\ApmFilters;
use App\Filters\AttendanceFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Carbon\Carbon;
use App\Helper\ExcelHelper as ExcelHelper;
use App\Area;
use App\District;
use App\Channel;
use App\SubChannel;
use App\Distributor;
use App\Store;
use App\Place;
use App\User;
use App\Group;
use App\Category;
use App\PromoActivity;
use App\PosmActivity;
use App\Product;
use App\Price;
use App\ProductFocuses;
use App\SalesmanProductFocuses;
use App\ProductPromos;
use App\Leadtime;
use App\Target;
use App\SalesmanTarget;
use App\Posm;
use App\GroupCompetitor;
use App\TimeGone;
use App\Apm;
use App\Attendance;
use App\Reports\SalesmanSummarySales;
use App\Reports\SummarySellIn;
use App\Reports\SummaryDisplayShare;
// use App\Reports\HistorySellIn;
use App\Reports\SummarySellOut;
// use App\Reports\HistorySellOut;
use App\Reports\SummarySoh;
// use App\Reports\HistorySoh;
use DB;
use Auth;
use App\VisitPlan;
use App\News;
use App\NewsRead;
use App\ProductKnowledge;
use App\ProductKnowledgeRead;
use App\FeedbackAnswer;
use App\Reports\HistoryDisplayShare;
use App\Reports\HistoryEmployeeStore;
use App\Reports\HistoryFreeProduct;
use App\Reports\HistoryRetConsument;
use App\Reports\HistoryRetDistributor;
use App\Reports\HistorySalesmanSales;
use App\Reports\HistorySalesmanTargetActual;
use App\Reports\HistorySellIn;
use App\Reports\HistorySellOut;
use App\Reports\HistorySoh;
use App\Reports\HistorySos;
use App\Reports\HistoryTargetActual;
use App\Reports\HistoryTbat;
use App\SellOutDetail;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Traits\ReportTrait;

class ExportController extends Controller
{
    use ReportTrait;

    protected $excelHelper;

    public function __construct(ExcelHelper $excelHelper)
    {
        $this->excelHelper = $excelHelper;
    }

    //
    public function exportSellIn(Request $request){

        $filename = 'Philips Retail Report Sell Thru ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);
        // $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Thru');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Thru Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL THRU', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AC1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSales($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AC1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AC1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSellInAll(Request $request){

        $filename = 'Philips Retail Report Sell Thru ' . Carbon::now()->format('d-m-Y');

            // Check data summary atau history
            $monthNow = Carbon::now()->format('m');
            $yearNow = Carbon::now()->format('Y');
            if($request['searchMonth']){
                $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
                $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
                // return "apa";
            }else
            if($request['searchDate']){
                $date = explode('-', $request['searchDate']);
                $monthRequest = $date[1];
                $yearRequest = $date[0];
                // return "apa2";
            }

            $userRole = Auth::user()->role->role_group;
            $userId = Auth::user()->id;

            // if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

                /* If filter */
                if($request['searchMonth']){
                    $month = Carbon::parse($request['searchMonth'])->format('m');
                    $year = Carbon::parse($request['searchMonth'])->format('Y');
                    // $filter = $data->where('month', $month)->where('year', $year);
                    $date1 = "$year-$month-01";
                    $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                    $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                    // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
                }else
                if($request['searchDate']){
                    $date1 = $request['searchDate'];
                    $date2 = $date1;
                    // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
                }else{
                    $month = Carbon::now()->format('m');
                    $year = Carbon::now()->format('Y');
                    $date1 = "$year-$month-01";
                    $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                    $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                }

                $data = SummarySellIn::whereRaw("DATE(date) >= '$date1'")->whereRaw("DATE(date) <= '$date2'")->select(DB::raw("summary_sell_ins.*, LEFT(date, 10) as date"));

                $filter = $data;

                if($request['byRegion']){
                    $filter = $filter->where('region_id', $request['byRegion']);
                }

                if($request['byArea']){
                    $filter = $filter->where('area_id', $request['byArea']);
                }

                if($request['byDistrict']){
                    $filter = $filter->where('district_id', $request['byDistrict']);
                }

                if($request['byStore']){
                    $store = Store::where('stores.id', $request['byStore'])
                                    ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                    ->pluck('storeses.id');
                    $filter = $filter->whereIn('storeId', $store);
                }

                if($request['byEmployee']){
                    $filter = $filter->where('user_id', $request['byEmployee']);
                }

                if ($userRole == 'RSM') {
                    $region = RsmRegion::where('user_id', $userId)
                                ->pluck('rsm_regions.region_id');
                        $filter = $filter->where('region_id', $region);
                }

                elseif ($userRole == 'DM') {
                    $area = DmArea::where('user_id', $userId)
                                ->pluck('dm_areas.area_id');
                        $filter = $filter->where('area_id', $area);
                }

                elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                    $store = Store::where('user_id', $userId)
                                ->pluck('stores.store_id');
                        $filter = $filter->wherein('store_id', $store);
                }

                $filter = $filter->get();


            // }else{ // Fetch data from history

            //     $historyData = new Collection();
            //     $history = HistorySellIn::where('year', $yearRequest)
            //                 ->where('month', $monthRequest)->get();

            //     foreach ($history as $data) {

            //         $details = json_decode($data->details);

            //         foreach ($details as $detail) {

            //             foreach ($detail->transaction as $transaction) {

            //                 $collection = new Collection();

            //                 /* Get Data and Push them to collection */
            //                 $collection['id'] = $data->id;
            //                 $collection['region_id'] = $detail->region_id;
            //                 $collection['area_id'] = $detail->area_id;
            //                 $collection['district_id'] = $detail->district_id;
            //                 $collection['storeId'] = $detail->storeId;
            //                 $collection['user_id'] = $detail->user_id;
            //                 $collection['week'] = $detail->week;
            //                 $collection['distributor_code'] = $detail->distributor_code;
            //                 $collection['distributor_name'] = $detail->distributor_name;
            //                 $collection['region'] = $detail->region;
            //                 $collection['channel'] = $detail->channel;
            //                 $collection['sub_channel'] = $detail->sub_channel;
            //                 $collection['area'] = $detail->area;
            //                 $collection['district'] = $detail->district;
            //                 $collection['store_name_1'] = $detail->store_name_1;
            //                 $collection['store_name_2'] = $detail->store_name_2;
            //                 $collection['store_id'] = $detail->store_id;
            //                 $collection['nik'] = $detail->nik;
            //                 $collection['promoter_name'] = $detail->promoter_name;
            //                 $collection['date'] = $detail->date;
            //                 $collection['model'] = $transaction->model;
            //                 $collection['group'] = $transaction->group;
            //                 $collection['category'] = $transaction->category;
            //                 $collection['product_name'] = $transaction->product_name;
            //                 $collection['quantity'] = $transaction->quantity;
            //                 $collection['unit_price'] = $transaction->unit_price;
            //                 $collection['value'] = $transaction->value;
            //                 $collection['value_pf_mr'] = $transaction->value_pf_mr;
            //                 $collection['value_pf_tr'] = $transaction->value_pf_tr;
            //                 $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
            //                 $collection['irisan'] = $transaction->irisan;
            //                 $collection['role'] = $detail->role;
            //                 $collection['role_id'] = $detail->role_id;
            //                 $collection['role_group'] = $detail->role_group;
            //                 $collection['spv_name'] = $detail->spv_name;
            //                 $collection['dm_name'] = $detail->dm_name;
            //                 $collection['trainer_name'] = $detail->trainer_name;

            //                 $historyData->push($collection);

            //             }

            //         }

            //     }

            //     $filter = $historyData;

            //     /* If filter */
            //     if($request['searchMonth']){
            //         $month = Carbon::parse($request['searchMonth'])->format('m');
            //         $year = Carbon::parse($request['searchMonth'])->format('Y');
            //         $date1 = "$year-$month-01";
            //         $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            //         $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

            //         $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            //     }

            //     if($request['byRegion']){
            //         $filter = $filter->where('region_id', $request['byRegion']);
            //     }

            //     if($request['byArea']){
            //         $filter = $filter->where('area_id', $request['byArea']);
            //     }

            //     if($request['byDistrict']){
            //         $filter = $filter->where('district_id', $request['byDistrict']);
            //     }

            //     if($request['byStore']){
            //         $store = Store::where('stores.id', $request['byStore'])
            //                         ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
            //                         ->pluck('storeses.id');
            //         $filter = $filter->whereIn('storeId', $store);
            //     }

            //     if($request['byEmployee']){
            //         $filter = $filter->where('user_id', $request['byEmployee']);
            //     }

            //     if ($userRole == 'RSM') {
            //         $regionIds = RsmRegion::where('user_id', $userId)
            //                             ->pluck('rsm_regions.region_id');
            //         $filter = $filter->whereIn('region_id', $regionIds);
            //     }

            //     if ($userRole == 'DM') {
            //         $areaIds = DmArea::where('user_id', $userId)
            //                             ->pluck('dm_areas.area_id');
            //         $filter = $filter->whereIn('area_id', $areaIds);
            //     }

            //     if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            //         $storeIds = Store::where('user_id', $userId)
            //                             ->pluck('stores.store_id');
            //         $filter = $filter->whereIn('store_id', $storeIds);
            //     }

            // }
            $filter->all();
            $data = $filter->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Thru');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Thru Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL THRU', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AC1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AC1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AC1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    //
    public function exportVisitPlan(Request $request){

        $filename = 'Philips Retail Report Visit Plan ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);
        // $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Visit Plan');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Visit Plan Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('VISIT PLAN', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:L1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportVisitPlan($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:L1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:L1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    //
    public function exportVisitPlanAll(Request $request){

        $filename = 'Philips Retail Report Visit Plan ' . Carbon::now()->format('d-m-Y');
        // $data = json_decode($request['data'], true);
        // $data = $request->data;

         // Check data summary atau history
            $monthNow = Carbon::now()->format('m');
            $yearNow = Carbon::now()->format('Y');
            if($request['searchMonth']){
                $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
                $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
                // return "apa";
            }else
            if($request['searchDate']){
                $date = explode('-', $request['searchDate']);
                $monthRequest = $date[1];
                $yearRequest = $date[0];
                // return "apa2";
            }

        if($request['searchMonth']){
            $month = Carbon::parse($request['searchMonth'])->format('m');
            $year = Carbon::parse($request['searchMonth'])->format('Y');
            // $filter = $data->where('month', $month)->where('year', $year);
            $date1 = "$year-$month-01";
            $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
        }else if($request['searchDate']){
            $date1 = $request['searchDate'];
            $date2 = $date1;
            // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
        }else{
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $date1 = "$year-$month-01";
            $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
        }

         $data = VisitPlan::
                    join('stores', 'visit_plans.store_id', '=', 'stores.id')
                    ->join('users', 'visit_plans.user_id', '=', 'users.id')
                    ->join('roles','roles.id','users.role_id')
                    ->select('visit_plans.*', 'users.nik as user_nik', 'users.name as user_name',  'roles.role_group as user_role', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeId')
                    ->where('date','>=',$date1)->where('date','<=',$date2)
                    ->get();

        $filter = $data;

        /* If filter */
        if($request['byNik']){
            $filter = $filter->where('user_id', $request['byNik']);
        }

        if($request['byRole'] != ''){
            $filter = $filter->where('user_role', $request['byRole']);
        }

        $excel = Excel::create($filename, function($excel) use ($filter) {

            // Set the title
            $excel->setTitle('Report Visit Plan');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Visit Plan Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('VISIT PLAN', function ($sheet) use ($filter) {
                $sheet->setAutoFilter('A1:L1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportVisitPlanAll($filter->all()), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:L1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:L1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSellOut(Request $request){

        $filename = 'Philips Retail Report Sell Out ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Out');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Out Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL OUT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AC1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSales($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AC1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AC1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSellOutAllCsv(Request $request, SellOutFilters $filters){

        $users = User::get(); // All users
        $csvExporter = new \Laracsv\Export();
        $csv = $csvExporter->build($users, ['email', 'name'])->getCsv();

        return response()->json(['file' => (string) $csv]);

    }

    public function exportSellOutAllNew(Request $request, SellOutFilters $filters){

        $filename = 'Philips Retail Report Sell Out ' . Carbon::now()->format('d-m-Y');

        // $data = SellOutDetail::filter($filters)->with('sellOut.store.district.area.region', 'sellOut.user', 'product.category.group')->get();

        $data = SellOutDetail::filter($filters)
                ->leftJoin('sell_outs', 'sell_out_details.sellout_id', '=', 'sell_outs.id')
                ->leftJoin('stores', 'sell_outs.store_id', '=', 'stores.id')
                // ->leftJoin('spv_demos', 'spv_demos.store_id', '=', 'stores.id')
                // ->leftJoin('users as spv_demo', 'spv_demos.user_id', '=', 'spv_demo.id')
                // ->leftJoin('users as spv', 'stores.user_id', '=', 'spv.id')
                // ->leftJoin('store_distributors', 'store_distributors.store_id', '=', 'stores.id')
                // ->leftJoin('distributors', 'store_distributors.distributor_id', '=', 'distributors.id')
                ->leftJoin('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                ->leftJoin('channels', 'sub_channels.channel_id', '=', 'channels.id')
                ->leftJoin('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                ->leftJoin('districts', 'stores.district_id', '=', 'districts.id')
                ->leftJoin('areas', 'districts.area_id', '=', 'areas.id')
                // ->leftJoin('dm_areas', 'dm_areas.area_id', '=', 'areas.id')
                // ->leftJoin('users as dm', 'dm_areas.user_id', '=', 'dm.id')
                // ->leftJoin('trainer_areas', 'trainer_areas.area_id', '=', 'areas.id')
                // ->leftJoin('users as trainer', 'trainer_areas.user_id', '=', 'trainer.id')
                ->leftJoin('regions', 'areas.region_id', '=', 'regions.id')
                ->leftJoin('users', 'sell_outs.user_id', '=', 'users.id')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->leftJoin('products', 'sell_out_details.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                ->groupBy('sell_out_details.id')
                // ->select('sell_out_details.id','sell_outs.week', 'distributors.code as distributor_code', 'distributors.name as distributor_name', 'channels.name as channel', 'sub_channels.name as sub_channel', 'regions.name as region', 'areas.name as area', 'districts.name as district', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id', 'users.nik', 'users.name as promoter_name', 'sell_outs.date', 'products.model', 'groups.name as group', 'categories.name as category', 'products.name as product_name', 'sell_out_details.quantity', 'sell_out_details.amount as unit_price', DB::raw('(sell_out_details.amount * sell_out_details.quantity) as value'), 'sell_out_details.irisan', 'roles.role_group as role', 'dm.name as dm_name', 'trainer.name as trainer_name', 'spv.name as spv_name', 'spv_demo.name as spv_demo_name')->get();
                ->select('sell_out_details.id','sell_outs.week', 'channels.name as channel', 'sub_channels.name as sub_channel', 'regions.name as region', 'areas.name as area', 'districts.name as district', 'stores.store_name_1', 'stores.store_name_2', 'stores.store_id', 'users.nik', 'users.name as promoter_name', 'sell_outs.date', 'products.model', 'groups.name as group', 'categories.name as category', 'products.name as product_name', 'sell_out_details.quantity', 'sell_out_details.amount as unit_price', DB::raw('(sell_out_details.amount * sell_out_details.quantity) as value'), 'sell_out_details.irisan', 'roles.role_group as role', 'stores.id as storeId')->get();

                // return $data;

                // $list = collect($data);

                // return $list;

                $filename = 'apalah.xlsx';
                $excel = (new FastExcel($data))->export('exports/excel/'.$filename, function ($item){
                    return [
                        'WEEK' => ($item->week) ? $item->week : '',
                        'DISTRIBUTOR CODE' => $this->getDistributorCode($item->storeId),
                        'DISTRIBUTOR NAME' => ($item->distributorName) ? $item->distributorName : '',
                        'REGION' => ($item->region) ? $item->region : '',
                        'CHANNEL' => ($item->channel) ? $item->channel : '',
                        // 'CHANNEL' => $item->channel,
                        // 'SUB CHANNEL' => $item->sub_channel,
                        // 'AREA' => $item->area,
                        // 'DISTRICT' => $item->district,
                        // 'STORE NAME 1' => $item->store_name_1,
                        // 'CUSTOMER CODE' => $item->store_name_2,
                        // 'STORE ID' => $item->store_id,
                        // 'NIK' => $item->nik,
                        // 'PROMOTER NAME' => $item->promoter_name,
                        // 'DATE' => $item->date,
                        // 'MODEL' => $item->model,
                        // 'GROUP' => $item->group,
                        // 'CATEGORY' => html_entity_decode($item->category),
                        // 'PRODUCT NAME' => html_entity_decode($item->product_name),
                        // 'QUANTITY' => $item->quantity,
                        // 'UNIT PRICE' => number_format($item->unit_price),
                        // 'VALUE' => number_format($item->unit_price * $item->quantity),
                        // 'VALUE PF MR' => 0,//number_format($item->value_pf_mr),
                        // 'VALUE PF TR' => 0,//number_format($item->value_pf_tr),
                        // 'VALUE PF PPE' => 0,//number_format($item->value_pf_ppe),
                        // 'IRISAN' => ($item->irisan == 0) ? '-' : ($item->irisan == null) ? '-' : 'Irisan',
                        // 'ROLE' => $item->role,
                        // 'SPV NAME' => $item->spv_name,
                        // 'DM NAME' => $item->dm_name,
                        // 'TRAINER NAME' => $item->trainer_name,
                    ];
                });

                // return response()->json(['name' => public_path('file.xlsx')]);

                // return response()->json(['name' => $filename, 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

                return response()->json(['url' => 'exports/excel/'.$filename]);

        // return $data->simple()->get();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Out');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Out Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL OUT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AC1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AC1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AC1', 'thin');
            });


        })->string('xlsx');
        
        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);
    }

    public function exportSellOutAll(Request $request){

        $filename = 'Philips Retail Report Sell Out ' . Carbon::now()->format('d-m-Y');

        // Check data summary atau history
        $monthNow = Carbon::now()->format('m');
        $yearNow = Carbon::now()->format('Y');
        if($request['searchMonth']){
            $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
            $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
            // return "apa";
        }else
        if($request['searchDate']){
            $date = explode('-', $request['searchDate']);
            $monthRequest = $date[1];
            $yearRequest = $date[0];
            // return "apa2";
        }else{
            $monthRequest = $monthNow;
            $yearRequest = $yearNow;
            // return "apa3";
        }


        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        // if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else
            if($request['searchDate']){
                $date1 = $request['searchDate'];
                $date2 = $date1;
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else{
                $month = Carbon::now()->format('m');
                $year = Carbon::now()->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
            }

            $data = SummarySellOut::whereRaw("DATE(date) >= '$date1'")->whereRaw("DATE(date) <= '$date2'")->select(DB::raw("summary_sell_outs.*, LEFT(date, 10) as date"));

            $filter = $data;

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $filter = $filter->where('region_id', $region);
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $filter = $filter->where('area_id', $area);
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $filter = $filter->wherein('store_id', $store);
            }

            $filter = $filter->get();

        // }else{ // Fetch data from history

        //     $historyData = new Collection();

        //     $history = HistorySellOut::where('year', $yearRequest)
        //                 ->where('month', $monthRequest)->get();

        //     foreach ($history as $data) {

        //         $details = json_decode($data->details);

        //         foreach ($details as $detail) {

        //             foreach ($detail->transaction as $transaction) {

        //                 $collection = new Collection();

        //                 /* Get Data and Push them to collection */
        //                 $collection['id'] = $data->id;
        //                 $collection['region_id'] = $detail->region_id;
        //                 $collection['area_id'] = $detail->area_id;
        //                 $collection['district_id'] = $detail->district_id;
        //                 $collection['storeId'] = $detail->storeId;
        //                 $collection['user_id'] = $detail->user_id;
        //                 $collection['week'] = $detail->week;
        //                 $collection['distributor_code'] = $detail->distributor_code;
        //                 $collection['distributor_name'] = $detail->distributor_name;
        //                 $collection['region'] = $detail->region;
        //                 $collection['channel'] = $detail->channel;
        //                 $collection['sub_channel'] = $detail->sub_channel;
        //                 $collection['area'] = $detail->area;
        //                 $collection['district'] = $detail->district;
        //                 $collection['store_name_1'] = $detail->store_name_1;
        //                 $collection['store_name_2'] = $detail->store_name_2;
        //                 $collection['store_id'] = $detail->store_id;
        //                 $collection['nik'] = $detail->nik;
        //                 $collection['promoter_name'] = $detail->promoter_name;
        //                 $collection['date'] = $detail->date;
        //                 $collection['model'] = $transaction->model;
        //                 $collection['group'] = $transaction->group;
        //                 $collection['category'] = $transaction->category;
        //                 $collection['product_name'] = $transaction->product_name;
        //                 $collection['quantity'] = $transaction->quantity;
        //                 $collection['unit_price'] = $transaction->unit_price;
        //                 $collection['value'] = $transaction->value;
        //                 $collection['value_pf_mr'] = $transaction->value_pf_mr;
        //                 $collection['value_pf_tr'] = $transaction->value_pf_tr;
        //                 $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
        //                 $collection['irisan'] = $transaction->irisan;
        //                 $collection['role'] = $detail->role;
        //                 $collection['role_id'] = $detail->role_id;
        //                 $collection['role_group'] = $detail->role_group;
        //                 $collection['spv_name'] = $detail->spv_name;
        //                 $collection['dm_name'] = $detail->dm_name;
        //                 $collection['trainer_name'] = $detail->trainer_name;

        //                 $historyData->push($collection);

        //             }

        //         }

        //     }

        //     $filter = $historyData;

        //     /* If filter */
        //     if($request['searchMonth']){
        //         $month = Carbon::parse($request['searchMonth'])->format('m');
        //         $year = Carbon::parse($request['searchMonth'])->format('Y');
        //         $date1 = "$year-$month-01";
        //         $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
        //         $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

        //         $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
        //     }

        //     if($request['byRegion']){
        //         $filter = $filter->where('region_id', $request['byRegion']);
        //     }

        //     if($request['byArea']){
        //         $filter = $filter->where('area_id', $request['byArea']);
        //     }

        //     if($request['byDistrict']){
        //         $filter = $filter->where('district_id', $request['byDistrict']);
        //     }

        //     if($request['byStore']){
        //         $store = Store::where('stores.id', $request['byStore'])
        //                         ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
        //                         ->pluck('storeses.id');
        //         $filter = $filter->whereIn('storeId', $store);
        //     }

        //     if($request['byEmployee']){
        //         $filter = $filter->where('user_id', $request['byEmployee']);
        //     }

        //     if ($userRole == 'RSM') {
        //         $regionIds = RsmRegion::where('user_id', $userId)
        //                             ->pluck('rsm_regions.region_id');
        //         $filter = $filter->whereIn('region_id', $regionIds);
        //     }

        //     if ($userRole == 'DM') {
        //         $areaIds = DmArea::where('user_id', $userId)
        //                             ->pluck('dm_areas.area_id');
        //         $filter = $filter->whereIn('area_id', $areaIds);
        //     }

        //     if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
        //         $storeIds = Store::where('user_id', $userId)
        //                             ->pluck('stores.store_id');
        //         $filter = $filter->whereIn('store_id', $storeIds);
        //     }

        // }

            $filter->all();
            $data = $filter->toArray();

            return $data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Out');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Out Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL OUT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AC1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AC1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AC1', 'thin');
            });


        })->string('xlsx');
        
        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);
    }

    public function exportRetConsument(Request $request){

        $filename = 'Philips Retail Report Ret. Consument ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Consument');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Consument Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. CONSUMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportRetConsumentAll(Request $request){

        $filename = 'Philips Retail Report Ret. Consument ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Consument');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Consument Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. CONSUMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportRetDistributor(Request $request){

        $filename = 'Philips Retail Report Ret. Distributor ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Distributor Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. DISTRIBUTOR', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportRetDistributorAll(Request $request){

        $filename = 'Philips Retail Report Ret. Distributor ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Distributor Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. DISTRIBUTOR', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportFreeProduct(Request $request){

        $filename = 'Philips Retail Report Free Product ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Free Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Free Product Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('FREE PRODUCT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportFreeProductAll(Request $request){

        $filename = 'Philips Retail Report Free Product ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Free Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Free Product Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('FREE PRODUCT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportTbat(Request $request){

        $filename = 'Philips Retail Report TBAT ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report TBAT');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('TBAT Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('TBAT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AE1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTbat($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AE1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AE1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportTbatAll(Request $request){

        $filename = 'Philips Retail Report TBAT ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report TBAT');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('TBAT Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('TBAT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AE1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTbatAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AE1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AE1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSoh(Request $request){

        $filename = 'Philips Retail Report SOH ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report SOH');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('SOH Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SOH', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSohAll(Request $request){

        $filename = 'Philips Retail Report SOH ' . Carbon::now()->format('d-m-Y');

        // Check data summary atau history
        $monthNow = Carbon::now()->format('m');
        $yearNow = Carbon::now()->format('Y');
        if($request['searchMonth']){
            $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
            $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
            // return "apa";
        }else
        if($request['searchDate']){
            $date = explode('-', $request['searchDate']);
            $monthRequest = $date[1];
            $yearRequest = $date[0];
            // return "apa2";
        }else{
            $monthRequest = $monthNow;
            $yearRequest = $yearNow;
            // return "apa3";
        }

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else
            if($request['searchDate']){
                $date1 = $request['searchDate'];
                $date2 = $date1;
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else{
                $month = Carbon::now()->format('m');
                $year = Carbon::now()->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
            }

            $filter = SummarySoh::select(DB::raw("summary_sohs.*, LEFT(date, 10) as date"))->whereRaw("DATE(date) >= '$date1'")->whereRaw("DATE(date) <= '$date2'");

            /* If filter */
            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $filter = $filter->where('region_id', $region);
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $filter = $filter->where('area_id', $area);
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $filter = $filter->wherein('store_id', $store);
            }

            $filter = $filter->get();

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySoh::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = number_format($transaction->quantity);
                        $collection['unit_price'] = number_format($transaction->unit_price);
                        $collection['value'] = number_format($transaction->value);
                        $collection['role'] = $detail->role;
                        $collection['role_id'] = $detail->role_id;
                        $collection['role_group'] = $detail->role_group;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }

        }

        $data = $filter->all();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report SOH');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('SOH Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SOH', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Y1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Y1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSos(Request $request){

        $filename = 'Philips Retail Report SOS ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report SOS');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('SOS Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SOS', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportDisplayShare(Request $request){

        $filename = 'Philips Retail Report Display Share ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Display Share');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Display Share Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('DISPLAY SHARE', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:V1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDisplayShare($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:V1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:V1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportDisplayShareAll(Request $request){

        $filename = 'Philips Retail Report Display Share ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        // if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

 
            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)
                            ->pluck('rsm_regions.region_id');
                    $data = SummaryDisplayShare::where('region_id', $region)->get();
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)
                            ->pluck('dm_areas.area_id');
                    $data = SummaryDisplayShare::where('area_id', $area)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                    $data = SummaryDisplayShare::wherein('store_id', $store)->get();
            }
            else{
                $data = SummaryDisplayShare::all();
            }          

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            // return $filter->all();
            
            $data = $filter->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Display Share');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Display Share Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('DISPLAY SHARE', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:V1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDisplayShare($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:V1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:V1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportMaintenanceRequest(Request $request){

        $filename = 'Philips Retail Report Maintenance Request ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Maintenance Request');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Maintenance Request Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('MAINTENANCE REQUEST', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportMaintenance($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportCompetitorActivity(Request $request){

        $filename = 'Philips Retail Report Competitor Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Competitor Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Competitor Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('COMPETITOR ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:P1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportCompetitor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:P1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:P1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportCompetitorActivityAll(Request $request){

        $filename = 'Philips Retail Report Competitor Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Competitor Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Competitor Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('COMPETITOR ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:P1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportCompetitor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:P1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:P1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPromoActivity(Request $request){

        $filename = 'Philips Retail Report Promo Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Promo Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Promo Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('PROMO ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Q1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportPromo($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Q1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Q1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPromoActivityAll(Request $request){

        $filename = 'Philips Retail Report Promo Activity ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        
        $monthNow = Carbon::now()->format('m');
        $yearNow = Carbon::now()->format('Y');
        if($request['searchMonth']){
            $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
            $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
            // return "apa";
        }else
        if($request['searchDate']){
            $date = explode('-', $request['searchDate']);
            $monthRequest = $date[1];
            $yearRequest = $date[0];
            // return "apa2";
        }else{
            $monthRequest = $monthNow;
            $yearRequest = $yearNow;
            // return "apa3";
        }

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        
        /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else
            if($request['searchDate']){
                $date1 = $request['searchDate'];
                $date2 = $date1;
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else{
                $month = Carbon::now()->format('m');
                $year = Carbon::now()->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
            }

            $data = PromoActivity::
                    join('promo_activity_details', 'promo_activity_details.promoactivity_id', '=', 'promo_activities.id')
                    ->join('stores', 'promo_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'promo_activities.user_id', '=', 'users.id')
                    ->join('products', 'promo_activity_details.product_id', '=', 'products.id')
                    ->whereRaw("DATE(promo_activities.date) >= '$date1'")
                    ->whereRaw("DATE(promo_activities.date) <= '$date2'")
                    ->select('promo_activities.*', 'promo_activity_details.promo as promo', 'promo_activity_details.product_id', 'promo_activities.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'products.model as product_model', 'products.name as product_name', 'products.variants as product_variants', 'stores.id as storeId')
                    ->get();

            $filter = $data;

            /* If filter */
            // if($request['searchMonth']){
            //     $month = Carbon::parse($request['searchMonth'])->format('m');
            //     $year = Carbon::parse($request['searchMonth'])->format('Y');
            //     // $filter = $data->where('month', $month)->where('year', $year);
            //     $date1 = "$year-$month-01";
            //     $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
            //     $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

            //     $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            // }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            if($request['byProduct']){
                $filter = $filter->where('product_id', $request['byProduct']);
            }

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)
                                    ->pluck('rsm_regions.region_id');
                $filter = $filter->whereIn('region_id', $regionIds);
            }

            if ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)
                                    ->pluck('dm_areas.area_id');
                $filter = $filter->whereIn('area_id', $areaIds);
            }

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)
                                    ->pluck('stores.store_id');
                $filter = $filter->whereIn('store_id', $storeIds);
            }
            
            // $filter = $filter->get();
            // $filter->all();
            $data = $filter->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Promo Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Promo Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('PROMO ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:Q1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportPromo($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:Q1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:Q1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPosmActivity(Request $request){

        $filename = 'Philips Retail Report POSM Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report POSM Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('POSM Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('POSM ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportPosm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPosmActivityAll(Request $request){

        $filename = 'Philips Retail Report POSM Activity ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

            $data = PosmActivity::
                    join('posm_activity_details', 'posm_activity_details.posmactivity_id', '=', 'posm_activities.id')
                    ->join('stores', 'posm_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'posm_activities.user_id', '=', 'users.id')
                    ->join('posms', 'posm_activity_details.posm_id', '=', 'posms.id')
                    ->join('groups', 'posms.group_id', '=', 'groups.id')
                    ->select('posm_activities.*', 'posm_activity_details.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'users.name as user_name', 'posms.name as posm_name', 'groups.name as group_product', 'posm_activity_details.quantity', 'posm_activity_details.photo', 'stores.id as storeId');

            $filter = $data;

            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }

            $data = $filter->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report POSM Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('POSM Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('POSM ACTIVITY', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportPosm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportAttendanceReport(Request $request){

        $filename = 'Philips Retail Report Attendance Report ' . Carbon::now()->format('d-m-Y');
        // $data = $request->data;
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Attendance');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Attendance Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('ATTENDANCE', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AJ1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAttendance($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportAttendanceReportAll(Request $request, $param){

        $filename = 'Philips Retail Report Attendance Report ' . Carbon::now()->format('d-m-Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

       

       if ($param == 1) { //Promoter
            $month = Carbon::parse($request['searchMonth'])->format('m');
           $year = Carbon::parse($request['searchMonth'])->format('Y');
           $date1 = "$year-$month-01";
           $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
           $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
           $data = Attendance::
            join('employee_stores', 'employee_stores.user_id', '=', 'attendances.user_id')
            ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
            ->join('districts', 'stores.district_id', '=', 'districts.id')
            ->join('areas', 'districts.area_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('roles','roles.id','users.role_id')
            ->groupBy('attendances.user_id')
            ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'roles.role_group as user_role', 'stores.id as store_id', 'stores.id as storeId', 'districts.id as district_id', 'areas.id as area_id', 'regions.id as region_id')
            ->where('attendances.date','>=',(string)$date1)->where('attendances.date','<=',(string)$date2)
            ->where('is_resign',0);

            if($request['byStore']){
                $data = $data->whereIn('stores.id',[$request['byStore']]);
            }
            if($request['byDistrict']){
                $data = $data->whereIn('districts.id', [$request['byDistrict']]);
            }
            if($request['byArea']){
                $data = $data->whereIn('areas.id', [$request['byArea']]);
            }
            if($request['byRegion']){
                $data = $data->whereIn('regions.id', [$request['byRegion']]);
            }
            if($request['byEmployee']){
                $data = $data->where('attendances.user_id', $request['byEmployee']);
            }
       }else if ($param == 2) { //Spv
            $month = Carbon::parse($request['searchMonthSpv'])->format('m');
           $year = Carbon::parse($request['searchMonthSpv'])->format('Y');
           $date1 = "$year-$month-01";
           $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
           $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
           $data = Attendance::
            join('stores', 'attendances.user_id', '=', 'stores.user_id')
            ->join('districts', 'stores.district_id', '=', 'districts.id')
            ->join('areas', 'districts.area_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('roles','roles.id','users.role_id')
            ->groupBy('attendances.user_id')
            ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'roles.role_group as user_role', 'stores.id as store_id', 'stores.id as storeId', 'districts.id as district_id', 'areas.id as area_id', 'regions.id as region_id')
            ->where('attendances.date','>=',(string)$date1)->where('attendances.date','<=',(string)$date2)
            ->where('is_resign',0);

            if($request['byStoreSpv']){
                $data = $data->whereIn('stores.id',[$request['byStoreSpv']]);
            }
            if($request['byDistrictSpv']){
                $data = $data->whereIn('districts.id', [$request['byDistrictSpv']]);
            }
            if($request['byAreaSpv']){
                $data = $data->whereIn('areas.id', [$request['byAreaSpv']]);
            }
            if($request['byRegionSpv']){
                $data = $data->whereIn('regions.id', [$request['byRegionSpv']]);
            }
            if($request['byEmployeeSpv']){
                $data = $data->where('attendances.user_id', $request['byEmployeeSpv']);
            }
       }else if ($param == 3) { //Demonstrator
            $month = Carbon::parse($request['searchMonthDemo'])->format('m');
           $year = Carbon::parse($request['searchMonthDemo'])->format('Y');
           $date1 = "$year-$month-01";
           $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
           $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
           $data = Attendance::
            join('spv_demos', 'spv_demos.user_id', '=', 'attendances.user_id')
            ->join('stores', 'spv_demos.store_id', '=', 'stores.id')
            ->join('districts', 'stores.district_id', '=', 'districts.id')
            ->join('areas', 'districts.area_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('roles','roles.id','users.role_id')
            ->groupBy('attendances.user_id')
            ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'roles.role_group as user_role', 'stores.id as store_id', 'stores.id as storeId', 'districts.id as district_id', 'areas.id as area_id', 'regions.id as region_id')
            ->where('attendances.date','>=',(string)$date1)->where('attendances.date','<=',(string)$date2)
            ->where('is_resign',0);

            if($request['byStoreDemo']){
                $data = $data->whereIn('stores.id',[$request['byStoreDemo']]);
            }
            if($request['byDistrictDemo']){
                $data = $data->whereIn('districts.id', [$request['byDistrictDemo']]);
            }
            if($request['byAreaDemo']){
                $data = $data->whereIn('areas.id', [$request['byAreaDemo']]);
            }
            if($request['byRegionDemo']){
                $data = $data->whereIn('regions.id', [$request['byRegionDemo']]);
            }
            if($request['byEmployeeDemo']){
                $data = $data->where('attendances.user_id', $request['byEmployeeDemo']);
            }
       }else if ($param == 4) { //Others
            $month = Carbon::parse($request['searchMonthOthers'])->format('m');
           $year = Carbon::parse($request['searchMonthOthers'])->format('Y');
           $date1 = "$year-$month-01";
           $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
           $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
            $promoterGroup = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT' , 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC', 'Supervisor', 'Supervisor Hybrid'];

           $data = Attendance::
            join('users', 'attendances.user_id', '=', 'users.id')
            ->join('roles','roles.id','users.role_id')
            ->groupBy('attendances.user_id')
            ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'roles.role_group as user_role')
            ->whereNotIn('roles.role_group',$promoterGroup)
            ->where('attendances.date','>=',(string)$date1)->where('attendances.date','<=',(string)$date2);

            
            if($request['byEmployeeOthers']){
                $data = $data->where('attendances.user_id', $request['byEmployeeOthers']);
            }
       }


        if ($userRole == 'RSM') {
            $regionIds = RsmRegion::where('user_id', $userId)
                                ->pluck('rsm_regions.region_id');
            $data = $data->whereIn('region_id', [$regionIds]);
        }
        if ($userRole == 'DM') {
            $areaIds = DmArea::where('user_id', $userId)
                                ->pluck('dm_areas.area_id');
            $data = $data->whereIn('area_id', [$areaIds]);
        }
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $storeIds = Store::where('user_id', $userId)
                                ->pluck('stores.store_id');
            $data = $data->whereIn('store_id', [$storeIds]);
        }
        $data = $data->get();

        // Generate Attendance Details
        $minDate = "$year-$month-01";
        $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
        $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));
        $statusA = ['Alpha','Masuk',     'Sakit',    'Izin',     'Pending Sakit','Pending Izin', 'Off', 'Pending Off'];
        foreach ($data as $key => $value) {

                $dataD = Attendance::
                        select(DB::raw('count(*) as total_hk'))
                        ->where('attendances.status', '!=', 'Off')
                        ->where('attendances.status', '!=', 'Sakit')
                        ->where('attendances.status', '!=', 'Izin')
                        ->where('attendances.status', '!=', 'Pending Sakit')
                        ->where('attendances.status', '!=', 'Pending Izin')
                        ->where('attendances.status', '!=', 'Alpha')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$value->user_id)
                        ->get()->all();
                $hk = 0;
                foreach ($dataD as $key => $value2) {
                    $hk = $value2->total_hk;
                }
            $value['total_hk'] = $hk;

                    

                    $dataDetail = Attendance::
                        select('attendances.*')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$value->user_id)
                        ->orderBy('id','asc')
                        ->get()->all();

                        $status = '';
                if ($param == 1) {
                    // foreach ($dataDetail as $key => $value2) {
                    //     if ($key==0) {
                    //         if (substr($value2->date,-2) > 1) {
                    //             $joinDate = substr($value2->date, -2);
                    //             $execOnce = false;
                    //         }

                    //         if (isset($joinDate)) {
                    //             $status .= '-';
                    //             for ($jd=1; $jd < $joinDate; $jd++) { 
                    //                 $status .= ',-';
                    //             }
                    //         }else{
                    //             $status .= $value2->status;
                    //         }
                    //     }else{
                    //         $status .= ','.$value2->status;
                    //     }
                    // }
                    // if ($value->user_role == 'Salesman Explorer') {
                    //     $dateAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    //     $statusAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    //     foreach ($dataDetail as $key => $value2) {
                    //         $statusAttendance[] = $value2->status;
                    //         $date = explode('-',$value2->date);
                    //         $dateAttendance[] = $date[2];
                    //     }

                    //     /* Repeat as much as max day in month */
                    //     $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    //     for ($i=1; $i <= $totalDay ; $i++) {                         
                            
                    //         if (!empty(array_search((string)($i),$dateAttendance))) {
                    //             $checkAttendance = array_search((string)($i),$dateAttendance);
                    //             foreach ($statusA as $key => $value2) {
                    //                 if (isset($statusAttendance[$checkAttendance])) {
                    //                     if ($value2 == $statusAttendance[$checkAttendance]) {
                    //                         $status .= ','.$value2;
                    //                         break;
                    //                     }
                    //                 }
                    //             }
                    //         }else{
                    //             $status .= ', Alpha';
                    //         }
                    //     }
                    // }else{
                    //     $dateAttendance = [];
                    //     foreach ($dataDetail as $key => $value2) {
                    //         $statusAttendance[] = $value2->status;
                    //         $idAttendance[] = $value2->id;
                    //         $dateAttendance[] = $value2->date;

                    //         if ($key == 0) {
                    //             if (substr($value2->date,-2) > 1) {
                    //                 $joinDate = substr($value2->date, -2);//tanggal dia masuk, tanggal berapa
                    //                 $execOnce = false;
                    //             }
                    //         }
                    //     }

                    //     /* Repeat as much as max day in month */
                        
                    //     // return $startNumber;
                    //     $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    //     if (isset($joinDate)) {
                    //         $totalDay -= ($joinDate-1);
                    //     }
                    //     for ($i=1; $i <= $totalDay ; $i++) { 

                    //         $index = 0;
                    //         foreach ($statusA as $key => $value2) {
                    //             // $index = $key;
                    //             if (isset($statusAttendance[$i-1])) {
                    //                 if ($value2 == $statusAttendance[$i-1]) {
                    //                     $index = $key;
                    //                     break;
                    //                 }
                    //             }
                    //         }


                    //         if (isset($joinDate)) {
                    //             $status .= '-';
                    //             for ($jd=1; $jd < $joinDate; $jd++) {
                    //                 $status .= ',-';
                    //             }
                    //             $execOnce = true;
                    //         }

                    //         $status .= ",".$statusA[$index];
                    //     }
                    // }
                    $dateAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    $statusAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                        foreach ($dataDetail as $key => $value2) {
                            $statusAttendance[] = $value2->status;
                            $date = explode('-',$value2->date);
                            $dateAttendance[] = $date[2];
                            // $status .= ",$date[2]-".$value2->status;
                        }

                        /* Repeat as much as max day in month */
                        $commas = '';
                        $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        for ($i=1; $i <= $totalDay ; $i++) {                         
                            if (!empty(array_search((string)($i),$dateAttendance))) {

                                $checkAttendance = array_search((string)($i),$dateAttendance);

                                foreach ($statusA as $key => $value2) {
                                    if (isset($statusAttendance[$checkAttendance])) {
                                        if ($value2 == $statusAttendance[$checkAttendance]) {
                                            $status .= $commas.$value2;
                                            break;
                                        }
                                    }
                                }

                            }else{
                                $status .= $commas.'Alpha';
                            }
                            $commas = ',';
                        }
                }else 
                if ($param == 4) {
                    $dateAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    $statusAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                        foreach ($dataDetail as $key => $value2) {
                            $statusAttendance[] = $value2->status;
                                    // $status .= ','.$value2->status;
                            $date = explode('-',$value2->date);
                            $dateAttendance[] = $date[2];
                        }

                        /* Repeat as much as max day in month */
                        $commas = '';
                        $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        for ($i=1; $i <= $totalDay ; $i++) {                         
                            if (!empty(array_search((string)($i),$dateAttendance))) {

                                $checkAttendance = array_search((string)($i),$dateAttendance);
                                // $status .= ','.$checkAttendance;
                                foreach ($statusA as $key => $value2) {
                                    if (isset($statusAttendance[$checkAttendance])) {
                                        if ($value2 == $statusAttendance[$checkAttendance]) {
                                            $status .= $commas.$value2;
                                            break;
                                        }
                                    }
                                }
                            }else{
                                $status .= $commas.'Alpha';
                            }
                            $commas = ',';
                        }
                
                }else{
                    $dateAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    $statusAttendance = ['z'];//handling karna (array ke) 0 pasti dianggap empty
                    foreach ($dataDetail as $key => $value2) {
                        $statusAttendance[] = $value2->status;
                        $idAttendance[] = $value2->id;
                        $date = explode('-',$value2->date);
                        $dateAttendance[] = $date[2];
                    }

                    /* Repeat as much as max day in month */

                    $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    for ($i=1; $i <= $totalDay ; $i++) {
                        if ($i==1) {
                            if (!empty(array_search((string)($i),$dateAttendance))) {
                                $checkAttendance = array_search((string)($i),$dateAttendance);
                                $status .= $statusAttendance[$checkAttendance];
                            }else{
                                $status .= 'Alpha';
                            }
                        }else{
                            if (!empty(array_search((string)($i),$dateAttendance))) {
                                $checkAttendance = array_search((string)($i),$dateAttendance);
                                $status .= ','.$statusAttendance[$checkAttendance];
                            }else{
                                $status .= ',Alpha';
                            }
                        }
                    }
                }

            $value['attendance_detail_excell'] = $status;
        }


        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Attendance');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Attendance Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('ATTENDANCE', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AJ1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAttendance($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportAchievementReport(Request $request){

        $filename = 'Philips Retail Report Achievement Report ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Achievement');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Achievement Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('ACHIEVEMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:BM1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAchievement($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:BM1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:BM1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    public function deleteExport(Request $request){

        try{

            $url = $request->data;
            File::delete(public_path() . '/' . $url);

        }catch (\Exception $exception){
            return "There is error in deleting excel";
        }

    }

    public function exportSalesman(Request $request){

        $filename = 'Philips Retail Report Salesman Sales ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Salesman');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SALESMAN', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesman($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSalesmanAll(Request $request){

        $filename = 'Philips Retail Report Salesman Sales ' . Carbon::now()->format('d-m-Y');

        // Check data summary atau history
        $monthNow = Carbon::now()->format('m');
        $yearNow = Carbon::now()->format('Y');
        if($request['searchMonth']){
            $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
            $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
            // return "apa";
        }else if($request['searchDate']){
            $date = explode('-', $request['searchDate']);
            $monthRequest = $date[1];
            $yearRequest = $date[0];
            // return "apa2";
        }

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;
        // if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $regionIds = RsmRegion::where('user_id', $userId)->pluck('region_id');
                $data = SalesmanSummarySales::whereIn('region_id', $regionIds)->get();
            }

            elseif ($userRole == 'DM') {
                $areaIds = DmArea::where('user_id', $userId)->pluck('area_id');
                $data = SalesmanSummarySales::whereIn('area_id', $areaIds)->get();
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $storeIds = Store::where('user_id', $userId)->pluck('id');
                $data = SalesmanSummarySales::whereIn('store_id', $storeIds)->get();
            }
            else{
                $data = SalesmanSummarySales::all();
            }

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else if($request['searchDate']){
                $date1 = $request['searchDate'];
                $date2 = $date1;
                // $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
            }else{
                $month = Carbon::now()->format('m');
                $year = Carbon::now()->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));
            }

            $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);


            if($request['byRegion']){
                $filter = $filter->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $filter->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $filter->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $store = Store::where('stores.id', $request['byStore'])
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->pluck('storeses.id');
                $filter = $filter->whereIn('storeId', $store);
            }

            if($request['byEmployee']){
                $filter = $filter->where('user_id', $request['byEmployee']);
            }

            $filter->all();
            $dataExcel = $filter->toArray();


        // }else{ // Fetch data from history

        //     $historyData = new Collection();

        //     $history = HistorySalesmanSales::where('year', $yearRequest)
        //                 ->where('month', $monthRequest)->get();

        //     foreach ($history as $data) {

        //         $details = json_decode($data->details);

        //         foreach ($details as $detail) {

        //             foreach ($detail->transaction as $transaction) {

        //                 $collection = new Collection();

        //                 /* Get Data and Push them to collection */
        //                 $collection['id'] = $data->id;
        //                 $collection['region_id'] = $detail->region_id;
        //                 $collection['area_id'] = $detail->area_id;
        //                 $collection['district_id'] = $detail->district_id;
        //                 $collection['storeId'] = $detail->storeId;
        //                 $collection['user_id'] = $detail->user_id;
        //                 $collection['week'] = $detail->week;
        //                 $collection['distributor_code'] = $detail->distributor_code;
        //                 $collection['distributor_name'] = $detail->distributor_name;
        //                 $collection['region'] = $detail->region;
        //                 $collection['channel'] = $detail->channel;
        //                 $collection['sub_channel'] = $detail->sub_channel;
        //                 $collection['area'] = $detail->area;
        //                 $collection['district'] = $detail->district;
        //                 $collection['store_name_1'] = $detail->store_name_1;
        //                 $collection['store_name_2'] = $detail->store_name_2;
        //                 $collection['store_id'] = $detail->store_id;
        //                 $collection['nik'] = $detail->nik;
        //                 $collection['promoter_name'] = $detail->promoter_name;
        //                 $collection['date'] = $detail->date;
        //                 $collection['model'] = $transaction->model;
        //                 $collection['group'] = $transaction->group;
        //                 $collection['category'] = $transaction->category;
        //                 $collection['product_name'] = $transaction->product_name;
        //                 $collection['quantity'] = number_format($transaction->quantity);
        //                 $collection['unit_price'] = number_format($transaction->unit_price);
        //                 $collection['value'] = number_format($transaction->value);
        //                 $collection['value_pf'] = number_format($transaction->value_pf);
        //                 $collection['role'] = $detail->role;
        //                 $collection['role_id'] = $detail->role_id;
        //                 $collection['role_group'] = $detail->role_group;

        //                 $historyData->push($collection);

        //             }

        //         }

        //     }

        //     $filter = $historyData;

        //     /* If filter */
        //     if($request['searchMonth']){
        //         $month = Carbon::parse($request['searchMonth'])->format('m');
        //         $year = Carbon::parse($request['searchMonth'])->format('Y');
        //         // $filter = $data->where('month', $month)->where('year', $year);
        //         $date1 = "$year-$month-01";
        //         $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
        //         $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

        //         $filter = $filter->where('date','>=',$date1)->where('date','<=',$date2);
        //     }

        //     if($request['byRegion']){
        //         $filter = $filter->where('region_id', $request['byRegion']);
        //     }

        //     if($request['byArea']){
        //         $filter = $filter->where('area_id', $request['byArea']);
        //     }

        //     if($request['byDistrict']){
        //         $filter = $filter->where('district_id', $request['byDistrict']);
        //     }

        //     if($request['byStore']){
        //         $store = Store::where('stores.id', $request['byStore'])
        //                         ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
        //                         ->pluck('storeses.id');
        //         $filter = $filter->whereIn('storeId', $store);
        //     }

        //     if($request['byEmployee']){
        //         $filter = $filter->where('user_id', $request['byEmployee']);
        //     }

        //     if ($userRole == 'RSM') {
        //         $regionIds = RsmRegion::where('user_id', $userId)->pluck('region_id');
        //         $filter = $filter->whereIn('region_id', $regionIds);
        //     }

        //     if ($userRole == 'DM') {
        //         $areaIds = DmArea::where('user_id', $userId)->pluck('area_id');
        //         $filter = $filter->whereIn('area_id', $areaIds);
        //     }

        //     if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
        //         $storeIds = Store::where('user_id', $userId)->pluck('id');
        //         $filter = $filter->whereIn('store_id', $storeIds);
        //     }

            // $filter->all();
            // $dataExcel = $filter->toArray();

        // }

        $excel = Excel::create($filename, function($excel) use ($dataExcel) {

            // Set the title
            $excel->setTitle('Report Salesman');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SALESMAN', function ($sheet) use ($dataExcel) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesman($dataExcel), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSalesmanAchievementReport(Request $request){

        $filename = 'Philips Retail Report Salesman Achievement Report ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Salesman Achievement');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Achievement Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SALESMAN ACHIEVEMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:W1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanAchievement($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    //
    public function exportArea(Request $request){

        $filename = 'Philips Retail Master Data Area ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Area');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Area Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Area', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportArea($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportAreaAll(){

        $filename = 'Philips Retail Master Data Area ' . Carbon::now()->format('d-m-Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;

        $data = Area::join('regions', 'areas.region_id', '=', 'regions.id')
                ->select('areas.*', 'regions.name as region_name')
                ->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->pluck('areas.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('user_id', $userId)
                        ->pluck('dm_areas.area_id');
            $data = $data->whereIn('id', $area);
        }

        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('user_id', $userId)
                        ->join('districts', 'stores.district_id', '=', 'districts.id')
                        ->join('areas', 'districts.area_id', '=', 'areas.id')
                        ->pluck('areas.id');
            $data = $data->whereIn('id', $store);
        }

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Area');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Area Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Area', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportArea($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }


    public function exportFeedbackAnswer(Request $request){

        $filename = 'Philips Retail Report Feedback Answer ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Feedback Answer');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Feedback Answer Report');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Report Answer Report', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFeedbackAnswer($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportFeedbackAnswerAll(Request $request){

        $filename = 'Philips Retail Report Feedback Answer ' . Carbon::now()->format('d-m-Y');

        $data = FeedbackAnswer::where('feedback_answers.deleted_at', null)
                    ->join('users as assessors', 'feedback_answers.assessor_id', '=', 'assessors.id')
                    ->join('users as promoters', 'feedback_answers.promoter_id', '=', 'promoters.id')
                    ->join('feedback_questions', 'feedback_answers.feedbackQuestion_id', '=', 'feedback_questions.id')
                    ->join('feedback_categories', 'feedback_questions.feedbackCategory_id', '=', 'feedback_categories.id')
                    ->select('feedback_answers.*', 'assessors.name as assessor_name', 'promoters.name as promoter_name', 'feedback_questions.question as feedback_question', 'feedback_categories.name as feedback_category')->get();

        $filter = $data;

        /* If filter */
            if($request['byAssesssor']){
                $filter = $data->where('assessor_id', $request['byAssesssor']);
            }

            if($request['byPromoter']){
                $filter = $data->where('promoter_id', $request['byPromoter']);
            }

        $excel = Excel::create($filename, function($excel) use ($filter) {

            // Set the title
            $excel->setTitle('Report Feedback Answer');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Feedback Answer Report');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Report Answer Report', function ($sheet) use ($filter) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFeedbackAnswer($filter->all()), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    //
    public function exportDistrict(Request $request){

        $filename = 'Philips Retail Master Data District ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data District');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('District Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master District', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistrict($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx')->remember(10);

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);
    }

    public function exportDistrictAll(){

        $filename = 'Philips Retail Master Data District ' . Carbon::now()->format('d-m-Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $data = District::join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->select('districts.*', 'areas.name as area_name', 'regions.name as region_name')->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->pluck('districts.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->pluck('districts.id');
            $data = $data->whereIn('id', $area);
        }

        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('user_id', $userId)
                        ->join('districts', 'stores.district_id', '=', 'districts.id')
                        ->join('areas', 'districts.area_id', '=', 'areas.id')
                        ->pluck('districts.id');
            $data = $data->whereIn('id', $store);
        }

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data District');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('District Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master District', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistrict($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);
    }

    //
    public function exportStore(Request $request){

        $filename = 'Philips Retail Master Data Store ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Store');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Store Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Store', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:W1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportStore($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportStoreAll(Request $request){

        $filename = 'Philips Retail Master Data Store ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Store');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Store Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Store', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:W1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportStoreAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportStoreAllAlt(StoreFilters $filters){

        $filename = 'Philips Retail Master Data Store ' . Carbon::now()->format('d-m-Y');

        // GET DATA
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        
        $data = Store::filter($filters)
                    ->leftJoin('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    ->leftJoin('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->leftJoin('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->leftJoin('districts', 'stores.district_id', '=', 'districts.id')
                    ->leftJoin('areas', 'districts.area_id', '=', 'areas.id')
                    ->leftJoin('regions', 'areas.region_id', '=', 'regions.id')
                    ->leftJoin('classifications', 'classifications.id', '=', 'stores.classification_id')
                    ->leftJoin('users', 'users.id', '=', 'stores.user_id')
                    ->leftJoin('spv_demos', 'stores.id', '=', 'spv_demos.store_id')
                    ->leftJoin('users as user2', 'user2.id', '=', 'spv_demos.user_id')
                    ->select('stores.*', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name'
                        ,'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name', 'classifications.classification as classification_id', 'users.name as spv_name', 'user2.name as spv_demo'
                        )
                    ->whereNull('stores.deleted_at')
                ->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $area);
        }
            
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('user_id', $userId)
                        ->pluck('stores.id');
            $data = $data->whereIn('id', $store);
        }

        // return response()->json($data);

        $data = $data->toArray();
        
        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Store');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Store Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Store', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:W1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportStoreAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportChannel(Request $request){

        $filename = 'Philips Retail Master Data Channel ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Channel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Channel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Channel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportChannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportChannelAll(){

        $filename = 'Philips Retail Master Data Channel ' . Carbon::now()->format('d-m-Y');

        $data = Channel::join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                ->select('channels.*', 'global_channels.name as globalchannel_name')
                ->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Channel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Channel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Channel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportChannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportSubchannel(Request $request){

        $filename = 'Philips Retail Master Data Subchannel ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Subchannel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Subchannel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Subchannel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSubchannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSubchannelAll(){

        $filename = 'Philips Retail Master Data Subchannel ' . Carbon::now()->format('d-m-Y');

        $data = SubChannel::join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                ->select('sub_channels.*', 'channels.name as channel_name', 'global_channels.name as globalchannel_name')
                ->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Subchannel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Subchannel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Subchannel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSubchannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportDistributor(Request $request){

        $filename = 'Philips Retail Master Data Distributor ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Distributor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Distributor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistributor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportDistributorAll(){

        $filename = 'Philips Retail Master Data Distributor ' . Carbon::now()->format('d-m-Y');

        $data = Distributor::get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Distributor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Distributor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistributor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportPlace(Request $request){

        $filename = 'Philips Retail Master Data Place ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Place');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Place Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Place', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:G1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPlace($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:G1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:G1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPlaceAll(){

        $filename = 'Philips Retail Master Data Place ' . Carbon::now()->format('d-m-Y');

        $data = Place::get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Place');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Place Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Place', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:G1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPlace($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:G1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:G1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportLeadtime(Request $request){

        $filename = 'Philips Retail Master Data Leadtime ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Leadtime');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Leadtime Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Leadtime', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportLeadtime($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportLeadtimeAll(){

        $filename = 'Philips Retail Master Data Leadtime ' . Carbon::now()->format('d-m-Y');

        $data = Leadtime::join('areas', 'areas.id', '=', 'leadtimes.area_id')
                    ->select('leadtimes.*', 'areas.name as area_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Leadtime');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Leadtime Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Leadtime', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportLeadtime($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportLeadtimeTemplate(){

        $filename = 'Philips Retail Master Data Leadtime ' . Carbon::now()->format('d-m-Y');

        $data = Leadtime::join('areas', 'areas.id', '=', 'leadtimes.area_id')
                    ->select('leadtimes.*', 'areas.name as area_name')->get()->toArray();

        $area = Area::join('regions', 'regions.id', '=', 'areas.region_id')
                ->select('areas.id', 'areas.name', 'regions.name as region_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data, $area) {

            // Set the title
            $excel->setTitle('Master Data Leadtime');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Leadtime Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Area', function ($sheet) use ($area) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportArea($area), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });

            $excel->sheet('Master Leadtime', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportLeadtime($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
                $sheet->cell('D1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportTimegone(Request $request){

        $filename = 'Philips Retail Master Data Timegone ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Timegone');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Timegone Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Timegone', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTimeGone($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportTimegoneAll(){

        $filename = 'Philips Retail Master Data Timegone ' . Carbon::now()->format('d-m-Y');

        $data = TimeGone::get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Timegone');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Timegone Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Timegone', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTimeGone($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportTimegoneTemplate(){

        $filename = 'Philips Retail Master Data Timegone ' . Carbon::now()->format('d-m-Y');

        $data = TimeGone::get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Timegone');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Timegone Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Timegone', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTimeGone($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
                $sheet->cell('C1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportUserPromoter(Request $request){

        $filename = 'Philips Retail Master Data User Promoter ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportUserPromoterAll(UserFilters $filters){

        $filename = 'Philips Retail Master Data User Promoter ' . Carbon::now()->format('d-m-Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        $data = User::filter($filters)->where('is_resign', 0)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->whereIn('role_group',$roles)->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $area);
        }

        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('stores.user_id', $userId)
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $store);
        }

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportUserNonPromoter(Request $request){

        $filename = 'Philips Retail Master Data User Non Promoter ' . Carbon::now()->format('d-m-Y');
        $data = json_decode($request['data'], true);

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Non Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Non Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Non Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportUserNonPromoterAll(UserFilters $filters){

        $filename = 'Philips Retail Master Data User Non Promoter ' . Carbon::now()->format('d-m-Y');

        $userRole = Auth::user()->role->role_group;
        $userId = Auth::user()->id;

        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        if ($userRole != 'Master') {
            $roles[] = 'Master';
            $roles[] = 'Admin';
        }

        $data = User::filter($filters)
                ->join('roles','roles.id','users.role_id')
                ->leftJoin('gradings','gradings.id','users.grading_id')
                ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
                ->whereNotIn('roles.role_group',$roles)->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $area);
        }

        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('stores.user_id', $userId)
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $store);
        }

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Non Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Non Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Non Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportGroup(Request $request){

        $filename = 'Philips Retail Master Data Group ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroup($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportGroupAll(){

        $filename = 'Philips Retail Master Data Group ' . Carbon::now()->format('d-m-Y');

        $data = Group::join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('groups.*', 'group_products.name as groupproduct_name')
                ->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroup($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportCategory(Request $request){

        $filename = 'Philips Retail Master Data Category ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Category');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Category Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Category', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportCategory($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportCategoryAll(){

        $filename = 'Philips Retail Master Data Category ' . Carbon::now()->format('d-m-Y');

        $data = Category::filter($filters)->join('groups', 'categories.group_id', '=', 'groups.id')
                ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Category');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Category Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Category', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportCategory($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportNewsRead(Request $request){

        $filename = 'Philips Retail Report News Read ' . Carbon::now()->format('d-m-Y');

        // $data = Category::filter($filters)->join('groups', 'categories.group_id', '=', 'groups.id')
        //         ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
        //         ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')->get();

        $news = News::where('news.deleted_at', null)
                    ->where('news.id', $request['id'])
                    ->join('users', 'news.user_id', '=', 'users.id')
                    ->select('news.*', 'users.name as user_name')->first();

        $data = NewsRead::where('news_id', $request['id'])
                    ->join('users', 'news_reads.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('news_reads.*', 'users.name as user_name', 'users.nik as user_nik', 'roles.role_group as user_role')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data, $news) {

            // Set the title
            $excel->setTitle('News Read');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('News Read Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('News Read', function ($sheet) use ($data, $news) {

                $sheet->setCellValue('D1', 'Space');
                $sheet->cells('D1', function ($cells) {
                    $cells->setFontColor('#FFFFFF');
                });

                $sheet->setCellValue('E1', 'Date');
                $sheet->setCellValue('F1', ':');
                $sheet->setCellValue('G1', @$news->date);

                $sheet->setCellValue('E2', 'Sender');
                $sheet->setCellValue('F2', ':');
                $sheet->setCellValue('G2', @$news->from);

                $sheet->setCellValue('E3', 'Subject');
                $sheet->setCellValue('F3', ':');
                $sheet->setCellValue('G3', @$news->subject);

                $sheet->setCellValue('E4', 'Content');
                $sheet->setCellValue('F4', ':');
                $sheet->setCellValue('G4', strip_tags(@$news->content));

                $sheet->cells('E1:G4', function ($cells) {
                    $cells->setAlignment('left');
                });

                $sheet->setAutoFilter('A1:C1');
                // $sheet->setHeight(5, 25);
                $sheet->fromModel($this->excelHelper->mapForExportNewsRead($data), null, 'A1', true, true);
                // $sheet->row(1, function ($row) {
                //     $row->setBackground('#82abde');
                // });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('E1:E5', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportGuideLineRead(Request $request){

        $filename = 'Philips Retail Report Guideline Read ' . Carbon::now()->format('d-m-Y');

        // $data = Category::filter($filters)->join('groups', 'categories.group_id', '=', 'groups.id')
        //         ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
        //         ->select('categories.*', 'groups.name as group_name', 'group_products.name as groupproduct_name')->get();

        $pk = ProductKnowledge::where('product_knowledges.deleted_at', null)
                    ->join('users', 'product_knowledges.user_id', '=', 'users.id')
                    ->select('product_knowledges.*', 'users.name as user_name')->first();

        $data = ProductKnowledgeRead::where('productknowledge_id', $request['id'])
                    ->join('users', 'product_knowledge_reads.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('product_knowledge_reads.*', 'users.name as user_name', 'users.nik as user_nik', 'roles.role_group as user_role')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data, $pk) {

            // Set the title
            $excel->setTitle('Guideline Read');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Guideline Read Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Guideline Read', function ($sheet) use ($data, $pk) {

                $sheet->setCellValue('D1', 'Space');
                $sheet->cells('D1', function ($cells) {
                    $cells->setFontColor('#FFFFFF');
                });

                $sheet->setCellValue('E1', 'Date');
                $sheet->setCellValue('F1', ':');
                $sheet->setCellValue('G1', @$pk->date);

                $sheet->setCellValue('E2', 'Sender');
                $sheet->setCellValue('F2', ':');
                $sheet->setCellValue('G2', @$pk->from);

                $sheet->setCellValue('E3', 'Subject');
                $sheet->setCellValue('F3', ':');
                $sheet->setCellValue('G3', @$pk->subject);

                $sheet->setCellValue('E4', 'Type');
                $sheet->setCellValue('F4', ':');
                $sheet->setCellValue('G4', @$pk->type);

                $sheet->cells('E1:G4', function ($cells) {
                    $cells->setAlignment('left');
                });

                $sheet->setAutoFilter('A1:C1');
                // $sheet->setHeight(5, 25);
                $sheet->fromModel($this->excelHelper->mapForExportNewsRead($data), null, 'A1', true, true);
                // $sheet->row(1, function ($row) {
                //     $row->setBackground('#82abde');
                // });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('E1:E5', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProduct(Request $request){

        $filename = 'Philips Retail Master Data Product ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProduct($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportProductAll(){

        $filename = 'Philips Retail Master Data Product ' . Carbon::now()->format('d-m-Y');

    $data = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))
                ->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProduct($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportPrice(Request $request){

        $filename = 'Philips Retail Master Data Price ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Price');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Price Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Price', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPrice($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
                // $sheet->cell('B1:C1', function($cell) {
                //     // manipulate the cell
                //     $cell->setBackground('#f4df24');
                // });
                // $sheet->cell('H1', function($cell) {
                //     // manipulate the cell
                //     $cell->setBackground('#75ff56');
                // });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPriceAll(PriceFilters $filters){

        $filename = 'Philips Retail Master Data Price ' . Carbon::now()->format('d-m-Y');

        $data = Price::filter($filters)->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'), 'global_channels.name as globalchannel_name')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Price');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Price Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Price', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPrice($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
                // $sheet->cell('B1:C1', function($cell) {
                //     // manipulate the cell
                //     $cell->setBackground('#f4df24');
                // });
                // $sheet->cell('H1', function($cell) {
                //     // manipulate the cell
                //     $cell->setBackground('#75ff56');
                // });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportPriceTemplate(PriceFilters $filters){

        $filename = 'Philips Retail Master Data Price ' . Carbon::now()->format('d-m-Y');

        $data = Price::filter($filters)->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'), 'global_channels.name as globalchannel_name')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Price');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Price Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Price', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPrice($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
                // $sheet->cell('B1:C1', function($cell) {
                //     // manipulate the cell
                //     $cell->setBackground('#f4df24');
                // });
                $sheet->cell('H1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportTarget(Request $request){

        $filename = 'Philips Retail Master Data Target ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportTargetAll(TargetFilters $filters){

        $filename = 'Philips Retail Master Data Target ' . Carbon::now()->format('d-m-Y');

        $data = Target::filter($filters)->join('users', 'targets.user_id', '=', 'users.id')
                    ->join('stores', 'targets.store_id', '=', 'stores.id')
                    ->select('targets.*', 'users.name as promoter_name', 'stores.store_name_1', 'stores.store_name_2')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportTargetTemplate(TargetFilters $filters){

        $filename = 'Philips Retail Master Data Target ' . Carbon::now()->format('d-m-Y');

        $data = Target::filter($filters)->join('users', 'targets.user_id', '=', 'users.id')
                    ->join('stores', 'targets.store_id', '=', 'stores.id')
                    ->select('targets.*', 'users.name as promoter_name', 'stores.store_name_1', 'stores.store_name_2')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:M1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:M1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:M1', 'thin');
                $sheet->cell('H1:M1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProductFocus(Request $request){

        $filename = 'Philips Retail Master Data Product Focus ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportProductFocusAll(){

        $filename = 'Philips Retail Master Data Product Focus ' . Carbon::now()->format('d-m-Y');

        $data = ProductFocuses::join('products', 'product_focuses.product_id', '=', 'products.id')
                ->select('product_focuses.*', 'products.name as product_name')->get();

        $data = $data->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProductPromo(Request $request){

        $filename = 'Philips Retail Master Data Product Promo Tracking ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product Promo Tracking');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Promo Tracking Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product Promo Tracking', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductPromo($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportProductPromoAll(){

        $filename = 'Philips Retail Master Data Product Promo Tracking ' . Carbon::now()->format('d-m-Y');

        $data = ProductPromos::join('products', 'product_promos.product_id', '=', 'products.id')
                    ->select('product_promos.*', 'products.name as product_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product Promo Tracking');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Promo Tracking Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product Promo Tracking', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductPromo($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProductPromoTemplate(){

        $filename = 'Philips Retail Master Data Product Promo Tracking ' . Carbon::now()->format('d-m-Y');

        $data = ProductPromos::join('products', 'product_promos.product_id', '=', 'products.id')
                    ->select('product_promos.*', 'products.name as product_name')->get()->toArray();

        $products = Product::where('products.deleted_at', null)
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                    ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data, $products) {

            // Set the title
            $excel->setTitle('Master Data Product Promo Tracking');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Promo Tracking Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($products) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductTemplate($products), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });

            $excel->sheet('Master Product Promo Tracking', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductPromo($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
                $sheet->cell('B1:C1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProductFocusTemplate(){

        $filename = 'Philips Retail Master Data Product Focus ' . Carbon::now()->format('d-m-Y');

        $data = ProductFocuses::join('products', 'product_focuses.product_id', '=', 'products.id')
                ->select('product_focuses.*', 'products.name as product_name')->get();

        $data = $data->toArray();

        $products = Product::where('products.deleted_at', null)
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                    ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))->get()->toArray();

        // return $products;

        $excel = Excel::create($filename, function($excel) use ($data, $products) {

            // Set the title
            $excel->setTitle('Master Data Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($products) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductTemplate($products), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });

            $excel->sheet('Master Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
                $sheet->cell('B1:D1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });




        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportSalesmanTarget(Request $request){

        $filename = 'Philips Retail Master Data Salesman Target ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSalesmanTargetAll(){

        $filename = 'Philips Retail Master Data Salesman Target ' . Carbon::now()->format('d-m-Y');

        $data = SalesmanTarget::join('users', 'salesman_targets.user_id', '=', 'users.id')
                    ->select('salesman_targets.*', 'users.name as salesman_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSalesmanTargetTemplate(){

        $filename = 'Philips Retail Master Data Salesman Target ' . Carbon::now()->format('d-m-Y');

        $data = SalesmanTarget::join('users', 'salesman_targets.user_id', '=', 'users.id')
                    ->select('salesman_targets.*', 'users.name as salesman_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:H1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
                $sheet->cell('D1:H1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportSalesmanProductFocus(Request $request){

        $filename = 'Philips Retail Master Data Salesman Product Focus ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportSalesmanProductFocusAll(){

        $filename = 'Philips Retail Master Data Salesman Product Focus ' . Carbon::now()->format('d-m-Y');

        $data = SalesmanProductFocuses::join('products', 'salesman_product_focuses.product_id', '=', 'products.id')->select('salesman_product_focuses.*', 'products.name as product_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportSalesmanProductFocusTemplate(Request $request){

        $filename = 'Philips Retail Master Data Salesman Product Focus ' . Carbon::now()->format('d-m-Y');

        $data = SalesmanProductFocuses::join('products', 'salesman_product_focuses.product_id', '=', 'products.id')->select('salesman_product_focuses.*', 'products.name as product_name')->get()->toArray();

        $products = Product::where('products.deleted_at', null)
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('groups', 'categories.group_id', '=', 'groups.id')
                    ->leftJoin('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
                    ->select('products.*', 'categories.name as category_name', 'groups.name as group_name', 'group_products.name as groupproduct_name', DB::raw('CONCAT(products.model, "/", products.variants) AS product_model'))->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data, $products) {

            // Set the title
            $excel->setTitle('Master Data Salesman Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($products) {
                $sheet->setAutoFilter('A1:F1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductTemplate($products), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:F1', 'thin');
            });

            $excel->sheet('Master Salesman Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
                $sheet->cell('B1:C1', function($cell) {
                    $cell->setBackground('#f4df24');
                });
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportPosm(Request $request){

        $filename = 'Philips Retail Master Data POSM ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data POSM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('POSM Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master POSM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPosm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportPosmAll(){

        $filename = 'Philips Retail Master Data POSM ' . Carbon::now()->format('d-m-Y');

        $data = Posm::join('groups', 'posms.group_id', '=', 'groups.id')
            ->select('posms.*', 'groups.name as group_name')->get()->toArray();


        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data POSM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('POSM Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master POSM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPosm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportGroupCompetitor(Request $request){

        $filename = 'Philips Retail Master Data Group Competitor ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group Competitor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Competitor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group Competitor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroupCompetitor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportGroupCompetitorAll(){

        $filename = 'Philips Retail Master Data Group Competitor ' . Carbon::now()->format('d-m-Y');

        $data = GroupCompetitor::join('groupcompetitor_groups', 'group_competitors.id', '=', 'groupcompetitor_groups.groupcompetitor_id')
                    ->join('groups', 'groupcompetitor_groups.group_id', '=', 'groups.id')
                    ->select('group_competitors.*', 'groups.id as group_id', 'groups.name as group_name')->get()->toArray();

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group Competitor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Competitor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group Competitor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroupCompetitor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportNews(Request $request){

        $filename = 'Philips Retail Master Data News ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data News');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('News Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master News', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportNews($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportProductKnowledge(Request $request){

        $filename = 'Philips Retail Master Data Guidelines(Product Knowledge) ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Guidelines(Product Knowledge)');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Guidelines(Product Knowledge) Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Guidelines(Product Knowledge)', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:K1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductKnowledge($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:K1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportFaq(Request $request){

        $filename = 'Philips Retail Master Data Faq ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Faq');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Faq Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Faq', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFaq($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportQuiz(Request $request){

        $filename = 'Philips Retail Master Data Quiz ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Quiz');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Quiz Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Quiz', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportQuiz($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportFanspage(Request $request){

        $filename = 'Philips Retail Master Data Fanspage ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Fanspage');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Fanspage Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Fanspage', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFanspage($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
    //
    public function exportMessageToAdmin(Request $request){

        $filename = 'Philips Retail Master Data Message To Admin ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Message To Admin');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Message To Admin Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Message To Admin', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:E1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportMessageToAdmin($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:E1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportKonfigPromoter(Request $request){

        $filename = 'Philips Retail Konfigurasi Promoter ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Konfigurasi Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Konfigurasi Promoter Report');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Konfigurasi Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:U1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportKonfigPromoter($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:U1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:U1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportKonfigStore(Request $request){

        $filename = 'Philips Retail Konfigurasi Store ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;



        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Konfigurasi Store');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Konfigurasi Store Report');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Konfigurasi Store', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:U1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportKonfigStore($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:U1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:U1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportApm(Request $request){

        $filename = 'Philips Retail Report APM ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report APM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('APM Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('APM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:P1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportApm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:P1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:P1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportApmAll(ApmFilters $filters){

        $filename = 'Philips Retail Report APM ' . Carbon::now()->format('d-m-Y');

        $data = Apm::filter($filters)
                ->join('stores', 'apms.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('products', 'apms.product_id', '=', 'products.id')
                    ->leftJoin('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    ->leftJoin('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->leftJoin('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('apms.*', 'stores.store_name_1 as store_name', 'stores.store_id as re_store_id', 'products.name as product_name', 'districts.name as district', 'areas.name as area', 'regions.name as region', 'global_channels.name as global_channel', 'channels.name as channel', 'sub_channels.name as sub_channel')->get()->toArray();


        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report APM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('APM Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('APM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:P1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportApmAll($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:P1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:P1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportApmTemplate(ApmFilters $filters){

        $filename = 'Philips Retail Report APM ' . Carbon::now()->format('d-m-Y');

        $data = Apm::filter($filters)
                ->join('stores', 'apms.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('products', 'apms.product_id', '=', 'products.id')
                    ->leftJoin('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    ->leftJoin('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->leftJoin('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->select('apms.*', 'stores.store_name_1 as store_name', 'stores.store_id as re_store_id', 'products.name as product_name', 'districts.name as district', 'areas.name as area', 'regions.name as region', 'global_channels.name as global_channel', 'channels.name as channel', 'sub_channels.name as sub_channel')->get()->toArray();


        $excel = Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report APM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('APM Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('APM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:R1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportApmTemplate($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:R1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:R1', 'thin');
            });


        })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

    public function exportVisitPlanTemplate(){

        // $filename = 'Upload Visit Plan ' . Carbon::now()->format('d-m-Y');

        // $excel = Excel::create($filename, function($excel){

        //     // Set the title
        //     $excel->setTitle('Visit Plan');

        //     // Chain the setters
        //     $excel->setCreator('Philips')
        //           ->setCompany('Philips');

        //     // Call them separately
        //     $excel->setDescription('Visit Plan');

        //     // $excel->getDefaultStyle()
        //     //     ->getAlignment()
        //     //     ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        //     //     ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //     $excel->sheet('VISIT PLAN', function($sheet){

        //         // $sheet->setAutoSize(false);

        //         // $sheet->setAutoFilter('A2:F2');

        //         $sheet->loadView('excel.visitplan');

        //         // $sheet->cells('A1:F2', function ($cells) {
        //         //     $cells->setFontWeight('bold');
        //         // });

        //         // Font family
        //         // $sheet->setFontFamily('Comic Sans MS');

        //         // Set font with ->setStyle()`
        //         $sheet->setStyle(array(
        //             'font' => array(
        //                 'name'      =>  'Calibri',
        //                 'size'      =>  8,
        //             )
        //         ));

        //         // $sheet->cells('A1:R1', function ($cells) {
        //         //     $cells->setStyle(array(
        //         //         'font' => array(
        //         //             'name'      =>  'Calibri',
        //         //             'size'      =>  10,
        //         //             'bold'      =>  true
        //         //         )
        //         //     ));
        //         // });

        //     });


        // })->string('xlsx');

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }
}
