<?php

namespace App\Http\Controllers\Master;

use App\AttendanceDetail;
use App\Reports\SummaryTargetActual;
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
use App\User;

class AttendanceController extends Controller
{
    use TargetTrait;

    public function index()
    {
        return view('master.attendance');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.attendance-form');
    }
    
    public function store(Request $request)
    {
        $user = $request['employee'];
        $user = User::where('id', $user)->first();
        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

        // Response if header was not set (command -> init:attendance)
        if($user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid' || $user->role->role_group == 'DM' || $user->role->role_group == 'Trainer' || $user->role->role_group == 'RSM' || $user->role->role_group == 'Salesman Explorer'){

            if(!$attendanceHeader) {
                $attendanceHeader = Attendance::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now(),
                    'status' => 'Alpha'
                ]);
            }

        }else{

            if(!$attendanceHeader) {
                return response()->json(['status' => false, 'message' => 'Tidak menemukan data absensi anda, silahkan hubungi administrator'], 500);
            }

        }

        // Count Attendance Details
        $attendanceDetailsCount = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->count();

        /* Insert user relation */
        if ($request['status'] == 'Masuk') {

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
                DB::transaction(function () use ($request, $attendanceHeader, $user) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Masuk'
                    ]);

                    $detail = 'User melakukan absensi melalui Admin';

                    // Attendance Detail Add
                    AttendanceDetail::create([
                        'attendance_id' => $attendanceHeader->id,
                        'store_id' => $request['store_id'],
                        // 'is_store' => $request['is_store'],
                        'check_in' => $request['check_in'],
                        'check_out' => $request['check_out'],
                        'detail' => $detail
                    ]);

                    // Change Actual Call - SEE
                    $this->changeActualCall($user->id);

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }
            
            if($user->role->role_group == 'Salesman Explorer' || $user->role->role_group == 'Supervisor' || $user->role->role_group == 'Supervisor Hybrid') {

                /* Set Visit Status */
                $visit = VisitPlan::where('user_id', $user->id)->where('store_id', $request['store_id'])->where('date', Carbon::now()->format('Y-m-d'))->first();

                if ($visit) {
                    $visit->update([
                        'visit_status' => 1,
                        'check_in' => Carbon::now(),
                        // 'check_in_location' => $content['location'],
                        'check_out' => Carbon::now(),
                        // 'check_out_location' => $content['location'],
                        ]);
                }

            }

            return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Absensi berhasil (Check In)']);
        }
        elseif ($request['status'] == 'Sakit') {

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
                    DB::transaction(function () use ($request, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Sakit',
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Absensi Berhasil (Sakit : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Anda sudah terhitung masuk untuk hari ini, status sakit tidak akan terhitung didalam data absensi']);

        }
        elseif ($request['status'] == 'Izin') {

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
                    DB::transaction(function () use ($request, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Izin',
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Absensi Berhasil (Izin : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Anda sudah terhitung masuk untuk hari ini, status izin tidak akan terhitung didalam data absensi']);


        }
        elseif ($request['status'] == 'Off') {

            // Check if promoter has already in off status
            if($attendanceHeader->status == 'Off'){
                return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status off(libur)'], 200);
            }

            // Promoter can set status to 'Off' just if in 'Alpha'
            if($attendanceHeader->status != 'Alpha' && $attendanceHeader->status != 'Pending Sakit' && $attendanceHeader->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Maaf, anda tidak bisa mengganti status menjadi off(libur)'], 200);
            }

            try {
                DB::transaction(function () use ($request, $attendanceHeader) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Off'
                    ]);

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            /* Change Weekly Target */
            $target = SummaryTargetActual::where('user_id', $user->id)->get();

            if($target){ // If Had

                foreach ($target as $data){

                    /* Change Weekly Target */
                    $total['da'] = $data['target_da'];
                    $total['pc'] = $data['target_pc'];
                    $total['mcc'] = $data['target_mcc'];

                    $this->changeWeekly($data, $total);

                }

            }

            return response()->json(['status' => true, 'url' => url('attendance'), 'message' => 'Absensi Berhasil (Off)']);
        }

    }
}
