<div class="row col-12 d-flex flex-nowrap pb-3">
    @foreach($taskStatus as $index => $ob)
        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card board">
                <div class="card-header bg-light border-0">
                    <h4 class="text-primary">{{ html_entity_decode($ob->name) }}</h4>
                </div>
                <div class="card-body p-2 bg-light">
                    <div class="infy-loader overlay-screen-lock" style="display: none">
                        @include('loader')
                    </div>
                    <div class="board-{{$index}}" data-board-status="{{ $ob->status }}">
                        @foreach($allTasks as $task)
                            @if($task->status != $ob->status)
                                @continue;
                            @endif
                            <div class="card mb-3 " data-id="{{ $task->id }}" data-status="{{ $ob->name }}"
                                 data-task-status="{{ $task->status }}">
                                <div class="card-body p-3 no-touch">
                                    <a href="#" class="mb-0 task-details text-primary showTaskKanbanDetails"
                                       data-id="{{ $task->id }}">{{ html_entity_decode($task->title) }}</a>
                                    <div class="task-footer d-flex align-items-center justify-content-between row">
                                        <div class="avatar-container col-xs-12 ml-2">
                                            @foreach($task->taskAssignee as $counter => $assignee)
                                                @if($counter < 4)
                                                    <figure class="avatar mr-2 avatar-sm" data-toggle="tooltip"
                                                            title="{{ html_entity_decode($assignee->name) }}">
                                                        <img src="{{ $assignee->img_avatar }}">
                                                    </figure>
                                                    @elseif($counter == $task->taskAssignee->count() - 1)
                                                        <div class="dropdown">
                                                            <figure class="avatar mr-2 avatar-sm more-avatar">
                                                                +{{ $task->taskAssignee->count() - 4 }}
                                                            </figure>
                                                        </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="pr-2">
                                            <div class="tracked-time text-right">
                                                {{ $task->taskDuration }}
                                            </div>
                                            <div class="col-xs-12 ml-1">
                                                <div class="d-flex justify-content-end">
                                                    <div class="due-date {{ Carbon\Carbon::now()->startOfDay() > Carbon\Carbon::parse($task->due_date)  ? 'text-danger' : '' }}">
                                                        @if(Carbon\Carbon::parse($task->due_date) == Carbon\Carbon::now()->startOfDay())
                                                            {{ __('messages.task.today') }}
                                                        @elseif(Carbon\Carbon::parse($task->due_date)->isTomorrow())
                                                            {{ __('messages.task.tomorrow') }}
                                                        @else
                                                            @if(Carbon\Carbon::now()->format('Y') ==  Carbon\Carbon::parse($task->due_date)->format(' Y') )
                                                                {{ Carbon\Carbon::parse($task->due_date)->translatedFormat('jS M') }}
                                                            @else
                                                                {{ Carbon\Carbon::parse($task->due_date)->translatedFormat('jS M, Y') }}
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="attachments ml-2">
                                                        <i class="fas fa-paperclip"></i>
                                                        {{ $task->media->count() }}
                                                    </div>
                                                    <div class="comments ml-3">
                                                        <i class="far fa-comment-alt"></i>
                                                        {{ $task->comments->count() }}
                                                    </div>
                                                    
                                                    <!-- Add Rating Icon -->
                                                    @php
                                                        $hasTaskRating = $task->rating()->exists();
                                                        $hasClientRating = $task->clientRatings()->exists();
                                                        $ratingClass = 'has-none';
                                                        $ratingTitle = 'No ratings';
                                                        
                                                        if ($hasTaskRating && $hasClientRating) {
                                                            $ratingClass = 'has-both';
                                                            $ratingTitle = 'Has both ratings';
                                                        } elseif ($hasTaskRating || $hasClientRating) {
                                                            $ratingClass = 'has-partial';
                                                            $ratingTitle = $hasTaskRating ? 'Task rating only' : 'Client rating only';
                                                        }
                                                    @endphp
                                                    
                                                    <div class="ratings ml-3">
                                                        <a href="#" class="rating-icon {{ $ratingClass }}" 
                                                           onclick="showTaskRatingPopup({{ $task->id }})"
                                                           title="{{ $ratingTitle }}" data-toggle="tooltip">
                                                            <i class="fas fa-star"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
