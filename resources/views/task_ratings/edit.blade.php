@extends('layouts.app')

@section('title')
    Edit Task Rating
@endsection

@section('css')
    <style>
        .star-rating {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }
        .star {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star.active {
            color: #ffc107;
        }
        .star:hover {
            color: #ffc107;
        }
        .rating-value {
            margin-left: 10px;
            font-weight: bold;
            color: #28a745;
        }
        .task-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
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
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Task Rating</h5>
                    </div>
                    <div class="card-body">
                        @if($rating->is_edited)
                            <div class="edit-info">
                                <h6><i class="fas fa-info-circle"></i> Edit History</h6>
                                <p class="mb-0">
                                    <strong>Last edited by:</strong> {{ $rating->editedBy->name ?? 'Unknown' }}<br>
                                    <strong>Last edited on:</strong> {{ $rating->edited_at ? $rating->edited_at->format('M d, Y \\a\\t h:i A') : 'N/A' }}
                                </p>
                            </div>
                        @endif

                        <div class="task-info">
                            <h6>Task Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Task #:</strong> {{ $task->task_number }}</p>
                                    <p><strong>Title:</strong> {{ $task->title }}</p>
                                    <p><strong>Project:</strong> {{ $task->project->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Assignee(s):</strong> 
                                        @if($task->taskAssignee->count() > 0)
                                            @foreach($task->taskAssignee as $assignee)
                                                <span class="badge badge-info">{{ $assignee->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </p>
                                    <p><strong>Completed On:</strong> {{ $task->completed_on ? $task->completed_on->format('M d, Y') : 'N/A' }}</p>
                                    <p><strong>Current Average:</strong> <span class="badge badge-success">{{ $rating->average_rating }}/10</span></p>
                                </div>
                            </div>
                        </div>

                        <form id="ratingForm">
                            @csrf
                            @method('PUT')
                            
                            @foreach($criteria as $key => $label)
                                <div class="form-group">
                                    <label for="{{ $key }}">{{ $label }} (1-5 scale)</label>
                                    <div class="star-rating" data-rating="{{ $key }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $rating->$key ? 'active' : '' }}" data-value="{{ $i }}">★</span>
                                        @endfor
                                        <span class="rating-value">{{ $rating->$key }}/5</span>
                                    </div>
                                    <input type="hidden" name="{{ $key }}" value="{{ $rating->$key }}" id="{{ $key }}">
                                </div>
                            @endforeach

                            <div class="form-group">
                                <label for="comments">Comments (Optional)</label>
                                <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Additional comments about the task performance...">{{ $rating->comments }}</textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Rating
                                </button>
                                <a href="{{ route('task-ratings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <a href="{{ route('task-ratings.show', $task) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Rating
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Star rating functionality
            $('.star-rating').each(function() {
                const $rating = $(this);
                const ratingKey = $rating.data('rating');
                const $stars = $rating.find('.star');
                const $valueDisplay = $rating.find('.rating-value');
                const $hiddenInput = $(`#${ratingKey}`);

                $stars.on('click', function() {
                    const value = $(this).data('value');
                    $hiddenInput.val(value);
                    $valueDisplay.text(`${value}/5`);
                    
                    $stars.removeClass('active');
                    $stars.slice(0, value).addClass('active');
                });

                $stars.on('mouseenter', function() {
                    const value = $(this).data('value');
                    $stars.removeClass('active');
                    $stars.slice(0, value).addClass('active');
                });

                $rating.on('mouseleave', function() {
                    const currentValue = $hiddenInput.val();
                    $stars.removeClass('active');
                    $stars.slice(0, currentValue).addClass('active');
                });
            });

            // Form submission
            $('#ratingForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: '{{ route("task-ratings.update", $task) }}',
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Rating updated successfully!');
                            setTimeout(() => {
                                window.location.href = '{{ route("task-ratings.show", $task) }}';
                            }, 1500);
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.values(errors).forEach(errorArray => {
                                errorArray.forEach(error => {
                                    toastr.error(error);
                                });
                            });
                        } else {
                            toastr.error('An error occurred while updating the rating');
                        }
                    }
                });
            });
        });
    </script>
@endsection