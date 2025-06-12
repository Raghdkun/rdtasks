<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        @if($totalTasks != 0)
            <div class="pr-0 pl-2 pt-2 pb-2 mb-3">
                <input wire:model.debounce.100ms="search" type="search" class="form-control mt-29px"
                       placeholder="{{ __('messages.common.search') }}"
                       id="search">
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div wire:loading id="live-wire-screen-lock">
            <div class="live-wire-infy-loader">
                @include('loader')
            </div>
        </div>
    </div>
    <div class="col-12 px-sm-0">
        <div class="accordion task-list" id="accordionExampleOne">
            <div id="collapseParentOne" class="collapse show" aria-labelledby="headingParentOne"
                 data-parent="#accordionExampleOne">
                <div class="card-body p-2">
                    <div class="accordion task-list" id="accordionExampleChildOne">
                        @forelse($tasks as $index => $task)
                            <div class="card mb-0 task-item {{ $loop->odd ? "task-index-odd-column" : "task-index-even-column"}} ">
                                <div class="card-header border-bottom justify-content-between"
                                     id="heading{{ $index }}">
                                    <div>
                                        <label class="check">
                                            @if($task->status == 1)
                                                <input type="checkbox" class="complete-task-checkbox" checked
                                                       name="yes" data-check="{{ $task->id }}">
                                            @else
                                                <input type="checkbox" class="complete-task-checkbox" name="no"
                                                       data-check="{{ $task->id }}">
                                            @endif
                                            <div class="box"></div>
                                        </label>
                                        <input type="text" class="task-input display-none ml-2"
                                               data-id="{{ $task->id }}">
                                        <p class="task-name mb-0 ml-2">{{ html_entity_decode($task->title) }} </p>
                                        <span class="d-lg-inline-block d-none taskDetails cursor-pointer"
                                              data-toggle="modal" data-target="#taskDetailsModal"
                                              data-id="{{$task->id}}"> ||
                                                <small class="task-duration text-primary"
                                                       title="{{($task->taskHours != "0") ? $task->taskHours:'0 Minutes' }}">{{ $task->taskDuration }}</small>
                                            </span>
                                        <span class="d-lg-inline-block d-none">||
                                                <div class="d-inline-block">
                                                    @foreach($task->taskAssignee as $counter => $assignee)
                                                        @if($counter < 5)
                                                            <img class="assignee__avatar"
                                                                 src="{{ $assignee->img_avatar }}"
                                                                 title="{{ html_entity_decode($assignee->name) }}">
                                                        @elseif($counter == (count($task->taskAssignee) - 1))
                                                            <span class="task_remaining_assignee"><span
                                                                        style="font-size: 12px;">+{{ (count($task->taskAssignee) - 5) }}</span></span>
                                                        @endif
                                                    @endforeach
                                                    <a href="javascript:void(0)" data-id="{{ $task->id }}"
                                                       class="edit-task-assignees" title="{{ __('messages.common.edit').' '.__('messages.task.assignee') }}"><img class="assignee__avatar p-1"
                                                                                        src="{{ asset('assets/img/add.svg') }}"></a>
                                                </div>
                                            </span>
                                    </div>
                                    <div class="right-side-content">
                                        <a href="{{ url('tasks',$task->project->prefix.-$task->task_number) }}"
                                           class=" float-right">
                                            <img class="task-arrow-img"
                                                 src="{{ asset('assets/img/next.png') }}">
                                        </a>
                                        <span class="float-right ml-2">
                                                <a class="dropdown dropdown-list-toggle">
                                                <a href="#" data-toggle="dropdown" class="notification-toggle mx-2">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <div class="dropdown-list-content dropdown-list-icons">

                                                        <a class="collapsed dropdown-item dropdown-item-desc editRecord"
                                                           data-toggle="collapse"
                                                           data-target="#collapse{{ $index }}" aria-expanded="true"
                                                           aria-controls="collapse{{ $index }}"
                                                           data-record-index="{{ $index }}" href="#">
                                                            <i class="fas fa-edit card-edit-icon mr-2"></i>{{ __('messages.common.edit') }}
                                                        </a>

                                                        @if($task->status != \App\Models\Task::$status['STATUS_COMPLETED'])
                                                            <a href="#"
                                                               class="dropdown-item dropdown-item-desc timeEntryAddModal addTaskTimeEntry"
                                                               data-id="{{ $task->id }}"
                                                               data-project-id={{$task->project->id}}>
                                                                <i class="fas fa-clock fa-task-clock mr-2 task-add-time-entry"></i>{{ __('messages.task.add_time_entry') }}
                                                            </a>
                                                        @endif

                                                        <a href="#"
                                                           class="dropdown-item dropdown-item-desc delete-recent-task"
                                                           data-id="{{ $task->id }}">
                                                            <i class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </a>
                                            </span>
                                        @if(!empty($task->due_date))
                                            <span class="float-right task-date-mb due-date-wrapper {{ Carbon\Carbon::now()->startOfDay() > Carbon\Carbon::parse($task->due_date)  ? 'text-danger' : '' }}">
                                             <input type="text" data-id="{{ $task->id }}"
                                                    class="form-control cursor-pointer float-right editDueDate"
                                                    value="{{ Carbon\Carbon::parse($task->due_date)->format('jS M, Y') }}"
                                                    autocomplete="off">
                                               @if(Carbon\Carbon::parse($task->due_date) == Carbon\Carbon::now()->startOfDay())
                                                    {{__('messages.task.today')}}
                                                @elseif(Carbon\Carbon::parse($task->due_date)->isTomorrow())
                                                    {{__('messages.task.tomorrow')}}
                                                @else
                                                    @if(Carbon\Carbon::now()->format('Y') ==  Carbon\Carbon::parse($task->due_date)->format(' Y') )
                                                        {{ Carbon\Carbon::parse($task->due_date)->translatedFormat('jS M') }}
                                                    @else
                                                        {{ Carbon\Carbon::parse($task->due_date)->translatedFormat('jS M, Y') }}
                                                    @endif
                                                @endif
                                        </span>
                                        @else
                                            <span class="float-right cursor-pointer task-date-mb due-date-wrapper">
                                                <input type="text" data-id="{{ $task->id }}"
                                                       class="form-control cursor-pointer float-right editDueDate pr-2"
                                                       autocomplete="off">
                                                <i class="fa fa-calendar-alt text-primary"></i></span>
                                        @endif

                                        <a href="#" class="mx-2 task-details" data-id="{{$task->id}}"
                                           title="{{__('messages.common.details')}}">
                                            <i class="fas fa-info"></i>
                                        </a>
                                        
                                        <!-- Add Rating Icon -->
                                        @php
                                            $hasTaskRating = $task->rating()->exists();
                                            $hasClientRating = $task->clientRatings()->exists();
                                            $ratingClass = 'has-none';
                                            $ratingTitle = 'No ratings';
                                            
                                            if ($hasTaskRating && $hasClientRating) {
                                                $ratingClass = 'has-both';
                                                $ratingTitle = 'Has both task and client ratings';
                                            } elseif ($hasTaskRating || $hasClientRating) {
                                                $ratingClass = 'has-partial';
                                                $ratingTitle = $hasTaskRating ? 'Has task rating only' : 'Has client rating only';
                                            }
                                        @endphp
                                        
                                        <a href="#" class="rating-icon {{ $ratingClass }}" 
                                           onclick="showTaskRatingPopup({{ $task->id }})"
                                           title="{{ $ratingTitle }}" data-toggle="tooltip">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <div class="d-sm-inline-block d-lg-none">
                                            <a href="#" data-toggle="dropdown"
                                               class="more-info badge badge-info mr-3 p-2">
                                                {{__('messages.common.more')}}
                                            </a>
                                            <div class="dropdown-menu more-info-menu dropdown-menu-right">
                                                <a href="#" class="dropdown-item">
                                                    <div class="d-block d-flex align-items-center w-100">
                                                        @foreach($task->taskAssignee as $counter => $assignee)
                                                            @if($counter < 7)
                                                                <img class="assignee__avatar"
                                                                     src="{{ $assignee->img_avatar }}"
                                                                     title="{{ $assignee->name }}">
                                                            @elseif($counter == (count($task->taskAssignee)) - 1)
                                                                <span class="tasks_remaining_user"><small>+{{ (count($task->taskAssignee) - 7) }} </small></span>
                                                            @endif
                                                        @endforeach
                                                        <span data-id="{{ $task->id }}"
                                                              class="edit-task-assignees">
                                                                    <img class="assignee__avatar p-1"
                                                                         src="{{ asset('assets/img/add.svg') }}"></span>
                                                    </div>
                                                </a>
                                                <a href="" class="dropdown-item">
                                                        <span class="task-duration text-muted" data-toggle="tooltip"
                                                              data-placement="bottom"
                                                              title="{{ $task->taskHours }}">{{ $task->taskDuration }}</span>
                                                </a>
                                                <a href="" class="dropdown-item">
                                                        <span
                                                                class="task-duration text-muted">{{ __('messages.task.project_name') }} {{ !empty($task->project) ? html_entity_decode($task->project->name) : '' }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        @php
                                            $inStyle = 'style';
                                            $bgColor = 'background-color';
                                        @endphp
                                        <a href="{{ getLoggedInUser()->can('manage_projects') ? route('projects.show',$task->project->id) : route('user.projects.show',$task->project->id) }}"
                                           target="_blank"><span
                                                    class="badge {{ RGBToHSL(HTMLToRGB($task->project->color))->lightness > 125 ? 'text-dark' : 'text-white'}} text-white p-2  mr-2 mt-1 float-right d-lg-inline-block d-none" {{$inStyle}}
                                            ="{{$bgColor}} :{{ $task->project->color }}">
                                            {{ !empty($task->project) ? html_entity_decode($task->project->name) : '' }}
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div id="collapse{{ $index }}" class="collapse task-detail"
                                     aria-labelledby="heading{{ $index }}" data-parent="#accordionExampleChildOne">
                                    <div class="card-body p-2">
                                        {{ Form::open(['class'=>'editForm']) }}
                                        {{ Form::hidden('task_id', $task->id, ['class'=>'taskId']) }}
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-6 col-lg-4">
                                                {{ Form::label('priorities',  __('messages.task.priority').':') }}
                                                {{ Form::select('priority', $priority, !empty($task->priority) ?    $task->priority: null, ['class' => 'form-control editPriority', 'data-edit-priority' => $index,'placeholder'=>'Select Priority']) }}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-6 col-lg-4">
                                                {{ Form::label('tags', __('messages.task.tags').':') }}
                                                {{ Form::select('tags[]',$tags, $task->tags, ['class' => 'form-control editTagIds', 'multiple' => true, 'data-edit-tags' => $index]) }}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-6 col-lg-4">
                                                {{ Form::label('project', __('messages.task.project').':') }}<span
                                                        class="required">*</span>
                                                {{ Form::select('project_id', $projects, $task->project->id, ['class' => 'form-control editProjectIds','required', 'placeholder'=>'Select Project', 'data-edit-project' => $index]) }}
                                            </div>
                                            <div class="form-group col-sm-12">
                                                {{ Form::label('description', __('messages.common.description').':') }}
                                                <textarea id="taskEditDescriptionContainer" name="description"
                                                          class="form-control taskEditDescriptionContainer-{{ $index }}">{{ $task->description }}</textarea>
                                            </div>
                                            <div class="col-sm-12 text-right">
                                                {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                                                <button type="button" data-toggle="collapse"
                                                        data-target="#collapse{{ $index }}" aria-expanded="true"
                                                        aria-controls="collapse{{ $index }}" href="#"
                                                        class="btn btn-light ml-2 elementCancel">{{ __('messages.common.cancel') }}</button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
                                @if(empty($search))
                                    <p class="text-dark">{{ __('messages.task.no_task_available') }}</p>
                                    @else
                                        <p class="text-dark">{{ __('messages.task.no_task_found') }}</p>
                                    @endif
                                </div>
                            @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                @if($totalTasks != 0)
                    <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $tasks->firstItem() }}</span> -
                    <span class="font-weight-bold ml-1 mr-1">{{ $tasks->lastItem() }}</span> {{ __('messages.common.of') }}
                    <span class="font-weight-bold ml-1">{{ $tasks->total() }}</span>
                </span>
                @endif
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
