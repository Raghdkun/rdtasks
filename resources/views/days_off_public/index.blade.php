@extends('layouts.app')
@section('title')
    {{ __('Team Days Off Schedule') }}
@endsection
@section('css')
    <style>
        .days-off-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .days-off-card:hover {
            transform: translateY(-2px);
        }
        .week-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }
        .day-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e9ecef;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }
        .day-cell {
            background: white;
            padding: 1rem;
            min-height: 120px;
            position: relative;
        }
        .day-name {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .user-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            margin: 0.125rem;
            border: 1px solid #bbdefb;
        }
        .no-requests {
            color: #6c757d;
            font-style: italic;
            font-size: 0.8rem;
        }
        .legend {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        .current-week .week-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .next-week .week-header {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }
        .filter-controls {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">
                <i class="fas fa-calendar-alt mr-2"></i>
                {{ __('Team Days Off Schedule') }}
            </h1>
        </div>
        <div class="section-body">
            <!-- Filter Controls -->
            <div class="filter-controls">
                <form method="GET" action="{{ route('days-off-public.index') }}" class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="view_mode">View Mode</label>
                            <select name="view_mode" id="view_mode" class="form-control" onchange="this.form.submit()">
                                <option value="both" {{ request('view_mode', 'both') == 'both' ? 'selected' : '' }}>Both Weeks</option>
                                <option value="current" {{ request('view_mode') == 'current' ? 'selected' : '' }}>Current Week Only</option>
                                <option value="next" {{ request('view_mode') == 'next' ? 'selected' : '' }}>Next Week Only</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Legend -->
            <div class="legend">
                <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Legend</h6>
                <div class="legend-item">
                    <div class="legend-color" style="background: #e3f2fd; border: 1px solid #bbdefb;"></div>
                    <span>Team member taking day off</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f5f5f5; border: 1px solid #ddd;"></div>
                    <span>Regular working day</span>
                </div>
            </div>

            @php
                $viewMode = request('view_mode', 'both');
            @endphp

            <!-- Current Week -->
            @if($viewMode == 'both' || $viewMode == 'current')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card days-off-card current-week">
                        <div class="week-header">
                            <h4 class="mb-1">
                                <i class="fas fa-calendar-week mr-2"></i>
                                Current Week
                            </h4>
                            <p class="mb-0 opacity-75">
                                {{ $currentWeek->format('M d') }} - {{ $currentWeek->copy()->endOfWeek()->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="day-grid">
                            @foreach($weekDays as $dayNum => $dayName)
                                <div class="day-cell">
                                    <div class="day-name">{{ $dayName }}</div>
                                    @php
                                        $dayRequests = $currentWeekRequests->filter(function($request) use ($dayNum) {
                                            return in_array($dayNum, $request->selected_days);
                                        });
                                    @endphp
                                    @if($dayRequests->count() > 0)
                                        @foreach($dayRequests as $request)
                                            <span class="user-badge">{{ $request->user->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="no-requests">All available</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Next Week -->
            @if($viewMode == 'both' || $viewMode == 'next')
            <div class="row">
                <div class="col-12">
                    <div class="card days-off-card next-week">
                        <div class="week-header">
                            <h4 class="mb-1">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Next Week
                            </h4>
                            <p class="mb-0 opacity-75">
                                {{ $nextWeek->format('M d') }} - {{ $nextWeek->copy()->endOfWeek()->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="day-grid">
                            @foreach($weekDays as $dayNum => $dayName)
                                <div class="day-cell">
                                    <div class="day-name">{{ $dayName }}</div>
                                    @php
                                        $dayRequests = $nextWeekRequests->filter(function($request) use ($dayNum) {
                                            return in_array($dayNum, $request->selected_days);
                                        });
                                    @endphp
                                    @if($dayRequests->count() > 0)
                                        @foreach($dayRequests as $request)
                                            <span class="user-badge">{{ $request->user->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="no-requests">All available</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">
                                <i class="fas fa-users mr-2"></i>
                                Current Week
                            </h5>
                            <h3 class="text-success">{{ $currentWeekRequests->count() }}</h3>
                            <p class="text-muted mb-0">Team members taking days off</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-info">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Next Week
                            </h5>
                            <h3 class="text-info">{{ $nextWeekRequests->count() }}</h3>
                            <p class="text-muted mb-0">Team members taking days off</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection