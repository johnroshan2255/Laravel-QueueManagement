<?php

use Illuminate\Support\Facades\Route;
use Japt\QueueManagement\Http\Controllers\QueueDashboardController;

Route::group(['middleware' => 'web'], function () {
    Route::get('queues/', [QueueDashboardController::class, 'index'])->name('queues.dashboard');
    Route::get('queues/queues', [QueueDashboardController::class, 'queues'])->name('queues.queues');
    Route::get('queues/jobs', [QueueDashboardController::class, 'jobs'])->name('queues.jobs');
    Route::get('failed', [QueueDashboardController::class, 'failed'])->name('queues.failed');
    Route::post('queues/run/{id}', [QueueDashboardController::class, 'runJob'])->name('queues.run');
    Route::post('queues/clear', [QueueDashboardController::class, 'clearQueue'])->name('queues.clear');
    Route::post('queues/cancel/{queue}', [QueueDashboardController::class, 'cancelQueue'])->name('queues.cancel');
    Route::post('queues/retry/{queue}', [QueueDashboardController::class, 'retryQueue'])->name('queues.retry');
    Route::post('queues/retry/job/{id}', [QueueDashboardController::class, 'retryJob'])->name('queues.retry.job');
    Route::post('queues/delete/job/{id}', [QueueDashboardController::class, 'deleteJob'])->name('queues.delete.job');
    Route::post('queues/cancel/job/{id}', [QueueDashboardController::class, 'cancelJob'])->name('queues.cancel.job');
    Route::get('queues/jobs/{id}/{type?}', [QueueDashboardController::class, 'showJobDetails'])->name('jobs.details');
    Route::get('/queues/job/progress', [QueueDashboardController::class, 'getJobProgress'])->name('queues.get.job.progress');
    Route::get('/queues/job/matrix', [QueueDashboardController::class, 'getJobMatrix'])->name('queues.get.job.metrics');


});
