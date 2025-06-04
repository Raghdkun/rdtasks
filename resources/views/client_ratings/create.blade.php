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
                                    <select name="task_id" id="task_id" class="form-control @error('task_id') is-invalid @enderror" required>
                                        <option value="">Select Task</option>
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" data-client-id="{{ $task->project->client_id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                                {{ $task->title }} ({{ $task->project->name }})
                                            </option>
                                        @endforeach
                                    </select>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('rating-slider');
    const numberInput = document.getElementById('rating');
    const ratingValue = document.getElementById('rating-value');
    const starDisplay = document.getElementById('star-display');
    const taskSelect = document.getElementById('task_id');
    const clientSelect = document.getElementById('client_id');

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
});
</script>
@endsection