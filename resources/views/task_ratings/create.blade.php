@extends('layouts.app')
@section('title')
    Rate Task
@endsection
@section('css')
    <style>
        .rating-container {
            margin-bottom: 1.5rem;
        }
        .rating-stars {
            display: flex;
            gap: 0.25rem;
            margin-top: 0.5rem;
            align-items: center;
        }
        .rating-star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .rating-star.active,
        .rating-star:hover {
            color: #ffc107;
        }
        .rating-value {
            margin-left: 10px;
            font-weight: bold;
            color: #495057;
        }
        .task-info {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Rate Task</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('task-ratings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Ratings
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-body">
                            <!-- Task Information -->
                            <div class="task-info">
                                <h5>{{ $task->title }}</h5>
                                <p class="text-muted mb-2">{{ $task->description }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Project:</strong> {{ $task->project->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Completed On:</strong> {{ $task->completed_on ? $task->completed_on->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Assignees:</strong>
                                    @foreach($task->taskAssignee as $assignee)
                                        <span class="badge badge-info">{{ $assignee->name }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Rating Form -->
                            <form id="ratingForm" action="{{ route('task-ratings.store', $task) }}" method="POST">
                                @csrf
                                
                                @foreach($criteria as $key => $label)
                                    <div class="rating-container">
                                        <label class="form-label font-weight-bold">{{ $label }}</label>
                                        <div class="rating-stars" data-rating="{{ $key }}">
                                            @for($i = 1; $i <= 10; $i++)
                                                <span class="rating-star" data-value="{{ $i }}">★</span>
                                            @endfor
                                            <span class="rating-value">0/10</span>
                                        </div>
                                        <input type="hidden" name="{{ $key }}" id="{{ $key }}" value="0" required>
                                        <small class="text-muted">Click stars to rate (1-10)</small>
                                    </div>
                                @endforeach

                                <div class="form-group">
                                    <label for="comments" class="form-label font-weight-bold">Comments (Optional)</label>
                                    <textarea name="comments" id="comments" class="form-control" rows="4" 
                                              placeholder="Add any additional comments about this task..."></textarea>
                                </div>

                                <div class="text-right">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-star"></i> Submit Rating
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle star rating clicks
    $('.rating-stars').on('click', '.rating-star', function() {
        const container = $(this).parent();
        const ratingKey = container.data('rating');
        const value = $(this).data('value');
        
        // Update hidden input
        $(`#${ratingKey}`).val(value);
        
        // Update star display
        container.find('.rating-star').each(function(index) {
            if (index < value) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
        
        // Update rating value display
        container.find('.rating-value').text(value + '/10');
    });
    
    // Handle star hover effects
    $('.rating-stars').on('mouseenter', '.rating-star', function() {
        const container = $(this).parent();
        const value = $(this).data('value');
        
        container.find('.rating-star').each(function(index) {
            if (index < value) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
        
        // Show hover value
        container.find('.rating-value').text(value + '/10');
    });
    
    $('.rating-stars').on('mouseleave', function() {
        const container = $(this);
        const ratingKey = container.data('rating');
        const currentValue = $(`#${ratingKey}`).val();
        
        container.find('.rating-star').each(function(index) {
            if (index < currentValue) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
        
        // Restore actual value
        container.find('.rating-value').text(currentValue + '/10');
    });
    
    // Form submission
    $('#ratingForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate all ratings are selected
        let allRated = true;
        @foreach($criteria as $key => $label)
            if (!$('#{{ $key }}').val() || $('#{{ $key }}').val() == '0') {
                allRated = false;
            }
        @endforeach
        
        if (!allRated) {
            alert('Please rate all criteria before submitting.');
            return;
        }
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("task-ratings.index") }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-star"></i> Submit Rating');
            }
        });
    });
});
</script>
@endsection