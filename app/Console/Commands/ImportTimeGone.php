<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\TimeGone;
use DB;
use App\Area;
use App\Traits\SummaryTrait;

class ImportTimeGone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:timegone {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Timegone Module';

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
        $dataFile = Excel::selectSheets('Master Timegone')->load($this->argument('file'))->get();

        $timegones = TimeGone::where('deleted_at', null)->delete();

        foreach ($dataFile as $detail) {
            
            TimeGone::create([
                'day' => $detail['day'],
                'percent' => $detail['timegone'],
            ]);

        }
    }
}
