@extends('layouts.app')
@section('title')
    My Days Off
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>My Days Off Schedule</h1>
        </div>
        <div class="section-body">
            @include('flash::message')
            
            @if($settings)
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Select Your Days Off for Week Starting {{ $currentWeekStart->format('M d, Y') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>Instructions:</strong> You can select up to {{ $settings->days_per_week }} days off per week.
                                    @if($settings->description)
                                        <br>{{ $settings->description }}
                                    @endif
                                </div>
                                
                                {{ Form::open(['id' => 'daysOffForm']) }}
                                {{ Form::hidden('week_start_date', $currentWeekStart->format('Y-m-d')) }}
                                
                                <div class="form-group">
                                    <label>Select Days Off:</label>
                                    <div class="row">
                                        @foreach($weekDays as $dayNum => $dayName)
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    {{ Form::checkbox('selected_days[]', $dayNum, 
                                                        $currentSelection && in_array($dayNum, $currentSelection->selected_days), 
                                                        ['class' => 'custom-control-input day-checkbox', 'id' => 'day_'.$dayNum]) }}
                                                    <label class="custom-control-label" for="day_{{ $dayNum }}">
                                                        {{ $dayName }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {{ Form::label('notes', 'Notes (Optional):') }}
                                    {{ Form::textarea('notes', $currentSelection->notes ?? '', ['id' => 'notes', 'class' => 'form-control', 'rows' => 3]) }}
                                </div>
                                
                                <div class="text-right">
                                    {{ Form::button('Save Selection', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Current Status</h4>
                            </div>
                            <div class="card-body">
                                @if($currentSelection)
                                    <div class="alert alert-{{ $currentSelection->status == 'approved' ? 'success' : ($currentSelection->status == 'rejected' ? 'danger' : 'warning') }}">
                                        <strong>Status:</strong> {{ ucfirst($currentSelection->status) }}
                                        <br><strong>Selected Days:</strong> {{ implode(', ', $currentSelection->selected_days_names) }}
                                        @if($currentSelection->approved_by)
                                            <br><strong>{{ $currentSelection->status == 'approved' ? 'Approved' : 'Rejected' }} by:</strong> {{ $currentSelection->approver->name }}
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        No selection made for this week yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- History Section -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>My Days Off History</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Week Starting</th>
                                                <th>Selected Days</th>
                                                <th>Status</th>
                                                <th>Approved By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($userHistory as $history)
                                                <tr>
                                                    <td>{{ $history->week_start_date->format('M d, Y') }}</td>
                                                    <td>{{ implode(', ', $history->selected_days_names) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $history->status == 'approved' ? 'success' : ($history->status == 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($history->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $history->approver->name ?? '-' }}</td>
                                                    <td>{{ $history->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No history found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $userHistory->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    Days off settings have not been configured yet. Please contact your administrator.
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const maxDays = {{ $settings->days_per_week ?? 0 }};
            
            $('.day-checkbox').on('change', function() {
                const checkedCount = $('.day-checkbox:checked').length;
                
                if (checkedCount > maxDays) {
                    $(this).prop('checked', false);
                    displayErrorMessage(`You can only select up to ${maxDays} days.`);
                }
            });
            
            $('#daysOffForm').on('submit', function(e) {
                e.preventDefault();
                
                const checkedCount = $('.day-checkbox:checked').length;
                if (checkedCount === 0) {
                    displayErrorMessage('Please select at least one day.');
                    return;
                }
                
                let formData = $(this).serialize();
                
                $.ajax({
                    url: '{{ route("user-days-off.store") }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        displaySuccessMessage(response.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        displayErrorMessage(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection