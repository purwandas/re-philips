<?php

namespace App\Http\Controllers;

use App\Traits\ApmTrait;
use Illuminate\Http\Request;
use App\User;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Auth;
use File;
use Illuminate\Support\Collection;
use Artisan;
use Carbon\Carbon;
use App\SellOut;
use App\SellOutDetail;
use DB;
use App\DmArea;
use App\StoreHistory;
use App\Store;

class ProfileController extends Controller
{
	use UploadTrait;
    use StringTrait;
    use ApmTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$data = User::find(Auth::user()->id);

        return view('master.form.user-profile', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $user = User::find(Auth::user()->id);
        $oldPhoto = "";

        if($user->photo != null && $request->photo_file != null) {
            /* Save old photo path */
            $oldPhoto = $user->photo;
        }

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";

        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Check if password empty
        if($request['password']){

        	$request['password'] = bcrypt($request['password']);        	
        	$user->update($request->all());

        }else{

            if($request->photo_file != null) {
                $user->update(['photo' => $request['photo']]);
            }

        }

        if($user->photo != null && $request->photo_file != null && $oldPhoto != ""){

            /* Delete Image after any transaction */
            $imagePath = explode('/', $oldPhoto);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);

        }

        if($user->photo != null && $request->photo_file != null) {

            /* Upload updated image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $imageFolder = "user/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo_file, $imageFolder, $imageName);

        }

        return response()->json(
            [
                'url' => url('profile'),
            ]);
    }

    public function sellin(){

        $history = Store::with('storeHistories')->where('id', 6111)->first();

        return $history;

        // $data = SellOutDetail::limit(100)->get();
        // $a = 0;

        // return $data;

        // foreach ($data as $item) {
        //     $detail = SellOutDetail::where('id', $item->id);
        //     // return $detail;
        //     $detail->update(['amount' => $detail->first()->quantity * $detail->first()->price]);            
        // }

        // SellOutDetail::limit(10)->chunk(5, function($data) use ($a){
        //     foreach ($data as $item) {
        //         // $detail = SellOutDetail::where('id', $item->id);
        //         // $detail->update(['amount' => $detail->first()->quantity * $detail->first()->price]);
        //         $a += 1;
        //     }
        // });

        // $data->update(['amount' => 199]);

        // User::where('id', 1)->update([]);

        // return $a;

        // GET

        // $sellOut = SellOutDetail::whereHas('sellOut', function($query){
        //                 return $query->whereYear('date', Carbon::now()->format('Y'))->whereMonth('date', Carbon::now()->format('n'));
        //             })->get()->sum('amount2');

        // $sellOut = SellOutDetail::get()->sum('amount2');

        // SELECT SUM

        // $sellOut = SellOutDetail::whereHas('sellOut', function($query){
        //                 return $query->whereYear('date', Carbon::now()->format('Y'))->whereMonth('date', 5);
                    // })->select(DB::raw('SUM(quantity * amount) as summary'))->first();

        $sellOut = SellOutDetail::select(DB::raw('SUM(quantity * amount) as summary'))->first();

        return $sellOut;

        // TES UPDATE

        $price = 10;

        SellOutDetail::where('created_at', '!=', null)->update(['amount' => 10]);

        return 'OK';

        // $dt = Carbon::parse('2018-5-21');

        // return $dt->weekOfMonth;

        // return $this->getProductTotalCurrent(3388, 1);

        // TES SELL OUT RAW
        $data = SellOutDetail::with('sellOut.store', 'sellOut.user', 'product')->limit(10);

        return $data;

        // return $data->sellOut->store->spvDemo;

        return $data->sellOut->store->trainer;
        return $data->sellOut->store->distributorName;

        return ($data->product->pf->where('type', 'MR')->first()) ? $data->amount : 0;
        return ($data->product->pf->where('type', 'TR')->first()) ? $data->amount : 0;
        return ($data->product->pf->where('type', 'PPE')->first()) ? $data->amount : 0;

        // return $data->product->price->where('globalchannel_id', '');

    }

    public function import(){

        // Artisan::call("test:import");

        $name = 'Dwi Yoga Dirgantara';

        Artisan::call("test:import", ['name' => $name]);

        return 'Processing....';

    }

}
