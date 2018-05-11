<?php

namespace App\Http\Controllers\Api\Master;

use App\AttendanceDetail;
use App\Reports\SummaryTargetActual;
use App\User;
use App\Store;
use App\Place;
use App\Traits\TargetTrait;
use App\VisitPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Geotools;
use App\Attendance;
use DB;

class AttendanceController extends Controller
{
    use TargetTrait;

    public function attendance(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        // CHECK PROMOTER OR NOT
        $promoter = 0;

        if($user->role->role_group == 'Promoter' || $user->role->role_group == 'Promoter Additional' || $user->role->role_group == 'Promoter Event' || $user->role->role_group == 'Demonstrator MCC' || $user->role->role_group == 'Demonstrator DA' || $user->role->role_group == 'ACT'  || $user->role->role_group == 'PPE' || $user->role->role_group == 'BDT' || $user->role->role_group == 'SMD' || $user->role->role_group == 'SMD Coordinator' || $user->role->role_group == 'HIC' || $user->role->role_group == 'HIE' || $user->role->role_group == 'SMD Additional' || $user->role->role_group == 'ASC'){
            $promoter = 1;
        }

        // Check header
        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

//        return response()->json($attendanceHeader);

        // Response if header was not set (command -> init:attendance)
        // if($user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid' || $user->role->role_group == 'DM' || $user->role->role_group == 'Trainer' || $user->role->role_group == 'RSM' || $user->role->role_group == 'Salesman Explorer'){
        
        // if($promoter == 0){

            if(!$attendanceHeader) {
                $attendanceHeader = Attendance::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now(),
                    'status' => 'Alpha'
                ]);
            }

        // }else{

        //     if(!$attendanceHeader) {
        //         return response()->json(['status' => false, 'message' => 'Tidak menemukan data absensi anda, silahkan hubungi administrator'], 200);
        //     }

        // }


        // Count Attendance Details
        $attendanceDetailsCount = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->count();

        /*
         * Checking mode of attendance input
         * 1 => Check In
         * 2 => Check Out
         * 3 => Sakit
         * 4 => Izin
         * 5 => Off
         */

        if($param == 1){ /* CHECK IN */

            // if($user->role->role_group != 'Salesman Explorer') {
            if($promoter == 1){

                if ($content['is_store'] == 1) {
                    $location = Store::where('id', $content['id'])->first();
                } else if ($content['is_store'] == 0) {
                    $location = Place::where('id', $content['id'])->first();
                }

                // Return if location longi and lati was not set
                if ($location->longitude == null || $location->latitude == null) {
                    return response()->json(['status' => false, 'message' => 'Tempat absensi yang anda pilih belum terkonfigurasi, silahkan hubungi administrator'], 200);
                }

                $coordStore = Geotools::coordinate([$location->latitude, $location->longitude]);
                $coordNow = Geotools::coordinate([$content['latitude'], $content['longitude']]);
                $distance = Geotools::distance()->setFrom($coordStore)->setTo($coordNow)->flat();

                // Check distance if above 350 meter(s)
                if ($distance > 350) {
                    return response()->json(['status' => false, 'message' => 'Jarak anda terlalu jauh dari tempat absensi'], 200);
                }

            }

            $checkOut = 0;

            // If promoter still didn't do check out
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                // if($attendanceDetail->check_out == null){
                //     return response()->json(['status' => false, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                // }

                if($attendanceDetail->check_out != null){
                    $checkOut = 1;
                }

            }

            // Add attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceHeader, $user, $checkOut, $attendanceDetailsCount) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Masuk'
                    ]);

                    $detail = ($content['other_store'] == 1) ? 'User melakukan absensi di toko lain' : null;

                    if($attendanceDetailsCount == 0){
                        // Attendance Detail Add
                        AttendanceDetail::create([
                            'attendance_id' => $attendanceHeader->id,
                            'store_id' => $content['id'],
                            'is_store' => $content['is_store'],
                            'check_in' => Carbon::now(),
                            'check_in_longitude' => $content['longitude'],
                            'check_in_latitude' => $content['latitude'],
                            'check_in_location' => $content['location'],
                            'detail' => $detail
                        ]);
                    }

                    if($checkOut == 1){

                        // Attendance Detail Add
                        AttendanceDetail::create([
                            'attendance_id' => $attendanceHeader->id,
                            'store_id' => $content['id'],
                            'is_store' => $content['is_store'],
                            'check_in' => Carbon::now(),
                            'check_in_longitude' => $content['longitude'],
                            'check_in_latitude' => $content['latitude'],
                            'check_in_location' => $content['location'],
                            'detail' => $detail
                        ]);

                    }

                    if($attendanceDetailsCount > 0){
                        $attendanceDetailSame = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->where('store_id', $content['id'])->count();

                        if($attendanceDetailSame == 0){
                            // Change Actual Call - SEE
                            $this->changeActualCall($user->id);
                        }
                    }else{
                        // Change Actual Call - SEE
                        $this->changeActualCall($user->id);
                    }                    

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            if($user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid' || $user->role->role_group == 'SMD' || $user->role->role_group == 'SMD Coordinator' || $user->role->role_group == 'SMD Additional') {

                /* Set Visit Status */
                $visit = VisitPlan::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', Carbon::now()->format('Y-m-d'))->first();

                if ($visit) {
                    $visit->update([
                        'visit_status' => 1,
                        'check_in' => Carbon::now(),
                        'check_in_location' => $content['location'],
                        ]);
                }

            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi berhasil (Check In)']);

        } elseif ($param == 2){ /* CHECK OUT */

            // If promoter hasn't data
            if($attendanceDetailsCount == 0){
                return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 200);
            }

            // Get last attendance detail
            $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

            $checkIn = 0;

            // If promoter hasn't check in
            if($attendanceDetailsCount > 0){

                // if($attendanceDetail->check_out != null){
                //     return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 200);
                // }

                if($attendanceDetail->check_out != null){
                    $checkIn = 1;
                }

            }

            // Update attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceDetail, $checkIn) {

                    if($checkIn == 0){

                        // Attendance Detail Update
                        $attendanceDetail->update([
                            'check_out' => Carbon::now(),
                            'check_out_longitude' => $content['longitude'],
                            'check_out_latitude' => $content['latitude'],
                            'check_out_location' => $content['location']
                        ]);

                    }

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            if($user->role == 'Salesman Explorer' || $user->role == 'Supervisor' || $user->role == 'Supervisor Hybrid') {

                /* Set Visit Status */
                $visit = VisitPlan::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', Carbon::now()->format('Y-m-d'))->first();

                if ($visit) {
                    $visit->update([
                        'check_out' => Carbon::now(),
                        'check_out_location' => $content['location'],
                        ]);
                }

            }
            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Check Out)']);

        } elseif ($param == 3){ /* SAKIT */

            // NOT PROMOTER 
            if($promoter == 0){

                // Check if promoter has approvement
                if($attendanceHeader->status == 'Sakit'){
                    return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status (Sakit)'], 200);
                }

                // If user has attendance data
                if($attendanceDetailsCount > 0){

                    // Get last attendance detail
                    $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                    if($attendanceDetail->check_out == null){
                        return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                    }

                }

                if($attendanceDetailsCount == 0){

                    try{
                        DB::transaction(function () use ($content, $attendanceHeader) {

                            // Attendance Header Update
                            $attendanceHeader->update([
                                'status' => 'Sakit',
                            ]);

                        });
                    }catch(\Exception $e){
                        return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                    }
                    return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Sakit)']);

                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status sakit tidak akan terhitung didalam data absensi']);
            }

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Update if no data in attendance detail
            if($attendanceDetailsCount == 0) {
                // Update attendance header
                                
                try {
                    DB::transaction(function () use ($content, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Sakit',
                        ]);
                    });
                    if ($user->role->role_group == 'Demonstrator DA') {
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('spv_demos', 'employee_stores.store_id', '=', 'spv_demos.store_id')
                                    ->join('users as spv_token', 'spv_demos.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }else{
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
                                    ->join('users as spv_token', 'stores.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }

                    if($spv_token->fcm_token != null){

                        $res = array();
                        $res['data']['title'] = 'absensi-Approval Sakit';
                        //$res['data']['is_background'] = $this->is_background;
                        $res['data']['message'] = $user->name .' absen sakit, butuh approval Anda! ';
                        //$res['data']['image'] = $this->image;
                        //$res['data']['payload'] = $this->data;
                        $res['data']['timestamp'] = date('Y-m-d G:i:s');

                        $fields = array(
                            'to' => $spv_token->fcm_token,
                            'data' => $res,

                        );

                        $url = 'https://fcm.googleapis.com/fcm/send';

                        $headers = array(
                            'Authorization: key=AAAAiy1AKL8:APA91bFexlzMrKvm_8GAuf5fo3sZBAx5HxP__GSAeg3UPrrrHuZiN6ghxuzRBNwZT4zoBv7btauByfnwRYAQKdAQ5sKWcACCOd51yzi_eDBujz_1wSItMPDSDFY2uIwND5IawvYqAoBa',
                            'Content-Type: application/json'
                        );
                        // Open connection
                        $ch = curl_init();

                    // return response()->json(['status' => false, 'message' => $fields], 200);
                    
                        // Set the url, number of POST vars, POST data
                        curl_setopt($ch, CURLOPT_URL, $url);

                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Disabling SSL Certificate support temporarly
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        // Execute post
                        $result = curl_exec($ch);
                        if ($result === FALSE) {
                            die('Curl failed: ' . curl_error($ch));
                        }

                        // Close connection
                        curl_close($ch);

                        // return $result;
                    }

                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }
                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Sakit : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status sakit tidak akan terhitung didalam data absensi']);

        } elseif ($param == 4){ /* IZIN */

            // NOT PROMOTER 
            if($promoter == 0){

                // Check if promoter has approvement
                if($attendanceHeader->status == 'Izin'){
                    return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status (Izin)'], 200);
                }

                // If user has attendance data
                if($attendanceDetailsCount > 0){

                    // Get last attendance detail
                    $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                    if($attendanceDetail->check_out == null){
                        return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                    }

                }

                if($attendanceDetailsCount == 0){

                    try{
                        DB::transaction(function () use ($content, $attendanceHeader) {

                            // Attendance Header Update
                            $attendanceHeader->update([
                                'status' => 'Izin',
                            ]);

                        });
                    }catch(\Exception $e){
                        return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                    }
                    return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Izin)']);

                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status izin tidak akan terhitung didalam data absensi']);
            }

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Update if no data in attendance detail
            if($attendanceDetailsCount == 0) {
                // Update attendance header
                try {
                    DB::transaction(function () use ($content, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Izin',
                        ]);

                    });

                    if ($user->role->role_group == 'Demonstrator DA') {
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('spv_demos', 'employee_stores.store_id', '=', 'spv_demos.store_id')
                                    ->join('users as spv_token', 'spv_demos.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }else{
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
                                    ->join('users as spv_token', 'stores.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }

                    if($spv_token->fcm_token != null){

                        $res = array();
                        $res['data']['title'] = 'absensi-Approval Izin';
                        //$res['data']['is_background'] = $this->is_background;
                        $res['data']['message'] = $user->name .' absen Izin, butuh approval Anda! ';
                        //$res['data']['image'] = $this->image;
                        //$res['data']['payload'] = $this->data;
                        $res['data']['timestamp'] = date('Y-m-d G:i:s');

                        $fields = array(
                            'to' => $spv_token->fcm_token,
                            'data' => $res,

                        );

                        $url = 'https://fcm.googleapis.com/fcm/send';

                        $headers = array(
                            'Authorization: key=AAAAiy1AKL8:APA91bFexlzMrKvm_8GAuf5fo3sZBAx5HxP__GSAeg3UPrrrHuZiN6ghxuzRBNwZT4zoBv7btauByfnwRYAQKdAQ5sKWcACCOd51yzi_eDBujz_1wSItMPDSDFY2uIwND5IawvYqAoBa',
                            'Content-Type: application/json'
                        );
                        // Open connection
                        $ch = curl_init();

                    // return response()->json(['status' => false, 'message' => $fields], 200);
                    
                        // Set the url, number of POST vars, POST data
                        curl_setopt($ch, CURLOPT_URL, $url);

                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Disabling SSL Certificate support temporarly
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        // Execute post
                        $result = curl_exec($ch);
                        if ($result === FALSE) {
                            die('Curl failed: ' . curl_error($ch));
                        }

                        // Close connection
                        curl_close($ch);

                        // return $result;
                    }

                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Izin : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status izin tidak akan terhitung didalam data absensi']);

        } elseif ($param == 5){ /* OFF || LIBUR */

            // NOT PROMOTER 
            if($promoter == 0){

                // Check if promoter has approvement
                if($attendanceHeader->status == 'Off'){
                    return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status (Off)'], 200);
                }

                // If user has attendance data
                if($attendanceDetailsCount > 0){

                    // Get last attendance detail
                    $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                    if($attendanceDetail->check_out == null){
                        return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                    }

                }

                if($attendanceDetailsCount == 0){

                    try{
                        DB::transaction(function () use ($content, $attendanceHeader) {

                            // Attendance Header Update
                            $attendanceHeader->update([
                                'status' => 'Off',
                            ]);

                        });
                    }catch(\Exception $e){
                        return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                    }
                    return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Off)']);

                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status off tidak akan terhitung didalam data absensi']);
            }

            // Check if promoter has already in off status
            if($attendanceHeader->status == 'Off'){
                return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status off(libur)'], 200);
            }

            // Promoter can set status to 'Off' just if in 'Alpha'
            if($attendanceHeader->status != 'Alpha' && $attendanceHeader->status != 'Pending Sakit' && $attendanceHeader->status != 'Pending Izin' && $attendanceHeader->status != 'Pending Off'){
                return response()->json(['status' => false, 'message' => 'Maaf, anda tidak bisa mengganti status menjadi off(libur)'], 200);
            }

                try {
                    DB::transaction(function () use ($content, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Off'
                        ]);

                    });

                    if ($user->role->role_group == 'Demonstrator DA') {
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('spv_demos', 'employee_stores.store_id', '=', 'spv_demos.store_id')
                                    ->join('users as spv_token', 'spv_demos.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }else{
                        $spv_token = User::where('users.id', $user->id)
                                    ->join('employee_stores', 'users.id', '=', 'employee_stores.user_id')
                                    ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
                                    ->join('users as spv_token', 'stores.user_id', '=', 'spv_token.id')
                                    ->select('spv_token.fcm_token')->first();
                    }

                    if($spv_token->fcm_token != null){
                        
                        $res = array();
                        $res['data']['title'] = 'absensi-Approval Off';
                        //$res['data']['is_background'] = $this->is_background;
                        $res['data']['message'] = $user->name .' absen Off, butuh approval Anda! ';
                        //$res['data']['image'] = $this->image;
                        //$res['data']['payload'] = $this->data;
                        $res['data']['timestamp'] = date('Y-m-d G:i:s');

                        $fields = array(
                            'to' => $spv_token->fcm_token,
                            'data' => $res,

                        );

                        $url = 'https://fcm.googleapis.com/fcm/send';

                        $headers = array(
                            'Authorization: key=AAAAiy1AKL8:APA91bFexlzMrKvm_8GAuf5fo3sZBAx5HxP__GSAeg3UPrrrHuZiN6ghxuzRBNwZT4zoBv7btauByfnwRYAQKdAQ5sKWcACCOd51yzi_eDBujz_1wSItMPDSDFY2uIwND5IawvYqAoBa',
                            'Content-Type: application/json'
                        );
                        // Open connection
                        $ch = curl_init();

                    // return response()->json(['status' => false, 'message' => $fields], 200);
                    
                        // Set the url, number of POST vars, POST data
                        curl_setopt($ch, CURLOPT_URL, $url);

                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Disabling SSL Certificate support temporarly
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        // Execute post
                        $result = curl_exec($ch);
                        if ($result === FALSE) {
                            die('Curl failed: ' . curl_error($ch));
                        }

                        // Close connection
                        curl_close($ch);

                        // return $result;
                    }

                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Off : masih dalam tahap verifikasi)']);
        }

    }

    /* GET DISTANCE METHOD */
    public function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {
        $earth_radius = 6371;

        $dLat = deg2rad( $latitude2 - $latitude1 );
        $dLon = deg2rad( $longitude2 - $longitude1 );

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

    /* Check if promoter had checked in */
    public function getCheckIn(){

        $user = JWTAuth::parseToken()->authenticate();

        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

        if($attendanceHeader){

            $attendanceDetails = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC');

            if($attendanceDetails->count() > 0){

                if($attendanceDetails->first()->check_out == null) {

                    if($attendanceDetails->first()->is_store == 1){
                        $store = Store::find($attendanceDetails->first()->store_id);

                        return response()->json(['status' => true, 'id_store' => $store->id, 'nama_store' => $store->store_name_1, 'jam_check_in' => $attendanceDetails->first()->check_in]);
                    }else{
                        $place = Place::find($attendanceDetails->first()->store_id);

                        return response()->json(['status' => true, 'id_store' => $place->id, 'nama_store' => $place->name, 'jam_check_in' => $attendanceDetails->first()->check_in]);
                    }

                }

            }

        }

        return response()->json(['status' => false, 'message' => 'Tidak berada dalam status check in']);

    }

    public function getTotalHK($id){

        $user = User::where('id', $id)->first();

        $countHK = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', Carbon::now()->format('m'))
                    ->whereYear('date', Carbon::now()->format('Y'))
                    ->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('status', '<>', 'Off')->count('id');

//        if($countHK > 26){
//            $countHK = 26;
//        }

        return $countHK;

    }

}
