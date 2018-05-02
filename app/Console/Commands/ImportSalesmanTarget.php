<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\SalesmanTarget;
use DB;
use App\Area;
use App\Traits\TargetTrait;

class ImportSalesmanTarget extends Command
{
    use TargetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:targetsalesman {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Salesman Target Module';

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
        $dataFile = Excel::selectSheets('Master Salesman Target')->load($this->argument('file'))->get();


        foreach ($dataFile as $detail) {
            
            $target = SalesmanTarget::where('user_id', $detail['user_id'])->first();

            if($detail['target_call'] == '' || $detail['target_call'] == null) $detail['target_call'] = 0;
            if($detail['target_active_outlet'] == '' || $detail['target_active_outlet'] == null) $detail['target_active_outlet'] = 0;
            if($detail['target_effective_call'] == '' || $detail['target_effective_call'] == null) $detail['target_effective_call'] = 0;
            if($detail['target_sales'] == '' || $detail['target_sales'] == null) $detail['target_sales'] = 0;
            if($detail['target_sales_pf'] == '' || $detail['target_sales_pf'] == null) $detail['target_sales_pf'] = 0;

            if($target){ // UPDATE

                $targetOldCall = $target->target_call;
                $targetOldActiveOutlet = $target->target_active_outlet;
                $targetOldEffectiveCall = $target->target_effective_call;
                $targetOldSales = $target->target_sales;
                $targetOldSalesPf = $target->target_sales_pf;

                if(($targetOldCall != $detail['target_call']) || ($targetOldActiveOutlet != $detail['target_active_outlet']) || ($targetOldEffectiveCall != $detail['target_effective_call']) || ($targetOldSales != $detail['target_sales']) || ($targetOldSalesPf != $detail['target_sales_pf'])){
                    try {

                        DB::transaction(function () use ($target, $detail, $targetOldCall, $targetOldActiveOutlet, $targetOldEffectiveCall, $targetOldSales, $targetOldSalesPf) {

                            $target->update([
                                'target_call' => $detail['target_call'],
                                'target_active_outlet' => $detail['target_active_outlet'],
                                'target_effective_call' => $detail['target_effective_call'],
                                'target_sales' => $detail['target_sales'],
                                'target_sales_pf' => $detail['target_sales_pf'],
                            ]);


                            /* Summary Target Add and/or Change */
                            $summary['user_id'] = $target->user_id;
                            $summary['targetOldCall'] = $targetOldCall;
                            $summary['targetOldActiveOutlet'] = $targetOldActiveOutlet;
                            $summary['targetOldEffectiveCall'] = $targetOldEffectiveCall;
                            $summary['targetOldSales'] = $targetOldSales;
                            $summary['targetOldSalesPf'] = $targetOldSalesPf;
                            $summary['target_call'] = $target->target_call;
                            $summary['target_active_outlet'] = $target->target_active_outlet;
                            $summary['target_effective_call'] = $target->target_effective_call;
                            $summary['target_sales'] = $target->target_sales;
                            $summary['target_sales_pf'] = $target->target_sales_pf;

                            $this->changeTargetSalesman($summary, 'change');

                        });

                    } catch (\Exception $e) {
                        // DO NOTHING
                    } 

                }

            }else{ // INSERT

                $target = SalesmanTarget::create([
                    'user_id' => $detail['user_id'],
                    'target_call' => $detail['target_call'],
                    'target_active_outlet' => $detail['target_active_outlet'],
                    'target_effective_call' => $detail['target_effective_call'],
                    'target_sales' => $detail['target_sales'],
                    'target_sales_pf' => $detail['target_sales_pf'],                    
                ]);

                /* Summary Target Add and/or Change */ // On Progress
                $summary['user_id'] = $target->user_id;
                $summary['target_call'] = $target->target_call;
                $summary['target_active_outlet'] = $target->target_active_outlet;
                $summary['target_effective_call'] = $target->target_effective_call;
                $summary['target_sales'] = $target->target_sales;
                $summary['target_sales_pf'] = $target->target_sales_pf;

                $this->changeTargetSalesman($summary, 'change');

            }

        }
    }
}
