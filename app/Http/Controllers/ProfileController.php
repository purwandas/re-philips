<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Auth;

class ProfileController extends Controller
{
	use UploadTrait;
    use StringTrait;
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

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->imageUpload($request->photo_file, "user/".$this->getRandomPath()) : $photo_url = "";        

        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Check if password empty
        if($request['password']){

        	$request['password'] = bcrypt($request['password']);        	
        	$user->update($request->all());

        }else{

        	$user->update(['photo' => $request['photo']]);

        }

        return response()->json(
            [
                'url' => url('profile'),
            ]);
    }
}
