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
                'STORE NAME 2' => @$item['store_name_2'],
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

    public function mapForExportReportMaintenance(Array $data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'USER' => @$item['user_name'],
                'REGION' => @$item['region_name'],
                'AREA' => @$item['area_name'],
                'STORE NAME 1' => @$item['store_name_1'],
                'STORE NAME 2' => @$item['store_name_2'],
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

}