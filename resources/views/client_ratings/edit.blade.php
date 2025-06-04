@extends('layouts.app')

@section('title')
    Edit Client Rating
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                        <h3 class="card-title mb-2 mb-sm-0">Edit Client Rating</h3>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyTaskLink('{{ route('tasks.show', $clientRating->task->id) }}')" title="Copy Task Link">
                                <i class="fa fa-link"></i> <span class="d-none d-sm-inline">Copy Task Link</span>
                            </button>
                            <a href="{{ route('client-ratings.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Task Information -->
                    <div class="alert alert-info">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <strong>Task:</strong> {{ $clientRating->task->title }}
                            </div>
                            <div class="col-12 col-md-6">
                                <strong>Project:</strong> {{ $clientRating->task->project->name }}
                            </div>
                            <div class="col-12 col-md-6">
                                <strong>Client:</strong> {{ $clientRating->client->name }}
                            </div>
                            <div class="col-12 col-md-6">
                                <strong>Originally Rated By:</strong> {{ $clientRating->ratedBy->name }}
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('client-ratings.update', $clientRating) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="rating" class="form-label">Rating (1-50) *</label>
                                    <div class="rating-input-container">
                                        <div class="rating-input">
                                            <input type="range" id="rating-slider" min="1" max="50" value="{{ old('rating', $clientRating->rating) }}" class="form-range">
                                            <input type="number" name="rating" id="rating" min="1" max="50" value="{{ old('rating', $clientRating->rating) }}" class="form-control rating-number @error('rating') is-invalid @enderror" required>
                                        </div>
                                        <div class="rating-display">
                                            <span class="rating-value"><span id="rating-value">{{ old('rating', $clientRating->rating) }}</span>/50</span>
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
                                    <textarea name="comment" id="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" placeholder="Add your comments about this task...">{{ old('comment', $clientRating->comment) }}</textarea>
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
                                <i class="fa fa-save"></i> Update Rating
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
function copyTaskLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        var toast = new bootstrap.Toast(document.getElementById('linkToast'));
        toast.show();
    }).catch(function(err) {
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

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('rating-slider');
    const numberInput = document.getElementById('rating');
    const ratingValue = document.getElementById('rating-value');
    const starDisplay = document.getElementById('star-display');

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

    // Initialize display
    updateRatingDisplay(numberInput.value);
});
</script>
@endsection