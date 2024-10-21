<?php

namespace Japt\QueueManagement\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorQueue extends Command
{
    protected $signature = 'queue:monitor';
    protected $description = 'Monitor the queue and log the status.';

    public function handle()
    {
        $interval = config('queuemanagement.monitor_interval');

        while (true) {
            // Here you can implement your logic to monitor the queue
            $queueStatus = 'Monitoring queues...'; // Replace with actual status
            Log::info($queueStatus);
            $this->info($queueStatus);
            
            sleep($interval); // Wait for the specified interval
        }
    }
}
