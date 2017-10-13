<?php

namespace App\Http\Controllers\Auth;

use App\Posm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Region;
use App\GroupProduct;
use App\Group;
use App\Account;
use App\AccountType;
use App\GroupCompetitor;
use Auth;
use Geotools;

class OnceController extends Controller
{
    //
    public function tesGeo(){
//        $coordA   = Geotools::coordinate([106.8920396, -6.2318409]);
        $coordA   = Geotools::coordinate([-6.2318409, 106.8920396]);
//        $coordB   = Geotools::coordinate([106.8632812, -6.2623681]);
        $coordB   = Geotools::coordinate([-6.2623681, 106.8632812]);
        $distance = Geotools::distance()->setFrom($coordA)->setTo($coordB);

        return $distance->flat();
    }

    public function createAdmin(){
    	$users = DB::table('users')->count();

    	if($users == 0){
    		User::create([
    			'name' => 'REM',
            	'email' => 'rem@gmail.com',            
            	'password' => bcrypt('admin'),
                'role' => 'Master',
    		]);
    	}

    	return redirect('/');
    }

    public function createRegion(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $region = DB::table('regions')->count();

                if($region == 0){
                    Region::create(['name'=>'East']);
                    Region::create(['name'=>'Jabodetabek']);
                    Region::create(['name'=>'Java']);
                    Region::create(['name'=>'Sumatra']);
                }
            }
        }

        return redirect('/');
    }

    public function createGroupProduct(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $groupProduct = DB::table('group_products')->count();

                if($groupProduct == 0){
                    GroupProduct::create(['name'=>'DA']);
                    GroupProduct::create(['name'=>'PC']);
                    GroupProduct::create(['name'=>'MCC']);                    
                }
            }
        }

        return redirect('/');
    }

    public function createGroup(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $group = DB::table('groups')->count();

                if($group == 0){
                    Group::create(['name'=>'Beverage Appliances', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Floor Care', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Garment Care', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Kitchen Appliances', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Pain Management', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Povos', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Male Grooming', 'groupproduct_id'=>'2']);
                    Group::create(['name'=>'Beauty', 'groupproduct_id'=>'2']);
                    Group::create(['name'=>'Bottles', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Breast Pumps', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Mealtime & Cups', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Other & Accs.', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Soothers', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Teats', 'groupproduct_id'=>'3']);
                }
            }
        }

        return redirect('/');
    }

    public function createGroupCompetitor(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $groupCompetitor = DB::table('group_competitors')->count();

                if($groupCompetitor == 0){
                    GroupCompetitor::create(['name'=>'COSMOS','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'KIRIN','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'MASPION','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'MIYAKO','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'OXONE','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'PANASONIC','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'YONG MA','kategori'=>NULL, 'groupproduct_id'=>'1']);
                    GroupCompetitor::create(['name'=>'OTHERS','kategori'=>NULL, 'groupproduct_id'=>'1']);

                    GroupCompetitor::create(['name'=>'BRAUN','kategori'=>'MALE GROOMING', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'GILLETE','kategori'=>'MALE GROOMING', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'PANASONIC','kategori'=>'MALE GROOMING', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'OTHERS','kategori'=>'MALE GROOMING', 'groupproduct_id'=>'2']);

                    GroupCompetitor::create(['name'=>'GLAM PALM','kategori'=>'BEAUTY', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'PANASONIC','kategori'=>'BEAUTY', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'REPID','kategori'=>'BEAUTY', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'SHARP','kategori'=>'BEAUTY', 'groupproduct_id'=>'2']);
                    GroupCompetitor::create(['name'=>'OTHERS','kategori'=>'BEAUTY', 'groupproduct_id'=>'2']);


                    GroupCompetitor::create(['name'=>'CHICCO','kategori'=>NULL, 'groupproduct_id'=>'3']);
                    GroupCompetitor::create(['name'=>'DR.BROWN','kategori'=>NULL, 'groupproduct_id'=>'3']);
                    GroupCompetitor::create(['name'=>'MEDELA','kategori'=>NULL, 'groupproduct_id'=>'3']);
                    GroupCompetitor::create(['name'=>'PIGEON','kategori'=>NULL, 'groupproduct_id'=>'3']);
                    GroupCompetitor::create(['name'=>'OTHERS','kategori'=>NULL, 'groupproduct_id'=>'3']);
                }
            }
        }

        return redirect('/');
    }

    public function createAccountType(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $accountype = DB::table('account_types')->count();

                if($accountype == 0){
                    AccountType::create(['name'=>'Counter']);
                    AccountType::create(['name'=>'Electronic Specialist']);
                    AccountType::create(['name'=>'Hypermarket']);
                    AccountType::create(['name'=>'Traditional']);                
                }
            }
        }

        return redirect('/');
    }

    public function createAccount(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $accountype = DB::table('accounts')->count();

                if($accountype == 0){
                    Account::create(['name'=>'Central','accounttype_id'=>'1']);
                    Account::create(['name'=>'Debenhams','accounttype_id'=>'1']);
                    Account::create(['name'=>'Love','accounttype_id'=>'1']);
                    Account::create(['name'=>'Metro','accounttype_id'=>'1']);
                    Account::create(['name'=>'Seibu','accounttype_id'=>'1']);
                    Account::create(['name'=>'Sogo','accounttype_id'=>'1']);

                    Account::create(['name'=>'Aeon','accounttype_id'=>'2']);
                    Account::create(['name'=>'Best Denki','accounttype_id'=>'2']);
                    Account::create(['name'=>'Courts','accounttype_id'=>'2']);
                    Account::create(['name'=>'Electronic City','accounttype_id'=>'2']);
                    Account::create(['name'=>'Electronic Solution','accounttype_id'=>'2']);

                    Account::create(['name'=>'Carrefour','accounttype_id'=>'3']);
                    Account::create(['name'=>'Hypermart','accounttype_id'=>'3']);
                    Account::create(['name'=>'Lottemart','accounttype_id'=>'3']);
                    Account::create(['name'=>'Lulu','accounttype_id'=>'3']);

                    Account::create(['name'=>'Traditional','accounttype_id'=>'4']);
                }
            }
        }

        return redirect('/');
    }

    public function createPosm(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $posm = DB::table('posms')->count();

                if($posm == 0){
                    Posm::create(['name'=>'KITCHEN ISLAND', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'LEAFLET', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'PLINTHS', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'POSTER PROMOTION', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'SHELFTALKER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'SHELFTALKER DI VACUUM', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI JUICER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI BLENDER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI SETRIKA', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STORE SIGN', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'TOP GONDOLA', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'TV BERJALAN BAIK', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'WALL RACK/PILLAR', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'WOBBLER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'1']);

                    Posm::create(['name'=>'AQUA TANK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'CUSTOMIZED RACK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'DEMO PRODUCTS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'FEATURE CARDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'IONIC BRUSH KARTON DISPLAY', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'KERASHINE FLOOR STANDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'PLOTAINER/RACK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'POSTER PROMOTION', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'SENSOTOUCH STAND', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'ABLETOP STANDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'TOP GONDOLA', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'WALL RACK/PILLAR', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'2']);

                    Posm::create(['name'=>'2nd DISPLAY', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'CATALOGUE', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'DEMO PRODUCT', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'FLAGCHAINS', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'LEAFLET', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'POSTER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'ROTATING DISPLAY', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'SHELFTALKER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'STORE SIGN', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'TV BERJALAN BAIK', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'WOBBLER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'3']);
                }
            }
        }

        return redirect('/');
    }

    public function createMaster(){
        if(Auth::user()){
            if(Auth::user()->role == 'Master'){
                $this->createRegion();
                $this->createGroupProduct();
                $this->createGroup();
                $this->createGroupCompetitor();
                $this->createAccountType();
                $this->createAccount();
                $this->createPosm();
            }
        }  

        return redirect('/');  
    }
}
