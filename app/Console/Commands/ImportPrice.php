<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Price;
use DB;
use App\Area;
use App\Traits\SummaryTrait;

class ImportPrice extends Command
{
    use SummaryTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:price {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Price Module';

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
        // $file_path = 'imports/price'.'/'.$this->argument('file_name');
        // $dataFile = Excel::selectSheets('Master Price')->load($file_path)->get();

        $dataFile = Excel::selectSheets('Master Price')->load($this->argument('file'))->get();

        foreach ($dataFile as $detail) {
            
            $price = Price::where('product_id', $detail['product_id'])->where('globalchannel_id', $detail['global_channel_id'])->where('sell_type', $detail['sell_type'])->first();

            if($price){ // UPDATE

                if($price->price != $detail['price']){
                    try {
                        DB::transaction(function () use ($price, $detail) {

                            $price->update(['price' => $detail['price']]);

                        });

                        /* Summary Change */
                        $summary['product_id'] = $detail['product_id'];
                        $summary['globalchannel_id'] = $detail['global_channel_id'];
                        $summary['sell_type'] = $detail['sell_type'];
                        $summary['price'] = $detail['price'];
                        $this->changeSummary($summary, 'change');

                    } catch (\Exception $e) {
                        // DO NOTHING
                    }                    
                }

            }else{ // INSERT NEW

                try {
                    DB::transaction(function () use ($price, $detail) {

                        $priceNew = Price::create([
                            'product_id' => $detail['product_id'],
                            'globalchannel_id' => $detail['global_channel_id'],
                            'sell_type' => $detail['sell_type'],
                            'price' => $detail['price'],
                        ]);

                        /* Summary Change */
                        $summary['product_id'] = $priceNew['product_id'];
                        $summary['globalchannel_id'] = $priceNew['globalchannel_id'];
                        $summary['sell_type'] = $priceNew['sell_type'];
                        $summary['price'] = $priceNew['price'];
                        $this->changeSummary($summary, 'change');

                    });
                } catch (\Exception $e) {
                    // DO NOTHING
                }  

            }

        }
    }
}
