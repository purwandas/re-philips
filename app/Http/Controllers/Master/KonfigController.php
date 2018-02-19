<?php

namespace App\Http\Controllers\Master;

use App\Employee;
use App\EmployeeStore;
use App\Filters\KonfigPromoFilters;
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

        $data = EmployeeStore::filter($filters)->with('user.role',
                'store.district.area.region', 'store.subChannel.channel.globalChannel', 'store.classification')->get();

//        return response()->json($data);

        return Datatables::of($data)
            ->addColumn('nik', function ($item) {
                return $item->user->nik;
            })
            ->addColumn('name', function ($item) {
                return $item->user->name;
            })
            ->addColumn('grading', function ($item) {
                if($item->user->grading != null || $item->user->grading != 0 || $item->user->grading != ''){
                    return Grading::where('id', $item->user->grading_id)->first()->grading;
                }
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
                return $item->store->subChannel->channel->globalChannel->name;
            })
            ->addColumn('channel', function ($item) {
                return $item->store->subChannel->channel->name;
            })
            ->addColumn('sub_channel', function ($item) {
                return $item->store->subChannel->name;
            })
            ->addColumn('distributor_code', function ($item) {
                $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                    return $query->where('store_id', $item->store_id);
                })->first();

                if($data){
                    return $data->code;
                }
                return "";
            })
            ->addColumn('distributor_name', function ($item) {
                $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                    return $query->where('store_id', $item->store_id);
                })->first();

                if($data){
                    return $data->name;
                }
                return "";
            })
            ->addColumn('spv_name', function ($item) {
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
                return "";
            })
            ->addColumn('dm_name', function ($item) {
                $data = DmArea::where('area_id', $item->store->district->area->id)
                                ->join('users', 'users.id', 'dm_areas.user_id')
                                ->select('users.name as dm_name')->first();
                if($data){
                    return $data->dm_name;
                }
                return "";
            })
            ->addColumn('trainer_name', function ($item) {
                $data = TrainerArea::where('area_id', $item->store->district->area->id)
                                ->join('users', 'users.id', 'trainer_areas.user_id')
                                ->select('users.name as trainer_name')->first();
                if($data){
                    return $data->trainer_name;
                }
                return "";
            })
            ->make(true);

        return Datatables::of($data)
//                ->editColumn('spv_name', function ($item) {
//                    if($item->user_id != null){
//                        $data = User::where('id', $item->user_id)
//                                    ->select('users.name as spv_name')->first();
//                        return $data->spv_name;
//                    }
//                    return "";
//
//                })
//                ->addColumn('nik', function ($item) {
//                    return $item->user->nik;
//                })
//                ->addColumn('name', function ($item) {
//                    return $item->user->name;
//                })
//                ->addColumn('grading', function ($item) {
//                    if($item->grading != null || $item->grading != 0 || $item->grading != ''){
//                        return Grading::where('id', $item->user->grading_id)->first()->grading;
//                    }
//                })
//                ->addColumn('role', function ($item) {
//                    return $item->user->role->role;
//                })
//                ->addColumn('status', function ($item) {
//                    return $item->user->status;
//                })
//                ->addColumn('join_date', function ($item) {
//                    return $item->user->join_date;
//                })
//                ->addColumn('region', function ($item) {
//                    return $item->store->district->area->region->name;
//                })
//                ->addColumn('area', function ($item) {
//                    return $item->store->district->area->name;
//                })
//                ->addColumn('district', function ($item) {
//                    return $item->store->district->name;
//                })
//                ->addColumn('store_id', function ($item) {
//                    return $item->store->store_id;
//                })
//                ->addColumn('store_name_1', function ($item) {
//                    return $item->store->store_name_1;
//                })
//                ->addColumn('store_name_2', function ($item) {
//                    return $item->store->store_name_2;
//                })
//                ->addColumn('classification', function ($item) {
//                    return $item->store->classification->classification;
//                })
//                ->addColumn('global_channel', function ($item) {
//                    return $item->store->subChannel->channel->globalChannel->name;
//                })
//                ->addColumn('channel', function ($item) {
//                    return $item->store->subChannel->channel->name;
//                })
//                ->addColumn('sub_channel', function ($item) {
//                    return $item->store->subChannel->name;
//                })
                ->addColumn('distributor_code', function ($item) {
                    $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                        return $query->where('store_id', $item->store_id);
                    })->first();

                    if($data){
                        return $data->code;
                    }
                    return "";
                })
                ->addColumn('distributor_name', function ($item) {
                    $data = Distributor::whereHas('storeDistributors', function ($query) use ($item){
                        return $query->where('store_id', $item->store_id);
                    })->first();

                    if($data){
                        return $data->name;
                    }
                    return "";
                })
                ->addColumn('spv_name', function ($item) {
                    if($item->spv_id != null){
                        $data = User::where('id', $item->spv_id)
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
                    return "";
                })
                ->addColumn('dm_name', function ($item) {
                    $data = DmArea::where('area_id', $item->area_id)
                                    ->join('users', 'users.id', 'dm_areas.user_id')
                                    ->select('users.name as dm_name')->first();
                    if($data){
                        return $data->dm_name;
                    }
                    return "";
                })
                ->addColumn('trainer_name', function ($item) {
                    $data = TrainerArea::where('area_id', $item->area_id)
                                    ->join('users', 'users.id', 'trainer_areas.user_id')
                                    ->select('users.name as trainer_name')->first();
                    if($data){
                        return $data->trainer_name;
                    }
                    return "";
                })
                ->make(true);

    }

    public function getDataWithFilters(KonfigPromoFilters $filters){

        $data = EmployeeStore::filter($filters)->get();
        return $data;

    }
}
