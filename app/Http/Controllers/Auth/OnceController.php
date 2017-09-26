<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Region;
use Auth;

class OnceController extends Controller
{
    //
    public function createAdmin(){
    	$users = DB::table('users')->count();

    	if($users == 0){
    		User::create([
    			'name' => 'REM',
            	'email' => 'rem@gmail.com',            
            	'password' => bcrypt('admin'),
                'role' => 'Master',
    		]);
    	}

    	return redirect('/');
    }

    public function createRegion(){
        if(Auth::user()->role == 'Master'){
            $region = DB::table('regions')->count();

            if($region == 0){
                Region::create(['name'=>'East']);
                Region::create(['name'=>'Jabodetabek']);
                Region::create(['name'=>'Java']);
                Region::create(['name'=>'Sumatra']);
            }
        }

        return redirect('/');
    }
}
