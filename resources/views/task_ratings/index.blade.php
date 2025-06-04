@extends('layouts.app')

@section('title')
    Task Ratings
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party/dataTables.bootstrap4.min.css') }}">
    <style>
        .rating-stars {
            color: #ffc107;
        }
        .rating-display {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .avg-rating {
            font-weight: bold;
            color: #28a745;
        }
        .edited-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.75rem;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Task Ratings Management</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <h6 class="mb-3"><i class="fas fa-filter"></i> Filters</h6>
                            <form id="filterForm" method="GET">
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="task_name" class="form-label">Task/Project Name</label>
                                        <input type="text" class="form-control" id="task_name" name="task_name" 
                                               value="{{ request('task_name') }}" placeholder="Search by task or project name...">
                                    </div>
                                    <div class="filter-group">
                                        <label for="assignee_name" class="form-label">Assignee Name</label>
                                        <input type="text" class="form-control" id="assignee_name" name="assignee_name" 
                                               value="{{ request('assignee_name') }}" placeholder="Search by assignee name...">
                                    </div>
                                    <div class="filter-group">
                                        <label for="date_from" class="form-label">Completed From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="filter-group">
                                        <label for="date_to" class="form-label">Completed To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                    <div class="filter-group">
                                        <label for="rating_status" class="form-label">Rating Status</label>
                                        <select class="form-control" id="rating_status" name="rating_status">
                                            <option value="">All Tasks</option>
                                            <option value="rated" {{ request('rating_status') == 'rated' ? 'selected' : '' }}>Rated</option>
                                            <option value="pending" {{ request('rating_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="edited" {{ request('rating_status') == 'edited' ? 'selected' : '' }}>Edited</option>
                                        </select>
                                    </div>
                                    <div class="filter-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('task-ratings.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Results Summary -->
                        @if(request()->hasAny(['task_name', 'assignee_name', 'date_from', 'date_to', 'rating_status']))
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Showing filtered results 
                                @if(request('task_name'))
                                    for task/project: "<strong>{{ request('task_name') }}</strong>"
                                @endif
                                @if(request('assignee_name'))
                                    for assignee: "<strong>{{ request('assignee_name') }}</strong>"
                                @endif
                                @if(request('date_from') || request('date_to'))
                                    from {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'beginning' }} 
                                    to {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'now' }}
                                @endif
                                @if(request('rating_status'))
                                    with status: <strong>{{ ucfirst(request('rating_status')) }}</strong>
                                @endif
                                ({{ $completedTasks->total() }} results)
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="ratingsTable">
                                <thead>
                                    <tr>
                                        <th>Task #</th>
                                        <th>Task Title</th>
                                        <th>Project</th>
                                        <th>Assignee(s)</th>
                                        <th>Completed On</th>
                                        <th>Average Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($completedTasks as $task)
                                        <tr>
                                            <td>{{ $task->task_number }}</td>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->project->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($task->taskAssignee->count() > 0)
                                                    @foreach($task->taskAssignee as $assignee)
                                                        <span class="badge badge-info">{{ $assignee->name }}</span>
                                                        @if(!$loop->last) @endif
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No assignee</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->completed_on ? $task->completed_on->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if($task->rating)
                                                    <div class="rating-display">
                                                        <span class="avg-rating">{{ $task->rating->average_rating }}/10</span>
                                                        <div class="rating-stars">
                                                            @for($i = 1; $i <= 10; $i++)
                                                                @if($i <= $task->rating->average_rating)
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not rated</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->rating)
                                                    <span class="badge badge-success">Rated</span>
                                                    @if($task->rating->is_edited)
                                                        <span class="edited-badge">Edited</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->rating)
                                                    <a href="{{ route('task-ratings.show', $task) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('task-ratings.edit', $task) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @else
                                                    <a href="{{ route('task-ratings.create', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-star"></i> Rate Task
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                @if(request()->hasAny(['task_name', 'assignee_name', 'date_from', 'date_to', 'rating_status']))
                                                    No tasks found matching your filter criteria.
                                                @else
                                                    No completed tasks found.
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $completedTasks->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/third-party/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/third-party/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with search disabled (we're using custom filters)
            $('#ratingsTable').DataTable({
                "searching": false,
                "paging": false,
                "info": false,
                "ordering": true,
                "order": [[ 4, "desc" ]], // Sort by completed date descending
                "columnDefs": [
                    { "orderable": false, "targets": [5, 6, 7] } // Disable sorting for rating, status, and actions columns
                ]
            });

            // Auto-submit form on input change (with debounce for text inputs)
            let searchTimeout;
            $('#task_name, #assignee_name').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 500); // 500ms debounce
            });

            // Auto-submit on date and select changes
            $('#date_from, #date_to, #rating_status').on('change', function() {
                $('#filterForm').submit();
            });

            // Clear individual filters
            $('.clear-filter').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(`#${target}`).val('');
                $('#filterForm').submit();
            });

            // Set max date for date inputs to today
            const today = new Date().toISOString().split('T')[0];
            $('#date_from, #date_to').attr('max', today);

            // Validate date range
            $('#date_from, #date_to').on('change', function() {
                const dateFrom = $('#date_from').val();
                const dateTo = $('#date_to').val();
                
                if (dateFrom && dateTo && dateFrom > dateTo) {
                    alert('"From" date cannot be later than "To" date.');
                    $(this).val('');
                }
            });
        });
    </script>
@endsection