@extends('layouts.app')
@section('title')
    {{ __('messages.project.project_details') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}">
@endSection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
    <link rel="stylesheet" href="{{mix('assets/style/css/project-details.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/style/css/tasks.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/style/css/task-details-kanban.css') }}">

@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ ucfirst($project->name) }}'s {{ __('messages.project.project_details') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="#" class="btn btn-primary form-btn edit-btn ml-3" data-id="{{ $project->id }}">
                    {{ __('messages.common.edit') }}
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-light form-btn ml-3">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                @include('projects.show_fields')
            </div>
        </div>
        @include('projects.edit_modal')
        @include('projects.add_client_modal')
        @include('tasks.edit_modal')
        @include('projects.task.create_modal')
        @include('tasks.task_details')
        @include('status.modal')
        @include('projects.templates.templates')
        @include('kanban.templates.templates')
        @include('kanban.task-kanban-details')
    </section>
@endsection
@section('page_js')
    <script src="{{ asset('assets/js/dropzone.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let projectId = '{{$project->id}}'
        let deleteAttachment = "{{ (__('messages.task.attachments')) }}"
        let canManageClients = "{{ (Auth::user()->can('manage_clients')) ? true : false }}"
        let taskUrl = '{{url('tasks')}}/'
        let isShow = true
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}"
        let reportStartDate = ''
        let reportEndDate = ''
        let taskDetailActionColumnIsVisible = false
        let isShowProject = true
        let projectTaskAssignees = ''
        let projectAttachment = "{{ route('projects.add-attachment',$project->id) }}"
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{ mix('assets/js/projects/task/create-task.js') }}"></script>
    <script src="{{ mix('assets/js/projects/show-edit.js') }}"></script>
    <script src="{{ mix('assets/js/input_price_format.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
    <script src="{{ mix('assets/js/task/task.js') }}"></script>
@endsection
