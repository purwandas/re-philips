<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Filters\FanspageFilters;
use App\Fanspage;

class FanspageController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFanspage()
    {
        $data = Fanspage::select('fanspages.name', 'fanspages.url')->get();

        return response()->json($data);
    }


}

