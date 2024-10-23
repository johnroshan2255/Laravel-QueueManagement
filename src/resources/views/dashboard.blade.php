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

    <div class="row mb-4">
        <div class="col-6">
            <div class="card stats-card">
                <div class="card-body p-4">
                    <h5 class="card-title text-muted">Active Queues</h5>
                    <h2 class="mb-0">{{ count($queues) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card stats-card">
                <div class="card-body p-4">
                    <h5 class="card-title text-muted">Failed Jobs</h5>
                    <h2 class="mb-0">{{ $failedJobs->count() }}</h2>
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

        .custom-btn {
            background: #e4e7f5
        }
    </style>
@endsection
