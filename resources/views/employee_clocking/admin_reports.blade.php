@extends('layouts.app')

@section('title')
    Employee Clocking Admin Reports
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Clocking Admin Reports</h3>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_id">Employee:</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">All Employees</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Summary Statistics -->
                    @if($statistics)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Employees</span>
                                    <span class="info-box-number">{{ $statistics['total_employees'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Work Hours</span>
                                    <span class="info-box-number">{{ $statistics['total_work_hours'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-coffee"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Break Hours</span>
                                    <span class="info-box-number">{{ $statistics['total_break_hours'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Days</span>
                                    <span class="info-box-number">{{ $statistics['total_days'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Reports Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Work Time</th>
                                    <th>Break Time</th>
                                    <th>Daily Hours</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clockings as $clocking)
                                <tr>
                                    <td>
                                        <strong>{{ $clocking->user->name }}</strong><br>
                                        <small class="text-muted">{{ $clocking->user->email }}</small>
                                    </td>
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
                                        <span class="badge badge-info">{{ floor($clocking->daily_work_hours / 60) }}h {{ $clocking->daily_work_hours % 60 }}m</span>
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
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="editClocking({{ $clocking->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteClocking({{ $clocking->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No clocking records found for the selected criteria.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    {{ $clockings->appends(request()->query())->links() }}
                    
                    <!-- Export Options -->
                    <div class="mt-4">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('employee-clocking.admin-reports', array_merge(request()->query(), ['export' => 'csv'])) }}">
                                    <i class="fas fa-file-csv"></i> Export as CSV
                                </a>
                                <a class="dropdown-item" href="{{ route('employee-clocking.admin-reports', array_merge(request()->query(), ['export' => 'excel'])) }}">
                                    <i class="fas fa-file-excel"></i> Export as Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('employee-clocking.admin-reports', array_merge(request()->query(), ['export' => 'pdf'])) }}">
                                    <i class="fas fa-file-pdf"></i> Export as PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Clocking Modal -->
<div class="modal fade" id="editClockingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Clocking Record</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editClockingForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_clocking_id">
                    
                    <div class="form-group">
                        <label for="edit_user_id">Employee:</label>
                        <select id="edit_user_id" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_work_date">Work Date:</label>
                        <input type="date" id="edit_work_date" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_clock_in_time">Clock In Time:</label>
                                <input type="time" id="edit_clock_in_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_clock_out_time">Clock Out Time:</label>
                                <input type="time" id="edit_clock_out_time" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_daily_work_hours">Daily Work Hours (minutes):</label>
                        <input type="number" id="edit_daily_work_hours" class="form-control" min="60" max="720" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_notes">Notes:</label>
                        <textarea id="edit_notes" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editClocking(id) {
    // Fetch clocking data
    fetch(`/employee-clocking/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const clocking = data.data;
            document.getElementById('edit_clocking_id').value = clocking.id;
            document.getElementById('edit_user_id').value = clocking.user_id;
            document.getElementById('edit_work_date').value = clocking.work_date;
            document.getElementById('edit_clock_in_time').value = clocking.clock_in_time.substring(11, 16);
            document.getElementById('edit_clock_out_time').value = clocking.clock_out_time ? clocking.clock_out_time.substring(11, 16) : '';
            document.getElementById('edit_daily_work_hours').value = clocking.daily_work_hours;
            document.getElementById('edit_notes').value = clocking.notes || '';
            
            $('#editClockingModal').modal('show');
        } else {
            alert('Error loading clocking data: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading clocking data');
    });
}

function deleteClocking(id) {
    if (confirm('Are you sure you want to delete this clocking record? This action cannot be undone.')) {
        fetch(`/employee-clocking/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting record: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting record');
        });
    }
}

document.getElementById('editClockingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('edit_clocking_id').value;
    const formData = {
        user_id: document.getElementById('edit_user_id').value,
        work_date: document.getElementById('edit_work_date').value,
        clock_in_time: document.getElementById('edit_clock_in_time').value,
        clock_out_time: document.getElementById('edit_clock_out_time').value,
        daily_work_hours: document.getElementById('edit_daily_work_hours').value,
        notes: document.getElementById('edit_notes').value
    };
    
    fetch(`/employee-clocking/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#editClockingModal').modal('hide');
            location.reload();
        } else {
            alert('Error updating record: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating record');
    });
});

$(document).ready(function() {
    // Auto-submit form when user selection changes
    $('#user_id').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush


@endsection