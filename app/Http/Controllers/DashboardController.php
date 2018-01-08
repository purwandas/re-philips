<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Auto logout jika yang login bukan DM, RSM, Admin, atau Master Admin
        if(Auth::user()->role == 'Supervisor Hybrid' || Auth::user()->role == 'Supervisor' || Auth::user()->role == 'DM' || Auth::user()->role == 'RSM' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Master'){
            return view('dashboard');
        }

        return redirect('/logout');
    }

}
