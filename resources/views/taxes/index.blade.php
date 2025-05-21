@extends('layouts.app')
@section('title')
    {{ __('messages.taxes') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1> {{ __('messages.taxes') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="#" class="btn btn-primary form-btn" data-toggle="modal"
                   data-target="#AddModal">{{ __('messages.tax.new_tax') }} <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('taxes')
                </div>
            </div>
        </div>
    </section>
    @include('taxes.add_modal')
    @include('taxes.edit_modal')
@endsection
@section('page_scripts')
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{mix('assets/js/tax/tax.js')}}"></script>
@endsection
