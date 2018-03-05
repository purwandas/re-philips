<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\SalesmanProductFocuses;
use DB;
use App\Traits\SummaryTrait;

class ImportProductFocusSalesman extends Command
{
    use SummaryTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:productfocussalesman {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Product Focus Salesman Module';

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
        $dataFile = Excel::selectSheets('Master Salesman Product Focus')->load($this->argument('file'))->get();

        $productfocuses = SalesmanProductFocuses::where('deleted_at', null)->get();

        foreach ($productfocuses as $detail) {

            /* Summary Delete */
            $summary['product_id'] = $detail['product_id'];
            $this->changeSummarySellInSalesman($summary, 'delete');

            SalesmanProductFocuses::where('id', $detail->id)->first()->delete();

        }

        foreach ($dataFile as $detail) {

            SalesmanProductFocuses::create([
                'product_id' => $detail['product_id'],
            ]);

            /* Summary Change */
            $summary['product_id'] = $detail['product_id'];
            $this->changeSummarySellInSalesman($summary, 'change');


        }
    }
}
