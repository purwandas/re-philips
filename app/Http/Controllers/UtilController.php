<?php

namespace App\Http\Controllers;

use App\Distributor;
use App\NewsRead;
use App\ProductKnowledgeRead;
use App\StoreDistributor;
use App\Traits\StoreTrait;
use Illuminate\Http\Request;
use App\User;
use App\Store;
use App\AreaApp;
use App\SpvDemo;
use App\EmployeeStore;
use App\AttendanceDetail;
use App\TargetQuiz;
use App\Reports\HistoryEmployeeStore;
use App\Reports\SalesActivity;
use DB;
use Activity;
use Auth;

class UtilController extends Controller
{
    use StoreTrait;

    //
    public function existEmailUser(Request $request){
    	$user = User::where('email', $request->email);

    	if($user->count() > 0){
            if($request->form_method == 'PATCH'){

                $oldUser = User::find($request->userId);

                if($oldUser->email == $request->email){
                    return "true";
                }    
            }    		
    		return "false";
    	}
    	return "true";
    }

    public function existEmailEmployee(Request $request){               
        $employee = Employee::where('email', $request->email);

        if($employee->count() > 0){
            if($request->form_method == 'PATCH'){

                $oldEmployee = Employee::find($request->employeeId);

                if($oldEmployee->email == $request->email){
                    return "true";
                }    
            }  
            return "false";
        }

        return "true";
    }

    public function getStoreForEmployee($userId){        
        // $empStore = EmployeeStore::where('employee_id', $employeeId);
        $empStore = EmployeeStore::where('user_id', $userId);        
        $empStoreIds = $empStore->pluck('store_id');
        $store = Store::where('stores.deleted_at', null)
                    // ->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    // ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    // ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
//                    ->join('users', 'stores.user_id', '=', 'users.id')
                    ->whereIn('stores.id', $empStoreIds)
                    ->select('stores.*', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name'
                        // , 'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name'
                        )
                    ->get();

        foreach ($store as $item){

            if($item->user_id == null){
                $item['spv_name'] = "";
            }else{
                $item['spv_name'] = $item->user->name;
            }

        }

        return response()->json($store);
    }

    public function getHistoryStoreForEmployee($userId){        
        // $empStore = EmployeeStore::where('employee_id', $employeeId);
        $emStore = HistoryEmployeeStore::where('user_id', $userId)
                    // ->join('stores as storeid', history_employee_stores.details, '=', 'stores.')
                    ->orderBy('id', 'desc')->get();
                    // ->select('history_employee_stores.*')
        // pluck('details');
        //         $result[0]['user_name']
        $index = 0;
        foreach ($emStore as $key => $value) {
            $result[$index]['id'] = $value->id;
            $result[$index]['user_id'] = $value->user_id;
            $datem = date('F', strtotime($value->created_at));
            $result[$index]['month'] = $datem;
            $result[$index]['year'] = $value->year;
            $result[$index]['details'] = $value->details;
                $emStoreIds = explode(",", $value->details);
            
                $store = Store::where('stores.deleted_at', null)
                            ->whereIn('stores.id', $emStoreIds)
                            ->select('stores.*')->get();
                    $idx = 0;
                    foreach ($store as $keyStore => $valueStore) {
                        $result[$index]['stores']['store_id'][$idx] = $valueStore->store_id;
                        $result[$index]['stores']['store_name_1'][$idx] = $valueStore->store_name_1;
                        $result[$index]['stores']['store_name_2'][$idx] = $valueStore->store_name_2;
                        $idx ++;
                    }
            $index++;
        }

        return response()->json($result);
    }

    public function getStoreForSpvEmployee($userId){        
        $store = Store::where('stores.deleted_at', null)
                    ->where('stores.user_id', $userId)
                    // ->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    // ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    // ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->select('stores.*', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name'
                        )
                    ->get();

        foreach ($store as $item){

            if($item->user_id == null){
                $item['user_id'] = "";
            }else{
                $item['user_id'] = $item->user->name;
            }

        }

        return response()->json($store);
    }

    public function getStoreForSpvDemoEmployee($userId){        
        $store = SpvDemo::where('spv_demos.deleted_at', null)
                    ->where('spv_demos.user_id', $userId)
                    ->join('stores', 'spv_demos.store_id', '=', 'stores.id')
                    // ->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    // ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    // ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->select('stores.*', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name'
                        // ,'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name'
                        )
                    ->get();

        foreach ($store as $item){

            if($item->user_id == null){
                $item['user_id'] = "";
            }else{
                $item['user_id'] = $item->user->name;
            }

        }

        return response()->json($store);
    }
    
    public function getTargetQuiz($quizId){        
        $quiz = TargetQuiz::where('target_quizs.deleted_at', null)
                    ->where('target_quizs.quiz_id', $quizId)
                    ->join('quiz_targets','quiz_targets.id','target_quizs.quiz_target_id')
                    ->select('quiz_targets.id','quiz_targets.role','quiz_targets.grading')
                    ->get();

        return response()->json($quiz);
    }

    public function getAttendanceDetail($attendance_id){        
        $attendance = AttendanceDetail::where('attendance_id',$attendance_id)
            ->join('stores','stores.id', 'attendance_details.store_id')
            ->select('attendance_details.*', 'stores.store_id as storeId', 'stores.store_name_1', 'stores.store_name_2')
            ->get();

        return response()->json($attendance);
    }

    public function getDistributorForStore($storeId){

        $storeDist = StoreDistributor::where('store_id', $storeId);
        $storeDistIds = $storeDist->pluck('distributor_id');
        $distributor = Distributor::where('distributors.deleted_at', null)
                    ->whereIn('distributors.id', $storeDistIds)->get();

        return response()->json($distributor);
    }

    public function getAreaApp($id){
        $data = AreaApp::find($id);
        return response()->json($data);
    }

    public function getStore($id){
        $data = Store::find($id);
        return response()->json($data);
    }

    public function getUser($id){
        $data = User::find($id);
        return response()->json($data);
    }

     public function getNewsRead($id){
        $data = NewsRead::where('news_id', $id)
                    ->join('users', 'news_reads.user_id', '=', 'users.id')
                    ->select('users.*', 'news_reads.created_at as read_at')
                    ->get();

        return response()->json($data);
    }

    public function getProductRead($id){
        $data = ProductKnowledgeRead::where('productknowledge_id', $id)
                    ->join('users', 'product_knowledge_reads.user_id', '=', 'users.id')
                    ->select('users.*', 'product_knowledge_reads.created_at as read_at')
                    ->get();

        return response()->json($data);
    }

    public function getUserOnline(){

        $users = Activity::users()->where('user_id', '<>', Auth::user()->id)->orderByUsers('name');

        return response()->json([
            'count' => $users->count(),
            'users' => $users->get(),
        ]);
    }

    public function getSalesHistory(){

        $activity = SalesActivity::
                    whereNull('sales_activities.status')
                    ->orwhere('sales_activities.status',0)
                    ->join('users','users.id','sales_activities.user_id')
                    ->select('sales_activities.*','users.name','users.role');

        return response()->json([
            'count' => $activity->count(),
            'activity' => $activity->get(),
        ]);
    }

    public function readSalesHistory(Request $request){

        $activity = SalesActivity::
                    where('id',$request['id'])
                    ->first();
        $act = $activity;
            if ($activity->count() >0) {
                $activity->update([
                    'status' => 1
                ]);
            }

        return response()->json([
            'status' => true,
            'activity' => $act,
        ]);
    }

    public function getStoreId(){

    		return $this->traitGetStoreId();
    }
}
