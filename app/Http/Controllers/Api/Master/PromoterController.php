<?php

namespace App\Http\Controllers\Api\Master;

use App\Attendance;
use App\AttendanceDetail;
use App\EmployeeStore;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class PromoterController extends Controller
{
    public function getAttendanceForSupervisor(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');

        $attendances = Attendance::whereIn('user_id', $promoterIds)->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                     ->join('users', 'attendances.user_id', '=', 'users.id')
                     ->select('attendances.id as attendance_id', 'users.name as name', 'users.nik as nik', 'users.photo as photo', 'attendances.status as status')->get();

        foreach($attendances as $attendance){

            $detail = AttendanceDetail::where('attendance_id', $attendance->attendance_id)
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->select('attendance_details.check_in', 'attendance_details.check_in_longitude', 'attendance_details.check_in_latitude', 'attendance_details.check_in_location',
                        'attendance_details.check_out', 'attendance_details.check_out_longitude', 'attendance_details.check_out_latitude', 'attendance_details.check_out_location', 'attendance_details.detail as keterangan',
                        'stores.store_id', 'stores.store_name_1', 'stores.store_name_2')
                    ->get();

            if($attendance->status == 'Masuk'){
                $attendance['detail'] = $detail;
            }else{
                $attendance['detail'] = [];
            }

        }

        return response()->json($attendances);
    }

    public function approval(Request $request, $param){

        $attendances = Attendance::where('id', $request->attendance_id)->first();
        $message = "";

        if($param == 1){

            if($attendances->status != 'Pending Sakit'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval sakit'], 500);
            }

            $attendances->update(['status' => 'Sakit']);
            $message = "Sakit";

        }else{

            if($attendances->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval izin'], 500);
            }

            $attendances->update(['status' => 'Izin']);
            $message = "Izin";
        }

        return response()->json(['status' => true, 'id_attendance' => $attendances->id, 'message' => 'Approval '.$message.' berhasil']);

    }

    public function checkAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $details = AttendanceDetail::where('attendances.status', 'Masuk')->where('attendances.user_id', $user->id)
                    ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->select('attendances.date as date', 'attendance_details.check_in as check_in', 'attendance_details.check_out as check_out',
                        'stores.store_name_1 as store_name')
                    ->get();

        return response()->json($details);

    }

    public function checkNotAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $details = Attendance::where('attendances.user_id', $user->id)
                    ->where(function ($query) {
                        return $query->where('attendances.status', 'Pending Sakit')->orWhere('attendances.status', 'Pending Izin')
                                     ->orWhere('attendances.status', 'Sakit')->orWhere('attendances.status', 'Izin')
                                     ->orWhere('attendances.status', 'Alpha');
                    })
                    ->where('date', '<=', Carbon::now())
                    ->select('date', 'status')
                    ->get();

        return response()->json($details);

    }
}
