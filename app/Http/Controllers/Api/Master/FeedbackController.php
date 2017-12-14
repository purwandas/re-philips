<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Store;
use App\EmployeeStore;
use App\User;

class FeedbackController extends Controller
{
    public function getListPromoterFeedback(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');
        $promoters = User::whereIn('id', $promoterIds)->get();

        return response()->json($promoters);
    }
}
