<?php

namespace Japt\QueueManagement\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\DB;

class JobEventListener
{
    public function handleJobProcessed(JobProcessed $event)
    {
        $this->logJobProgress($event->job->getJobId(), $event->job->uuid(), $event->job->getName(), 'Completed', 100);
    }

    public function handleJobFailed(JobFailed $event)
    {
        $this->logJobProgress($event->job->getJobId(), $event->job->uuid(), $event->job->getName(), 'Failed', 0);
    }

    public function handleJobProcessing(JobProcessing $event)
    {
        $this->logJobProgress($event->job->getJobId(), $event->job->uuid(), $event->job->getName(), 'In Progress', 0);
    }

    public function handleLooping(Looping $event)
    {
        \Log::info("Here");
        $jobs = DB::table('job_progress')->where('status', 'In Progress')->get();
        if ($jobs) {

            foreach ($jobs as $job) {
                $progress = min($job->progress + 5, 100);
                $this->logJobProgress(
                    $job['job_id'],
                    $job['uuid'],
                    $job['job_name'],
                    'In Progress',
                    $progress
                );

                \Log::info("Updated job {$job->job_id}: status - Still In Progress, memory - " . memory_get_usage());
            }
        }
    }

    protected function logJobProgress($jobId, $uuid, $jobName, $status, $progress = null)
    {
        $data = [
            'job_name' => $jobName,
            'uuid' => $uuid,
            'status' => $status,
            'updated_at' => now(),
            'memory_usage' => memory_get_usage(),
            'peak_memory_usage' => memory_get_peak_usage(),
            'cpu_load' => sys_getloadavg()[0]
        ];

        if ($progress !== null) {
            $data['progress'] = $progress;
        }

        DB::table('job_progress')->updateOrInsert(
            ['job_id' => $jobId],
            $data + ['created_at' => DB::raw('COALESCE(created_at, NOW())')]
        );
    }
}