<?php

namespace Japt\QueueManagement\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearQueue extends Command
{
    protected $signature = 'queue:clear';
    protected $description = 'Clear the specified queue.';

    public function handle()
    {
        // You can customize the queue clearing logic here
        Artisan::call('queue:flush');
        $this->info('Queue has been cleared.');
    }
}
