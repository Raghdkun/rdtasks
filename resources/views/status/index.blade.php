@extends('layouts.app')
@section('title')
    {{ __('messages.status.status') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.status.status') }}</h1>
            <div class="filter-container section-header-breadcrumb">
                <div class="ml-auto">
                    <a href="#" class="btn btn-primary addStatus" data-toggle="modal"
                       data-target="#addStatusModal" id="status_modal">{{ __('messages.status.new_status') }} <i
                                class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('statuses')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('status.modal')
        @include('status.edit_modal')
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let newStatus = "{{ __('messages.status.new_status') }}";
    </script>
    <script src="{{ mix('assets/js/status/status.js') }}"></script>
@endsection

