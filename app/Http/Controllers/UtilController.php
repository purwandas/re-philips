<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UtilController extends Controller
{
    //
    public function existEmail(Request $request){    	    	
    	$userCount = User::where('email', $request->email)->count();

    	if($userCount > 0){
    		return "false";
    	}

    	return "true";
    }
}
