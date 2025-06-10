@extends('layouts.app')

@section('title')
    My Performance Dashboard
@endsection

@section('css')
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #660609 0%, #D81619 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .performance-badge {
            font-size: 16px;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 25px;
        }
        .grade-a-plus { background-color: #47c363; color: white; }
        .grade-a { background-color: #D81619; color: white; }
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
        .task-item {
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .under-performing {
            border-left-color: #fc544b;
        }
        .incentive-card {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
        }
        .btn-period {
            margin: 2px;
        }
        .btn-period.active {
            background-color: #D81619;
            border-color: #D81619;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="fas fa-user-chart"></i> My Performance Dashboard</h2>
                            <p class="mb-0">{{ $dashboardData['summary_stats']['period_label'] }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <span class="performance-badge grade-{{ strtolower(str_replace('+', '-plus', $dashboardData['kpi_data']['performance_grade'])) }}">
                                {{ $dashboardData['kpi_data']['performance_grade'] }} Grade
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Filters -->
        <div class="row">
            <div class="col-12">
                <div class="filter-card">
                    <h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Time Period</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('user-dashboard.index', ['period' => 'current_month']) }}" 
                           class="btn btn-outline-primary btn-period {{ $period == 'current_month' ? 'active' : '' }}">
                            Current Month
                        </a>
                        <a href="{{ route('user-dashboard.index', ['period' => 'last_month']) }}" 
                           class="btn btn-outline-primary btn-period {{ $period == 'last_month' ? 'active' : '' }}">
                            Last Month
                        </a>
                        <a href="{{ route('user-dashboard.index', ['period' => 'last_3_months']) }}" 
                           class="btn btn-outline-primary btn-period {{ $period == 'last_3_months' ? 'active' : '' }}">
                            Last 3 Months
                        </a>
                        <a href="{{ route('user-dashboard.index', ['period' => 'last_6_months']) }}" 
                           class="btn btn-outline-primary btn-period {{ $period == 'last_6_months' ? 'active' : '' }}">
                            Last 6 Months
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4 class="text-primary">{{ $dashboardData['summary_stats']['completed_tasks'] }}</h4>
                    <p class="mb-0">Completed Tasks</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4 class="text-success">{{ $dashboardData['summary_stats']['total_projects'] }}</h4>
                    <p class="mb-0">Projects Worked</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4 class="text-info">{{ $dashboardData['kpi_data']['total_percentage'] }}%</h4>
                    <p class="mb-0">Overall Performance</p>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <div class="stats-card text-center">
                    <h4 class="text-warning">${{ number_format($dashboardData['incentive_data']['total_incentive'], 2) }}</h4>
                    <p class="mb-0">Total Incentive</p>
                </div>
            </div> --}}
        </div>

        <!-- Performance Breakdown -->
        <div class="row">
            <div class="col-md-12">
                <div class="stats-card">
                    <h5 class="card-title mb-4"><i class="fas fa-chart-bar"></i> Performance Breakdown</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Task Rating Performance (50% max)</label>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-info" style="width: {{ ($dashboardData['kpi_data']['task_percentage']/50)*100 }}%">
                                    {{ $dashboardData['kpi_data']['task_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $dashboardData['kpi_data']['task_avg_rating'] }}/10 avg ({{ $dashboardData['kpi_data']['task_total_ratings'] }} tasks)</small>
                        </div>
                        <div class="col-md-6">
                            <label>Client Rating Performance (50% max)</label>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-success" style="width: {{ ($dashboardData['kpi_data']['client_percentage']/50)*100 }}%">
                                    {{ $dashboardData['kpi_data']['client_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $dashboardData['kpi_data']['client_avg_rating'] }}/50 avg ({{ $dashboardData['kpi_data']['client_total_ratings'] }} ratings)</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <label>Total Performance</label>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-primary" style="width: {{ $dashboardData['kpi_data']['total_percentage'] }}%">
                                    <strong>{{ $dashboardData['kpi_data']['total_percentage'] }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            {{-- <div class="col-md-4">
                <div class="incentive-card">
                    <h5 class="mb-3"><i class="fas fa-dollar-sign"></i> Incentive Breakdown</h5>
                    <div class="mb-2">
                        <strong>Base Incentive:</strong> ${{ number_format($dashboardData['incentive_data']['base_incentive'], 2) }}
                    </div>
                    <div class="mb-2">
                        <strong>Performance Multiplier:</strong> {{ $dashboardData['incentive_data']['performance_multiplier'] }}x
                    </div>
                    <div class="mb-2">
                        <strong>Performance Incentive:</strong> ${{ number_format($dashboardData['incentive_data']['performance_incentive'], 2) }}
                    </div>
                    @if($dashboardData['incentive_data']['bonus'] > 0)
                    <div class="mb-2">
                        <strong>Performance Bonus:</strong> ${{ number_format($dashboardData['incentive_data']['bonus'], 2) }}
                    </div>
                    @endif
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="h4">
                        <strong>Total: ${{ number_format($dashboardData['incentive_data']['total_incentive'], 2) }}</strong>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Under-performing Projects -->
        @if(count($dashboardData['under_performing_projects']) > 0)
        <div class="row">
            <div class="col-12">
                <div class="stats-card">
                    <h5 class="card-title mb-4"><i class="fas fa-exclamation-triangle text-warning"></i> Projects Under 50% Performance</h5>
                    <div class="row">
                        @foreach($dashboardData['under_performing_projects'] as $project)
                        <div class="col-md-6 mb-3">
                            <div class="task-item under-performing">
                                <h6 class="mb-2">{{ $project->name }}</h6>
                                <div class="progress progress-custom mb-2">
                                    <div class="progress-bar bg-danger" style="width: {{ $project->total_percentage }}%">
                                        {{ $project->total_percentage }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $project->total_tasks }} tasks completed
                                    <br>Task Avg: {{ round($project->avg_task_rating ?? 0, 2) }}/10 | Client Avg: {{ round($project->avg_client_rating ?? 0, 2) }}/50
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Completed Tasks -->
        <div class="row">
            <div class="col-12">
                <div class="stats-card">
                    <h5 class="card-title mb-4"><i class="fas fa-tasks"></i> Recent Completed Tasks</h5>
                    @if(count($dashboardData['recent_tasks']) > 0)
                        @foreach($dashboardData['recent_tasks'] as $task)
                        <div class="task-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $task->title }}</h6>
                                    <small class="text-muted">{{ $task->project->name ?? 'No Project' }}</small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Completed:</small><br>
                                    <strong>{{ \Carbon\Carbon::parse($task->completed_on)->format('M j, Y') }}</strong>
                                </div>
                                <div class="col-md-3 text-right">
                                    @if($task->taskRating)
                                        <span class="badge badge-info">Task: {{ round(($task->taskRating->code_quality + $task->taskRating->delivery_output + $task->taskRating->time_score + $task->taskRating->collaboration + $task->taskRating->complexity_urgency) / 5, 1) }}/10</span>
                                    @endif
                                    @if($task->clientRating)
                                        <span class="badge badge-success">Client: {{ $task->clientRating->rating }}/50</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> No completed tasks found for this period.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Add any interactive functionality here
            console.log('User Dashboard loaded');
        });
    </script>
@endsection