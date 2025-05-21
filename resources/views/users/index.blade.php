@extends('layouts.app')
@section('title')
    {{ __('messages.users') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.users') }}</h1>

            <div class="filter-container section-header-breadcrumb justify-content-end d-block d-md-flex">
                <div class="mr-3 align-items-center min-width-150">
                    <label for="clients" class="lbl-block mr-2"><b>{{__('messages.status.status')}}</b></label>
                    {{Form::select('status',$status,null,['id'=>'filterStatus','class'=>'form-control','placeholder' => 'All'])  }}
                </div>
                <div>
                <a href="#" class="btn btn-primary mt-4" data-toggle="modal"
                   data-target="#AddModal">{{ __('messages.user.new_user') }} <i class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('users')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('users.modal')
        @include('users.edit_modal')
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let defaultImageUrl = "{{ asset('assets/img/user-avatar.png') }}";
        let user = "{{__('messages.task.user')}}";
        let deleteUserConfirm = "{{ __('messages.user.by_deleting_this_user') }}";
    </script>
    <script src="{{ mix('assets/js/users/user.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection

