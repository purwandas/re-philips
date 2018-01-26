<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ExcelHelper
{
    /**
     * ExcelHelper constructor.
     */
    public function __construct()
    {

    }


    /**
     * Merubah format data ajax menjadi hasil yang di download di excel
     *
     */
    public function mapForExport(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'WEEK' => @$item['week'],
                'DISTRIBUTOR CODE' => @$item['distributor_code'],
                'DISTRIBUTOR NAME' => @$item['distributor_name'],
                'REGION' => @$item['region'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['store_id'],
                'NIK' => @$item['nik'],
                'PROMOTER NAME' => @$item['promoter_name'],
                'DATE' => @$item['date'],
                'MODEL' => @$item['model'],
                'GROUP' => @$item['group'],
                'CATEGORY' => @$item['category'],
                'PRODUCT NAME' => @$item['product_name'],
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => @$item['unit_price'],
                'VALUE' => @$item['value'],
                'VALUE PF MR' => @$item['value_pf_mr'],
                'VALUE PF TR' => @$item['value_pf_tr'],
                'VALUE PF PPE' => @$item['value_pf_ppe'],
                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

    public function mapForExportDisplayShare(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'WEEK' => @$item['week'],
                'DISTRIBUTOR CODE' => @$item['distributor_code'],
                'DISTRIBUTOR NAME' => @$item['distributor_name'],
                'REGION' => @$item['region'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['store_id'],
                'NIK' => @$item['nik'],
                'PROMOTER NAME' => @$item['promoter_name'],
                'DATE' => @$item['date'],
                
                // 'MODEL' => @$item['model'],
                // 'GROUP' => @$item['group'],

                'CATEGORY' => @$item['category'],

                // 'PRODUCT NAME' => @$item['product_name'],
                // 'QUANTITY' => @$item['quantity'],
                // 'UNIT PRICE' => @$item['unit_price'],
                // 'VALUE' => @$item['value'],
                // 'VALUE PF MR' => @$item['value_pf_mr'],
                // 'VALUE PF TR' => @$item['value_pf_tr'],
                // 'VALUE PF PPE' => @$item['value_pf_ppe'],

                'PHILIPS' => @$item['philips'],
                'ALL' => @$item['all'],
                'PERCENTAGE' => @$item['percentage'].'%',

                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

    public function mapForExportReportMaintenance(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'USER' => @$item['user_name'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'DISTRICT' => @$item['district_name'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['storeid'],
                'CATEGORY' => @$item['category'],
                'CHANNEL' => @$item['channel'],
                'TYPE' => @$item['type'],
                'REPORT' => @$item['report'],
                'PHOTO' => @$item['photo2'],
                'DATE' => @$item['date'],
            ];
        });
    }

    public function mapForExportReportCompetitor(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'USER' => @$item['user_name'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'DISTRICT' => @$item['district_name'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['storeid'],
                'WEEK' => @$item['week'],
                'SKU' => @$item['sku'],
                'GROUP COMPETITOR' => @$item['group_competitor'],
                'PROMO TYPE' => @$item['promo_type'],
                'INFORMATION' => @$item['information'],
                'START PERIOD' => @$item['start_period'],
                'END PERIOD' => @$item['end_period'],
                'PHOTO' => @$item['photo2'],
                'DATE' => @$item['date'],
            ];
        });
    }

    public function mapForExportReportPromo(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'USER' => @$item['user_name'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'DISTRICT' => @$item['district_name'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['storeid'],
                'WEEK' => @$item['week'],
                'PROMO TYPE' => @$item['promo_type'],
                'INFORMATION' => @$item['information'],
                'START PERIOD' => @$item['start_period'],
                'END PERIOD' => @$item['end_period'],
                'PRODUCT NAME' => @$item['product_name'],
                'PRODUCT MODEL' => @$item['product_model'],
                'PRODUCT VARIANTS' => @$item['product_variants'],
                'PHOTO' => @$item['photo2'],
                'DATE' => @$item['date'],
            ];
        });
    }

    public function mapForExportAttendance(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            $array = [
                'USER NAME' => @$item['user_name'],
                'USER NIK' => @$item['user_nik'],
                'USER ROLE' => @$item['user_role'],
                'ATTENDANCE' => @$item['total_hk'],
                'HK TOTAL' => 26,
            ];

            $status = explode(',', @$item['attendance_detail_excell']);
            foreach ( $status as $key => $value) {
                $array[$key+1] = $value;
            }

            return $array;
        });
    }


    public function mapForExportSalesman(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [

                'WEEK' => @$item['week'],
                'DISTRIBUTOR CODE' => @$item['distributor_code'],
                'DISTRIBUTOR NAME' => @$item['distributor_name'],
                'REGION' => @$item['region'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'STORE ID' => @$item['store_id'],
                'NIK' => @$item['nik'],
                'PROMOTER NAME' => @$item['promoter_name'],
                'DATE' => @$item['date'],
                'MODEL' => @$item['model'],
                'GROUP' => @$item['group'],
                'CATEGORY' => @$item['category'],
                'PRODUCT NAME' => @$item['product_name'],
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => @$item['unit_price'],
                'VALUE' => @$item['value'],
                'VALUE PF' => @$item['value_pf'],
                'ROLE' => @$item['role'],
            ];
        });
    }


    public function mapForExportAchievement(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'REGION'=> @$item['region'], 
                'AREA'=> @$item['area'], 
                'DISTRICT'=> @$item['district'], 
                'NIK'=> @$item['nik'], 
                'PROMOTER NAME'=> @$item['promoter_name'], 
                'ACCOUNT TYPE'=> @$item['account_type'], 
                'TITLE OF PROMOTER'=> @$item['title_of_promoter'], 
                'CLASSIFICATION STORE'=> @$item['classification_store'], 
                'ACCOUNT'=> @$item['account'], 
                'STORE ID'=> @$item['store_id'], 
                'STORE NAME 1'=> @$item['store_name_1'], 
                'CUSTOMER CODE'=> @$item['store_name_2'], 
                'SPV NAME'=> @$item['spv_name'], 
                'TRAINER'=> @$item['trainer'], 
                'SELL TYPE'=> @$item['sell_type'], 

                'TARGET DAPC'=> @$item['target_dapc'], 
                'ACTUAL DAPC'=> @$item['actual_dapc'], 
                'TARGET DA'=> @$item['target_da'], 
                'ACTUAL DA'=> @$item['actual_da'], 
                'TARGET PC'=> @$item['target_pc'], 
                'ACTUAL PC'=> @$item['actual_pc'], 
                'TARGET MCC'=> @$item['target_mcc'], 
                'ACTUAL MCC'=> @$item['actual_mcc'], 
                'TARGET PF DA'=> @$item['target_pf_da'], 
                'ACTUAL PF DA'=> @$item['actual_pf_da'], 
                'TARGET PF PC'=> @$item['target_pf_pc'], 
                'ACTUAL PF PC'=> @$item['actual_pf_pc'], 
                'TARGET PF MCC'=> @$item['target_pf_mcc'], 
                'ACTUAL PF MCC'=> @$item['actual_pf_mcc'], 

                'TARGET DA W1'=> @$item['target_da_w1'], 
                'ACTUAL DA W1'=> @$item['actual_da_w1'], 
                'TARGET DA W2'=> @$item['target_da_w2'], 
                'ACTUAL DA W2'=> @$item['actual_da_w2'], 
                'TARGET DA W3'=> @$item['target_da_w3'], 
                'ACTUAL DA W3'=> @$item['actual_da_w3'], 
                'TARGET DA W4'=> @$item['target_da_w4'], 
                'ACTUAL DA W4'=> @$item['actual_da_w4'], 
                'TARGET DA W5'=> @$item['target_da_w5'], 
                'ACTUAL DA W5'=> @$item['actual_da_w5'], 
                'TARGET PC W1'=> @$item['target_pc_w1'], 
                'ACTUAL PC W1'=> @$item['actual_pc_w1'], 
                'TARGET PC W2'=> @$item['target_pc_w2'], 
                'ACTUAL PC W2'=> @$item['actual_pc_w2'], 
                'TARGET PC W3'=> @$item['target_pc_w3'], 
                'ACTUAL PC W3'=> @$item['actual_pc_w3'], 
                'TARGET PC W4'=> @$item['target_pc_w4'], 
                'ACTUAL PC W4'=> @$item['actual_pc_w4'], 
                'TARGET PC W5'=> @$item['target_pc_w5'], 
                'ACTUAL PC W5'=> @$item['actual_pc_w5'], 
                'TARGET MCC W1'=> @$item['target_mcc_w1'], 
                'ACTUAL MCC W1'=> @$item['actual_mcc_w1'], 
                'TARGET MCC W2'=> @$item['target_mcc_w2'], 
                'ACTUAL MCC W2'=> @$item['actual_mcc_w2'], 
                'TARGET MCC W3'=> @$item['target_mcc_w3'], 
                'ACTUAL MCC W3'=> @$item['actual_mcc_w3'], 
                'TARGET MCC W4'=> @$item['target_mcc_w4'], 
                'ACTUAL MCC W4'=> @$item['actual_mcc_w4'], 
                'TARGET MCC W5'=> @$item['target_mcc_w5'], 
                'ACTUAL MCC W5'=> @$item['actual_mcc_w5'], 
                        
                'SUM TARGET STORE'=> @$item['sum_target_store'], 
                'SUM ACTUAL STORE'=> @$item['sum_actual_store'], 
                'SUM TARGET AREA'=> @$item['sum_target_area'], 
                'SUM ACTUAL AREA'=> @$item['sum_actual_area'], 
                'SUM TARGET REGION'=> @$item['sum_target_region'], 
                'SUM ACTUAL REGION'=> @$item['sum_actual_region'], 

            ];
        });
    }
}