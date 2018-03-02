<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;

class testImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:import {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Testing';

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
        Employee::create([
            'nik' => '123',
            'name' => $this->argument('name'),
            'email' => 'a@mail.com',
            'passowrd' => 'abcd'            
        ]);
    }
}
