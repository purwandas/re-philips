<?php

namespace App\Traits;

use Carbon\Carbon;
use App\User;
use App\Attendance;
use App\EmployeeStore;

trait AttendanceTrait {

    public function generateAttendace($id){

        $dayOfPromoterWorks = (integer)Carbon::now()->format('d');
        $dayOfMonth = date('t');

        for ($i=$dayOfPromoterWorks;$i<=$dayOfMonth;$i++){

            $employee = $this->getEmployees($id);

            if ($this->getStores($employee->id)->count() > 0) {

                $generateDate = Carbon::createFromDate(Carbon::now()->year, Carbon::now()->month, $i);

                $countAttendance = Attendance::where('user_id', $employee->id)
                                ->where('date', Carbon::parse($generateDate)->format('Y-m-d'))->count();

                if($countAttendance == 0) {

                    /* Main Method */
                    Attendance::create([
                        'user_id' => $employee->id,
                        'date' => $generateDate,
                        'status' => 'Alpha',
                    ]);

                }

            }

        }

        return $dayOfPromoterWorks;
    }

    public function getEmployees($id){
        $employee = User::find($id);
        return $employee;
    }

    public function getStores($userId){
        $storeIds = EmployeeStore::where('user_id', $userId)->get();

        return $storeIds;
    }

}