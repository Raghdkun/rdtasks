@extends('layouts.app')
@section('title')
{{ __('messages.clients') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.clients') }}</h1>
            <div class="filter-container section-header-breadcrumb d-block d-md-flex">
                <div class="mr-3 align-items-center">
                    <label for="department" class="lbl-block mr-2"><b>{{ __('messages.client.department') }}</b></label>
                    {{Form::select('department_id', $departments, null, ['id' => 'filter_department', 'class'=>'form-control', 'placeholder' => 'All'])  }}
                </div>
                <a href="#" class="btn btn-primary filter-container__btn mt-4 float-right" data-toggle="modal"
                   data-target="#AddModal">{{ __('messages.client.new_client') }} <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('clients')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('clients.modal')
        @include('clients.edit_modal')
        @include('clients.show')
        @include('clients.department_modal')

    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let canManageDepartment = "{{ (Auth::user()->can('manage_department')) ? true : false }}";
        let byDeleteThisClient = "{{ __('messages.client.by_deleting_this_client') }}";
        let deleteClientConfirm = "{{ __('messages.client.delete_client_confirm') }}";
        let defaultImageUrl = "{{ asset('assets/img/user-avatar.png') }}";
    </script>
    <script src="{{ mix('assets/js/clients/client.js') }}"></script>
@endsection

