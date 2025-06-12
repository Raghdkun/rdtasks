<div class="task-rating-popup">
    <h6><strong>{{ $task->prefix_task_number }}: {{ $task->title }}</strong></h6>
    <p class="text-muted">Project: {{ $task->project->name }}</p>
    
    <div class="row">
        <div class="col-md-6">
            <h6>Task Rating</h6>
            @if($task->rating)
                <div class="rating-details">
                    <div class="rating-display mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $task->rating->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ml-2"><strong>{{ number_format($task->rating->average_rating, 1) }}/5</strong></span>
                    </div>
                    <small class="text-muted">Rated by: {{ $task->rating->ratedBy->name }}</small>
                    <div class="mt-2">
                        <small>
                            <strong>Breakdown:</strong><br>
                            Code Quality: {{ $task->rating->code_quality }}/5<br>
                            Delivery: {{ $task->rating->delivery_output }}/5<br>
                            Time Management: {{ $task->rating->time_score }}/5<br>
                            Collaboration: {{ $task->rating->collaboration }}/5<br>
                            Complexity Handling: {{ $task->rating->complexity_urgency }}/5
                        </small>
                    </div>
                    @if($task->rating->comments)
                        <div class="mt-2">
                            <strong>Comments:</strong>
                            <p class="text-muted">{{ $task->rating->comments }}</p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-muted">No task rating available</p>
            @endif
        </div>
        
        <div class="col-md-6">
            <h6>Client Ratings</h6>
            @if($task->clientRatings->count() > 0)
                @foreach($task->clientRatings as $clientRating)
                    <div class="client-rating mb-3">
                        <div class="rating-display mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $clientRating->rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="ml-2"><strong>{{ $clientRating->rating }}/5</strong></span>
                        </div>
                        <small class="text-muted">
                            Client: {{ $clientRating->client->name }}<br>
                            Rated by: {{ $clientRating->ratedBy->name }}
                        </small>
                        @if($clientRating->comment)
                            <p class="text-muted mt-1">{{ $clientRating->comment }}</p>
                        @endif
                    </div>
                @endforeach
            @else
                <p class="text-muted">No client ratings available</p>
            @endif
        </div>
    </div>
</div>