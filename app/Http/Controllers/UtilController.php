<?php

namespace App\Http\Controllers;

use App\NewsRead;
use App\ProductKnowledgeRead;
use Illuminate\Http\Request;
use App\User;
use App\Store;
use App\AreaApp;
use App\EmployeeStore;
use DB;

class UtilController extends Controller
{
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
        $store = Store::whereIn('id', $empStoreIds)->get();        

        return response()->json($store);
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
}
