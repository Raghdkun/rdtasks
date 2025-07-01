@extends('layouts.app')

@section('title')
    Employee Clocking System
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Clocking System</h3>
                    <div class="card-tools">
                        <a href="{{ route('employee-clocking.reports') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> View Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Clock Display -->
                        <div class="col-lg-6">
                            <div class="text-center mb-4">
                                <div class="clock-display">
                                    <h1 id="currentTime" class="display-4 text-primary">00:00:00</h1>
                                    <p class="text-muted">{{ now()->format('l, F j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Clocking Controls -->
                        <div class="col-lg-6">
                            <div class="clocking-controls">
                                <div id="clockingStatus" class="mb-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Loading status...
                                    </div>
                                </div>
                                
                                <!-- Work Time Counter -->
                                <div class="time-counter mb-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="workTimeCounter">00:00:00</h4>
                                                    <small>Work Time</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="breakTimeCounter">00:00:00</h4>
                                                    <small>Break Time</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="action-buttons text-center">
                                    <button id="clockInBtn" class="btn btn-success btn-lg mr-2" style="display: none;">
                                        <i class="fas fa-play"></i> Clock In
                                    </button>
                                    
                                    <button id="startBreakBtn" class="btn btn-warning btn-lg mr-2" style="display: none;">
                                        <i class="fas fa-pause"></i> Start Break
                                    </button>
                                    
                                    <button id="endBreakBtn" class="btn btn-info btn-lg mr-2" style="display: none;">
                                        <i class="fas fa-play"></i> Continue Working
                                    </button>
                                    
                                    <button id="clockOutBtn" class="btn btn-danger btn-lg" style="display: none;">
                                        <i class="fas fa-stop"></i> Leave Office
                                    </button>
                                </div>
                                
                                <!-- Daily Work Hours Setting -->
                                <div class="mt-4">
                                    <div class="form-group">
                                        <label for="dailyWorkHours">Daily Work Hours:</label>
                                        <select id="dailyWorkHours" class="form-control">
                                            <option value="420">7 Hours</option>
                                            <option value="480">8 Hours</option>
                                            <option value="540">9 Hours</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clock Out Modal -->
<div class="modal fade" id="clockOutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clock Out</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="clockOutNotes">Notes (Optional):</label>
                    <textarea id="clockOutNotes" class="form-control" rows="3" placeholder="Add any notes about your work day..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmClockOut" class="btn btn-danger">Clock Out</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentStatus = null;
    let workStartTime = null;
    let breakStartTime = null;
    let totalBreakTime = 0;
    
    // Update current time display
    function updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        $('#currentTime').text(timeString);
    }
    
    // Update work and break time counters
    function updateCounters() {
        if (currentStatus && currentStatus.clocking) {
            const clocking = currentStatus.clocking;
            
            if (clocking.status !== 'clocked_out') {
                // Calculate current work time
                const clockInTime = new Date(clocking.clock_in_time);
                const now = new Date();
                const totalMinutes = Math.floor((now - clockInTime) / (1000 * 60));
                const workMinutes = totalMinutes - (clocking.total_break_minutes || 0);
                
                // Update work time counter
                const workHours = Math.floor(workMinutes / 60);
                const workMins = workMinutes % 60;
                const workSecs = Math.floor((now - clockInTime) / 1000) % 60;
                $('#workTimeCounter').text(`${String(workHours).padStart(2, '0')}:${String(workMins).padStart(2, '0')}:${String(workSecs).padStart(2, '0')}`);
                
                // Update break time counter
                let currentBreakTime = clocking.total_break_minutes || 0;
                if (clocking.status === 'on_break' && currentStatus.current_break) {
                    const breakStart = new Date(currentStatus.current_break.start_time);
                    const currentBreakMinutes = Math.floor((now - breakStart) / (1000 * 60));
                    currentBreakTime += currentBreakMinutes;
                }
                
                const breakHours = Math.floor(currentBreakTime / 60);
                const breakMins = currentBreakTime % 60;
                $('#breakTimeCounter').text(`${String(breakHours).padStart(2, '0')}:${String(breakMins).padStart(2, '0')}:00`);
            }
        }
    }
    
    // Get current clocking status
    function getCurrentStatus() {
        $.get('{{ route("employee-clocking.status") }}', function(response) {
            currentStatus = response.data;
            updateUI();
        }).fail(function() {
            showAlert('Error loading clocking status.', 'danger');
        });
    }
    
    // Update UI based on current status
    function updateUI() {
        const status = currentStatus.status;
        
        // Hide all buttons first
        $('.action-buttons button').hide();
        
        switch(status) {
            case 'not_clocked_in':
                $('#clockingStatus').html('<div class="alert alert-secondary"><i class="fas fa-clock"></i> Ready to clock in</div>');
                $('#clockInBtn').show();
                break;
                
            case 'clocked_in':
                $('#clockingStatus').html('<div class="alert alert-success"><i class="fas fa-check"></i> Clocked in and working</div>');
                $('#startBreakBtn, #clockOutBtn').show();
                break;
                
            case 'on_break':
                $('#clockingStatus').html('<div class="alert alert-warning"><i class="fas fa-pause"></i> Currently on break</div>');
                $('#endBreakBtn, #clockOutBtn').show();
                break;
                
            case 'clocked_out':
                $('#clockingStatus').html('<div class="alert alert-info"><i class="fas fa-stop"></i> Clocked out for the day</div>');
                break;
        }
    }
    
    // Show alert message
    function showAlert(message, type = 'success') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>`;
        
        $('.card-body').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    // Clock in
    $('#clockInBtn').click(function() {
        const dailyWorkHours = $('#dailyWorkHours').val();
        
        $.post('{{ route("employee-clocking.clock-in") }}', {
            daily_work_hours: dailyWorkHours,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            showAlert(response.message);
            getCurrentStatus();
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response.message || 'Error clocking in.', 'danger');
        });
    });
    
    // Start break
    $('#startBreakBtn').click(function() {
        $.post('{{ route("employee-clocking.start-break") }}', {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            showAlert(response.message);
            getCurrentStatus();
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response.message || 'Error starting break.', 'danger');
        });
    });
    
    // End break
    $('#endBreakBtn').click(function() {
        $.post('{{ route("employee-clocking.end-break") }}', {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            showAlert(response.message);
            getCurrentStatus();
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response.message || 'Error ending break.', 'danger');
        });
    });
    
    // Clock out
    $('#clockOutBtn').click(function() {
        $('#clockOutModal').modal('show');
    });
    
    $('#confirmClockOut').click(function() {
        const notes = $('#clockOutNotes').val();
        
        $.post('{{ route("employee-clocking.clock-out") }}', {
            notes: notes,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            $('#clockOutModal').modal('hide');
            showAlert(response.message);
            getCurrentStatus();
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response.message || 'Error clocking out.', 'danger');
        });
    });
    
    // Initialize
    getCurrentStatus();
    
    // Update time every second
    setInterval(updateCurrentTime, 1000);
    setInterval(updateCounters, 1000);
    
    // Refresh status every 30 seconds
    setInterval(getCurrentStatus, 30000);
});
</script>
@endsection

@section('css')
<style>
.clock-display {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.time-counter .card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.action-buttons .btn {
    min-width: 150px;
    margin: 5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.clocking-controls {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}
</style>
@endsection