<?php

namespace App\Http\Controllers;

use App\Traits\AchievementTrait;
use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    use AchievementTrait;

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

    public function getDataNational(){
        return $this->dataNational();
    }

    public function getDataRegion(){
        return $this->dataRegion();
    }

    public function getDataArea(){
        return $this->dataArea();
    }

    public function getDataSupervisor(){
        return $this->dataSupervisor();
    }
}
