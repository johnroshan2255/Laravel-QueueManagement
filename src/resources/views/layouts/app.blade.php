<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Management Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4361ee;
            --sidebar-bg: #ffffff;
            --active-bg: #EEF2FF;
            --text-color: #64748b;
        }
        
        body {
            background-color: #f9fafb;
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: var(--sidebar-bg);
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            padding: 1rem;
            border-right: 1px solid #f1f5f9;
        }

        .menu-label {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            color: var(--text-color);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none
        }
        
        .nav-link.active {
            background-color: var(--active-bg);
            color: var(--primary-color);
        }
        
        .nav-link i {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            width: 100%
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        .card {
            border: none;
        }

        .card_color {
            background: #f9fafb
        }

        .page-link {
            background-color: #f9fafb !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
        }
        .active>.page-link {
            background-color: var(--active-bg) !important;
        }

        
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar">
        <h5 class="mb-4 ps-4">Dashboard</h5>
        <div class="menu-label">MENU</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('queues.dashboard') ? 'active' : '' }}" href="{{ route('queues.dashboard') }}">
                    <i class="fas fa-chart-bar"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('queues.queues') ? 'active' : '' }}" href="{{ route('queues.queues') }}">
                    <i class="fas fa-hourglass-half"></i>
                    Queues
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('queues.jobs') ? 'active' : '' }}" href="{{ route('queues.jobs') }}">
                    <i class="fas fa-tasks"></i>
                    Jobs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('queues.failed') ? 'active' : '' }}" href="{{ route('queues.failed') }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed Jobs
                </a>
            </li>
        </ul>
    </nav>

    <div class="main-content">
        @yield('content')
    </div>
</div>

<!-- Job Details Modal -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1" role="dialog" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobDetailsModalLabel">Job Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="job-details-output" style="font-family: 'Courier New', Courier, monospace; white-space: pre-wrap;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

@stack('scripts')

<script>
    function showJobDetails(jobId, type = '') {
        fetch(`{{ url('queues/jobs') }}/${jobId}/${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            // Populate modal with job details formatted as terminal output
            document.getElementById('job-details-output').innerText = data.output;
            // Show the modal
            $('#jobDetailsModal').modal('show');
        })
        .catch(error => console.error('Error fetching job details:', error));
    }
</script>

</body>
</html>