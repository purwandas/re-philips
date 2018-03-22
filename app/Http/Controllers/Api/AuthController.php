<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\EmployeeStore;
use App\Store;
use App\SpvDemo;
use Carbon\Carbon;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use File;

class AuthController extends Controller
{
    use UploadTrait;
    use StringTrait;

    public function login(Request $request)
	{
		// grab credentials from the request
		$credentials = $request->only('nik', 'password');

		// If NIK is null
		if($request->nik == null){
			return response()->json(['status' => 'false', 'message' => 'invalid_credentials'], 401);
		}

		try {			
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['status' => 'false', 'message' => 'invalid_credentials'], 401);
			}

		} catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json(['status' => 'false', 'message' => 'could_not_create_token'], 500);			
		}

		// Get user data
		$user = Auth::user();

		// Check if user was resign
		if($user->is_resign == 1){
			return response()->json(['status' => 'false', 'message' => 'Maaf anda berada dalam status resign.' ], 200);
		}

		// If user has not login
		if ( $user->status_login != 'Login') {
			// if hp login pertama berbeda
			if ( $user->hp_id != null and $user->hp_id != $request->hp_id and $user->jenis_hp != $request->jenis_hp ) {
				return response()->json(['status' => 'false', 'message' => 'Cannot login in other handphone' ], 200);
			}
			// update status, jenis_hp and id hp ketika null
			if ( $user->hp_id == null ) {
		        $user->update([
					'status_login' => 'Login',
					'jenis_hp' => $request->jenis_hp,
					'hp_id' => $request->hp_id
				]);
		    }

		} else {
			// user has login
			return response()->json(['status' => 'false', 'message' => 'User has been logged in'], 200);
		}

		// Check Promoter Group
		$isPromoter = 0;
		if($user->role->role_group == 'Promoter' || $user->role->role_group == 'Promoter Additional' || $user->role->role_group == 'Promoter Event' || $user->role->role_group == 'Demonstrator MCC' || $user->role->role_group == 'Demonstrator DA' || $user->role->role_group == 'ACT'  || $user->role->role_group == 'PPE' || $user->role->role_group == 'BDT' || $user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'SMD' || $user->role->role_group == 'SMD Coordinator' || $user->role->role_group == 'HIC' || $user->role->role_group == 'HIE' || $user->role->role_group == 'SMD Additional' || $user->role->role_group == 'ASC'){
			$isPromoter = 1;
		}

		// Get stores from user if linked
		$storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
		$store = Store::whereIn('id', $storeIds)->select('id', 'store_id', 'store_name_1', 'longitude', 'latitude')->get();

		// Generate access for mobile
        $access = "";

        if($user->role->role_group == 'Promoter' || $user->role->role_group == 'Promoter Additional' || $user->role->role_group == 'Promoter Event' || $user->role->role_group == 'Demonstrator MCC' || $user->role->role_group == 'Demonstrator DA' || $user->role->role_group == 'ACT'  || $user->role->role_group == 'PPE' || $user->role->role_group == 'BDT' || $user->role->role_group == 'SMD' || $user->role->role_group == 'SMD Coordinator' || $user->role->role_group == 'HIC' || $user->role->role_group == 'HIE' || $user->role->role_group == 'SMD Additional' || $user->role->role_group == 'ASC'){
            $access = "Promoter";
        }

        // Get Promoter KPI
        $kpi = '';
        if(count($storeIds) > 0){

            if (isset(Store::where('id', $storeIds[0])->first()->subChannel->channel->globalChannel->name)) {
            	$channel = Store::where('id', $storeIds[0])->first()->subChannel->channel->globalChannel->name;
            }else{
            	$channel = '';
            }

            if($channel == 'TR' || $channel == 'Traditional Retail'){
                $kpi = 'Sell In';
            }else{
                $kpi = 'Sell Out';
            }
        }

        if($user->role->role_group == 'Salesman Explorer'){
            $kpi = 'Sell In';
        }

        if($user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid'){
        	$spvDemo = SpvDemo::where('user_id', $user->id)->first();

        	if($spvDemo){
        		$firstStore = Store::where('id', $spvDemo->store_id)->first();
        	}else{
        		$firstStore = Store::where('user_id', $user->id)->first();
        	}

        	if($firstStore){

	        	if($firstStore->subChannel->channel->globalChannel->name == 'TR' || $firstStore->subChannel->channel->globalChannel->name == 'Traditional Retail'){
	                $kpi = 'Sell In';
	            }else{
	                $kpi = 'Sell Out';
	            }

        	}

        }

        if($user->role->role_group == 'Salesman Explorer') $access = "Salesman";
        if($user->role->role_group == 'Supervisor') $access = "Supervisor";
        if($user->role->role_group == 'DM') $access = "DM";
        if($user->role->role_group == 'Trainer') $access = "DM";
        if($user->role->role_group == 'Trainer Demo') $access = "DM";
        if($user->role->role_group == 'RSM') $access = "RSM";
        if($user->role->role_group == 'Master') $access = "REM";

        $grading = '';
        if (isset($user->grading->grading)) {
        	$grading = $user->grading->grading;
        }

		// all good so return the token
		return response()->json(['status' => true, 'token' => $token, 'user_id' => $user->id, 'name' => $user->name, 'role' => $user->role->role, 'grading' => $grading, 'is_promoter' => $isPromoter, 'kpi' => $kpi, 'mobile_access' => $access, 'status_promoter' => $user->status, 'store' => $store]);
	}

	public function tes(){
		// $user = JWTAuth::parseToken()->authenticate();
		return response()->json('test');
	}

	public function getUser()
	{
		
		// $week = Carbon::now()->weekOfMonth;
		// return response()->json(compact('week'));

		try {

			// if (! $user = JWTAuth::toUser(JWTAuth::getToken())) {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		// the token is valid and we have found the user via the sub claim
		return response()->json(compact('user'));
	}

	public function getProfile(){

	    try {

			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		$data['photo'] = $user->photo;
		$data['nik'] = $user->nik;
	    $data['name'] = $user->name;
	    $data['email'] = $user->email;
	    $data['join_date'] = $user->join_date;
    $data['grading'] = '';
    if(isset($user->grading->grading)){
      $data['grading'] = $user->grading->grading;
    }    
	    $data['certificate'] = $user->certificate;

		// the token is valid and we have found the user via the sub claim
		return response()->json($data);

    }

	public function setProfile(Request $request){

	    $user = JWTAuth::parseToken()->authenticate();

	    if(!isset($request->photo) || $request->photo == null){
            return response()->json(['status' => false, 'message' => 'Photo tidak boleh kosong'], 500);
        }

        $userData = User::where('id', $user->id)->first();
        $oldPhoto = "";

        if($userData->photo != null && $request->photo != null) {
            /* Save old photo path */
            $oldPhoto = $userData->photo;
        }

        $photo_url = $this->getUploadPathName($request->photo, "user/".$this->getRandomPath(), 'USER');


        // Update photo to null
        $userData->update([
            'photo' => $photo_url
        ]);

	    if($userData->photo != null && $request->photo != null && $oldPhoto != ""){
	        /* Delete Image (Include Folder) */
            $imagePath = explode('/', $oldPhoto);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);
        }

        // Upload image process
        $imagePath = explode('/', $userData->photo);
        $count = count($imagePath);
        $imageFolder = "user/" . $imagePath[$count - 2];
        $imageName = $imagePath[$count - 1];

        $this->upload($request->photo, $imageFolder, $imageName);

        return response()->json(['status' => true, 'message' => 'Profil berhasil di update']);

    }

    public function updateProfile(Request $request){

	    $user = JWTAuth::parseToken()->authenticate();

	    if(!isset($request->name) || $request->name == null || !isset($request->email) || $request->email == null)
	    {
            return response()->json(['status' => false, 'message' => 'Nama & Email tidak boleh kosong'], 500);
        }

        $userData = User::where('id', $user->id)->first();
        if (!isset($userData)) {
        	return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
        $userData->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($userData) {
        	return response()->json(['status' => true, 'message' => 'Profil berhasil di update'], 200);
        }

        return response()->json(['status' => false, 'message' => 'Profil Gagal di update'], 500);

    }

	public function logout($id){
		//user logout
        $userData = User::where('id', $id)->first();
        if (!isset($userData)) {
			return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $userData->update([
            'status_login' => 'Logout',
            'fcm_token' => null,
        ]);

			return response()->json(['status' => true, 'message' => 'logout berhasil'], 200);
	}

	public function getFcmTokenToDB(Request $request){
	    $user = JWTAuth::parseToken()->authenticate();
        $userData = User::where('id', $user->id)->first();
        if (!isset($userData)) {
			return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
		if($request->fcm_token != null){
			if ($userData->fcm_token != null){
				if ($userData->fcm_token != $request->fcm_token){
			        $userData->update([
						'fcm_token' => $request->fcm_token,
					]);
					return response()->json(['status' => true, 'message' => 'token berhasil diubah'], 200);
				}
			}else{
		        $userData->update([
					'fcm_token' => $request->fcm_token,
				]);
				return response()->json(['status' => true, 'message' => 'token berhasil ditambahkan'], 200);
			}
		}
		return response()->json(['status' => false, 'message' => 'token kosong'], 200);
	}


	public function changePassword(Request $request){

	    $user = JWTAuth::parseToken()->authenticate();

        $userData = User::where('id', $user->id)->first();
        if (!isset($userData)) {
        	return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $userData->update([
            'password' => bcrypt($request->password),
        ]);

        if ($userData) {
        	return response()->json(['status' => true, 'message' => 'Password berhasil di ubah'], 200);
        }

        return response()->json(['status' => false, 'message' => 'Password gagal di ubah'], 500);

    }

    public function checkResign(){

    	$user = JWTAuth::parseToken()->authenticate();

    	return response()->json(['is_resign' => $user->is_resign]);

    }

}
