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
            <h5 class="card-title">Queues</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Queue Name</th>
                            <th scope="col">Job Count</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $queue)
                            <tr>
                                <td>{{ $queue->queue }}</td>
                                <td>{{ $queue->job_count }}</td>
                                <td>
                                    <form action="{{ route('queues.cancel', $queue->queue) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm custom-btn">Cancel</button>
                                    </form>
                                    <form action="{{ route('queues.retry', $queue->queue) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm custom-btn">Retry Failed Jobs</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No active queues.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($queueDriver === 'database')
                    <div class="d-flex justify-content-center">
                        {{ $queues->links('queuemanagement::pagination.custom-pagination') }}
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
    </style>
@endsection
