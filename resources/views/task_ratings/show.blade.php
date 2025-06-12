@extends('layouts.app')

@section('title')
    Task Rating Details
@endsection

@section('css')
    <style>
        .rating-stars {
            color: #ffc107;
            font-size: 18px;
        }
        .task-info, .rating-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .rating-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .rating-item:last-child {
            border-bottom: none;
        }
        .rating-value {
            font-weight: bold;
            color: #28a745;
        }
        .overall-rating {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Task Rating Details</h5>
                        <div>
                            @role('admin')
                                <a href="{{ route('task-ratings.edit', $task) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Rating
                                </a>
                            @endrole
                            <a href="{{ route('task-ratings.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($rating->is_edited)
                            <div class="edit-info">
                                <h6><i class="fas fa-edit"></i> This rating has been edited</h6>
                                <p class="mb-0">
                                    <strong>Last edited by:</strong> {{ $rating->editedBy->name ?? 'Unknown' }}<br>
                                    <strong>Last edited on:</strong> {{ $rating->edited_at ? $rating->edited_at->format('M d, Y \\a\\t h:i A') : 'N/A' }}
                                </p>
                            </div>
                        @endif

                        <div class="overall-rating">
                            <h3>Overall Rating</h3>
                            <h1>{{ $rating->average_rating }}/5</h1>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="task-info">
                                    <h6>Task Information</h6>
                                    <p><strong>Task #:</strong> {{ $task->task_number }}</p>
                                    <p><strong>Title:</strong> {{ $task->title }}</p>
                                    <p><strong>Project:</strong> {{ $task->project->name ?? 'N/A' }}</p>
                                    <p><strong>Assignee(s):</strong> 
                                        @if($task->taskAssignee->count() > 0)
                                            @foreach($task->taskAssignee as $assignee)
                                                <span class="badge badge-primary mr-1">{{ $assignee->name }}</span>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p><strong>Completed On:</strong> {{ $task->completed_on ? $task->completed_on->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rating-details">
                                    <h6>Rating Breakdown</h6>
                                    @foreach($criteria as $key => $label)
                                        <div class="rating-item">
                                            <span>{{ $label }}</span>
                                            <div>
                                                <span class="rating-value">{{ $rating->$key }}/5</span>
                                                <div class="rating-stars ml-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating->$key)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($rating->comments)
                            <div class="rating-details">
                                <h6>Comments</h6>
                                <p>{{ $rating->comments }}</p>
                            </div>
                        @endif

                        <div class="rating-details">
                            <h6>Rating Information</h6>
                            <p><strong>Rated by:</strong> {{ $rating->ratedBy->name ?? 'Unknown' }}</p>
                            <p><strong>Rated on:</strong> {{ $rating->created_at ? $rating->created_at->format('M d, Y \\a\\t h:i A') : 'N/A' }}</p>
                            @if($rating->is_edited)
                                <p><strong>Last edited by:</strong> {{ $rating->editedBy->name ?? 'Unknown' }}</p>
                                <p><strong>Last edited on:</strong> {{ $rating->edited_at ? $rating->edited_at->format('M d, Y \\a\\t h:i A') : 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection