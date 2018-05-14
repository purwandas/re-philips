<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use App\Traits\UploadTrait;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Artisan;
use App\VisitPlan;

class ImportController extends Controller
{
	use UploadTrait;

    //
    public function importPrice(Request $request){
    	$file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'price', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "price/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        // $HCs = Excel::selectSheets('Master Price')->load($file_uploaded)->get();

        // $data2 = Excel::selectSheets('Master Price')->load('imports/price'.'/'.$file_name)->get();

        // $data = collect($HCs);

        // $data = new Collection();

        // $data = Excel::selectSheets('Master Price')->load($file_uploaded)->map(function ($price){
        //     return $price->price;
        // })

        // foreach ($HCs as $detail) {
        // 	$data['id'] = $detail['id'];
        //     $data['product_id'] = $detail['product_id'];
        //     $data['globalchannel_id'] = $detail['global_channel_id'];
        // 	$data['price'] = $detail['price'];
        // }

        // return $HCs;

        // return response()->json($HCs);

        Artisan::call("import:price", ['file' => $file_uploaded]);

    	return response()->json(['url' => url('/price')]);
    }

    public function importApm(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'apm', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "apm/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        // $dataFile = Excel::selectSheets('APM')->load($file_uploaded)->get();

        // $dataFile2 = Excel::selectSheets('APM')->load($file_uploaded)->first();

        // $val = 'so_value_march_2018';

        // $soMin6 = 'so_value_' . strtolower(Carbon::now()->subMonths(6)->format('F_Y'));
        // $soMin5 = 'so_value_' . strtolower(Carbon::now()->subMonths(5)->format('F_Y'));
        // $soMin4 = 'so_value_' . strtolower(Carbon::now()->subMonths(4)->format('F_Y'));
        // $soMin3 = 'so_value_' . strtolower(Carbon::now()->subMonths(3)->format('F_Y'));
        // $soMin2 = 'so_value_' . strtolower(Carbon::now()->subMonths(2)->format('F_Y'));
        // $soMin1 = 'so_value_' . strtolower(Carbon::now()->subMonths(1)->format('F_Y'));

        // $valueMin6 = (string)str_replace(',', '', $dataFile2[$soMin6]);
        // $valueMin5 = (string)str_replace(',', '', $dataFile2[$soMin5]);
        // $valueMin4 = (string)str_replace(',', '', $dataFile2[$soMin4]);
        // $valueMin3 = (string)str_replace(',', '', $dataFile2[$soMin3]);
        // $valueMin2 = (string)str_replace(',', '', $dataFile2[$soMin2]);
        // $valueMin1 = (string)str_replace(',', '', $dataFile2[$soMin1]);

        // // return response()->json($soMin1);
        // return response()->json((double)$valueMin6);

        Artisan::call("import:apm", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/apm')]);
    }

    public function importPriceProcess(Request $request){

        Artisan::call("import:price", ['file_name' => $request->data]);

        return response()->json($request->data);

    }

    public function importLeadtime(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'leadtime', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "leadtime/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:leadtime", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/leadtime')]);
    }

    public function importTimeGone(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'timegone', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "timegone/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:timegone", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/timegone')]);
    }

    public function importProductFocus(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'productfocus', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "productfocus/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:productfocus", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/productfocus')]);
    }

    public function importProductPromo(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'productpromo', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "productpromo/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:productpromo", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/productpromo')]);
    }

    public function importProductFocusSalesman(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'productfocussalesman', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "productfocussalesman/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:productfocussalesman", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/productfocussalesman')]);
    }

    public function importTarget(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'target', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "target/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:target", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/target')]);
    }

    public function importSalesmanTarget(Request $request){
        $file = $request->upload_file;
        $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'targetsalesman', $date);

        $path = explode('/', $file_origin);
        $count = count($path);
        $folder = "targetsalesman/";
        $file_name = $path[$count - 1];

        $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        Artisan::call("import:targetsalesman", ['file' => $file_uploaded]);

        return response()->json(['url' => url('/targetsalesman')]);
    }

    public function importVisitPlan(Request $request){

        // return $request->upload_file->getRealPath();

        // $file = $request->upload_file;
        // $date = Carbon::now()->format('m').Carbon::now()->format('Y');

        // $file_origin = $this->getUploadPathNameFileForImport($request->upload_file, 'visitplan', $date);

        // $path = explode('/', $file_origin);
        // $count = count($path);
        // $folder = "visitplan/";
        // $file_name = $path[$count - 1];

        // $file_uploaded = $this->uploadFileForImport($request->upload_file, $folder, $file_name);

        // $dataFile = Excel::selectSheetsByIndex(0)->load($file_uploaded)->first();

        // return $dataFile;


        if($request->hasFile('upload_file')){

            Excel::filter('chunk')->selectSheetsByIndex(0)->load($request->file('upload_file')->getRealPath())->chunk(10, function ($reader) {

                $columnCount = $reader->first()->keys()->count();

                $columnHeaders = $reader->first()->keys()->toArray();

                // foreach ($reader->toArray() as $data) {
                //     VisitPlan::create([
                //             'user_id' => $data[$columnHeaders[2]],
                //             'store_id' => $data[$columnHeaders[5]],
                //             'date' => Carbon::now(),
                //             'check_out_location' => $data[$columnHeaders[7]],                           
                //         ]);
                // }

                // for($i=0;$i<$columnCount;$i++){
                //     VisitPlan::create([
                //             'user_id' => 1,
                //             'store_id' => 1,
                //             'date' => Carbon::now(),                            
                //         ]);
                // }

                foreach ($reader->toArray() as $data) {
                    for($i=6;$i<$columnCount;$i++){
                        if($data[$columnHeaders[$i]] == 1){

                            $dateExplode = explode('_', $columnHeaders[$i]);

                            // $date = Carbon::parse(str_replace('_', '-', $columnHeaders[$i]));
                            $date = Carbon::create($dateExplode[0], $dateExplode[1], $dateExplode[2]);

                            $cekVisit = VisitPlan::where('user_id', $data['id_promoter'])
                                                    ->where('store_id', $data['id_store'])
                                                    ->where('date', $date)
                                                    ->first();

                            if(!$cekVisit){
                                VisitPlan::create([
                                    'user_id' => $data['id_promoter'],
                                    'store_id' => $data['id_store'],
                                    'date' => $date,
                                    ]);
                            }

                        }
                    }
                }
            });

            
            // Excel::filter('chunk')->selectSheetsByIndex(0)->load($request->file('upload_file')->getRealPath())->chunk(100, function ($reader) {

            //     foreach ($reader as $data) {
            //         VisitPlan::create([
            //                 'user_id' => 1,
            //                 'store_id' => 1,
            //                 'date' => Carbon::now(),                            
            //             ]);
            //     }

            //     // return response()->json(['url' => $reader->toArray()]);

            // });

        }

        return response()->json(['url' => url('/visitplan')]);
    }
}
