@extends('layouts.app')
@section('title')
    {{ __('messages.activity_log.activity_logs') }}
@endsection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('assets/style/css/activity-logs.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.activity_log.activity_logs') }}</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('activity-logs')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('activity_logs.templates.templates')
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let noMoreRecords = ' {{__('messages.activity_log.no_more_logs')}}';
    </script>
    <script src="{{ mix('assets/js/activity_logs/activity_logs.js') }}"></script>
@endsection

