<?php

namespace App\Http\Controllers\Api\Master;

use App\AttendanceDetail;
use App\Store;
use App\Place;
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
    public function attendance(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        // Check header
        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

        // Response if header was not set (command -> init:attendance)
        if(!$attendanceHeader) {
            return response()->json(['status' => false, 'message' => 'Tidak menemukan data absensi anda, silahkan hubungi administrator'], 500);
        }

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

            if($content['is_store'] == 1){
                $location = Store::find($content['id']);
            }else if($content['is_store'] == 0){
                $location = Place::find($content['id']);
            }

            // Return if location longi and lati was not set
            if($location->longitude == null || $location->latitude == null){
                return response()->json(['status' => false, 'message' => 'Tempat absensi yang anda pilih belum terkonfigurasi, silahkan hubungi administrator'], 500);
            }

            $coordStore = Geotools::coordinate([$location->latitude, $location->longitude]);
            $coordNow = Geotools::coordinate([$content['latitude'], $content['longitude']]);
            $distance = Geotools::distance()->setFrom($coordStore)->setTo($coordNow)->flat();

            // Check distance if above 250 meter(s)
            if($distance > 250){
                return response()->json(['status' => false, 'message' => 'Jarak anda terlalu jauh dari tempat absensi'], 200);
            }

            // If promoter still didn't do check out
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Add attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceHeader) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Masuk'
                    ]);

                    $detail = ($content['other_store'] == 1) ? 'User melakukan absensi di toko lain' : null;

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


                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi berhasil (Check In)']);

        } elseif ($param == 2){ /* CHECK OUT */

            // If promoter hasn't data
            if($attendanceDetailsCount == 0){
                return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 500);
            }

            // Get last attendance detail
            $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

            // If promoter hasn't check in
            if($attendanceDetailsCount > 0){

                if($attendanceDetail->check_out != null){
                    return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 500);
                }

            }

            // Update attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceDetail) {

                    // Attendance Detail Update
                    $attendanceDetail->update([
                        'check_out' => Carbon::now(),
                        'check_out_longitude' => $content['longitude'],
                        'check_out_latitude' => $content['latitude'],
                        'check_out_location' => $content['location']
                    ]);


                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Check Out)']);

        } elseif ($param == 3){ /* SAKIT */

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 500);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 500);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 500);
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
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Sakit : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status sakit tidak akan terhitung didalam data absensi']);

        } elseif ($param == 4){ /* IZIN */

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 500);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 500);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 500);
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
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Izin : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status izin tidak akan terhitung didalam data absensi']);

        } elseif ($param == 5){ /* OFF || LIBUR */

            // Check if promoter has already in off status
            if($attendanceHeader->status == 'Off'){
                return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status off(libur)'], 500);
            }

            // Promoter can set status to 'Off' just if in 'Alpha'
            if($attendanceHeader->status != 'Alpha' && $attendanceHeader->status != 'Pending Sakit' && $attendanceHeader->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Maaf, anda tidak bisa mengganti status menjadi off(libur)'], 500);
            }

            try {
                DB::transaction(function () use ($content, $attendanceHeader) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Off'
                    ]);

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Off)']);
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

}
