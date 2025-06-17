@extends('layouts.app')

@section('title')
    Create Client Rating
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                        <h3 class="card-title mb-2 mb-sm-0">Create New Client Rating</h3>
                        <a href="{{ route('client-ratings.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('client-ratings.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="task_id" class="form-label">Task *</label>
                                    
                                    <!-- Task Search Bar -->
                                    <div class="task-search-container mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                                            <input type="text" id="task-search" class="form-control" placeholder="Search completed and rated tasks...">
                                            <button type="button" id="clear-search" class="btn btn-outline-secondary">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Task Dropdown -->
                                    <select name="task_id" id="task_id" class="form-control @error('task_id') is-invalid @enderror" required>
                                        <option value="">Select Task</option>
                                        @foreach($tasks as $task)
                                            @if($task->status === 'completed' && $task->ratings->count() > 0)
                                                <option value="{{ $task->id }}" 
                                                        data-client-id="{{ $task->project->client_id }}" 
                                                        data-search-text="{{ strtolower($task->title . ' ' . $task->project->name) }}"
                                                        {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                                    {{ $task->title }} ({{ $task->project->name }}) 
                                                    <span class="text-muted">- {{ $task->ratings->count() }} rating(s)</span>
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    
                                    <!-- No Results Message -->
                                    <div id="no-tasks-message" class="text-muted mt-2" style="display: none;">
                                        <small><i class="fa fa-info-circle"></i> No completed and rated tasks found matching your search.</small>
                                    </div>
                                    
                                    @error('task_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="client_id" class="form-label">Client *</label>
                                    <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="rating" class="form-label">Rating (1-50) *</label>
                                    <div class="rating-input-container">
                                        <div class="rating-input">
                                            <input type="range" id="rating-slider" min="1" max="50" value="{{ old('rating', 25) }}" class="form-range">
                                            <input type="number" name="rating" id="rating" min="1" max="50" value="{{ old('rating', 25) }}" class="form-control rating-number @error('rating') is-invalid @enderror" required>
                                        </div>
                                        <div class="rating-display">
                                            <span class="rating-value"><span id="rating-value">{{ old('rating', 25) }}</span>/50</span>
                                            <div id="star-display" class="star-display"></div>
                                        </div>
                                    </div>
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea name="comment" id="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" placeholder="Add your comments about this task...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Rating
                            </button>
                            <a href="{{ route('client-ratings.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #dee2e6;
}

.rating-input {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

@media (min-width: 576px) {
    .rating-input {
        flex-direction: row;
        align-items: center;
        margin-bottom: 0.5rem;
    }
}

.rating-number {
    width: 100px;
    text-align: center;
    font-weight: bold;
}

.rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

@media (min-width: 576px) {
    .rating-display {
        flex-direction: row;
        align-items: center;
    }
}

.rating-value {
    font-weight: bold;
    font-size: 1.1em;
    color: #495057;
}

.star-display {
    font-size: 1.2em;
}

.form-range {
    flex: 1;
}

/* Task Search Styles */
.task-search-container {
    position: relative;
}

.task-search-container .input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

#task-search {
    border-color: #ced4da;
}

#task-search:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#clear-search {
    border-left: none;
}

.task-option-hidden {
    display: none !important;
}

#no-tasks-message {
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('rating-slider');
    const numberInput = document.getElementById('rating');
    const ratingValue = document.getElementById('rating-value');
    const starDisplay = document.getElementById('star-display');
    const taskSelect = document.getElementById('task_id');
    const clientSelect = document.getElementById('client_id');
    const taskSearch = document.getElementById('task-search');
    const clearSearch = document.getElementById('clear-search');
    const noTasksMessage = document.getElementById('no-tasks-message');

    // Store all task options for filtering
    const allTaskOptions = Array.from(taskSelect.options).slice(1); // Exclude the first "Select Task" option

    function updateRatingDisplay(value) {
        ratingValue.textContent = value;
        const stars = Math.round((value / 50) * 5);
        let starHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= stars) {
                starHtml += '<i class="fas fa-star text-warning"></i>';
            } else {
                starHtml += '<i class="far fa-star text-muted"></i>';
            }
        }
        starDisplay.innerHTML = starHtml;
    }

    // Task search functionality
    function filterTasks() {
        const searchTerm = taskSearch.value.toLowerCase().trim();
        let visibleCount = 0;

        allTaskOptions.forEach(option => {
            const searchText = option.dataset.searchText || '';
            const shouldShow = searchTerm === '' || searchText.includes(searchTerm);
            
            if (shouldShow) {
                option.style.display = '';
                option.classList.remove('task-option-hidden');
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.classList.add('task-option-hidden');
            }
        });

        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noTasksMessage.style.display = 'block';
        } else {
            noTasksMessage.style.display = 'none';
        }

        // Reset task selection if current selection is hidden
        if (taskSelect.value && taskSelect.options[taskSelect.selectedIndex].classList.contains('task-option-hidden')) {
            taskSelect.value = '';
            clientSelect.value = '';
        }
    }

    // Clear search functionality
    function clearTaskSearch() {
        taskSearch.value = '';
        filterTasks();
        taskSearch.focus();
    }

    // Event listeners
    taskSearch.addEventListener('input', filterTasks);
    taskSearch.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            clearTaskSearch();
        }
    });
    
    clearSearch.addEventListener('click', clearTaskSearch);

    slider.addEventListener('input', function() {
        numberInput.value = this.value;
        updateRatingDisplay(this.value);
    });

    numberInput.addEventListener('input', function() {
        slider.value = this.value;
        updateRatingDisplay(this.value);
    });

    // Auto-select client when task is selected
    taskSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.clientId) {
            clientSelect.value = selectedOption.dataset.clientId;
        }
    });

    // Initialize display
    updateRatingDisplay(numberInput.value);
    
    // Initialize task filtering
    filterTasks();
});
</script>
@endsection