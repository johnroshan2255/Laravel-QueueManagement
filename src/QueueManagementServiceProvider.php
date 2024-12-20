<?php

namespace Japt\QueueManagement;

use Illuminate\Support\ServiceProvider;
use Japt\QueueManagement\Listeners\JobEventListener;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\Looping;

class QueueManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register commands
        $this->commands([
            \Japt\QueueManagement\Console\MonitorQueue::class,
            \Japt\QueueManagement\Console\ClearQueue::class,
        ]);

        // Merge the package configuration file
        $this->mergeConfigFrom(__DIR__ . '/../config/queuemanagement.php', 'queuemanagement');
    }

    public function boot()
    {
        // Register the event listeners
        $this->app['events']->listen(JobProcessed::class, [JobEventListener::class, 'handleJobProcessed']);
        $this->app['events']->listen(JobFailed::class, [JobEventListener::class, 'handleJobFailed']);
        $this->app['events']->listen(JobProcessing::class, [JobEventListener::class, 'handleJobProcessing']);
        $this->app['events']->listen(Looping::class, [JobEventListener::class, 'handleLooping']);

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'queuemanagement');

        // Register publishable resources
        $this->publishes([
            __DIR__.'/config/queuemanagement.php' => config_path('queuemanagement.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/queuemanagement'),
        ], 'views');
    }
}