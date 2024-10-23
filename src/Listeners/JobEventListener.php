<?php

namespace Japt\QueueManagement\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobEventListener
{
    private $progress = 0;

    public function handleJobProcessed(JobProcessed $event)
    {
        $this->progress = 100;
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'Completed');
        DB::table('job_progress')->where('job_id', $event->job->getJobId())->delete(); // Remove job progress on completion
    }

    public function handleJobFailed(JobFailed $event)
    {
        $this->progress = 0;
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'Failed');
    }

    public function handleJobProcessing(JobProcessing $event)
    {
        $this->progress = 50;
        $jobId = $event->job->getJobId(); 
        Log::info('Job processed: ID = ' . $jobId . ', Name = ' . $event->job->getName());
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'In Progress');
    }

    protected function logJobProgress($jobId, $jobName, $status)
    {
        // Store the job progress in the job_progress table
        DB::table('job_progress')->updateOrInsert(
            ['job_id' => $jobId], // Condition for insertion
            ['job_name' => $jobName, 'status' => $status, 'progress' => $this->progress, 'created_at' => now(), 'updated_at' => now()] // Values to insert or update
        );
    }
}
