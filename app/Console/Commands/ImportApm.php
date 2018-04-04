<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Apm;
use Carbon\Carbon;

class ImportApm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:apm {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import APM Module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dataFile = Excel::selectSheets('APM')->load($this->argument('file'))->get();

        $soMin6 = 'so_value_' . strtolower(Carbon::now()->subMonths(6)->format('F_Y'));
        $soMin5 = 'so_value_' . strtolower(Carbon::now()->subMonths(5)->format('F_Y'));
        $soMin4 = 'so_value_' . strtolower(Carbon::now()->subMonths(4)->format('F_Y'));
        $soMin3 = 'so_value_' . strtolower(Carbon::now()->subMonths(3)->format('F_Y'));
        $soMin2 = 'so_value_' . strtolower(Carbon::now()->subMonths(2)->format('F_Y'));
        $soMin1 = 'so_value_' . strtolower(Carbon::now()->subMonths(1)->format('F_Y'));

        foreach ($dataFile as $detail) {
            
            $apm = Apm::where('store_id', $detail['store_id'])->where('product_id', $detail['product_id'])->first();

            if($apm){

                $valueMin6 = (string)str_replace(',', '', $detail[$soMin6]);
                $valueMin5 = (string)str_replace(',', '', $detail[$soMin5]);
                $valueMin4 = (string)str_replace(',', '', $detail[$soMin4]);
                $valueMin3 = (string)str_replace(',', '', $detail[$soMin3]);
                $valueMin2 = (string)str_replace(',', '', $detail[$soMin2]);
                $valueMin1 = (string)str_replace(',', '', $detail[$soMin1]);

                $valueMin6 = (string)str_replace('.', '', $valueMin6);
                $valueMin5 = (string)str_replace('.', '', $valueMin5);
                $valueMin4 = (string)str_replace('.', '', $valueMin4);
                $valueMin3 = (string)str_replace('.', '', $valueMin3);
                $valueMin2 = (string)str_replace('.', '', $valueMin2);
                $valueMin1 = (string)str_replace('.', '', $valueMin1);

                $apm->update([
                        'month_minus_6_value' => (double)$valueMin6,
                        'month_minus_5_value' => (double)$valueMin5,
                        'month_minus_4_value' => (double)$valueMin4,
                        'month_minus_3_value' => (double)$valueMin3,
                        'month_minus_2_value' => (double)$valueMin2,
                        'month_minus_1_value' => (double)$valueMin1,
                    ]);

            }

        }
    }
}
