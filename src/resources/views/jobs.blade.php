@extends('queuemanagement::layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body card_color">
            <h5 class="card-title">Jobs</h5>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Job Name</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                        <tr>
                            <td>{{ $job->job_id }}</td>
                            <td>{{ $job->job_name }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                            id="progress-bar-{{ $job->id }}" 
                                            style="width: {{ $job->progress ?? 0 }}%;" 
                                            aria-valuenow="{{ $job->progress ?? 0 }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        {{ $job->progress ?? 0 }}%
                                    </div>
                                </div>
                            </td>
                            <td>{{ $job->status }}</td>
                            <td>{{ $job->created_at }}</td>
                            <td>
                                @if(isset($job->oj_id))
                                    <button class="btn btn-sm custom-btn" onclick="showJobDetails({{ $job->oj_id }})">Show Info</button>
                                    <form action="{{ route('queues.cancel.job', $job->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm custom-btn">Cancel</button>
                                    </form>
                                @endif
                                <form action="{{ route('queues.delete.job', $job->job_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm custom-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No jobs in the queue.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                
                @if($queueDriver === 'database')
                    <div class="d-flex justify-content-center">
                        {{ $jobs->links('queuemanagement::pagination.custom-pagination') }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>

    <!-- Add more content or components as needed -->



    <style>
        :root {
            --card-bg: #EEF2FF;
        }

        .stats-card {
            background: var(--card-bg);
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stats-card h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2b2b2b;
        }

        /* Table styling */
        table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        th {
            font-weight: 600;
            background-color: #f8f9ff !important;
            color: #495057;
            text-align: center;
        }

        td {
            text-align: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .custom-btn {
            background: #e4e7f5
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .close {
            margin-left: auto;
            border: none;
            background: #ffffff00
        }
    </style>
@endsection

@push('scripts')
    <script>

        @if(count($jobs) > 0)

            setInterval(function() {
                fetchJobProgress();
            }, 5000);

            function fetchJobProgress() {
                $.ajax({
                    url: "{{ route('queues.get.job.progress') }}",  // Define a route to get job progress
                    method: 'GET',
                    success: function(response) {
                        // Loop through each job and update the progress bar
                        response.jobs.forEach(function(job) {
                            let progressBar = document.getElementById('progress-bar-' + job.id);
                            progressBar.style.width = job.progress + '%';
                            progressBar.setAttribute('aria-valuenow', job.progress);
                            progressBar.textContent = job.progress + '%';
                        });
                    },
                    error: function() {
                        console.error('Failed to fetch job progress.');
                    }
                });
            }

        @endif

    </script>
@endpush
