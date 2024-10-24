<?php

namespace Japt\QueueManagement\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobEventListener
{
    private $progress;
    private $jobId;
    private $job_status = false;

    public function handleJobProcessed(JobProcessed $event)
    {
        $this->job_status = false;
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'Completed', 100);
        // DB::table('job_progress')->where('job_id', $event->job->getJobId())->delete();
    }

    public function handleJobFailed(JobFailed $event)
    {
        $this->job_status = false;
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'Failed', 0);
        // DB::table('job_progress')->where('job_id', $event->job->getJobId())->delete();
    }

    public function handleJobProcessing(JobProcessing $event)
    {
        $this->jobId = $event->job->getJobId();
        $this->job_status = true;
        $this->logJobProgress($event->job->getJobId(), $event->job->getName(), 'In Progress', 0);
        $this->startProgressTimer();
    }

    protected function logJobProgress($jobId, $jobName, $status, $progress = null)
    {
        $data = [
            'job_name' => $jobName,
            'status' => $status,
            'updated_at' => now()
        ];

        if ($progress !== null) {
            $data['progress'] = $progress;
        }

        DB::table('job_progress')->updateOrInsert(
            ['job_id' => $jobId],
            $data + ['created_at' => DB::raw('COALESCE(created_at, NOW())')]
        );
    }

    private function startProgressTimer()
    {
        $interval = 5;

        while ($this->job_status) {
            if ($this->progress < 100) {
                $this->progress += 5;
                $this->updateProgress($this->progress);
                sleep($interval);
            } else { return;}
        }
    }

    private function updateProgress($progress)
    {
        if ($this->jobId) {
            DB::table('job_progress')->updateOrInsert(
                ['job_id' => $this->jobId],
                [
                    'progress' => $progress,
                    'updated_at' => now()
                ]
            );
        }
    }
}