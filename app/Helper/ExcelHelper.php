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
    public function mapForExportAll(Array $data)
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
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => number_format(@$item['unit_price']),
                'VALUE' => number_format(@$item['value']),
                // 'VALUE PF MR' => number_format(@$item['value_pf_mr']),
                // 'VALUE PF TR' => number_format(@$item['value_pf_tr']),
                // 'VALUE PF PPE' => number_format(@$item['value_pf_ppe']),
                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

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
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => @$item['unit_price'],
                'VALUE' => @$item['value'],
                // 'VALUE PF MR' => @$item['value_pf_mr'],
                // 'VALUE PF TR' => @$item['value_pf_tr'],
                // 'VALUE PF PPE' => @$item['value_pf_ppe'],
                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

    public function mapForExportSales(Array $data)
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
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => @$item['unit_price'],
                'VALUE' => @$item['value'],
                'VALUE PF MR' => @$item['value_pf_mr'],
                'VALUE PF TR' => @$item['value_pf_tr'],
                'VALUE PF PPE' => @$item['value_pf_ppe'],
                'IRISAN' => (@$item['irisan'] == 0) ? '-' : (@$item['irisan'] == null) ? '-' : 'Irisan',
                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

    public function mapForExportSalesAll(Array $data)
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
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => number_format(@$item['unit_price']),
                'VALUE' => number_format(@$item['value']),
                'VALUE PF MR' => number_format(@$item['value_pf_mr']),
                'VALUE PF TR' => number_format(@$item['value_pf_tr']),
                'VALUE PF PPE' => number_format(@$item['value_pf_ppe']),
                'IRISAN' => (@$item['irisan'] == 0) ? '-' : (@$item['irisan'] == null) ? '-' : 'Irisan',
                'ROLE' => @$item['role'],
                'SPV NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }

    public function mapForExportApm(Array $data)
    {
        $collection = collect($data);

        $arMonth = [
                    Carbon::now()->subMonths(1)->format('F Y'),
                    Carbon::now()->subMonths(2)->format('F Y'),
                    Carbon::now()->subMonths(3)->format('F Y'),
                    Carbon::now()->subMonths(4)->format('F Y'),
                    Carbon::now()->subMonths(5)->format('F Y'),
                    Carbon::now()->subMonths(6)->format('F Y'),
                   ];

        return $collection->map(function ($item) use ($arMonth) {
            return [
                'NO.' => @$item['id'],
                'GLOBAL CHANNEL' => @$item['global_channel'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'REGION' => @$item['region'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'RE STORE ID' => @$item['re_store_id'],
                'STORE' => @$item['store_name'],
                'PRODUCT' => html_entity_decode(@$item['product_name']),
                'SO VALUE '.$arMonth[0] => @$item['month_minus_1_value'],
                'SO VALUE '.$arMonth[1] => @$item['month_minus_2_value'],
                'SO VALUE '.$arMonth[2] => @$item['month_minus_3_value'],
                'SO VALUE '.$arMonth[3] => @$item['month_minus_4_value'],
                'SO VALUE '.$arMonth[4] => @$item['month_minus_5_value'],
                'SO VALUE '.$arMonth[5] => @$item['month_minus_6_value'],
            ];
        });
    }

    public function mapForExportApmAll(Array $data)
    {
        $collection = collect($data);

        $arMonth = [
                    Carbon::now()->subMonths(1)->format('F Y'),
                    Carbon::now()->subMonths(2)->format('F Y'),
                    Carbon::now()->subMonths(3)->format('F Y'),
                    Carbon::now()->subMonths(4)->format('F Y'),
                    Carbon::now()->subMonths(5)->format('F Y'),
                    Carbon::now()->subMonths(6)->format('F Y'),
                   ];

        return $collection->map(function ($item) use ($arMonth) {
            return [
                'NO.' => @$item['id'],
                'GLOBAL CHANNEL' => @$item['global_channel'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'REGION' => @$item['region'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'RE STORE ID' => @$item['re_store_id'],
                'STORE' => @$item['store_name'],
                'PRODUCT' => html_entity_decode(@$item['product_name']),
                'SO VALUE '.$arMonth[0] => number_format(@$item['month_minus_1_value']),
                'SO VALUE '.$arMonth[1] => number_format(@$item['month_minus_2_value']),
                'SO VALUE '.$arMonth[2] => number_format(@$item['month_minus_3_value']),
                'SO VALUE '.$arMonth[3] => number_format(@$item['month_minus_4_value']),
                'SO VALUE '.$arMonth[4] => number_format(@$item['month_minus_5_value']),
                'SO VALUE '.$arMonth[5] => number_format(@$item['month_minus_6_value']),
            ];
        });
    }

    public function mapForExportApmTemplate(Array $data)
    {
        $collection = collect($data);

        $arMonth = [
                    Carbon::now()->subMonths(1)->format('F Y'),
                    Carbon::now()->subMonths(2)->format('F Y'),
                    Carbon::now()->subMonths(3)->format('F Y'),
                    Carbon::now()->subMonths(4)->format('F Y'),
                    Carbon::now()->subMonths(5)->format('F Y'),
                    Carbon::now()->subMonths(6)->format('F Y'),
                   ];

        return $collection->map(function ($item) use ($arMonth) {
            return [
                'NO.' => @$item['id'],
                'GLOBAL CHANNEL' => @$item['global_channel'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'REGION' => @$item['region'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'STORE ID' => @$item['store_id'],
                'RE STORE ID' => @$item['re_store_id'],
                'STORE' => @$item['store_name'],
                'PRODUCT ID' => @$item['product_id'],
                'PRODUCT' => html_entity_decode(@$item['product_name']),
                'SO VALUE '.$arMonth[0] => number_format(@$item['month_minus_1_value']),
                'SO VALUE '.$arMonth[1] => number_format(@$item['month_minus_2_value']),
                'SO VALUE '.$arMonth[2] => number_format(@$item['month_minus_3_value']),
                'SO VALUE '.$arMonth[3] => number_format(@$item['month_minus_4_value']),
                'SO VALUE '.$arMonth[4] => number_format(@$item['month_minus_5_value']),
                'SO VALUE '.$arMonth[5] => number_format(@$item['month_minus_6_value']),
            ];
        });
    }

    public function mapForExportTbat(Array $data)
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
                'STORE NAME 1 DESTINATION' => @$item['store_destination_name_1'],
                'CUSTOMER CODE DESTINATION' => @$item['store_destination_name_2'],
                'STORE ID DESTINATION' => @$item['store_destination_id'],
                'NIK' => @$item['nik'],
                'PROMOTER NAME' => @$item['promoter_name'],
                'DATE' => @$item['date'],
                'MODEL' => @$item['model'],
                'GROUP' => @$item['group'],
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
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

    public function mapForExportTbatAll(Array $data)
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
                'STORE NAME 1 DESTINATION' => @$item['store_destination_name_1'],
                'CUSTOMER CODE DESTINATION' => @$item['store_destination_name_2'],
                'STORE ID DESTINATION' => @$item['store_destination_id'],
                'NIK' => @$item['nik'],
                'PROMOTER NAME' => @$item['promoter_name'],
                'DATE' => @$item['date'],
                'MODEL' => @$item['model'],
                'GROUP' => @$item['group'],
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'QUANTITY' => @$item['quantity'],
                'UNIT PRICE' => number_format(@$item['unit_price']),
                'VALUE' => number_format(@$item['value']),
                'VALUE PF MR' => number_format(@$item['value_pf_mr']),
                'VALUE PF TR' => number_format(@$item['value_pf_tr']),
                'VALUE PF PPE' => number_format(@$item['value_pf_ppe']),            
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

                'CATEGORY' => html_entity_decode(@$item['category']),

                // 'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
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
                'CATEGORY' => html_entity_decode(@$item['category']),
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
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'PRODUCT MODEL' => @$item['product_model'],
                'PRODUCT VARIANTS' => @$item['product_variants'],
                'PHOTO' => @$item['photo2'],
                'DATE' => @$item['date'],
            ];
        });
    }

    public function mapForExportReportPosm(Array $data)
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
                'POSM NAME' => @$item['posm_name'],
                'GROUP' => @$item['group_product'],
                'QUANTITY' => @$item['quantity'],
                'PHOTO' => @$item['photo'],
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
                'CATEGORY' => html_entity_decode(@$item['category']),
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
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

    public function mapForExportSalesmanAchievement(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'NIK'=> @$item['nik'],
                'SALESMAN NAME'=> @$item['salesman_name'],
                'AREA'=> @$item['area'],

                'TARGET CALL'=> @$item['target_call'],
                'ACTUAL CALL'=> @$item['actual_call'],
                'TARGET ACTIVE OUTLET'=> @$item['target_active_outlet'],
                'ACTUAL ACTIVE OUTLET'=> @$item['actual_active_outlet'],
                'TARGET EFFECTIVE CALL'=> @$item['target_effective_call'],
                'ACTUAL EFFECTIVE CALL'=> @$item['actual_effective_call'],
                'TARGET SALES'=> @$item['target_sales'],
                'ACTUAL SALES'=> @$item['actual_sales'],
                'TARGET SALES PF'=> @$item['target_sales_pf'],
                'ACTUAL SALES PF'=> @$item['actual_sales_pf'],

                'SUM NATIONAL TARGET CALL'=> @$item['sum_national_target_call'],
                'SUM NATIONAL ACTUAL CALL'=> @$item['sum_national_actual_call'],
                'SUM NATIONAL TARGET ACTIVE OUTLET'=> @$item['sum_national_target_active_outlet'],
                'SUM NATIONAL ACTUAL ACTIVE OUTLET'=> @$item['sum_national_actual_active_outlet'],
                'SUM NATIONAL TARGET EFFECTIVE CALL'=> @$item['sum_national_target_effective_call'],
                'SUM NATIONAL ACTUAL EFFECTIVE CALL'=> @$item['sum_national_actual_effective_call'],
                'SUM NATIONAL TARGET SALES'=> @$item['sum_national_target_sales'],
                'SUM NATIONAL ACTUAL SALES'=> @$item['sum_national_actual_sales'],
                'SUM NATIONAL TARGET SALES PF'=> @$item['sum_national_target_sales_pf'],
                'SUM NATIONAL ACTUAL SALES PF'=> @$item['sum_national_actual_sales_pf'],

            ];
        });
    }

    public function mapForExportArea(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'REGION' => @$item['region_name'],
            ];
        });
    }

    public function mapForExportDistrict(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'AREA' => @$item['area_name'],
                'REGION' => @$item['region_name'],
            ];
        });
    }

    public function mapForExportStore(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'STORE ID' => @$item['store_id'],
                'STORE NAME 1' => html_entity_decode(@$item['store_name_1']),
                'CUSTOMER CODE' => html_entity_decode(@$item['store_name_2']),
                'DEDICATE' => @$item['dedicate'],
                'LONGITUDE' => @$item['longitude'],
                'LATITUDE' => @$item['latitude'],
                'GLOBAL CHANNEL' => @$item['globalchannel_name'],
                'CHANNEL' => @$item['channel_name'],
                'SUB CHANNEL' => @$item['subchannel_name'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'DISTRICT' => @$item['district_name'],
                'SPV NAME' => @$item['spv_name'],
                'ADDRESS' => @$item['address'],
                'CLASSIFICATION' => @$item['classification_id'],
                'NO TELPON TOKO' => @$item['no_telp_toko'],
                'NO TELPON PEMILIK TOKO' => @$item['no_telp_pemilik_toko'],
                'KEPEMILIK TOKO' => @$item['kepemilikan_toko'],
                'KONDISI TOKO' => @$item['kondisi_toko'],
                'LOKASI TOKO' => @$item['lokasi_toko'],
                'TIPE TANSAKSI PEMBAYARAN' => @$item['tipe_transaksi'],
                'TIPE TANSAKSI PEMBELIAN' => @$item['tipe_transaksi_2'],
            ];
        });
    }
    public function mapForExportStoreAll(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'STORE ID' => @$item['store_id'],
                'STORE NAME 1' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'DEDICATE' => @$item['dedicate'],
                'LONGITUDE' => @$item['longitude'],
                'LATITUDE' => @$item['latitude'],
                'GLOBAL CHANNEL' => @$item['globalchannel_name'],
                'CHANNEL' => @$item['channel_name'],
                'SUB CHANNEL' => @$item['subchannel_name'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'DISTRICT' => @$item['district_name'],
                'SPV NAME' => (@$item['spv_name']) ? (@$item['spv_name']) : (@$item['spv_demo']),
                'ADDRESS' => @$item['address'],
                'CLASSIFICATION' => @$item['classification_id'],
                'NO TELPON TOKO' => @$item['no_telp_toko'],
                'NO TELPON PEMILIK TOKO' => @$item['no_telp_pemilik_toko'],
                'KEPEMILIK TOKO' => @$item['kepemilikan_toko'],
                'KONDISI TOKO' => @$item['kondisi_toko'],
                'LOKASI TOKO' => @$item['lokasi_toko'],
                'TIPE TANSAKSI PEMBAYARAN' => @$item['tipe_transaksi'],
                'TIPE TANSAKSI PEMBELIAN' => @$item['tipe_transaksi_2'],
            ];
        });
    }
    public function mapForExportChannel(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'GLOBAL CHANNEL' => @$item['globalchannel_name'],
            ];
        });
    }
    public function mapForExportSubchannel(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'CHANNEL' => @$item['channel_name'],
                'GLOBAL CHANNEL' => @$item['globalchannel_name'],
            ];
        });
    }
    public function mapForExportDistributor(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'CODE' => @$item['code'],
                'NAME' => @$item['name'],
            ];
        });
    }

    public function mapForExportPlace(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'STORE ID' => @$item['store_id'],
                'NAME' => @$item['name'],
                'LONGITUDE' => @$item['longitude'],
                'LATITUDE' => @$item['latitude'],
                'ADDRESS' => @$item['address'],
                'DESCRIPTION' => @$item['description'],
            ];
        });
    }

    public function mapForExportLeadtime(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'AREA ID' => @$item['area_id'],
                'AREA' => @$item['area_name'],
                'LEADTIME' => @$item['leadtime'],
            ];
        });
    }

    public function mapForExportTimeGone(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'DAY' => @$item['day'],
                'TIMEGONE' => @$item['percent'],                
            ];
        });
    }

    public function mapForExportUser(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NIK' => @$item['nik'],
                'NAME' => @$item['name'],
                'EMAIL' => @$item['email'],
                'ROLE' => @$item['role'],
                'STATUS' => @$item['status'],
                'PHOTO' => @$item['photo'],
                'JOIN DATE' => @$item['join_date'],
                'GRADING' => @$item['grading'],
                'CERTIFICATE' => @$item['certificate'],
            ];
        });
    }
    public function mapForExportGroup(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'GROUP PRODUCT' => @$item['groupproduct_name'],
            ];
        });
    }
    public function mapForExportCategory(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'GROUP' => @$item['group_name'],
                'GROUP PRODUCT' => @$item['groupproduct_name'],
            ];
        });
    }
    public function mapForExportProduct(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],                
                'MODEL' => @$item['product_model'],
                'NAME' => @$item['name'],
                'CATEGORY' => @$item['category_name'],
                'GROUP' => html_entity_decode(@$item['group_name']),
                'GROUP PRODUCT' => @$item['groupproduct_name'],
            ];
        });
    }
    public function mapForExportProductTemplate(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],  
                'NAME' => @$item['name'],              
                'MODEL' => @$item['product_model'],                
                'CATEGORY' => @$item['category_name'],
                'GROUP' => @$item['group_name'],
                'GROUP PRODUCT' => @$item['groupproduct_name'],
            ];
        });
    }
    public function mapForExportPrice(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'PRODUCT ID' => @$item['product_id'],
                'GLOBAL CHANNEL ID' => @$item['globalchannel_id'],
                'MODEL' => @$item['product_model'],
                'NAME' => html_entity_decode(@$item['product_name']),
                'GLOBAL CHANNEL' => @$item['globalchannel_name'],
                'SELL TYPE' => @$item['sell_type'],
                'PRICE' => @$item['price'],
            ];
        });
    }
    public function mapForExportTarget(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            if(@$item['sell_type'] == 'Sell In') @$item['sell_type'] = 'Sell Thru';
            return [
                'ID' => @$item['id'],
                'USER ID' => @$item['user_id'],
                'PROMOTER' => @$item['promoter_name'],
                'STORE ID' => @$item['store_id'],
                'STORE NAME' => @$item['store_name_1'],
                'SELL TYPE' => @$item['sell_type'],
                'PARTNER' => @$item['partner'],
                'TARGET DA'=> @$item['target_da'],
                'TARGET PF DA'=> @$item['target_pf_da'],
                'TARGET PC'=> @$item['target_pc'],
                'TARGET PF PC'=> @$item['target_pf_pc'],
                'TARGET MCC'=> @$item['target_mcc'],
                'TARGET PF MCC'=> @$item['target_pf_mcc'],
            ];
        });
    }
    public function mapForExportProductFocus(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'PRODUCT ID' => @$item['product_id'],
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
                'TYPE' => @$item['type'],
            ];
        });
    }
    public function mapForExportProductPromo(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'PRODUCT ID' => @$item['product_id'],
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
            ];
        });
    }
    public function mapForExportSalesmanTarget(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'USER ID' => @$item['user_id'],
                'SALESMAN' => @$item['salesman_name'],
                'TARGET CALL' => @$item['target_call'],
                'TARGET ACTIVE OUTLET'=> @$item['target_active_outlet'],
                'TARGET EFFECTIVE CALL'=> @$item['target_effective_call'],
                'TARGET SALES'=> @$item['target_sales'],
                'TARGET SALES PF'=> @$item['target_sales_pf'],
            ];
        });
    }
    public function mapForExportSalesmanProductFocus(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'PRODUCT ID' => @$item['product_id'],
                'PRODUCT NAME' => html_entity_decode(@$item['product_name']),
            ];
        });
    }
    public function mapForExportPosm(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'GROUP ID' => @$item['group_id'],
                'GROUP NAME' => @$item['group_name'],
                'POSM' => @$item['name'],
            ];
        });
    }
    public function mapForExportGroupCompetitor(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'GROUP ID' => @$item['group_id'],
                'GROUP NAME' => @$item['group_name'],
                'GROUP COMPETITOR' => @$item['name'],
            ];
        });
    }
    public function mapForExportNews(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'USER ID' => @$item['user_id'],
                'NAME' => @$item['name'],
                'FROM' => @$item['from'],
                'SUBJECT' => @$item['subject'],
                'DATE' => @$item['date'],
                'FIlENAME' => @$item['filename'],
                'CONTENT' => @$item['content'],
                'TARGET TYPE' => @$item['target_type'],
                'TARGET DETAIL' => @$item['target_detail'],
            ];
        });
    }
    public function mapForExportProductKnowledge(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'USER ID' => @$item['user_id'],
                'TYPE' => @$item['type'],
                'FROM' => @$item['from'],
                'SUBJECT' => @$item['subject'],
                'DATE' => @$item['date'],
                'FIlENAME' => @$item['filename'],
                'FILE' => @$item['file'],
                'TARGET TYPE' => @$item['target_type'],
                'TARGET DETAIL' => @$item['target_detail'],
                'TOTAL READ' => @$item['total_read'],
            ];
        });
    }
    public function mapForExportFaq(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'QUESTION' => @$item['question'],
                'ANSWER' => @$item['answer'],
            ];
        });
    }
    public function mapForExportQuiz(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'TITLE' => @$item['title'],
                'DESCRIPTION' => @$item['description'],
                'LINK' => @$item['link'],
                'DATE' => @$item['date'],
            ];
        });
    }
    public function mapForExportFanspage(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'NAME' => @$item['name'],
                'URL' => @$item['url'],
            ];
        });
    }
    public function mapForExportMessageToAdmin(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'ID' => @$item['id'],
                'USER ID' => @$item['user_id'],
                'SUBJECT' => @$item['subject'],
                'MESSAGE' => @$item['message'],
                'DATE' => @$item['date'],
            ];
        });
    }
    public function mapForExportKonfigPromoter(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'NIK' => @$item['nik'],
                'NAME' => @$item['name'],
                'GRADING' => @$item['grading'],
                'ROLE' => @$item['role'],
                'STATUS' => @$item['status'],
                'JOIN DATE' => @$item['join_date'],
                'REGION' => @$item['region'],
                'AREA' => @$item['area'],
                'DISTRICT' => @$item['district'],
                'STORE ID' => @$item['store_id_gen'],
                'STORE NAME' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'CLASSIFICATION' => @$item['classification'],
                'GLOBAL CHANNEL' => @$item['global_channel'],
                'CHANNEL' => @$item['channel'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'DISTRIBUTOR CODE' => @$item['distributor_code'],
                'DISTRIBUTOR NAME' => @$item['distributor_name'],
                'SUPERVISOR NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }
    public function mapForExportKonfigStore(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'STORE ID' => @$item['store_id_gen'],
                'STORE NAME' => @$item['store_name_1'],
                'CUSTOMER CODE' => @$item['store_name_2'],
                'CLASSIFICATION' => @$item['classification'],
                'DISTRICT' => @$item['district'],                
                'AREA' => @$item['area'],
                'REGION' => @$item['region'],
                'SUB CHANNEL' => @$item['sub_channel'],
                'CHANNEL' => @$item['channel'],
                'GLOBAL CHANNEL' => @$item['global_channel'],            
                'DISTRIBUTOR CODE' => @$item['distributor_code'],
                'DISTRIBUTOR NAME' => @$item['distributor_name'],
                'NIK' => @$item['nik'],
                'NAME' => @$item['name'],
                'GRADING' => @$item['grading'],
                'ROLE' => @$item['role'],
                'STATUS' => @$item['status'],
                'JOIN DATE' => @$item['join_date'],
                'SUPERVISOR NAME' => @$item['spv_name'],
                'DM NAME' => @$item['dm_name'],
                'TRAINER NAME' => @$item['trainer_name'],
            ];
        });
    }
}