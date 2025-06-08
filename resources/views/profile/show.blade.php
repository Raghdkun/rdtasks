@extends('layouts.app')

@section('title')
    {{ $user->name }}'s Profile
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
        
        .public-profile-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .insight-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .public-stats {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .profile-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ $user->name }}'s Public Profile</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Public Profile</div>
        </div>
    </div>
    
    <div class="section-body">
        <!-- Public Profile Header -->
        <div class="public-profile-header position-relative">
            <div class="profile-badge">
                <i class="fas fa-eye"></i> Public View
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-md-3 text-center">
                    <img src="{{ $user->img_avatar }}" class="rounded-circle border border-white" width="150" height="150" alt="{{ $user->name }}">
                </div>
                <div class="col-md-6 text-center">
                    <h1 class="mb-3">{{ $user->name }}</h1>
                    <p class="mb-2"><i class="fas fa-calendar"></i> Member since {{ $user->created_at->format('F Y') }}</p>
                    @if($user->is_active)
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active Member</span>
                    @endif
                    <div class="mt-4">
                        <h3 class="mb-1">{{ count($profileData['achievements']) }}</h3>
                        <p class="mb-0">Achievements Earned</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Public Statistics -->
        <div class="public-stats">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="h2 text-primary mb-1">{{ $profileData['task_stats']['completed_tasks'] }}</div>
                    <p class="mb-0 text-muted"><i class="fas fa-check-circle"></i> Tasks Completed</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="h2 text-info mb-1">{{ number_format($profileData['time_stats']['total_hours'], 0) }}</div>
                    <p class="mb-0 text-muted"><i class="fas fa-clock"></i> Hours Contributed</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="h2 text-success mb-1">{{ $profileData['task_stats']['total_tasks'] }}</div>
                    <p class="mb-0 text-muted"><i class="fas fa-tasks"></i> Total Tasks</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    @php
                        $completionRate = $profileData['task_stats']['total_tasks'] > 0 
                            ? round(($profileData['task_stats']['completed_tasks'] / $profileData['task_stats']['total_tasks']) * 100) 
                            : 0;
                    @endphp
                    <div class="h2 text-warning mb-1">{{ $completionRate }}%</div>
                    <p class="mb-0 text-muted"><i class="fas fa-percentage"></i> Completion Rate</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Achievements Section -->
            <div class="col-lg-6">
                <div class="insight-card">
                    <h5><i class="fas fa-trophy text-warning"></i> Achievements & Recognition</h5>
                    <div class="mt-3">
                        @forelse($profileData['achievements'] as $achievement)
                            <span class="achievement-badge {{ $achievement['color'] }}">
                                <i class="{{ $achievement['icon'] }}"></i> {{ $achievement['name'] }}
                            </span>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-medal fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No achievements yet. This user is working towards their first milestone!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Performance Overview -->
                <div class="insight-card">
                    <h5><i class="fas fa-chart-line text-primary"></i> Performance Overview</h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h4 text-success mb-1">{{ $profileData['task_stats']['completed_tasks'] }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h4 text-info mb-1">{{ $profileData['task_stats']['in_progress_tasks'] }}</div>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($profileData['time_stats']['total_hours'] > 0)
                    <div class="mt-3 p-3 bg-primary text-white rounded text-center">
                        <h6 class="mb-1">Average Productivity</h6>
                        <p class="mb-0">
                            @php
                                $avgHours = $profileData['task_stats']['completed_tasks'] > 0 
                                    ? round($profileData['time_stats']['total_hours'] / $profileData['task_stats']['completed_tasks'], 1) 
                                    : 0;
                            @endphp
                            {{ $avgHours }} hours per completed task
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Activity Timeline -->
            <div class="col-lg-6">
                <div class="insight-card">
                    <h5><i class="fas fa-history text-info"></i> Recent Activity</h5>
                    <div class="mt-3">
                        @forelse($profileData['recent_tasks']->take(5) as $task)
                            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                <div class="mr-3">
                                    <i class="fas fa-task fa-lg text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($task->title, 40) }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> {{ $task->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge badge-light">Task #{{ $task->id }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity to display.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Monthly Performance Summary -->
                <div class="insight-card">
                    <h5><i class="fas fa-calendar-alt text-success"></i> Monthly Performance</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">Tasks</th>
                                    <th class="text-center">Hours</th>
                                    <th class="text-center">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profileData['monthly_data']->take(3) as $month)
                                <tr>
                                    <td><strong>{{ $month['month_short'] }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $month['tasks_completed'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $month['hours_logged'] }}h</span>
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
                </div>
            </div>
        </div>
        
        <!-- Professional Summary -->
        <div class="row">
            <div class="col-12">
                <div class="insight-card">
                    <h5><i class="fas fa-user-tie text-dark"></i> Professional Summary</h5>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                                <h6>Member Since</h6>
                                <p class="text-muted mb-0">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                <h6>Productivity Level</h6>
                                @php
                                    $latestMonth = $profileData['monthly_data']->last();
                                    $score = $latestMonth ? $latestMonth['productivity_score'] : 0;
                                @endphp
                                <p class="text-muted mb-0">
                                    @if($score >= 7)
                                        <span class="text-success">High Performer</span>
                                    @elseif($score >= 4)
                                        <span class="text-warning">Good Performer</span>
                                    @else
                                        <span class="text-info">Developing</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-trophy fa-2x text-warning mb-2"></i>
                                <h6>Recognition Level</h6>
                                <p class="text-muted mb-0">
                                    @if(count($profileData['achievements']) >= 3)
                                        <span class="text-warning">Highly Recognized</span>
                                    @elseif(count($profileData['achievements']) >= 1)
                                        <span class="text-info">Recognized</span>
                                    @else
                                        <span class="text-muted">Emerging Talent</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back to Team Button -->
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</section>
@endsection

@section('page_js')
<script>
    // Add any interactive features for the public profile
    $(document).ready(function() {
        // Tooltip initialization
        $('[data-toggle="tooltip"]').tooltip();
        
        // Smooth scrolling for any anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if( target.length ) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });
    });
</script>
@endsection