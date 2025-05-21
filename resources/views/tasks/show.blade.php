@extends('layouts.app')
@section('title')
    {{ __('messages.task.task_details') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link href="{{mix('assets/style/css/task-detail.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/style/css/task-details-kanban.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/style/css/tasks.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.task.task_details') }}</h1>
            <div class="filter-container section-header-breadcrumb">
                <div class="ml-auto">
                    <button class="btn btn-primary edit-task-btn mr-2" type="button" data-id="{{$task->id}}">
                        {{ __('messages.common.edit') }}
                    </button>
                    <a class="btn btn-light" href="{{ route('tasks.index') }}">{{ __('messages.common.back') }}</a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" id="customer" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="taskDetails" data-toggle="tab" href="#tDetails"
                                       role="tab" aria-selected="true">{{ __('messages.task.task_details') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="#taskAttachment" data-toggle="tab" href="#tAttachments"
                                       role="tab" aria-selected="false">{{ __('messages.task.attachments') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="#taskComment" data-for-comment="1" data-toggle="tab"
                                       href="#tComments"
                                       role="tab" aria-selected="false">{{ __('messages.task.comments') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="#taskTimeEntries" data-toggle="tab" href="#tTimeEntries"
                                       role="tab" aria-selected="false">{{ __('messages.task.task_time_entries') }}</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tDetails" role="tabpanel"
                                     aria-labelledby="taskDetails">
                                    <div class="alert alert-danger display-none" id="taskValidationErrorsBox"></div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h4 class="mb-3">
                                                <span
                                                        class="text-info pr-2">{{$task->prefix_task_number}}</span>{{html_entity_decode($task->title)}}
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="row task-detail">
                                        @if(!empty($task->taskAssignee->pluck('name')->toArray()))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                            class="font-weight-bold">{{ __('messages.task.assignee') }}
                                                        :</label>
                                                    <p class="flex-1">{{html_entity_decode(implode(", ",$task->taskAssignee->pluck('name')->toArray()))}}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($task->due_date))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                            class="font-weight-bold">{{ __('messages.task.due_date') }}
                                                        :</label>
                                                    <p>{{\Carbon\Carbon::parse($task->due_date)->translatedFormat('jS F, Y')}}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">{{ __('messages.task.status') }}
                                                    :</label>
                                                <div class="custom-control custom-checkbox mb-0" id="client_checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="taskStatus"  data-id="{{$task->id}}" {{$task->status == 1 ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="taskStatus">
                                                            @if(isset($taskStatus[$task->status]))
                                                                <span class="badge {{ isset($taskBadges[$task->status]) ? $taskBadges[$task->status] : 'badge-primary'}} text-uppercase mt-0">{{$taskStatus[$task->status]}}</span>
                                                            @endif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                            @if(!empty($task->priority))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                            class="font-weight-bold">{{ __('messages.task.priority') }}
                                                        :</label>
                                                    <p>
                                                            <i class="fa fa-arrow-up task-detail__priority-heading--{{$task->priority}}"
                                                               aria-hidden="true"></i>
                                                            {{ucfirst($task->priority)}}

                                                    </p>
                                                </div>
                                            </div>
                                            @endif

                                            @if(!empty($task->tags->pluck('name')->toArray()))
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label
                                                                class="font-weight-bold">{{ __('messages.task.tags') }}
                                                            :</label><br>
                                                    @foreach($task->tags->pluck('name') as $tags)
                                                        <span
                                                                class="badge badge-{{ getBadgeColor($loop->index) }} task-details-tags">{{html_entity_decode($tags)}}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label
                                                        class="font-weight-bold">{{ __('messages.task.time_tracking') }}
                                                    : </label><br>
                                                <span class="pointer">
                                                    <span>
                                                        {{ (!empty($task->timeEntries->isNotEmpty())) ? roundToQuarterHour($task->timeEntries()->sum('duration')): '00:00'}}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                            class="font-weight-bold">{{ __('messages.task.reporter') }}
                                                        :</label>
                                                    <p class="flex-1">{{(isset($task->createdUser->name) ? html_entity_decode($task->createdUser->name) : '')}}</p>
                                                </div>
                                            </div>
                                            @if(!empty($task->estimate_time))
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{__('messages.task.estimate_time')}}
                                                            :</label>
                                                        <p class="flex-1">
                                                            @if($task->estimate_time_type == \App\Models\Task::IN_HOURS)
                                                                <?php
                                                                    $values = explode(':', $task->estimate_time);
                                                                    $hours = (isset($values[0]) && $values[0] != 0) ? $values[0].' '.__('messages.invoice.hours') : '';
                                                                    $minutes = (isset($values[1]) && $values[1] != 0) ? ' '.$values[1].' '.__('messages.task.minutes') : '';
                                                                ?>
                                                                {{ $hours.$minutes }}
                                                            @else
                                                                {{ $task->estimate_time.' '.__('messages.task.days')  }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{ Form::label('created_at', __('messages.common.created_on').(':'),['class'=>'font-weight-bold']) }}
                                                    <br>
                                                    <span data-toggle="tooltip" data-placement="right"
                                                          title="{{ date('jS M, Y', strtotime($task->created_at)) }}">{{ $task->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{ Form::label('created_at', __('messages.common.last_updated').(':'),['class'=>'font-weight-bold']) }}
                                                    <br>
                                                    <span data-toggle="tooltip" data-placement="right"
                                                          title="{{ date('jS M, Y', strtotime($task->updated_at)) }}">{{ $task->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">{{ __('messages.task.description') }}
                                                        :</label><br>
                                                    <span>
                                                        {!! !empty($task->description) ? html_entity_decode($task->description) : __('messages.common.n/a') !!}
                                                    </span>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tAttachments" role="tabpanel"
                                     aria-labelledby="taskAttachment">
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-12">
                                            <div class="mb-3 d-flex">
                                                <span
                                                        class="task-detail__attachment-heading w-100">{{ __('messages.task.attachments') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <form method="post" action="{{url("tasks/".$task->id."/add-attachment")}}"
                                                  enctype="multipart/form-data"
                                                  class="dropzone" id="dropzone">
                                                {{csrf_field()}}
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tComments" role="tabpanel" aria-labelledby="taskComment">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <strong>{{ Form::label('add_comment', __('messages.task.add_comment')) }}</strong>
                                            <div id="commentContainer" class="quill-editor-container"></div>
                                            <div class="text-left mt-3">
                                                {{ Form::button(__('messages.common.save'), ['type'=>'button','class' => 'btn btn-primary', 'id'=>'btnComment', 'data-edit-mode' => '0', 'data-comment-id' => '0', 'data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                                                <button type="reset" id="btnCancel" class="btn btn-light ml-1">
                                                    {{ __('messages.common.cancel') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <!-- Comment Box starts -->
                                            <div class="card task-chat-box comments" id="mychatbox">
                                                <div>
                                                    <div class="mb-3 d-flex">
                                                        <span class="flex-1 ml-5 no_comments text-center @if(!($task->comments->isEmpty())) d-none @endif">{{ __('messages.task.no_comments_added_yet') }}</span>
                                                    </div>
                                                </div>
                                                <div id="itemsWrapper"
                                                     class="outline-none card-body task-chat-content activities @if(($task->comments->isEmpty())) d-none @endif"
                                                     tabindex="2">
                                                    @foreach($task->comments as $comment)
                                                        @php
                                                            $deletedUser = (isset($comment->createdUser->deleted_at)) ?
                                                                            "<span class='user__deleted-user text-info'>(deactivated user)</span>" : ''
                                                        @endphp
                                                        <div class="task-chat-item position-relative comment-item-{{ $comment->id }} {{ getLoggedInUserId() == $comment->created_by ? 'task-chat-right' : 'task-chat-left' }}"
                                                             data-comment-item="{{ $comment->id }}">
                                                            <img src="{{ $comment->createdUser->img_avatar }}"
                                                                 class="user__img profile uProfileLayout {{ getLoggedInUserId() == $comment->created_by ? 'mr-3' : 'ml-3' }}"
                                                                 alt="User Image" data-toggle="tooltip" data-html="true"
                                                                 title="{{ isset($comment->createdUser->name) ? $comment->createdUser->name . ' ' . $deletedUser : '' }}"/>
                                                            <div class="task-chat-details">
                                                                <div class="task-chat-text {{'comment-'.$comment->id}}">
                                                                    {!! html_entity_decode($comment->comment) !!}
                                                                </div>
                                                                <div class="task-chat-time">{{timeElapsedString($comment->created_at)}}</div>
                                                            </div>
                                                            @if($comment->created_by == Auth::id())
                                                                <a class="dropdown dropdown-list-toggle">
                                                                    <a href="javascript:void(0)" data-toggle="dropdown"
                                                                       class="list-toggle notification-toggle action-dropdown d-none position-xs-bottom uCommentToggle">
                                                                        <i class="fas fa-ellipsis-v action-toggle-mr"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <div class="dropdown-list-content dropdown-list-icons">
                                                                            <a href="#"
                                                                               class="dropdown-item dropdown-item-desc edit-comment"
                                                                               data-id="{{ $comment->id }}"><i
                                                                                        class="fas fa-edit mr-2 card-edit-icon"></i>{{ __('messages.common.edit') }}
                                                                            </a>
                                                                            <a href="#"
                                                                               class="dropdown-item dropdown-item-desc del-comment"
                                                                               data-id="{{ $comment->id }}"><i
                                                                                        class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tTimeEntries" role="tabpanel"
                                     aria-labelledby="taskTimeEntries">
                                    <div class="task-time-entry-table">
                                        <div class="filter-container section-header-breadcrumb d-flex">
                                            <a href="#"
                                               class="btn btn-primary form-btn mr-3 ml-1 mb-2 timeEntryAddModal addTaskTimeEntry task-button-padding"
                                               data-id="{{ $task->id }}"
                                               data-project-id={{$task->project->id}}>{{ __('messages.task.add_time_entry') }}
                                                <i class="fas fa-plus"></i></a>
                                            <div class="mr-3 mb-2">
                                                {{ Form::select('drp_activity',$activityTypes,null,['id'=>'filterActivity','class'=>'form-control', 'placeholder' => 'Activity Type'])  }}
                                            </div>
                                            <div class="mr-2 align-items-center mt-3 mt-lg-0">
                                                <div id="time_range" class="time_range date_range_task_time_entry">
                                                    <i class="far fa-calendar-alt"
                                                       aria-hidden="true"></i>&nbsp;&nbsp;<span></span> <b
                                                            class="caret"></b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="notice"></div>
                                            <table class="table table-responsive-lg table-striped table-bordered"
                                                   id="taskTimeEntryTable">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>{{ __('messages.time_entry.activity_type') }}</th>
                                                    <th>{{ __('messages.time_entry.start_time') }}</th>
                                                    <th>{{ __('messages.time_entry.end_time') }}</th>
                                                    <th>{{ __('messages.time_entry.duration') }}</th>
                                                    <th>{{ __('messages.time_entry.type') }}</th>
                                                    <th>{{ __('messages.common.created_at') }}</th>
                                                    <th class="w-10">{{ __('messages.common.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="previewEle">
        </div>
        @include('tasks.edit_modal')
        @include('tasks.time_tracking_modal')
        @include('time_entries.edit_modal')
        @include('tasks.show_time_entry_note')
        @include('time_entries.modal')
        @include('status.modal')
        @include('projects.templates.templates')
    </section>
@endsection
@section('page_js')
    <script src="{{ asset('assets/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/ekko-lightbox.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let taskUrl = '{{url('tasks')}}/';
        let taskId = '{{$task->id}}';
        let attachmentUrl = '{{ $attachmentUrl }}/';
        let authId = '{{Auth::id()}}';
        let timeEntryUrl = "{{url('time-entries')}}/";
        let taskTimeEntryUrl = "{{url('task-time-entry')}}/";
        let canManageEntries = "{{ (Auth::user()->can('manage_time_entries')) ? true : false }}";
        let projectsURL = "{{url('projects')}}/";
        let isShow = true;
        let usersURL = "{{ url('get-user-lists') }}";
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}";
        let downloadTasks = "{{ route('tasks.index')  }}";
        let deleteAttachment = "{{__('messages.task.attachments')}}";
        let projectTaskAssignees = "{{ $task->taskAssignee->pluck('id') }}";
        let loginUserAdmin = "{{ getLoggedInUser()->hasRole('Admin') ? true : false}}";
        let URL = '{{ parse_url(url()->current(),PHP_URL_PATH) }}';
        let loginUserName = "{{ getLoggedInUser()->name }}";
        let canManageStatus = "{{ (Auth::user()->can('manage_status')) ? true : false }}";
        let isShowProject = false
    </script>
    <script src="{{ mix('assets/js/task/task.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_detail.js') }}"></script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
@endsection
