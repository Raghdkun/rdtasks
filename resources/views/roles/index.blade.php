@extends('layouts.app')
@section('title')
    {{ __('messages.roles') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.roles') }}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a class="pull-right btn btn-primary"
                   href="{{ route('roles.create') }}">{{ __('messages.role.new_role') }} <i
                            class="fas fa-plus"></i></a>
            </div>
        </div>
        @include('flash::message')
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('roles')
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
    <script src="{{ mix('assets/js/roles/role.js') }}"></script>
@endsection

