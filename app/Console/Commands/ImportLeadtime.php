<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Leadtime;

class ImportLeadtime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:leadtime {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Leadtime Module';

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

        $dataFile = Excel::selectSheets('Master Leadtime')->load($this->argument('file'))->get();

        $leadtime = leadtime::where('deleted_at', null)->delete();

        foreach ($dataFile as $detail) {
            
            Leadtime::create([
                'area_id' => $detail['area_id'],
                'leadtime' => $detail['leadtime'],
            ]);

        }
    }
}
