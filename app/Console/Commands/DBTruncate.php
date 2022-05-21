<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Candidate;
use App\Models\AdministrativeSuggestion;
use App\Models\Bonus;
use App\Models\DayOff;
use App\Models\DayOffRequest;
use App\Models\Fee;
use App\Models\PortRequest;
use App\Models\RefusedAdministrativeSuggestion;
use App\Models\RefusedWarning;
use App\Models\Warning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DBTruncate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        PortRequest::truncate();
        DB::table('user_enter_port_request')->truncate();

    }
}
