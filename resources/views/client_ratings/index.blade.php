@extends('layouts.app')

@section('title')
    Client Ratings
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h3 class="card-title mb-2 mb-md-0">Client Ratings</h3>
                        <a href="{{ route('client-ratings.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> <span class="d-none d-sm-inline">Add New Rating</span>
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <select name="client_id" class="form-control">
                                    <option value="">All Clients</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <input type="text" name="task_name" class="form-control" placeholder="Task Name" value="{{ request('task_name') }}">
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <input type="number" name="rating_min" class="form-control" placeholder="Min Rating" min="1" max="50" value="{{ request('rating_min') }}">
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <input type="number" name="rating_max" class="form-control" placeholder="Max Rating" min="1" max="50" value="{{ request('rating_max') }}">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-info flex-fill">Filter</button>
                                    <a href="{{ route('client-ratings.index') }}" class="btn btn-secondary flex-fill">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Mobile Card View -->
                    <div class="d-block d-lg-none">
                        @forelse($clientRatings as $rating)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $rating->task->title }}</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('client-ratings.edit', $rating) }}"><i class="fa fa-edit"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="copyTaskLink('{{ route('tasks.show', $rating->task->id) }}')"><i class="fa fa-link"></i> Copy Task Link</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('client-ratings.destroy', $rating) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $rating->task->project->name }} • {{ $rating->client->name }}</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2">{{ $rating->rating }}/50</span>
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round(($rating->rating / 50) * 5))
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rating->comment)
                                        <p class="small text-muted mb-2">{{ Str::limit($rating->comment, 100) }}</p>
                                    @endif
                                    <small class="text-muted">By {{ $rating->ratedBy->name }} • {{ $rating->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info text-center">
                                <h5>No Ratings Found</h5>
                                <p class="mb-0">No client ratings match your current filters.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Rated By</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientRatings as $rating)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $rating->task->title }}</span>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyTaskLink('{{ route('tasks.show', $rating->task->id) }}')" title="Copy Task Link">
                                                    <i class="fa fa-link"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ $rating->task->project->name }}</td>
                                        <td>{{ $rating->client->name }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $rating->rating }}/50</span>
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= round(($rating->rating / 50) * 5))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($rating->comment, 50) }}</td>
                                        <td>{{ $rating->ratedBy->name }}</td>
                                        <td>{{ $rating->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('client-ratings.edit', $rating) }}" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('client-ratings.destroy', $rating) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No client ratings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $clientRatings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast for notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="linkToast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fa fa-link text-primary me-2"></i>
            <strong class="me-auto">Link Copied</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Task link has been copied to clipboard!
        </div>
    </div>
</div>

<script>
function copyTaskLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Show toast notification
        var toast = new bootstrap.Toast(document.getElementById('linkToast'));
        toast.show();
    }).catch(function(err) {
        // Fallback for older browsers
        var textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        var toast = new bootstrap.Toast(document.getElementById('linkToast'));
        toast.show();
    });
}
</script>
@endsection