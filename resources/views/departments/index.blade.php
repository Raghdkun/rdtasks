@extends('layouts.app')
@section('title')
    {{ __('messages.departments') }}
@endsection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.departments') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="#"
                   class="btn btn-primary filter-container__btn mt-0 addNewDepartment"> {{ __('messages.department.new_department') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('departments')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('departments.modal')
        @include('departments.show')
        @include('departments.edit_modal')
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{ mix('assets/js/department/department.js') }}"></script>
@endsection
