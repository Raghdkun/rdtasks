@extends('layouts.app')
@section('title')
    {{ __('messages.project.project_details') }}
@endsection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{mix('assets/style/css/project-details.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/style/css/tasks.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project.project_details') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="{{ route('projects.index') }}" class="btn btn-light form-btn ml-3">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                @include('my_projects.show_fields')
            </div>
        </div>
        @include('tasks.edit_modal')
        @include('tasks.task_details')
        @include('projects.task.create_modal')
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let taskUrl = '{{url('tasks')}}/';
        let isShow = true;
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}";
        let reportStartDate = '';
        let reportEndDate = '';
        let taskDetailActionColumnIsVisible = false;
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ mix('assets/js/my_projects/my_project.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
    <script src="{{ mix('assets/js/projects/task/create-task.js') }}"></script>
@endsection
