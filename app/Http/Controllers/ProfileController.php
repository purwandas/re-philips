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

        return $this->getProductTotalCurrent(3388, 1);

    }

    public function import(){

        // Artisan::call("test:import");

        $name = 'Dwi Yoga Dirgantara';

        Artisan::call("test:import", ['name' => $name]);

        return 'Processing....';

    }

}
