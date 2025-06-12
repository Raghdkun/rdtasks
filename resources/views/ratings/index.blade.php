@extends('layouts.app')
@section('title')
    Task & Client Ratings
@endsection
@section('css')
    <style>
        .rating-icon {
            font-size: 16px;
            margin-left: 5px;
            cursor: pointer;
        }
        .rating-icon.has-both {
            color: #28a745;
        }
        .rating-icon.has-partial {
            color: #ffc107;
        }
        .rating-icon.has-none {
            color: #6c757d;
        }
        .rating-stars {
            color: #ffc107;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Task & Client Ratings Overview</h1>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ $stats['total_tasks'] }}</h3>
                        <p class="mb-0">Total Tasks</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ $stats['tasks_with_ratings'] }}</h3>
                        <p class="mb-0">Task Ratings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ $stats['tasks_with_client_ratings'] }}</h3>
                        <p class="mb-0">Client Ratings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ $stats['tasks_with_both_ratings'] }}</h3>
                        <p class="mb-0">Both Ratings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ number_format($stats['avg_task_rating'], 1) }}</h3>
                        <p class="mb-0">Avg Task Rating</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3>{{ number_format($stats['avg_client_rating'], 1) }}</h3>
                        <p class="mb-0">Avg Client Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" action="{{ route('ratings.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label><b>Project</b></label>
                        <select name="project_id" class="form-control">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label><b>Task Rating</b></label>
                        <select name="has_task_rating" class="form-control">
                            <option value="">All</option>
                            <option value="1" {{ request('has_task_rating') == '1' ? 'selected' : '' }}>Has Rating</option>
                            <option value="0" {{ request('has_task_rating') == '0' ? 'selected' : '' }}>No Rating</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label><b>Client Rating</b></label>
                        <select name="has_client_rating" class="form-control">
                            <option value="">All</option>
                            <option value="1" {{ request('has_client_rating') == '1' ? 'selected' : '' }}>Has Rating</option>
                            <option value="0" {{ request('has_client_rating') == '0' ? 'selected' : '' }}>No Rating</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label><b>Status</b></label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $status)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ route('ratings.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Assignees</th>
                                    <th>Task Rating</th>
                                    <th>Client Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ $task->prefix_task_number }}</strong><br>
                                            {{ Str::limit($task->title, 50) }}
                                        </td>
                                        <td>{{ $task->project->name }}</td>
                                        <td>
                                            @foreach($task->taskAssignee->take(3) as $assignee)
                                                <img src="{{ $assignee->img_avatar }}" class="rounded-circle" width="25" height="25" title="{{ $assignee->name }}">
                                            @endforeach
                                            @if($task->taskAssignee->count() > 3)
                                                <span class="badge badge-info">+{{ $task->taskAssignee->count() - 3 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->rating)
                                                <div class="rating-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $task->rating->average_rating ? 'rating-stars' : 'text-muted' }}"></i>
                                                    @endfor
                                                    <span class="ml-2">{{ number_format($task->rating->average_rating, 1) }}/5</span>
                                                </div>
                                                <small class="text-muted">by {{ $task->rating->ratedBy->name }}</small>
                                            @else
                                                <span class="text-muted">Not rated</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->clientRatings->count() > 0)
                                                @php $avgClientRating = $task->clientRatings->avg('rating'); @endphp
                                                <div class="rating-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $avgClientRating ? 'rating-stars' : 'text-muted' }}"></i>
                                                    @endfor
                                                    <span class="ml-2">{{ number_format($avgClientRating, 1) }}/5</span>
                                                </div>
                                                <small class="text-muted">{{ $task->clientRatings->count() }} rating(s)</small>
                                            @else
                                                <span class="text-muted">Not rated</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->status == 1 ? 'success' : 'warning' }}">
                                                {{ $statuses[$task->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if(!$task->rating && $task->status == 1)
                                                    <a href="{{ route('task-ratings.create', $task) }}" class="btn btn-sm btn-primary" title="Add Task Rating">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                @endif
                                                @if($task->rating)
                                                    <a href="{{ route('task-ratings.show', $task) }}" class="btn btn-sm btn-info" title="View Task Rating">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-sm btn-secondary" onclick="showRatingModal({{ $task->id }})" title="View Details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No tasks found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </section>

    <!-- Rating Details Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rating Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ratingModalContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function showRatingModal(taskId) {
    $('#ratingModal').modal('show');
    $('#ratingModalContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    $.get(`/ratings/task/${taskId}/details`)
        .done(function(data) {
            $('#ratingModalContent').html(data);
        })
        .fail(function() {
            $('#ratingModalContent').html('<div class="alert alert-danger">Error loading rating details</div>');
        });
}
</script>
@endsection