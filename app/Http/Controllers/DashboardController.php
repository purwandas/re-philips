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
        if(Auth::user()->role->role_group == 'Supervisor Hybrid' || Auth::user()->role->role_group == 'Supervisor' || Auth::user()->role->role_group == 'DM' || Auth::user()->role->role_group == 'RSM' || Auth::user()->role->role_group == 'Admin' || Auth::user()->role->role_group == 'Master'){
            return view('dashboard');
        }

        // return response()->json(Auth::user());

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

    public function getDataNationalSalesman(){
        return $this->dataNationalSalesman();
    }
}
