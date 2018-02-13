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

        $filename = 'Philips Retail Report Sell Thru ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Sell Thru');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Thru Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL THRU', function ($sheet) use ($data) {
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

            $excel->sheet('SELL OUT', function ($sheet) use ($data) {
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

            $excel->sheet('DISPLAY SHARE', function ($sheet) use ($data) {
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

            $excel->sheet('MAINTENANCE REQUEST', function ($sheet) use ($data) {
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

            $excel->sheet('COMPETITOR ACTIVITY', function ($sheet) use ($data) {
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

            $excel->sheet('PROMO ACTIVITY', function ($sheet) use ($data) {
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

            $excel->sheet('ATTENDANCE', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AJ1');
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

    public function exportAchievementReport(Request $request){

        $filename = 'Philips Retail Report Achievement Report ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Achievement');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Achievement Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('ACHIEVEMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:BM1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportAchievement($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:BM1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:BM1', 'thin');
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

    public function exportSalesman(Request $request){

        $filename = 'Philips Retail Report Salesman Sales ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Salesman');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SALESMAN', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:AB1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesman($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }

    public function exportSalesmanAchievementReport(Request $request){

        $filename = 'Philips Retail Report Salesman Achievement Report ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;

        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Report Salesman Achievement');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Achievement Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SALESMAN ACHIEVEMENT', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:W1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanAchievement($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:W1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:W1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }

    //
    public function exportArea(Request $request){

        $filename = 'Philips Retail Master Data Area ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;
        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Area');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Area Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Area', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportArea($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }

    //
    public function exportDistrict(Request $request){

        $filename = 'Philips Retail Master Data District ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data District');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('District Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master District', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistrict($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportStore(Request $request){

        $filename = 'Philips Retail Master Data Store ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Store');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Store Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Store', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:T1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportStore($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:T1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:T1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportChannel(Request $request){

        $filename = 'Philips Retail Master Data Channel ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Channel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Channel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Channel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportChannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportSubchannel(Request $request){

        $filename = 'Philips Retail Master Data Subchannel ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Subchannel');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Subchannel Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Subchannel', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSubchannel($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportDistributor(Request $request){

        $filename = 'Philips Retail Master Data Distributor ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Distributor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Distributor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Distributor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportDistributor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportPlace(Request $request){

        $filename = 'Philips Retail Master Data Place ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Place');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Place Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Place', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:G1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPlace($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:G1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:G1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportUserPromoter(Request $request){

        $filename = 'Philips Retail Master Data User Promoter ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportUserNonPromoter(Request $request){

        $filename = 'Philips Retail Master Data User Non Promoter ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data User Non Promoter');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('User Non Promoter Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master User Non Promoter', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportUser($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportGroup(Request $request){

        $filename = 'Philips Retail Master Data Group ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroup($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportCategory(Request $request){

        $filename = 'Philips Retail Master Data Category ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Category');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Category Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Category', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportCategory($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportProduct(Request $request){

        $filename = 'Philips Retail Master Data Product ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:E1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProduct($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:E1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportPrice(Request $request){

        $filename = 'Philips Retail Master Data Price ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Price');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Price Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Price', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:E1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPrice($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:E1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportTarget(Request $request){

        $filename = 'Philips Retail Master Data Target ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:K1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:K1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportProductFocus(Request $request){

        $filename = 'Philips Retail Master Data Product Focus ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportSalesmanTarget(Request $request){

        $filename = 'Philips Retail Master Data Salesman Target ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Target');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Target Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Target', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:G1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanTarget($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:G1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:G1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportSalesmanProductFocus(Request $request){

        $filename = 'Philips Retail Master Data Salesman Product Focus ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Salesman Product Focus');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Salesman Product Focus Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Salesman Product Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:B1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportSalesmanProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:B1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:B1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportPosm(Request $request){

        $filename = 'Philips Retail Master Data POSM ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data POSM');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('POSM Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master POSM', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportPosm($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportGroupCompetitor(Request $request){

        $filename = 'Philips Retail Master Data Group Competitor ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Group Competitor');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Group Competitor Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Group Competitor', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:B1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportGroupCompetitor($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:B1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:B1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportNews(Request $request){

        $filename = 'Philips Retail Master Data News ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data News');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('News Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master News', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:J1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportNews($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:J1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:J1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportProductKnowledge(Request $request){

        $filename = 'Philips Retail Master Data Guidelines(Product Knowledge) ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Guidelines(Product Knowledge)');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Guidelines(Product Knowledge) Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Guidelines(Product Knowledge)', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:K1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportProductKnowledge($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:K1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportFaq(Request $request){

        $filename = 'Philips Retail Master Data Faq ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Faq');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Faq Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Faq', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFaq($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportQuiz(Request $request){

        $filename = 'Philips Retail Master Data Quiz ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Quiz');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Quiz Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Quiz', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportQuiz($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportFanspage(Request $request){

        $filename = 'Philips Retail Master Data Fanspage ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Fanspage');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Fanspage Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Fanspage', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:C1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportFanspage($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:C1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
    //
    public function exportMessageToAdmin(Request $request){

        $filename = 'Philips Retail Master Data Message To Admin ' . Carbon::now()->format('d-m-Y');
        $data = $request->data;


        
        Excel::create($filename, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master Data Message To Admin');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Message To Admin Master Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Master Message To Admin', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:E1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForExportMessageToAdmin($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:E1', 'thin');
            });


        })->store('xlsx', public_path('exports/excel'));

        return response()->json(['url' => 'exports/excel/'.$filename.'.xlsx', 'file' => $filename]);

    }
}
