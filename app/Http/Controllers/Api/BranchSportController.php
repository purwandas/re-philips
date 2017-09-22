<?php

namespace App\Http\Controllers\Api;

use App\BranchSport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BranchSportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
    }
    public function index()
    {
        return response()->json(BranchSport::get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function nearby_competition()
    {
        $inputJSON = file_get_contents('php://input');
        $decode = json_decode($inputJSON, true);
        
        $get_lat = $decode['data'][0]['latitude'];
        $get_long = $decode['data'][0]['longitude'];

        $branch_sport = BranchSport::get();
        foreach ($branch_sport as $branch) {
            $datamaps = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$get_lat.",".$get_long."&destinations=".$branch['latitude'].",".$branch['longitude']."&key=%20AIzaSyCWpwVwu1hO6TJW1H8x_zlhrLfbSbQ2r3o");
            $decode_maps = json_decode($datamaps,true);
            $distance = $decode_maps['rows'][0]['elements'][0]['distance']['value'];
            // echo $distance."<br>";
            if ($distance < 1500000) {
                $data[] = $branch;
            }else{
                $data[] = "lewat";
            }
            return response()->json($data);
        }
    }
}
