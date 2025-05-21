<div class="row">
    @if($totalTasks != 0)
        <div class="col-12 d-flex justify-content-between pr-0">
            <div class="pr-0 pl-2 pt-2 pb-2 mb-3 ml-auto">
                <input wire:model.debounce.100ms="search" type="search" class="form-control"
                       placeholder="{{ __('messages.common.search') }}"
                       id="search">
            </div>
        </div>
    @endif
    <div class="col-md-12">
        <div wire:loading id="live-wire-screen-lock">
            <div class="live-wire-infy-loader">
                @include('loader')
            </div>
        </div>
    </div>
    <div class="col-12 px-sm-0">
        <div class="accordion task-list" id="accordionExampleChildOne">
            @forelse($projectTasks as $index => $task)
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
                            <input type="text" class="task-input display-none"
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
                                                </div>
                                            </span>
                        </div>
                        <div class="right-side-content">

                            @if (in_array(Auth::id(), $task->taskAssignee->pluck('id')->toArray(), true) || Auth::user()->role_names == \App\Models\User::ADMIN || Auth::user()->role_names == \App\Models\User::DEVELOPER)
                                <span class="float-right ml-2">
                                    <a href="#" class="mx-2 edit-task-btn" data-id="{{$task->id}}"
                                       title="{{__('messages.common.edit')}}">
                                        <i class="fa fa-edit card-edit-icon"></i>
                                    </a>
                                </span>
                            @endif
                            <a href="#" class="mx-2 task-details" data-id="{{$task->id}}"
                               title="{{__('messages.common.details')}}">
                                <i class="fas fa-info"></i>
                            </a>
                            @if(!empty($task->due_date))
                                <span class="float-right task-date-mb due-date-wrapper {{ Carbon\Carbon::now()->startOfDay() > Carbon\Carbon::parse($task->due_date)  ? 'text-danger' : '' }}">
                                             <input type="text" data-id="{{ $task->id }}"
                                                    class="form-control float-right editDueDate"
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
                            @endif
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
                                        <span class="task-duration text-muted">{{ __('messages.task.project_name') }} {{ !empty($task->project) ? html_entity_decode($task->project->name) : '' }}</span>
                                    </a>
                                </div>
                            </div>
                            @if(!empty($task->priority))
                                <span class="badge {{\App\Models\Task::PRIORITY_BADGE[$task->priority]}}  mr-2">{{!empty(strtoupper($task->priority)) ? html_entity_decode($task->priority) : ''}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
                    @if(empty($search))
                        <div class="row">
                            <div class="empty-state col-sm-12" data-height="400">
                                <div class="empty-state-icon d-flex justify-content-center align-items-center">
                                    <i class="fas fa-question"></i>
                                </div>
                                <h2>{{__('messages.project.no_task_found_of_project')}}</h2>
                            </div>
                        </div>
                    @else
                        <p class="text-dark">{{ __('messages.task.no_task_found') }}</p>
                    @endif
                </div>
            @endforelse
            <div class="mt-4 mb-2 col-12">
                <div class="row paginatorRow">
                    <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                        @if($totalTasks != 0)
                            <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                            <span class="font-weight-bold ml-1 mr-1">{{ $projectTasks->firstItem() }}</span> -
                            <span class="font-weight-bold ml-1 mr-1">{{ $projectTasks->lastItem() }}</span> {{ __('messages.common.of') }}
                            <span class="font-weight-bold ml-1">{{ $projectTasks->total() }}</span>
                        </span>
                        @endif
                    </div>
                    <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                        {{ $projectTasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
