<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UtilController extends Controller
{
    //
    public function existEmail(Request $request){    	    	
    	$user = User::where('email', $request->email);

    	if($user->count() > 0){
    		// if($user->first()->email == $request->email){
    		// 	return "true";
    		// }
    		return "false";
    	}

    	return "true";
    }
}
