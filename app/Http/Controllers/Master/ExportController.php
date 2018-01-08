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

    public function exportSellOut(Request $request){

        $filename = 'Philips Retail Report Sell Out ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Out');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Out Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL Out', function ($sheet) use ($data) {
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

    public function exportRetConsument(Request $request){

        $filename = 'Philips Retail Report Ret. Consument ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Consument');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Consument Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. CONSUMENT', function ($sheet) use ($data) {
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

    public function exportRetDistributor(Request $request){

        $filename = 'Philips Retail Report Ret. Distributor ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Ret. Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Ret. Distributor Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('RET. DISTRIBUTOR', function ($sheet) use ($data) {
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

    public function exportFreeProduct(Request $request){

        $filename = 'Philips Retail Report Free Product ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Free Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Free Product Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('FREE PRODUCT', function ($sheet) use ($data) {
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

    public function exportTbat(Request $request){

        $filename = 'Philips Retail Report TBAT ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report TBAT');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('TBAT Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('TBAT', function ($sheet) use ($data) {
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

    public function exportSoh(Request $request){

        $filename = 'Philips Retail Report SOH ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report SOH');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('SOH Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SOH', function ($sheet) use ($data) {
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

    public function exportSos(Request $request){

        $filename = 'Philips Retail Report SOS ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report SOS');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('SOS Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SOS', function ($sheet) use ($data) {
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

    public function exportDisplayShare(Request $request){

        $filename = 'Philips Retail Report Display Share ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Display Share');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Display Share Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Display Share', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDisplayShare($data), null, 'A1', true, true);
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

    public function exportMaintenanceRequest(Request $request){

        $filename = 'Philips Retail Report Maintenance Request ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Maintenance Request');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Maintenance Request Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Maintenance Request', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportMaintenance($data), null, 'A1', true, true);
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

    public function exportCompetitorActivity(Request $request){

        $filename = 'Philips Retail Report Competitor Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Competitor Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Competitor Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Maintenance Request', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportCompetitor($data), null, 'A1', true, true);
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
    
    public function exportPromoActivity(Request $request){

        $filename = 'Philips Retail Report Promo Activity ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Promo Activity');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Promo Activity Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Maintenance Request', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportReportPromo($data), null, 'A1', true, true);
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
    public function exportAttendanceReport(Request $request){

        $filename = 'Philips Retail Report Attendance Report ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Attendance');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Attendance Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Attendance', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAttendance($data), null, 'A1', true, true);
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
