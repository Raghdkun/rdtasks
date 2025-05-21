@extends('layouts.app')
@section('title')
    {{ __('messages.projects') }}
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.projects') }}</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('user-projects')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let canManageTags = "{{ (Auth::user()->can('manage_tags')) ? true : false }}";
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ mix('assets/js/my_projects/my_project.js') }}"></script>
@endsection

