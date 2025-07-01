@extends('layouts.app')

@section('title')
    Clocking Reports
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Clocking Reports</h3>
                    <div class="card-tools">
                        <a href="{{ route('employee-clocking.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-clock"></i> Back to Clocking
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Reports Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Work Time</th>
                                    <th>Break Time</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clockings as $clocking)
                                <tr>
                                    <td>{{ $clocking->work_date->format('M d, Y') }}</td>
                                    <td>{{ $clocking->clock_in_time->format('H:i:s') }}</td>
                                    <td>
                                        @if($clocking->clock_out_time)
                                            {{ $clocking->clock_out_time->format('H:i:s') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $clocking->getFormattedWorkTime() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $clocking->getFormattedBreakTime() }}</span>
                                    </td>
                                    <td>
                                        @switch($clocking->status)
                                            @case('clocked_in')
                                                <span class="badge badge-success">Working</span>
                                                @break
                                            @case('on_break')
                                                <span class="badge badge-warning">On Break</span>
                                                @break
                                            @case('clocked_out')
                                                <span class="badge badge-secondary">Completed</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($clocking->notes)
                                            <span title="{{ $clocking->notes }}">{{ Str::limit($clocking->notes, 30) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No clocking records found for the selected period.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    {{ $clockings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection