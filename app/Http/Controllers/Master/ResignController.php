<?php

namespace App\Http\Controllers\Master;

use App\Store;
use App\SpvDemo;
use App\TrainerArea;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use App\Attendance;
use App\AttendanceDetail;
use App\SalesmanDedicate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Traits\AttendanceTrait;
use Illuminate\Support\Collection;
use Auth;
use App\Filters\UserFilters;
use File;
use App\NewsRead;
use App\ProductKnowledge;
use App\ProductKnowledgeRead;
use App\Reports\HistoryEmployeeStore;
use Carbon\Carbon;
use DB;

class ResignController extends Controller
{
    use UploadTrait;
    use StringTrait;
    use AttendanceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.resign');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        
        $data = User::where('is_resign', 0)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('users.id', '<>', Auth::user()->id)
            ->whereIn('role_group',$roles);

//        $data = User::all();



        $filter = $data;

        /* If filter */
            if($request['byName']){
                $filter = $filter->where('users.id', $request['byName']);
            }

            if($request['byNik']){
                $filter = $filter->where('users.id', $request['byNik']);
            }

            if($request['byRole']){
                $filter = $filter->where('role', $request['byRole']);
            }

        return Datatables::of($filter->get())
                ->editColumn('role',function ($item) {
                    
                    if ($item->role == 'Salesman Explorer') {
                        $dedicate = '';
                        $salesmanDedicate = SalesmanDedicate::where('user_id',$item->id)->first();
                        if (isset($salesmanDedicate)) {
                            $dedicate = ' - '.$salesmanDedicate->dedicate;
                        }
                        return $item->role.$dedicate;
                    }


                    return $item->role;                    
                    
                })
                ->addColumn('action', function ($item) {
                    
                    return "<a href='#resign' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-danger confirm-resign'><i class='fa fa-sign-out'></i></a>";
                    
                })
                ->addColumn('store', function ($item) {

                    $countStore = $item->employeeStores()->count();

                    if($countStore > 0){
                        return
                        "<a class='open-employee-store-modal btn btn-primary' data-target='#employee-store-modal' data-toggle='modal' data-url='util/empstore' data-title='List Store' data-promoter-name='".$item->name."' data-id='".$item->id."'> See Details </a>";
                    }

                    return;

                })
                ->addColumn('supervisor', function ($item) {
                    if($item->role == 'Demonstrator DA'){
                        $storeIds = EmployeeStore::
                                        with('store.user')
                                        // ->distinct('store.user.name')
                                        ->where('user_id', $item->id)
                                        // ->groupBy('user_id')
                                        ->pluck('store_id');

                                        // return $storeIds;

                        $spvDemoIds = SpvDemo::whereIn('store_id', $storeIds)->pluck('user_id')->toArray();
                        $newSpvDemoIds = array_unique($spvDemoIds);

                        $spvDemo = '';

                        if(count($newSpvDemoIds) > 0){
                            $check = 0;
                            foreach ($newSpvDemoIds as $key => $value){
                                $user = User::where('id', $value)->first();
                                $userTmp = (isset($user)) ? $user->name : '';
                                
                                
                                if ($check > 0 && $userTmp != '') {
                                    $spvDemo .= ', ';
                                }
                                
                                
                                if (isset($userTmp) && !empty($userTmp) && $userTmp != ''){
                                    $spvDemo .= $userTmp;
                                    $check = 1;
                                }
                                
                            }
                        }

                        return $spvDemo;
                    }
                    $storeIds = EmployeeStore::
                                        with('store.user')
                                        // ->distinct('store.user.name')
                                        ->where('user_id', $item->id)
                                        // ->groupBy('user_id')
                                        ->get();

                    $storeSpv = [];
                    foreach ($storeIds as $storeId){
                        if (isset($storeId->store->user->name)) {
                            $storeSpv[] = $storeId->store->user->name;
                        }
                    }

                    $newStoreSpv = array_unique($storeSpv);

                    $storeSpvName = '';
                    foreach ($newStoreSpv as $key => $value) {
                        if ($key > 0) {
                            $storeSpvName .= ', ';
                        }
                        $storeSpvName .= $value;
                    }

                    return $storeSpvName;

                })
                ->addColumn('area', function ($item) {
                    $store = EmployeeStore::
                                        where('employee_stores.user_id', $item->id)
                                        ->join('stores','stores.id','employee_stores.store_id')
                                        ->join('districts','districts.id','stores.district_id')
                                        ->join('areas','areas.id','districts.area_id')
                                        ->groupBy('areas.id')
                                        ->select('areas.name as area_name')
                                        ->get();
                        
                        $area='';
                        foreach ($store as $key => $value) {
                            if ($key == 0) {
                                $area = $value->area_name;
                            }else{
                                $area .= ", ".$value->area_name;
                            }
                        }
                        return $area;

                    return $area;

                })
                ->addColumn('history', function ($item) {

                    $countStore = $item->historyEmployeeStores()->count();

                    if($countStore > 0){
                        return
                        "<a class='open-history-employee-store-modal btn btn-primary' data-target='#history-employee-store-modal' data-toggle='modal' data-url='util/historyempstore' data-title='List History' data-promoter-name='".$item->name."' data-id='".$item->id."'> See History </a>";
                    }

                    return;

                })
                ->rawColumns(['history', 'store', 'action'])
                ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRejoin()
    {
        return view('master.rejoin');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTableRejoin(Request $request){
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        
        $data = User::where('is_resign', 1)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('users.id', '<>', Auth::user()->id)
            ->whereIn('role_group',$roles);

//        $data = User::all();



        $filter = $data;

        /* If filter */
            if($request['byName']){
                $filter = $filter->where('users.id', $request['byName']);
            }

            if($request['byNik']){
                $filter = $filter->where('users.id', $request['byNik']);
            }

            if($request['byRole']){
                $filter = $filter->where('role', $request['byRole']);
            }

        return Datatables::of($filter->get())
                ->editColumn('role',function ($item) {
                    
                    if ($item->role == 'Salesman Explorer') {
                        $dedicate = '';
                        $salesmanDedicate = SalesmanDedicate::where('user_id',$item->id)->first();
                        if (isset($salesmanDedicate)) {
                            $dedicate = ' - '.$salesmanDedicate->dedicate;
                        }
                        return $item->role.$dedicate;
                    }


                    return $item->role;                    
                    
                })
                ->addColumn('action', function ($item) {
                    
                    return "<a href='".url('userpromoter/edit/'.$item->id)."' class='btn btn-sm btn-success'><i class='fa fa-sign-in'></i></a>";
                    
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function getDataGroupPromoterWithFiltersCheck(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        $data = User::filter($filters)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('is_resign', 1)
            ->limit(1)
            ->whereIn('role_group',$roles)->get();

        return $data;
    }

    public function getDataGroupPromoterWithFilters(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];

        $data = User::filter($filters)
            ->join('roles','roles.id','users.role_id')
            ->leftJoin('gradings','gradings.id','users.grading_id')
            ->select('users.*','roles.role_group as role','roles.role as roles', 'roles.role_group', 'gradings.grading')
            ->where('is_resign', 1)
            ->whereIn('role_group',$roles)->get();

        return $data;
    }
}
