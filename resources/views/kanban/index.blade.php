@extends('layouts.app')
@section('title')
    {{ __('messages.tasks') }}  {{ __('messages.kanban') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
@endsection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/dragula.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/style/css/kanban.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/style/css/task-details-kanban.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.task.kanban_list') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <div class="row kanban-header">
                    <div class="col-xs-12 mr-2">
                        <div class="project-drp-container">
                            <label for="projectDropdown"
                                   class="lbl-block mr-2"><b>{{ __('messages.projects') }}</b></label>
                            {{Form::select('drp_projects', $projects, null, ['id'=>'projectDropdown', 'class'=>'form-control'])  }}
                        </div>
                    </div>
                    @can('manage_users')
                    <div class="col-xs-12 mr-2">
                        <div class="user-drp-container">
                            <label for="projectDropdown"
                                   class="lbl-block mr-2"><b>{{ __('messages.users') }}</b></label>
                            {{Form::select('drp_users', [], null, ['id'=>'usersDropdown', 'class'=>'form-control','placeholder' => 'All'])  }}
                        </div>
                    </div>
                    @endcan
                    @if(!getLoggedInUser()->hasRole('Admin'))
                        <input type="hidden" hidden value="{{ Auth::id() }}" id="loginUserId">
                    @endif
                    <div class="col-xs-12">
                        <div>
                            <a href="{{ route('tasks.index') }}"
                               class="btn btn-warning form-btn p-2 ml-2"
                               title="{{ __('messages.common.list_view') }}" data-toggle="tooltip"><i
                                        class="fa fa-tasks font-size-20px"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <a class="btn btn-primary form-btn addTasksModal kanban-add-task-btn ml-2 mr-3"
                           title="{{ __('messages.task.new_task') }}" data-toggle="tooltip"><i
                                    class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="col-12">
                    <div class="row flex-nowrap pt-3 overflow-auto board-container">
                        <div class="lock-board">
                        </div>
                        @livewire('kanban')
                    </div>
                </div>
            </div>
        </div>
        @include('tasks.modal')
    </section>
    @include('kanban.templates.templates')
@endsection
@include('kanban.task-kanban-details')

@section('page_js')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let taskUrl = '{{ url('tasks') }}';
        let loginUserRole = "{{ getLoggedInUser()->hasRole('Admin') ? true : false}}";
        let authUserId = "{{ getLoggedInUserId() }}";
        let deleteAttachment = "{{ (__('messages.task.attachments')) }}";
        let downloadTasks = "{{ route('tasks.index')  }}";
        {{--let taskDetailUrl = '{{url('task-details')}}';--}}
        {{--let timeEntryUrl = "{{url('time-entries')}}/";--}}
        {{--let getTaskUrl = "{{url('get-tasks')}}/";--}}
        {{--let projectsURL = "{{url('projects')}}/";--}}
        {{--let taskStatus = JSON.parse('@json($taskStatus)');--}}
        {{--let taskBadges = JSON.parse('@json($taskBadges)');--}}
        // let taskDetailActionColumnIsVisible = false;
        // let reportStartDate = '';
        // let reportEndDate = '';
        {{--let canManageEntries = "{{ (Auth::user()->can('manage_time_entries')) ? true : false }}";--}}
        let currentLoggedInUserId = "{{ getLoggedInUserId() }}";
        {{--let copyTodayActivity = "{{ url('copy-today-activity') }}/";--}}
        let isShow = false
        let isShowProject = true
        {{--let usersURL = "{{ url('get-user-lists') }}";--}}
        {{--let loginUserName = "{{ getLoggedInUser()->name }}";--}}
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}";
        {{--let loginUserId = {{ getLoggedInUserId() }};--}}
        {{--let orderId = '{{\App\Models\Task::CREATED_AT_DESC}}';--}}
{{--        let canManageProjects = "{{Auth::user()->can('manage_projects') ? true : false}}";--}}
    </script>
    <script src="{{ mix('assets/js/dom-autoscroller.js') }}"></script>
    <script src="{{ mix('assets/js/dragula.js') }}"></script>
    <script src="{{ mix('assets/js/projects/kanban.js') }}"></script>
    <script src="{{ mix('assets/js/task/task.js') }}"></script>
    <script src="{{ mix('assets/js/input_price_format.js') }}"></script>
@endsection
