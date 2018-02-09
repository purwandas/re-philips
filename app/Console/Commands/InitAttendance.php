<?php

namespace App\Console\Commands;

use App\Attendance;
use App\EmployeeStore;
use App\Reports\SummaryTargetActual;
use App\Store;
use App\Traits\TargetTrait;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;

class InitAttendance extends Command
{
    use TargetTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate days of month to attendance table and set status to 0 (Alpha)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dayOfMonth = date('t');
        for ($i=1;$i<=$dayOfMonth;$i++){
            foreach ($this->getEmployees() as $employee) {

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

            // Change Target Weekly If Had
            foreach ($this->getEmployees() as $employee){

                $target = SummaryTargetActual::where('user_id', $employee->id)->get();

                if($target){ // If Summary TA Exist

                    foreach ($target as $data){

                        /* Change Weekly Target */
                        $total['da'] = $data['target_da'];
                        $total['pc'] = $data['target_pc'];
                        $total['mcc'] = $data['target_mcc'];

                        $this->changeWeekly($data, $total);

                    }

                }

            }
        }
        $this->info('Konfigurasi Tanggal Absensi Berhasil');
    }

    public function getEmployees(){

        $role = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $employee = User::whereHas('role', function($query) use ($role){
                        return $query->whereIn('role_group', $role);
                    })->get();

        return $employee;
    }

    public function getStores($userId){
        $storeIds = EmployeeStore::where('user_id', $userId)->get();

        return $storeIds;
    }
}
