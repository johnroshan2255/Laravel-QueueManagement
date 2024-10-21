<?php

return [
    'default_queue' => env('QUEUE_DRIVER', 'database'),
    'monitor_interval' => env('QUEUE_MONITOR_INTERVAL', 10),
];
