<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\ProductPromos;
use DB;
use App\Traits\SummaryTrait;

class ImportProductPromo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:productpromo {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Product Promo Module';

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
        $dataFile = Excel::selectSheets('Master Product Promo Tracking')->load($this->argument('file'))->get();

        $productpromos = ProductPromos::where('deleted_at', null)->delete();

        foreach ($dataFile as $detail) {

            ProductPromos::create([
                'product_id' => $detail['product_id'],
            ]);

        }
    }
}
