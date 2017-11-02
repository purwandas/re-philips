<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\EmployeeStore;
use App\Store;
use Carbon\Carbon;

class AuthController extends Controller
{
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

		// Check Promoter Group
		$isPromoter = 0;
		if($user->role == 'Promoter' || $user->role == 'Promoter Additional' || $user->role == 'Promoter Event' || $user->role == 'Demonstrator MCC' || $user->role == 'Demonstrator DA' || $user->role == 'ACT'  || $user->role == 'PPE' || $user->role == 'BDT' || $user->role == 'Salesman Explorer' || $user->role == 'SMD' || $user->role == 'SMD Coordinator' || $user->role == 'HIC' || $user->role == 'HIE' || $user->role == 'SMD Additional' || $user->role == 'ASC'){
			$isPromoter = 1;
		}

		// Get stores from user if linked
		$storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
		$store = Store::whereIn('id', $storeIds)->select('id', 'store_id', 'store_name_1', 'longitude', 'latitude')->get();

		// all good so return the token
		return response()->json(['status' => 'true', 'token' => $token, 'role' => $user->role, 'is_promoter' => $isPromoter, 'status_promoter' => $user->status, 'store' => $store]);
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

	public function setProfilePhoto(){
        // On Progress
    }
}
