@extends('layouts.app')
@section('title')
    {{ __('messages.tasks') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/style/css/tasks.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/style/css/task-details-kanban.css') }}">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header task-header-section">
            <h1 class="page__heading">{{ __('messages.tasks') }}</h1>
            <div class="filter-container section-header-breadcrumb row justify-content-md-end">
                <div class="pl-sm-3 py-1">
                    <div class="dropdown">
                        <a class="dropdown-toggle btn btn-primary" href="#" data-toggle="dropdown"
                           title="{{__('messages.common.filter')}}" id="filter_toggle"><i
                                    class="fas fa-filter"></i> </a>
                        <div class="dropdown-menu dropdown-large dropdown-menu-right">
                            <div class="row">
                                <div class="form-group mb-2 col-sm-6 d-flex justify-content-start">
                                    <a class="btn btn-primary" id="resetFilters">{{__('messages.task.reset')}}</a>
                                </div>
                                <div class="form-group mb-0 col-sm-6 d-flex justify-content-end">
                                    <button type="button" aria-label="Close" class="close outline-none">×</button>
                                </div>
                            </div>
                            <div class="row">
                                @if(getLoggedInUser()->can('manage_projects'))
                                <div class="form-group col-sm-6 col-md-6 col-lg-4">
                                    <label class="lbl-block"><b>{{ __('messages.task.assign_to') }}</b></label>
                                    {{ Form::select('drp_users',$assignees, Auth::id(), ['id'=>'filter_user', 'class'=>'form-control min-width-150',  'placeholder' => 'All']) }}
                                </div>
                                @endif
                                <div class="form-group col-sm-6 col-md-6 col-lg-4">
                                    <label class="lbl-block"><b>{{ __('messages.task.project') }}</b></label>
                                    {{Form::select('drp_project',$projects,null,['id'=>'project_filter', 'class'=>'form-control min-width-150', 'placeholder' => 'Select Project'])  }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 col-lg-4 col-12">
                                    <label class="lbl-block"><b>{{ __('messages.task.due_date') }}</b></label>
                                    {{ Form::text('due_date_filter', null, ['id'=>'dueDateFilter', 'class' => 'form-control min-width-150', 'placeholder' => 'Enter Date', 'autocomplete'=>'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 col-lg-4 col-12">
                                    <label class="lbl-block"><b>{{ __('messages.task.status') }}</b></label>
                                    {{Form::select('drp_status',$status,0,['id'=>'filter_status', 'class'=>'form-control min-width-150', 'placeholder' => 'All'])  }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 col-lg-4 col-12">
                                    <label class="lbl-block"><b>{{ __('messages.common.page_size') }}</b></label>
                                    {{ Form::select('per_page', $perPageOption, '10', ['id'=>'filter_per_page', 'class'=>'form-control perPage tasks-w-150'])  }}
                                </div>
                                    <div class="form-group col-sm-12 col-md-6 col-lg-4 col-12">
                                        <label class="lbl-block"><b>{{ __('messages.common.sort_by') }}</b></label>
                                        {{ Form::select('tasks_filter', $tasksFilterOptions, \App\Models\Task::CREATED_AT_DESC, ['id'=>'filter_task', 'class'=>'form-control tasksFilter tasks-w-150 mr-3', 'placeholder' => 'All'])  }}
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pl-sm-3 pl-2 py-1">
                    <div class="">
                        <a href="{{ route('kanban.index') }}" class="btn btn-primary"
                           style="padding-top: 8px;padding-bottom: 3px"
                           title="{{ __('messages.task.switch_to_kanban') }}"
                           data-toggle="tooltip"><i
                                    class="fab fa-trello font-size-20px"></i></a>
                    </div>
                </div>
                <div class="pl-sm-3 pr-sm-3 pl-2 pr-2 py-1 task-action">
                    <div class="dropdown d-inline ">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2"
                                data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">{{ __('messages.common.action') }}
                        </button>
                        <div class="dropdown-menu copy-today-activity">
                            <a class="dropdown-item has-icon addTasksModal text-content-wrap" href="#"
                               data-placement="bottom" title="{{ __('messages.task.new_task') }}"
                               data-delay='{"show":"500", "hide":"50"}'>
                                <i class="fas fa-plus"></i>{{ __('messages.task.new_task') }}</a>
                            <a class="dropdown-item has-icon timeEntryAddModal manuallyTaskTimeEntry text-content-wrap"
                               href="#"
                               data-placement="bottom" title="{{ __('messages.task.add_time_entry') }}"
                               data-delay='{"show":"500", "hide":"50"}'>
                                <i class="fas fa-clock"></i>{{ __('messages.task.add_time_entry') }}</a>
                            <a class="dropdown-item has-icon text-content-wrap" href="#" id="copyTodayEntry"
                               data-placement="bottom" title="{{ __('messages.time_entry.copy_today_activity') }}"
                               data-delay='{"show":"500", "hide":"50"}'>
                                <i class="far fa-copy"></i>{{ __('messages.time_entry.copy_today_activity') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body tasks-wrapper" >
                            @livewire('tasks', ['userId' => $userId, 'projects' => $projects, 'tags' => $tags])
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('tasks.modal')
        @include('tasks.edit_modal')
        <div id="taskEditModal">
            @include('tasks.edit_assignee_modal')
        </div>
        @include('tasks.task_details')
        @include('time_entries.modal')
        @include('time_entries.edit_modal')
        @include('kanban.templates.templates')
        @include('kanban.task-kanban-details')
    </section>
@endsection
@section('page_js')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let deleteAttachment = "{{ (__('messages.task.attachments')) }}";
        let taskUrl = '{{url('tasks')}}/';
        let taskStatus = JSON.parse('@json($taskStatus)');
        let taskBadges = JSON.parse('@json($taskBadges)');
        let taskDetailActionColumnIsVisible = false;
        let reportStartDate = '';
        let reportEndDate = '';
        let canManageEntries = "{{ (Auth::user()->can('manage_time_entries')) ? true : false }}";
        let currentLoggedInUserId = "{{ getLoggedInUserId() }}";
        let isShow = false
        let loginUserName = "{{ getLoggedInUser()->name }}"
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}"
        let loginUserId = {{ getLoggedInUserId() }};
        let orderId = '{{\App\Models\Task::CREATED_AT_DESC}}'
        let canManageProjects = "{{Auth::user()->can('manage_projects') ? true : false}}"
        let isShowProject = false
        let isTask = true
    </script>
    <script src="{{ mix('assets/js/task/task.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
    {{--    <script src="{{ mix('assets/js/projects/kanban.js') }}"></script>--}}
@endsection
