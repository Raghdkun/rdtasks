@extends('layouts.app')
@section('title')
    {{ __('Days Off Management') }}
@endsection
@section('css')
    @livewireStyles
    <style>
        .edit-modal .form-check {
            margin-bottom: 10px;
        }
        .edit-modal .form-check-input {
            margin-right: 8px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('Days Off Management') }}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a href="{{ route('days-off-settings.index') }}" class="btn btn-secondary mr-2">
                    {{ __('Settings') }} <i class="fas fa-cog"></i>
                </a>
                <a href="{{ route('days-off-public.index') }}" class="btn btn-info">
                    {{ __('Public View') }} <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            
            <!-- Pending Requests -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('Pending Requests') }}</h4>
                        </div>
                        <div class="card-body">
                            @if($pendingRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Week Starting') }}</th>
                                                <th>{{ __('Selected Days') }}</th>
                                                <th>{{ __('Notes') }}</th>
                                                <th>{{ __('Submitted') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingRequests as $request)
                                                <tr id="request-row-{{ $request->id }}">
                                                    <td>{{ $request->user->name }}</td>
                                                    <td>{{ $request->week_start_date->format('M d, Y') }}</td>
                                                    <td>
                                                        @foreach($request->selected_days_names as $day)
                                                            <span class="badge badge-info mr-1">{{ $day }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $request->notes ?? '-' }}</td>
                                                    <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm mr-1 edit-btn" 
                                                                data-request-id="{{ $request->id }}"
                                                                data-selected-days='{{ json_encode($request->selected_days) }}'
                                                                data-notes="{{ $request->notes }}"
                                                                data-user-name="{{ $request->user->name }}"
                                                                data-week-date="{{ $request->week_start_date->format('M d, Y') }}">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <form method="PATCH" action="{{ route('days-off-admin.approve', $request->id) }}" class="d-inline approve-form">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm mr-1" data-request-id="{{ $request->id }}">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('days-off-admin.reject', $request->id) }}" class="d-inline reject-form">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-danger btn-sm" data-request-id="{{ $request->id }}">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">{{ __('No pending requests') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Requests History -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('All Requests History') }}</h4>
                        </div>
                        <div class="card-body">
                            @if($allRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Week Starting') }}</th>
                                                <th>{{ __('Selected Days') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Approved By') }}</th>
                                                <th>{{ __('Approved At') }}</th>
                                                <th>{{ __('Notes') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allRequests as $request)
                                                <tr>
                                                    <td>{{ $request->user->name }}</td>
                                                    <td>{{ $request->week_start_date->format('M d, Y') }}</td>
                                                    <td>
                                                        @foreach($request->selected_days_names as $day)
                                                            <span class="badge badge-info mr-1">{{ $day }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if($request->status === 'approved')
                                                            <span class="badge badge-success">{{ ucfirst($request->status) }}</span>
                                                        @elseif($request->status === 'rejected')
                                                            <span class="badge badge-danger">{{ ucfirst($request->status) }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ ucfirst($request->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $request->approver->name ?? '-' }}</td>
                                                    <td>{{ $request->approved_at ? $request->approved_at->format('M d, Y H:i') : '-' }}</td>
                                                    <td>{{ $request->notes ?? '-' }}</td>
                                                    <td>
                                                        @if($request->status !== 'rejected')
                                                            <button type="button" class="btn btn-warning btn-sm edit-btn" 
                                                                    data-request-id="{{ $request->id }}"
                                                                    data-selected-days='{{ json_encode($request->selected_days) }}'
                                                                    data-notes="{{ $request->notes }}"
                                                                    data-user-name="{{ $request->user->name }}"
                                                                    data-week-date="{{ $request->week_start_date->format('M d, Y') }}">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $allRequests->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">{{ __('No requests found') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content edit-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Days Off Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label><strong>User:</strong> <span id="editUserName"></span></label>
                        </div>
                        <div class="form-group">
                            <label><strong>Week Starting:</strong> <span id="editWeekDate"></span></label>
                        </div>
                        <div class="form-group">
                            <label>Selected Days:</label>
                            <div id="editDaysContainer">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="selected_days[]" value="{{ $index + 1 }}" id="edit_day_{{ $index + 1 }}">
                                        <label class="form-check-label" for="edit_day_{{ $index + 1 }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editNotes">Notes:</label>
                            <textarea class="form-control" id="editNotes" name="notes" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveEditBtn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        $(document).ready(function() {
            let currentRequestId = null;
            
            // Handle edit button click
            $('.edit-btn').on('click', function() {
                currentRequestId = $(this).data('request-id');
                const selectedDays = $(this).data('selected-days');
                const notes = $(this).data('notes');
                const userName = $(this).data('user-name');
                const weekDate = $(this).data('week-date');
                
                // Populate modal
                $('#editUserName').text(userName);
                $('#editWeekDate').text(weekDate);
                $('#editNotes').val(notes || '');
                
                // Clear all checkboxes first
                $('input[name="selected_days[]"]').prop('checked', false);
                
                // Check selected days
                if (selectedDays && Array.isArray(selectedDays)) {
                    selectedDays.forEach(function(day) {
                        $('#edit_day_' + day).prop('checked', true);
                    });
                }
                
                $('#editModal').modal('show');
            });
            
            // Handle edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!currentRequestId) return;
                
                const formData = $(this).serialize();
                const saveBtn = $('#saveEditBtn');
                
                // Disable button and show loading
                saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                
                $.ajax({
                    url: `/days-off-admin/${currentRequestId}`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        displaySuccessMessage(response.message || 'Request updated successfully!');
                        $('#editModal').modal('hide');
                        location.reload(); // Refresh to show updated data
                    },
                    error: function(xhr) {
                        displayErrorMessage(xhr.responseJSON?.message || 'Error updating request');
                    },
                    complete: function() {
                        // Re-enable button
                        saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
                    }
                });
            });
            
            // Handle approve form submission
            $('.approve-form').on('submit', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to approve this request?')) {
                    return;
                }
                
                let form = $(this);
                let button = form.find('button');
                let requestId = button.data('request-id');
                
                // Disable button and show loading
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        displaySuccessMessage(response.message || 'Request approved successfully!');
                        // Remove the row from pending requests table
                        $('#request-row-' + requestId).fadeOut(500, function() {
                            $(this).remove();
                            // Check if no more pending requests
                            if ($('.approve-form').length === 0) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        displayErrorMessage(xhr.responseJSON?.message || 'Error approving request');
                        // Re-enable button
                        button.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
                    }
                });
            });
            
            // Handle reject form submission
            $('.reject-form').on('submit', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to reject this request?')) {
                    return;
                }
                
                let form = $(this);
                let button = form.find('button');
                let requestId = button.data('request-id');
                
                // Disable button and show loading
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        displaySuccessMessage(response.message || 'Request rejected successfully!');
                        // Remove the row from pending requests table
                        $('#request-row-' + requestId).fadeOut(500, function() {
                            $(this).remove();
                            // Check if no more pending requests
                            if ($('.reject-form').length === 0) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        displayErrorMessage(xhr.responseJSON?.message || 'Error rejecting request');
                        // Re-enable button
                        button.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
                    }
                });
            });
        });
    </script>
@endsection