@extends('layouts.app')
@section('title')
    {{ __('messages.activity_types') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.activity_types') }}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a href="#" class="btn btn-primary" data-toggle="modal"
                   data-target="#AddModal">{{ __('messages.activity_type.new_activity_type') }} <i
                            class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('activity-types')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('activity_types.modal')
        @include('activity_types.edit_modal')
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{ mix('assets/js/activity_types/activity.js') }}"></script>
@endsection

