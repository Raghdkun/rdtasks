<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $client->name }} - Task Ratings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rating-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .rating-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .star-rating {
            font-size: 1.2em;
        }
        .rating-badge {
            font-size: 1.1em;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }
            .display-4 {
                font-size: 2rem;
            }
        }
        .rating-progress {
            height: 8px;
            border-radius: 4px;
        }
        .rating-score {
            font-size: 2rem;
            font-weight: bold;
        }
        @media (max-width: 576px) {
            .rating-score {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <h1 class="display-4 mb-3">{{ $client->name }}</h1>
                        <p class="lead mb-4">Task Performance Ratings</p>
                        @if($totalRatings > 0)
                            <div class="row justify-content-center">
                                <div class="col-12 col-md-8 col-lg-6">
                                    <div class="card bg-white bg-opacity-90">
                                        <div class="card-body">
                                            <h5 class="text-dark mb-3">Overall Performance</h5>
                                            <div class="rating-score text-primary">{{ number_format($averageRating, 1) }}/50</div>
                                            <div class="star-rating mt-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= round(($averageRating / 50) * 5))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="progress rating-progress mt-3">
                                                <div class="progress-bar bg-success" style="width: {{ ($averageRating / 50) * 100 }}%"></div>
                                            </div>
                                            <small class="text-muted mt-2 d-block">Based on {{ $totalRatings }} task(s)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ratings Section -->
    <div class="container py-5">
        <div class="row">
            @forelse($ratings as $rating)
                <div class="col-12 col-md-6 col-xl-4 mb-4">
                    <div class="card rating-card h-100">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $rating->task->title }}</h5>
                                    <small class="opacity-75">Project: {{ $rating->task->project->name }}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-light ms-2" onclick="copyTaskLink('{{ route('tasks.show', $rating->task->id) }}')" title="Copy Task Link">
                                    <i class="fa fa-link"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="rating-score text-success mb-2">{{ $rating->rating }}/50</div>
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round(($rating->rating / 50) * 5))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="progress rating-progress mt-2">
                                    <div class="progress-bar bg-success" style="width: {{ ($rating->rating / 50) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            @if($rating->comment)
                                <div class="mt-3">
                                    <h6 class="text-muted mb-2"><i class="fa fa-comment me-1"></i> Comments:</h6>
                                    <p class="text-dark">{{ $rating->comment }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light text-muted">
                            <div class="d-flex justify-content-between align-items-center">
                                <small><i class="fa fa-calendar me-1"></i> {{ $rating->created_at->format('M d, Y') }}</small>
                                @if($rating->edited_at)
                                    <small class="text-info"><i class="fa fa-edit me-1"></i> Updated {{ $rating->edited_at->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fa fa-star-o fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Ratings Yet</h4>
                        <p class="text-muted">No task ratings have been submitted for {{ $client->name }} yet.</p>
                    </div>
                </div>
            @endforelse
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html>