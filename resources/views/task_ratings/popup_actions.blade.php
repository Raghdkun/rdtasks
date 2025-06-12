@if(getLoggedInUser()->hasRole('Admin'))
    @if(!$task->rating && $task->status == 1)
        <a href="{{ route('task-ratings.create', $task) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-star"></i> Add Task Rating
        </a>
    @endif

    @if($task->rating)
        <a href="{{ route('task-ratings.show', $task) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> View Details
        </a>
        <a href="{{ route('task-ratings.edit', $task) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i> Edit Rating
        </a>
    @endif

    <a href="{{ route('client-ratings.create') }}?task_id={{ $task->id }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus"></i> Add Client Rating
    </a>
@else
    @if($task->rating)
        <a href="{{ route('task-ratings.show', $task) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> View Details
        </a>
    @endif
@endif