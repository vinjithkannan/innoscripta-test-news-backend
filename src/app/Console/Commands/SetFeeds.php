<?php

namespace App\Console\Commands;

use App\Services\NewsFeederService;
use Illuminate\Console\Command;

class SetFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-feeds {feeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $feeder = $this->argument('feeder');
        $feederService = sprintf('%sApiService', $feeder);
        $newsFeederService = new NewsFeederService(new $feederService);

        $newsFeederService->feedNewses();
    }
}
