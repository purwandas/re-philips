<?php

namespace App\Http\Controllers\Master;

use App\Employee;
use App\EmployeeStore;
use App\Filters\KonfigPromoFilters;
use App\Filters\KonfigStoreFilters;
use App\Grading;
use App\SpvDemo;
use App\TrainerArea;
use App\Traits\PromoterTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
use App\Store;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\Distributor;
use App\StoreDistributor;

class KonfigController extends Controller
{
    use PromoterTrait;

    public function konfigStoreIndex(){
        return view('report.konfig-store');
    }

    public function konfigPromoterIndex(){
        return view('report.konfig-promoter');
    }

    public function promoterData(Request $request, KonfigPromoFilters $filters){

        /*$data = EmployeeStore:://join('users', 'users.id', '=', 'employee_stores.user_id')
//                ->join('gradings', 'gradings.id', 'users.grading_id')
//                ->join('roles', 'roles.id', 'users.role_id')
//                join('stores', 'stores.id', '=', 'employee_stores.store_id')
//                ->join('sub_channels', 'sub_channels.id', 'stores.subchannel_id')
//                ->join('channels', 'channels.id', 'sub_channels.channel_id')
//                ->join('global_channels', 'global_channels.id', 'channels.globalchannel_id')
//                ->join('classifications', 'classifications.id', 'stores.classification_id')
//                ->join('districts', 'districts.id', 'stores.district_id')
//                ->join('areas', 'areas.id', 'districts.area_id')
//                ->join('regions', 'regions.id', 'areas.region_id')
//                ->select('employee_stores.*', 'users.nik', 'users.name', 'gradings.grading', 'roles.role',
//                    'users.status', 'users.join_date', 'regions.name as region', 'areas.name as area', 'districts.name as district',
//                    'stores.store_id', 'stores.store_name_1', 'stores.store_name_2')
                with('user.role', 'store.district.area.region',
                        'store.classification', 'store.subChannel.channel.globalChannel', 'store.storeDistributors.distributor')
//                ->select('employee_stores.*', 'stores.*')
                ->orderBy('employee_stores.user_id')
                ->get();

        $data = EmployeeStore::filter($filters)
                ->join('users', 'users.id', '=', 'employee_stores.user_id')
                ->leftJoin('gradings', 'gradings.id', 'users.grading_id')
                ->join('roles', 'roles.id', 'users.role_id')
                ->join('stores', 'stores.id', '=', 'employee_stores.store_id')
                ->leftJoin('sub_channels', 'sub_channels.id', 'stores.subchannel_id')
                ->leftJoin('channels', 'channels.id', 'sub_channels.channel_id')
                ->leftJoin('global_channels', 'global_channels.id', 'channels.globalchannel_id')
                ->leftJoin('classifications', 'classifications.id', 'stores.classification_id')
                ->leftJoin('districts', 'districts.id', 'stores.district_id')
                ->leftJoin('areas', 'areas.id', 'districts.area_id')
                ->leftJoin('regions', 'regions.id', 'areas.region_id')
                ->select('employee_stores.*', 'users.nik', 'users.name', 'gradings.grading', 'roles.role', 'stores.user_id as spv_id', 'areas.id as area_id',
                    'users.status', 'users.join_date', 'regions.name as region', 'areas.name as area', 'districts.name as district',
                    'global_channels.name as global_channel', 'channels.name as channel', 'sub_channels.name as sub_channel',
                    'stores.store_id as store_id_gen', 'stores.store_name_1', 'stores.store_name_2', 'classifications.classification as classification')
                ->orderBy('employee_stores.user_id')
                ->get();

//        $data = EmployeeStore::with('user.role', 'user.grading', 'store.storeDistributors.distributor',
//                'store.district.area.region', 'store.subChannel.channel.globalChannel', 'store.classification')->get();

        $data2 = User::with('employeeStores.store.subChannel.channel.globalChannel', 'employeeStores.store.district.area.region',
                    'employeeStores.store.classification', 'role', 'grading')
                    ->whereHas('role', function($query){
                        return $query->whereIn('role_group', $this->getPromoterGroup());
                    })
                    ->get();

//        return response()->json($data);*/

        $data = EmployeeStore::filter($filters)->with('user.role', 'user.grading', 'store.district.area.dmAreas.user',
                'store.district.area.trainerAreas.user', 'store.district.area.region', 'store.subChannel.channel.globalChannel', 'store.classification', 'store.storeDistributors.distributor')->get();

//        return response()->json($data);

        return Datatables::of($data)
            ->addColumn('nik', function ($item) {
                return $item->user->nik;
            })
            ->addColumn('name', function ($item) {
                return $item->user->name;
            })
            ->addColumn('grading', function ($item) {                
                if($item->user->grading_id != null || $item->user->grading_id != 0 || $item->user->grading_id != ''){
                    // return Grading::where('id', $item->user->grading_id)->first()->grading;
                    return $item->user->grading->grading;
                }
                return '-';
            })
            ->addColumn('role', function ($item) {
                return $item->user->role->role;
            })
            ->addColumn('status', function ($item) {
                return $item->user->status;
            })
            ->addColumn('join_date', function ($item) {
                return $item->user->join_date;
            })
            ->addColumn('region', function ($item) {
                return $item->store->district->area->region->name;
            })
            ->addColumn('area', function ($item) {
                return $item->store->district->area->name;
            })
            ->addColumn('district', function ($item) {
                return $item->store->district->name;
            })
            ->addColumn('store_id_gen', function ($item) {
                return $item->store->store_id;
            })
            ->addColumn('store_name_1', function ($item) {
                return $item->store->store_name_1;
            })
            ->addColumn('store_name_2', function ($item) {
                return $item->store->store_name_2;
            })
            ->addColumn('classification', function ($item) {
                return $item->store->classification->classification;
            })
            ->addColumn('global_channel', function ($item) {
                if($item->store->subchannel_id != null) return $item->store->subChannel->channel->globalChannel->name;
                return '-';
            })
            ->addColumn('channel', function ($item) {
                if($item->store->subchannel_id != null) return $item->store->subChannel->channel->name;
                return '-';
            })
            ->addColumn('sub_channel', function ($item) {
                if($item->store->subchannel_id != null) return $item->store->subChannel->name;
                return '-';
            })
            ->addColumn('distributor_code', function ($item) {
                if($item->store->storeDistributors->first()){
                    return $item->store->storeDistributors->first()->distributor->code;
                }

                // $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                //     return $query->where('store_id', $item->store_id);
                // })->first();

                // if($data){
                //     return $data->code;
                // }
                return "-";
            })
            ->addColumn('distributor_name', function ($item) {

                if($item->store->storeDistributors->first()){
                    return $item->store->storeDistributors->first()->distributor->name;
                }

                // $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                //     return $query->where('store_id', $item->store_id);
                // })->first();

                // if($data){
                //     return $data->name;
                // }
                return "-";
            })
            ->addColumn('spv_name', function ($item) {
                if($item->user->role->role == 'Demonstrator DA'){
                    $data = SpvDemo::where('store_id', $item->store_id)
                            ->join('users', 'users.id', 'spv_demos.user_id')
                            ->select('users.name as spv_name');

                    if($data->count() > 0){
                        return $data->first()->spv_name;
                    }
                }
                if($item->store->user_id != null){
                    $data = User::where('id', $item->store->user_id)
                                ->select('users.name as spv_name')->first();
                    return $data->spv_name;
                }else{
                    $data = SpvDemo::where('store_id', $item->store_id)
                            ->join('users', 'users.id', 'spv_demos.user_id')
                            ->select('users.name as spv_name');

                    if($data->count() > 0){
                        return $data->first()->spv_name;
                    }
                }
                return "-";
            })
            ->addColumn('dm_name', function ($item) {
                if($item->store->district->area->dmAreas->first()){
                    return $item->store->district->area->dmAreas->first()->user->name;
                }
                
                // $data = DmArea::where('area_id', $item->store->district->area->id)
                //                 ->join('users', 'users.id', 'dm_areas.user_id')
                //                 ->select('users.name as dm_name')->first();
                // if($data){
                //     return $data->dm_name;
                // }
                return "-";
            })
            ->addColumn('trainer_name', function ($item) {
                if($item->store->district->area->trainerAreas->first()){
                    return $item->store->district->area->trainerAreas->first()->user->name;
                }            

                // $data = TrainerArea::where('area_id', $item->store->district->area->id)
                //                 ->join('users', 'users.id', 'trainer_areas.user_id')
                //                 ->select('users.name as trainer_name')->first();
                // if($data){
                //     return $data->trainer_name;
                // }
                return "-";
            })
            ->make(true);

    }

    public function getDataWithFilters(KonfigPromoFilters $filters){

        $data = EmployeeStore::filter($filters)->get();
        return $data;

    }

    public function storeData(Request $request, KonfigStoreFilters $filters){

        // $storeIds = Store::pluck('id');

        // $data = EmployeeStore::with('user.role',
        //         'store.district.area.region', 'store.subChannel.channel.globalChannel', 'store.classification')
        //         ->whereIn('employee_stores.store_id', $storeIds)
        //         ;

        // $data = Store::leftJoin('employee_stores', 'employee_stores.store_id', 'stores.id')
        //         ->with('employeeStores.user.role','district.area.region', 'subChannel.channel.globalChannel', 'classification')
        //         ->where('stores.id', 12);

        // $data = $data->filter($filters);

        // $data = Store::crossJoin('employee_stores');
        // $data = DB::table('stores')->crossJoin('employee_stores');
        $data = Store::withCount('employeeStores')->leftJoin('employee_stores', 'employee_stores.store_id', 'stores.id')->with('employeeStores.user.role', 'employeeStores.user.grading','district.area.region', 'subChannel.channel.globalChannel', 'classification', 'storeDistributors.distributor', 'district.area.dmAreas', 'district.area.trainerAreas')
            ;

        $data = $data->filter($filters);

        $data = $data->get();

        $count = 0;
        $maxCount = 0;
        foreach ($data as $detail) {

            $detail['count'] = $count;

            $maxCount = $detail['employee_stores_count'];
            $count += 1;
            if($count == $maxCount){
                $count = 0;
            }            

            if($maxCount == 0) $count = 0;
        }

        // $data = Store::withCount('employeeStores')
        //         ->leftJoin('employee_stores', 'employee_stores.store_id', 'stores.id')->with('employeeStores.user');

        // return response()->json($data);

        return Datatables::of($data)
            ->addColumn('nik', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){
                        return $item->employeeStores->first()->user->nik;
                    }else if($item->count > 0){
                        return $item->employeeStores()->skip($item->count)->first()->user->nik;
                    }

                }
                return '-';
            })
            ->addColumn('name', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){
                        return $item->employeeStores->first()->user->name;
                    }else if($item->count > 0){
                        return $item->employeeStores()->skip($item->count)->first()->user->name;
                    }

                }
                return '-';
            })
            ->addColumn('grading', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){

                        if($item->employeeStores->first()->user->grading_id != null || $item->employeeStores->first()->user->grading_id != 0 || $item->employeeStores->first()->user->grading_id != ''){
                            return $item->employeeStores->first()->user->grading->grading;
                        }

                    }else if($item->count > 0){

                        if($item->employeeStores()->skip($item->count)->first()->user->grading_id != null || $item->employeeStores()->skip($item->count)->first()->user->grading_id != 0 || $item->employeeStores()->skip($item->count)->first()->user->grading_id != ''){
                            return $item->employeeStores()->skip($item->count)->first()->user->grading->grading;
                        }

                    }

                }
                return '-';
            })
            ->addColumn('role', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){
                        return $item->employeeStores->first()->user->role->role;
                    }else if($item->count > 0){
                        return $item->employeeStores()->skip($item->count)->first()->user->role->role;
                    }

                }
                return '-';
            })
            ->addColumn('status', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){
                        return $item->employeeStores->first()->user->status;
                    }else if($item->count > 0){
                        return $item->employeeStores()->skip($item->count)->first()->user->status;
                    }

                }
                return '-';
            })
            ->addColumn('join_date', function ($item) {
                if($item->employeeStores->first()){
                    if($item->count == 0){
                        return $item->employeeStores->first()->user->join_date;
                    }else if($item->count > 0){
                        return $item->employeeStores()->skip($item->count)->first()->user->join_date;
                    }

                }
                return '-';
            })
            ->addColumn('region', function ($item) {
                // return '-';
                return $item->district->area->region->name;
            })
            ->addColumn('area', function ($item) {
                // return '-';
                return $item->district->area->name;
            })
            ->addColumn('district', function ($item) {
                // return '-';
                return $item->district->name;
            })
            ->addColumn('store_id_gen', function ($item) {
                // return '-';
                return $item->store_id;
            })
            ->addColumn('store_name_1', function ($item) {
                // return '-';
                return $item->store_name_1;
            })
            ->addColumn('store_name_2', function ($item) {
                // return '-';
                return $item->store_name_2;
            })
            ->addColumn('classification', function ($item) {
                // return '-';
                return $item->classification->classification;
            })
            ->addColumn('global_channel', function ($item) {
                // return '-';
                if($item->subchannel_id != null) return $item->subChannel->channel->globalChannel->name;
                return '-';
            })
            ->addColumn('channel', function ($item) {
                // return '-';
                if($item->subchannel_id != null) return $item->subChannel->channel->name;
                return '-';
            })
            ->addColumn('sub_channel', function ($item) {
                // return '-';
                if($item->subchannel_id != null) return $item->subChannel->name;
                return '-';
            })
            ->addColumn('distributor_code', function ($item) {
                if($item->storeDistributors->first()){
                    return $item->storeDistributors->first()->distributor->code;
                }
                // return '-';
            })
            ->addColumn('distributor_name', function ($item) {
                if($item->storeDistributors->first()){
                    return $item->storeDistributors->first()->distributor->name;
                }
                // return '-';
            })
            ->addColumn('spv_name', function ($item) {
                if($item->employeeStores->first()){

                    if($item->count == 0){
                       
                        if($item->employeeStores->first()->user->role->role == 'Demonstrator DA'){
                            $data = SpvDemo::where('store_id', $item->id)
                                    ->join('users', 'users.id', 'spv_demos.user_id')
                                    ->select('users.name as spv_name');

                            if($data->count() > 0){
                                return $data->first()->spv_name;
                            }
                        }
                        if($item->user_id != null){
                            $data = User::where('id', $item->user_id)
                                        ->select('users.name as spv_name')->first();
                            return $data->spv_name;
                        }else{
                            $data = SpvDemo::where('store_id', $item->id)
                                    ->join('users', 'users.id', 'spv_demos.user_id')
                                    ->select('users.name as spv_name');

                            if($data->count() > 0){
                                return $data->first()->spv_name;
                            }
                        }
                        

                    }else if($item->count > 0){
                        
                        if($item->employeeStores()->skip($item->count)->first()->user->role->role == 'Demonstrator DA'){
                            $data = SpvDemo::where('store_id', $item->id)
                                    ->join('users', 'users.id', 'spv_demos.user_id')
                                    ->select('users.name as spv_name');

                            if($data->count() > 0){
                                return $data->first()->spv_name;
                            }
                        }
                        if($item->user_id != null){
                            $data = User::where('id', $item->user_id)
                                        ->select('users.name as spv_name')->first();
                            return $data->spv_name;
                        }else{
                            $data = SpvDemo::where('store_id', $item->id)
                                    ->join('users', 'users.id', 'spv_demos.user_id')
                                    ->select('users.name as spv_name');

                            if($data->count() > 0){
                                return $data->first()->spv_name;
                            }
                        }

                    }

                    

                }
                return "-";
                
            })
            ->addColumn('dm_name', function ($item) {
                if($item->district->area->dmAreas->first()){
                    return $item->district->area->dmAreas->first()->user->name;
                }
                return "-";
            })
            ->addColumn('trainer_name', function ($item) {
                if($item->district->area->trainerAreas->first()){
                    return $item->district->area->trainerAreas->first()->user->name;
                }
                return "-";
            })
            ->make(true);

      

    }

    public function getDataStoreWithFilters(KonfigPromoFilters $filters){

        $data = EmployeeStore::filter($filters)->get();
        return $data;

    }
}
