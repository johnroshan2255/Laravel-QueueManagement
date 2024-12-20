<?php

namespace Japt\QueueManagement\Http\Controllers;

use Exception;
use GPBMetadata\Google\Protobuf\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QueueDashboardController extends BaseController
{
    public function index()
    {

        $queueDriver = config('queue.default');
        $queues = $failedJobs = collect();

        if($queueDriver === 'database'){
            $queues = DB::table('jobs')
            ->select('queue', DB::raw('COUNT(*) as job_count'))
            ->groupBy('queue')
            ->get();
        } elseif ($queueDriver === 'redis') {
            $queues = [];
        }
        
        if ($queueDriver !== 'sync') {
            $failedJobs = DB::table('failed_jobs')->get();
        }

        return view('queuemanagement::dashboard', compact('queues', 'failedJobs'));
    }

    public function queues()
    {

        $queueDriver = config('queue.default');
        $queues = $failedJobs = collect();

        if($queueDriver === 'database'){
            $queues = DB::table('jobs')
            ->select('queue', DB::raw('COUNT(*) as job_count'))
            ->groupBy('queue')
            ->paginate(10);
        } elseif ($queueDriver === 'redis') {
            $queues = [];
        }
        
        if ($queueDriver !== 'sync') {
            $failedJobs = DB::table('failed_jobs')->get();
        }

        return view('queuemanagement::queues', compact('queues', 'failedJobs', 'queueDriver'));
    }

    public function jobs()
    {

        $queueDriver = config('queue.default');
        $jobs = collect();

        if($queueDriver === 'database'){
            $jobs = DB::table('job_progress as jb')->leftJoin('jobs as j','j.id','jb.job_id')->select('jb.*','j.id as oj_id')->paginate(10);
            $ids = collect($jobs->items())->pluck('id')->toArray();
        } elseif ($queueDriver === 'redis') {
            $jobs = [];
        }

        return view('queuemanagement::jobs', compact('jobs', 'queueDriver', 'ids'));
    }

    public function getJobProgress(Request $request)
    {
        $jobProgress = DB::table('job_progress')->whereIn('id', $request->ids ?? [])->get();

        return response()->json(['jobs' => $jobProgress]);
    }

    public function getJobMatrix(Request $request)
    {
        $jobs = DB::table('job_progress')->whereIn('id', $request->ids ?? [])->select('id', 'memory_usage', 'peak_memory_usage', 'cpu_load')->get();
        return response()->json(['jobs' => $jobs]);
    }

    public function failed()
    {
        $queueDriver = config('queue.default');
        $failedJobs = collect();
        
        if ($queueDriver !== 'sync') {
            $failedJobs = DB::table('failed_jobs')->paginate(10);
        }

        return view('queuemanagement::failedjobs', compact('failedJobs', 'queueDriver'));
    }

    public function runJob($id)
    {
        return redirect()->back()->with('success', 'Job is running now.');
    }

    public function retryJob($id)
    {
        try{
            DB::beginTransaction();
            $job = DB::table('failed_jobs')->find($id);

            if ($job) {
                DB::table('jobs')->insert([
                    'queue' => $job->queue,
                    'payload' => $job->payload,
                    'attempts' => 0, // Reset attempts
                    'reserved_at' => null,
                    'available_at' => time(),
                    'created_at' => time()
                ]);
                DB::table('failed_jobs')->where('id', $id)->delete();
                DB::table('job_progress')->where('uuid', $job->uuid)->delete();
                DB::commit();
            }
            return redirect()->back()->with('success', 'Job retried successfully.');
        } catch(Exception $th){
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function deleteJob($id)
    {
        DB::table('job_progress')->where('job_id', $id)->delete();
        DB::table('jobs')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Job deleted successfully.');
    }

    public function cancelJob($id)
    {
        DB::table('jobs')->where('id', $id)->delete();
        DB::table('job_progress')->where('job_id', $id)->delete();
        return redirect()->back()->with('success', 'Job cancelled successfully.');
    }

    public function cancelQueue($queueName)
    {
        DB::table('jobs')->where('queue', $queueName)->delete();
        return redirect()->back()->with('success', 'All jobs in the queue "' . $queueName . '" canceled successfully.');
    }

    public function retryQueue($queueName)
    {
        $failedJobs = DB::table('failed_jobs')->where('queue', $queueName)->get();

        foreach ($failedJobs as $job) {
            DB::table('jobs')->insert([
                'queue' => $job->queue,
                'payload' => $job->payload,
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now(),
                'created_at' => now()
            ]);
            DB::table('failed_jobs')->where('id', $job->id)->delete();
        }

        return redirect()->back()->with('success', 'All jobs in the queue "' . $queueName . '" retried successfully.');
    }

    public function clearQueue()
    {
        return redirect()->back()->with('success', 'Job cancelled successfully.');
    }

    public function showJobDetails($id, $type = '')
    {
        $table = $type === 'failed' ? 'failed_jobs' : 'jobs';
        $job = DB::table($table)->where('id', $id)->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }
        $date = $type === 'failed' ? $job->failed_at : $job->created_at;
        $output = "Job ID: {$job->id}\n";
        $output .= "Queue: {$job->queue}\n";
        $output .= "Payload: \n" . json_encode(json_decode($job->payload), JSON_PRETTY_PRINT) . "\n";
        $output .= "Created At: {$date}\n";
        $output .= "Status: " . ($job->status ?? 'Pending') . "\n";

        if ($type === 'failed' && isset($job->exception)) {
            $output .= "Exception: \n" . $job->exception . "\n";
        }
        
        return response()->json(['output' => $output]);
    }
}
