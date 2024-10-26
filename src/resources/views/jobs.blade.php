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
                                <button class="toggle-metrics" onclick="toggleMetrics({{ $job->id }})">
                                    Show Metrics
                                </button>
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
                        <tr class="metrics-row">
                            <td colspan="6">
                                <div id="metrics-{{ $job->id }}" class="metrics-container">
                                    <div class="metrics-grid">
                                        <div class="metric-card">
                                            <div class="metric-title">Memory Usage</div>
                                            <div class="metric-value" id="memory-{{ $job->id }}">
                                                0<span class="metric-unit">MB</span>
                                            </div>
                                        </div>
                                        <div class="metric-card">
                                            <div class="metric-title">Peak Memory</div>
                                            <div class="metric-value" id="peak-memory-{{ $job->id }}">
                                                0<span class="metric-unit">MB</span>
                                            </div>
                                        </div>
                                        <div class="metric-card">
                                            <div class="metric-title">CPU Load</div>
                                            <div class="metric-value" id="cpu-{{ $job->id }}">
                                                0<span class="metric-unit">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="chart-{{ $job->id }}"></canvas>
                                    </div>
                                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>

        let charts = {};

        function toggleMetrics(jobId) {
            const metricsContainer = document.getElementById(`metrics-${jobId}`);
            const isHidden = metricsContainer.style.display === 'none' || metricsContainer.style.display === '';
                        
            metricsContainer.style.display = isHidden ? 'block' : 'none';
            
            if (isHidden && !charts[jobId]) {
                initializeChart(jobId);
            }
        }

        function initializeChart(jobId) {
            const ctx = document.getElementById(`chart-${jobId}`).getContext('2d');
            charts[jobId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Memory Usage (MB)',
                        data: [],
                        borderColor: '#3498db',
                        tension: 0.4
                    }, {
                        label: 'CPU Load (%)',
                        data: [],
                        borderColor: '#e74c3c',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updateMetrics(jobId, memory, peakMemory, cpu) {
            if (!charts[jobId]) return;

            const memoryNum = Number(memory) || 0;
            const peakMemoryNum = Number(peakMemory) || 0;
            const cpuNum = Number(cpu) || 0;

            // Update displayed values
            document.getElementById(`memory-${jobId}`).innerHTML = 
                (memoryNum / 1024 / 1024).toFixed(2) + '<span class="metric-unit">MB</span>';
            document.getElementById(`peak-memory-${jobId}`).innerHTML = 
                (peakMemoryNum / 1024 / 1024).toFixed(2) + '<span class="metric-unit">MB</span>';
            document.getElementById(`cpu-${jobId}`).innerHTML = 
                cpuNum.toFixed(2) + '<span class="metric-unit">%</span>';

            // Update chart
            const timestamp = new Date().toLocaleTimeString();
            const chart = charts[jobId];
            
            chart.data.labels.push(timestamp);
            chart.data.datasets[0].data.push((memoryNum / 1024 / 1024).toFixed(2));
            chart.data.datasets[1].data.push(cpuNum);

            // Keep last 10 data points
            if (chart.data.labels.length > 10) {
                chart.data.labels.shift();
                chart.data.datasets.forEach(dataset => dataset.data.shift());
            }

            chart.update();
        }

        @if(count($jobs) > 0)

            setInterval(function() {
                fetchJobProgress();
                fetchJobMetrics();
            }, 5000);

            function fetchJobProgress() {
                $.ajax({
                    url: "{{ route('queues.get.job.progress') }}",
                    data: { ids: {!! json_encode($ids) !!} },
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

            function fetchJobMetrics() {
                $.ajax({
                    url: "{{ route('queues.get.job.metrics') }}",
                    data: { ids: {!! json_encode($ids) !!} },
                    method: 'GET',
                    success: function(response) {
                        response.jobs.forEach(function(job) {
                            updateMetrics(
                                job.id,
                                job.memory_usage,
                                job.peak_memory_usage,
                                job.cpu_load
                            );
                        });
                    },
                    error: function() {
                        console.error('Failed to fetch job metrics.');
                    }
                });
            }

        @endif

    </script>
@endpush
