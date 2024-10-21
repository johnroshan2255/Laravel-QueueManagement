<?php

namespace Japt\QueueManagement;

use Illuminate\Support\ServiceProvider;

class QueueManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            \Japt\QueueManagement\Console\MonitorQueue::class,
            \Japt\QueueManagement\Console\ClearQueue::class,
        ]);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'queuemanagement');

        // Publish config
        $this->publishes([
            __DIR__.'/config/queuemanagement.php' => config_path('queuemanagement.php'),
        ]);
    }

    public function boot()
    {
        // Merge the package configuration file
        $this->mergeConfigFrom(__DIR__ . '/../config/queuemanagement.php', 'queuemanagement');
    }
}
