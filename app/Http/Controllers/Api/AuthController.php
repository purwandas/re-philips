<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\EmployeeStore;
use App\Store;

class AuthController extends Controller
{
    public function login(Request $request)
	{


		// grab credentials from the request
		$credentials = $request->only('nik', 'password');

		// If NIK is null
		if($request->nik == null){
			return response()->json(['message' => 'invalid_credentials'], 401);
		}

		try {			
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['status' => 'false', 'message' => 'invalid_credentials'], 200);
			}

		} catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json(['status' => 'false', 'message' => 'could_not_create_token'], 500);			
		}

		// Get user data
		$user = Auth::user();

		// Get stores from user if linked
		$storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
		$store = Store::whereIn('id', $storeIds)->get();

		// all good so return the token
		return response()->json(['status' => 'true', 'token' => $token, 'role' => $user->role, 'status_promoter' => $user->status, 'store' => $store]);
	}

	public function tes(){
		// $user = JWTAuth::parseToken()->authenticate();
		return response()->json('test');	
	}

	public function getUser()
	{
		
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
}
