@extends('layouts.app')
@section('title')
    {{ __('My Profile') }}
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/style/css/dashboard.css') }}">
    <style>
        .achievement-badge {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .achievement-badge.gold { background: linear-gradient(45deg, #FFD700, #FFA500); }
        .achievement-badge.silver { background: linear-gradient(45deg, #C0C0C0, #808080); }
        .achievement-badge.bronze { background: linear-gradient(45deg, #CD7F32, #8B4513); }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .insight-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ __('My Profile & Achievements') }}</h1>
    </div>
    
    <div class="section-body">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ $user->img_avatar }}" class="rounded-circle" width="120" height="120" alt="Profile">
                </div>
                <div class="col-md-8">
                    <h2>{{ $user->name }}</h2>
                    <p class="mb-1"><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                    <p class="mb-1"><i class="fas fa-phone"></i> {{ $user->phone ?? 'Not provided' }}</p>
                    <p class="mb-0"><i class="fas fa-calendar"></i> Member since {{ $user->created_at->format('M Y') }}</p>
                    <div class="mt-3">
                        <h4>{{ count($profileData['achievements']) }}</h4>
                        <p>Achievements Earned</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card text-center">
                    <i class="fas fa-tasks fa-2x mb-3"></i>
                    <h3>{{ $profileData['task_stats']['completed_tasks'] }}</h3>
                    <p>Tasks Completed</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card text-center">
                    <i class="fas fa-clock fa-2x mb-3"></i>
                    <h3>{{ number_format($profileData['time_stats']['total_hours'], 1) }}</h3>
                    <p>Hours Logged</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card text-center">
                    <i class="fas fa-calendar-month fa-2x mb-3"></i>
                    <h3>{{ number_format($profileData['time_stats']['this_month_hours'], 1) }}</h3>
                    <p>This Month Hours</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Achievements Section -->
            <div class="col-lg-6">
                <div class="insight-card">
                    <h5><i class="fas fa-trophy"></i> Achievements & Badges</h5>
                    <div class="mt-3">
                        @forelse($profileData['achievements'] as $achievement)
                            <span class="achievement-badge {{ $achievement['color'] }}">
                                <i class="{{ $achievement['icon'] }}"></i> {{ $achievement['name'] }}
                            </span>
                        @empty
                            <p class="text-muted">No achievements yet. Keep working to earn your first badge!</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Task Performance -->
                <div class="insight-card">
                    <h5><i class="fas fa-chart-pie"></i> Task Performance</h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $profileData['task_stats']['completed_tasks'] }}</div>
                                <small>Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $profileData['task_stats']['in_progress_tasks'] }}</div>
                                <small>In Progress</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-info">{{ $profileData['task_stats']['total_tasks'] }}</div>
                                <small>Total Tasks</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-danger">{{ $profileData['task_stats']['overdue_tasks'] }}</div>
                                <small>Overdue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Performance Chart -->
            <div class="col-lg-6">
                <div class="insight-card">
                    <h5><i class="fas fa-chart-line"></i> Monthly Performance</h5>
                    <canvas id="performanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Monthly Insights Table -->
            <div class="col-lg-6">
                <div class="insight-card">
                    <h5><i class="fas fa-calendar-alt"></i> Monthly Performance Insights</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">Tasks</th>
                                    <th class="text-center">Hours</th>
                                    <th class="text-center">Avg/Task</th>
                                    <th class="text-center">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profileData['monthly_data'] as $month)
                                <tr>
                                    <td><strong>{{ $month['month_short'] }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $month['tasks_completed'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $month['hours_logged'] }}h</span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $month['avg_hours_per_task'] }}h</small>
                                    </td>
                                    <td class="text-center">
                                        @if($month['productivity_score'] >= 7)
                                            <span class="badge badge-success">{{ $month['productivity_score'] }}</span>
                                        @elseif($month['productivity_score'] >= 4)
                                            <span class="badge badge-warning">{{ $month['productivity_score'] }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $month['productivity_score'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Quick Insights -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-lightbulb"></i> Quick Insights</h6>
                        @php
                            $currentMonth = $profileData['monthly_data'][5] ?? null;
                            $previousMonth = $profileData['monthly_data'][4] ?? null;
                        @endphp
                        
                        @if($currentMonth && $previousMonth)
                            @if($currentMonth['tasks_completed'] > $previousMonth['tasks_completed'])
                                <small class="text-success"><i class="fas fa-arrow-up"></i> Task completion improved this month!</small><br>
                            @elseif($currentMonth['tasks_completed'] < $previousMonth['tasks_completed'])
                                <small class="text-warning"><i class="fas fa-arrow-down"></i> Task completion decreased this month.</small><br>
                            @endif
                            
                            @if($currentMonth['productivity_score'] > $previousMonth['productivity_score'])
                                <small class="text-success"><i class="fas fa-arrow-up"></i> Productivity score improved!</small>
                            @elseif($currentMonth['productivity_score'] < $previousMonth['productivity_score'])
                                <small class="text-info"><i class="fas fa-arrow-down"></i> Focus on efficiency this month.</small>
                            @endif
                        @endif
                        
                        @if($currentMonth && $currentMonth['avg_hours_per_task'] > 0)
                            <br><small class="text-muted"><i class="fas fa-clock"></i> You spend an average of {{ $currentMonth['avg_hours_per_task'] }} hours per task.</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Recent Tasks -->
            <div class="col-lg-12">
                <div class="insight-card">
                    <h5><i class="fas fa-tasks"></i> Recent Tasks</h5>
                    <div class="list-group list-group-flush">
                        @forelse($profileData['recent_tasks'] as $task)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $task->title }}</h6>
                                        <small class="text-muted">{{ $task->created_at->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge badge-secondary">
                                        Task #{{ $task->id }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No recent tasks found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page_js')
{{-- Chart.js and chart script removed --}}
@endsection