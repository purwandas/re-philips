<?php

namespace App\Http\Controllers\Master;

use App\Filters\SellinFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Carbon\Carbon;
use App\Helper\ExcelHelper as ExcelHelper;

class ExportController extends Controller
{
    protected $excelHelper;

    public function __construct(ExcelHelper $excelHelper)
    {
        $this->excelHelper = $excelHelper;
    }

    //
    public function exportSellIn(Request $request){

        $filename = 'Philips Retail Report Sell In ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell In');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell In Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL IN', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExport($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:AB1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:AB1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }

    public function deleteExport(Request $request){

        try{

            $url = $request->data;
            File::delete(public_path() . '/' . $url);

        }catch (\Exception $exception){
            return "There is error in deleting excel";
        }

    }
}
