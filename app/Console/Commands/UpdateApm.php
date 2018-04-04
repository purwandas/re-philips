<?php

namespace App\Console\Commands;

use App\Apm;
use App\Reports\SummarySellOut;
use App\Store;
use App\Traits\ApmTrait;
use Illuminate\Console\Command;

class UpdateApm extends Command
{
    use ApmTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:apm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update APM for store by product from previous 3 months';

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
        $stores = Store::all();

        foreach ($stores as $store) {

            $summary = SummarySellOut::where('storeId', $store['id'])
                        ->groupBy('product_id')->select('product_id')->get();

            if($summary){

                foreach ($summary as $product){

                    $apm = Apm::where('store_id', $store['id'])->where('product_id', $product['product_id'])->first();

                    if($apm){ // UPDATE

                        $apm->update([
                            'month_minus_6_value' => $apm->month_minus_5_value,
                            'month_minus_5_value' => $apm->month_minus_4_value,
                            'month_minus_4_value' => $apm->month_minus_3_value,
                            'month_minus_3_value' => $apm->month_minus_2_value,
                            'month_minus_2_value' => $apm->month_minus_1_value,
                            'month_minus_1_value' => $this->getProductTotalCurrent($store['id'], $product['product_id']),
                        ]);

                    }else { // ADD

                        Apm::create([
                            'store_id' => $store['id'],
                            'product_id' => $product['product_id'],
                            'month_minus_6_value' => 0,
                            'month_minus_5_value' => 0,
                            'month_minus_4_value' => 0,
                            'month_minus_3_value' => 0,
                            'month_minus_2_value' => 0,
                            'month_minus_1_value' => $this->getProductTotalCurrent($store['id'], $product['product_id']),
                        ]);

                    }

//                    $this->info('Store : '.$store['id'].' - Product : '.$product['product_id']);

                }

            }

        }

        $this->info('Update APM berhasil dilakukan');
    }
}
