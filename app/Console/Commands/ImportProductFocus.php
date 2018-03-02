<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\ProductFocuses;
use DB;
use App\Traits\SummaryTrait;

class ImportProductFocus extends Command
{
    use SummaryTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:productfocus {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Product Focus Module';

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
        $dataFile = Excel::selectSheets('Master Product Focus')->load($this->argument('file'))->get();

        $productfocuses = ProductFocuses::where('deleted_at', null)->get();

        foreach ($productfocuses as $detail) {

            /* Summary Delete */
            $summary['product_id'] = $detail['product_id'];
            $summary['type'] = $detail['type'];
            $this->changeSummary($summary, 'delete');

            ProductFocuses::where('id', $detail->id)->first()->delete();

        }

        foreach ($dataFile as $detail) {

            ProductFocuses::create([
                'product_id' => $detail['product_id'],
                'type' => $detail['type'],
            ]);

            /* Summary Change */
            $summary['product_id'] = $detail['product_id'];
            $summary['type'] = $detail['type'];
            $this->changeSummary($summary, 'change');


        }
    }
}
