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
            <h5 class="card-title">Faild Jobs</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Queue Name</th>
                            <th>Payload</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($failedJobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>{{ $job->queue }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($job->payload, 50) }}</td>
                            <td>{{ $job->failed_at }}</td>
                            <td>
                                <button class="btn btn-sm custom-btn" onclick="showJobDetails({{ $job->id }}, 'failed')">Show Info</button>
                                <form action="{{ route('queues.cancel.job', $job->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm custom-btn">Cancel</button>
                                </form>
                        
                                <form action="{{ route('queues.retry.job', $job->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm custom-btn">Retry</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No jobs in the queue.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $failedJobs->links('pagination::bootstrap-4') }}
                </div>
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

        .card_color {
            background: #f8f9ff
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
        
    </script>
@endpush
