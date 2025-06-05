@extends('layouts.app')

@section('title')
    Team KPI Report
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <style>
        .kpi-card {
            background: linear-gradient(135deg, #000000 0%, #2c2c2c 50%, #000000 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .performance-badge {
            font-size: 14px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .grade-a-plus { background-color: #47c363; color: white; }
        .grade-a { background-color: #3abaf4; color: white; }
        .grade-b-plus { background-color: #D81619; color: white; }
        .grade-b { background-color: #34395e; color: white; }
        .grade-c-plus { background-color: #ffa426; color: white; }
        .grade-c { background-color: #e3eaef; color: #191d21; }
        .grade-d { background-color: #fc544b; color: white; }
        .progress-custom {
            height: 25px;
            border-radius: 15px;
        }
        .filter-card {
            background-color: #fcfcfd;
            border: 1px solid #e4e6fc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .screenshot-area {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #D81619;
            border-color: #D81619;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #394eea;
            border-color: #394eea;
        }
        .progress-bar.bg-info {
            background-color: #3abaf4 !important;
        }
        .progress-bar.bg-success {
            background-color: #47c363 !important;
        }
        .progress-bar.bg-primary {
            background-color: #D81619 !important;
        }
        .card-header {
            background-color: #fcfcfd;
            border-bottom: 1px solid #e4e6fc;
        }
        .thead-dark {
            background-color: #D81619 !important;
            color: white !important;
        }
        .thead-dark th {
            background-color: #D81619 !important;
            color: white !important;
            border-color: #D81619 !important;
        }
        .badge-warning {
            background-color: #ffa426;
        }
        .badge-secondary {
            background-color: #34395e;
        }
        .text-primary {
            color: #D81619 !important;
        }
        .text-success {
            color: #47c363 !important;
        }
        .text-warning {
            color: #ffa426 !important;
        }
        .text-info {
            color: #3abaf4 !important;
        }
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 12px;
            }
            .kpi-card {
                padding: 15px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid" id="full-report-area">
        <div class="row">
            <div class="col-12">
                <div class="kpi-card screenshot-area">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="fas fa-chart-line"></i> Team KPI Report</h2>
                            <p class="mb-0">Performance analysis based on Task Ratings (50% max) + Client Ratings (50% max)</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-light" onclick="exportData()">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                            <button class="btn btn-outline-light ml-2" onclick="takeScreenshot()">
                                <i class="fas fa-camera"></i> Screenshot
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="filter-card screenshot-area">
                    <form method="GET" action="{{ route('team-kpi.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="user_id">Filter by User:</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from">Date From:</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to">Date To:</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-info btn-sm" id="thisMonthBtn">
                                    <i class="fas fa-calendar-alt"></i> This Month
                                </button>
                                <a href="{{ route('team-kpi.index') }}" class="btn btn-secondary btn-sm ml-2">
                                    <i class="fas fa-times"></i> Clear All Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI Table -->
        <div class="row">
            <div class="col-12">
                <div class="card screenshot-area" id="kpi-table-area">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table"></i> Team Performance Overview
                            <small class="text-muted">({{ count($kpiData) }} team members)</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="kpiTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Team Member</th>
                                        <th>Task Rating<br><small>(Avg/Total)</small></th>
                                        <th>Task %<br><small>(50% max)</small></th>
                                        <th>Client Rating<br><small>(Avg/Total)</small></th>
                                        <th>Client %<br><small>(50% max)</small></th>
                                        <th>Total %</th>
                                        <th>Performance</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kpiData as $index => $data)
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $data['user']->name }}</strong>
                                                <br><small class="text-muted">{{ $data['user']->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $data['task_avg_rating'] }}/10</strong>
                                                <br><small class="text-muted">({{ $data['task_total_ratings'] }} tasks)</small>
                                            </td>
                                            <td>
                                                <div class="progress progress-custom">
                                                    <div class="progress-bar bg-info" style="width: {{ ($data['task_percentage']/50)*100 }}%">
                                                        {{ $data['task_percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $data['client_avg_rating'] }}/50</strong>
                                                <br><small class="text-muted">({{ $data['client_total_ratings'] }} ratings)</small>
                                            </td>
                                            <td>
                                                <div class="progress progress-custom">
                                                    <div class="progress-bar bg-success" style="width: {{ ($data['client_percentage']/50)*100 }}%">
                                                        {{ $data['client_percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress progress-custom">
                                                    <div class="progress-bar bg-primary" style="width: {{ $data['total_percentage'] }}%">
                                                        <strong>{{ $data['total_percentage'] }}%</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data['total_percentage'] >= 80)
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif($data['total_percentage'] >= 60)
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="far fa-star text-muted"></i>
                                                @else
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="far fa-star text-muted"></i>
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="performance-badge grade-{{ strtolower(str_replace('+', '-plus', $data['performance_grade'])) }}">
                                                    {{ $data['performance_grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No KPI data available for the selected filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        @if(count($kpiData) > 0)
            <div class="row mt-4 screenshot-area" id="summary-cards">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-primary">{{ count($kpiData) }}</h5>
                            <p class="mb-0">Total Team Members</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-success">{{ number_format(collect($kpiData)->avg('total_percentage'), 1) }}%</h5>
                            <p class="mb-0">Average Performance</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-warning">{{ collect($kpiData)->where('total_percentage', '>=', 80)->count() }}</h5>
                            <p class="mb-0">High Performers (≥80%)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-info">{{ collect($kpiData)->sum('task_total_ratings') + collect($kpiData)->sum('client_total_ratings') }}</h5>
                            <p class="mb-0">Total Ratings</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#kpiTable').DataTable({
                "pageLength": 25,
                "order": [[ 6, "desc" ]], // Sort by Total % descending
                "columnDefs": [
                    { "orderable": false, "targets": [7] } // Disable sorting for Performance column
                ],
                "language": {
                    "search": "Search team members:",
                    "lengthMenu": "Show _MENU_ members per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ team members",
                    "emptyTable": "No team members found"
                }
            });

            // This Month button functionality
            $('#thisMonthBtn').click(function() {
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                
                const formatDate = (date) => {
                    return date.getFullYear() + '-' + 
                           String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                           String(date.getDate()).padStart(2, '0');
                };
                
                $('#date_from').val(formatDate(firstDay));
                $('#date_to').val(formatDate(lastDay));
                
                // Auto-submit the form
                $('#filterForm').submit();
            });

            // Auto-submit on date/select changes
            $('#user_id, #date_from, #date_to').change(function() {
                $('#filterForm').submit();
            });
        });

        function exportData() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route("team-kpi.export") }}?' + params.toString();
        }

        function takeScreenshot() {
            const element = document.getElementById('full-report-area');
            
            html2canvas(element, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                width: element.scrollWidth,
                height: element.scrollHeight
            }).then(function(canvas) {
                // Create download link
                const link = document.createElement('a');
                link.download = 'team-kpi-report-' + new Date().toISOString().slice(0,10) + '.png';
                link.href = canvas.toDataURL();
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message
                alert('Full KPI report screenshot saved successfully!');
            }).catch(function(error) {
                console.error('Error taking screenshot:', error);
                alert('Error taking screenshot. Please try again.');
            });
        }

        // Copy table to clipboard functionality
        function copyTableToClipboard() {
            const table = document.getElementById('kpiTable');
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            
            try {
                document.execCommand('copy');
                alert('Table copied to clipboard!');
            } catch (err) {
                alert('Failed to copy table to clipboard.');
            }
            
            window.getSelection().removeAllRanges();
        }
    </script>
@endsection