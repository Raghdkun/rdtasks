@extends('layouts.app')
@section('title')
    @if($groupName === 'invoice_template')
        {{ __('messages.setting_menu.invoice_template') }}
    @else
        {{ __('messages.setting_menu.general') }}
    @endif
@endsection
@section('page_css')
    @if($groupName === 'invoice_template')
        <link rel="stylesheet" href="{{ asset('assets/style/css/invoice-template.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.settings') }}</h1>
        </div>
    </section>
    <div class="section-body">
        @include('flash::message')
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-0 pl-2">
                            <ol class="p-0">{{ $errors->first() }}</ol>
                        </ul>
                    </div>
                @endif
                <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
                @include('settings.fields')
            </div>
        </div>
    </div>
    @include('settings.invoices.templates')
@endsection
@section('scripts')
    @if($groupName === 'invoice_template')
        <script>
            let companyAddress = "{{ html_entity_decode($settings['company_address']) }}";
            let companyPhoneNumber = "{{ $settings['company_phone'] }}";
            let companyName = "{{ html_entity_decode($settings['company_name']) }}";
        </script>
        <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
        <script src="{{ mix('assets/js/settings/invoice-template.js') }}"></script>
    @endif
    <script src="{{ mix('assets/js/settings/setting.js') }}"></script>
@endsection
