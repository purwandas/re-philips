<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Target;
use DB;
use App\Area;
use App\Traits\TargetTrait;

class ImportTarget extends Command
{
    use TargetTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:target {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Target Module';

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
        $dataFile = Excel::selectSheets('Master Target')->load($this->argument('file'))->get();

        foreach ($dataFile as $detail) {

            $tempSellType = strtolower($detail['sell_type']);

            $sellType = 'Sell In';

            if($tempSellType == 'sell in' || $tempSellType == 'sell thru') $sellType = 'Sell In';
            if($tempSellType == 'sell out') $sellType = 'Sell Out';
            
            $target = Target::where('user_id', $detail['user_id'])->where('store_id', $detail['store_id'])->where('sell_type', $sellType)->first();

            if($detail['target_da'] == '' || $detail['target_da'] == null) $detail['target_da'] = 0;
            if($detail['target_pf_da'] == '' || $detail['target_pf_da'] == null) $detail['target_pf_da'] = 0;
            if($detail['target_pc'] == '' || $detail['target_pc'] == null) $detail['target_pc'] = 0;
            if($detail['target_pf_pc'] == '' || $detail['target_pf_pc'] == null) $detail['target_pf_pc'] = 0;
            if($detail['target_mcc'] == '' || $detail['target_mcc'] == null) $detail['target_mcc'] = 0;
            if($detail['target_pf_mcc'] == '' || $detail['target_pf_mcc'] == null) $detail['target_pf_mcc'] = 0;
            if($detail['partner'] == '' || $detail['partner'] == null) $detail['partner'] = 0;

            if($target){ // UPDATE

                $targetOldDa = $target->target_da;
                $targetOldPfDa = $target->target_pf_da;
                $targetOldPc = $target->target_pc;
                $targetOldPfPc = $target->target_pf_pc;
                $targetOldMcc = $target->target_mcc;
                $targetOldPfMcc = $target->target_pf_mcc;
                $partnerOld = $target->partner;

                if(($targetOldDa != $detail['target_da']) || ($targetOldPfDa != $detail['target_pf_da']) || ($targetOldPc != $detail['target_pc']) || ($targetOldPfPc != $detail['target_pf_pc']) || ($targetOldMcc != $detail['target_mcc']) || ($targetOldPfMcc != $detail['target_pf_mcc']) || ($partnerOld != $detail['partner'])){
                    try {

                        DB::transaction(function () use ($target, $detail, $targetOldDa, $targetOldPfDa, $targetOldPc, $targetOldPfPc, $targetOldMcc, $targetOldPfMcc, $partnerOld) {

                            $target->update([
                                'target_da' => $detail['target_da'],
                                'target_pf_da' => $detail['target_pf_da'],
                                'target_pc' => $detail['target_pc'],
                                'target_pf_pc' => $detail['target_pf_pc'],
                                'target_mcc' => $detail['target_mcc'],
                                'target_pf_mcc' => $detail['target_pf_mcc'],
                                'partner' => $detail['partner'],
                            ]);


                            /* Summary Target Add and/or Change */
                            $summary['user_id'] = $target->user_id;
                            $summary['store_id'] = $target->store_id;
                            $summary['partner'] = $target->partner;
                            $summary['partnerOld'] = $partnerOld;
                            $summary['targetOldDa'] = $targetOldDa;
                            $summary['targetOldPfDa'] = $targetOldPfDa;
                            $summary['targetOldPc'] = $targetOldPc;
                            $summary['targetOldPfPc'] = $targetOldPfPc;
                            $summary['targetOldMcc'] = $targetOldMcc;
                            $summary['targetOldPfMcc'] = $targetOldPfMcc;
                            $summary['target_da'] = $target->target_da;
                            $summary['target_pf_da'] = $target->target_pf_da;
                            $summary['target_pc'] = $target->target_pc;
                            $summary['target_pf_pc'] = $target->target_pf_pc;
                            $summary['target_mcc'] = $target->target_mcc;
                            $summary['target_pf_mcc'] = $target->target_pf_mcc;
                            $summary['sell_type'] = $target->sell_type;

                            $this->changeTarget($summary, 'change');

                        });

                    } catch (\Exception $e) {
                        // DO NOTHING
                    } 

                }

            }else{ // INSERT

                $target = Target::create([
                    'user_id' => $detail['user_id'],
                    'store_id' => $detail['store_id'],
                    'sell_type' => $sellType,
                    'partner' => $detail['partner'],
                    'target_da' => $detail['target_da'],
                    'target_pf_da' => $detail['target_pf_da'],
                    'target_pc' => $detail['target_pc'],
                    'target_pf_pc' => $detail['target_pf_pc'],
                    'target_mcc' => $detail['target_mcc'],
                    'target_pf_mcc' => $detail['target_pf_mcc'],
                ]);


                /* Summary Target Add and/or Change */ // On Progress
                $summary['user_id'] = $target->user_id;
                $summary['store_id'] = $target->store_id;
                $summary['partner'] = $target->partner;
                $summary['target_da'] = $target->target_da;
                $summary['target_pf_da'] = $target->target_pf_da;
                $summary['target_pc'] = $target->target_pc;
                $summary['target_pf_pc'] = $target->target_pf_pc;
                $summary['target_mcc'] = $target->target_mcc;
                $summary['target_pf_mcc'] = $target->target_pf_mcc;
                $summary['sell_type'] = $target->sell_type;

                $this->changeTarget($summary, 'change');

            }

        }
    }
}
