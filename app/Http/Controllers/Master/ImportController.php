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
}
