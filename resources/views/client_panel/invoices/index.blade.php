@extends('client_panel.layouts.app')
@section('title')
    {{ __('messages.invoices') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoices') }}</h1>
            <div class="filter-container section-header-breadcrumb row justify-content-md-end">
                <div class="col-xs-8">
                    <div class="mr-2">
                        <label class="lbl-block"><b>{{ __('messages.common.sort_by') }}</b></label>
                        {{ Form::select('due_date',$dueDateFilter, null, ['id'=>'due_date_filter','class'=>'form-control', 'placeholder' => __('messages.common.all') ]) }}
                    </div>
                </div>
                <div class="col-xs-8">
                    <div class="mr-2">
                        <label class="lbl-block"><b>{{ __('messages.task.status') }}</b></label>
                        {{ Form::select('drp_status',$status, 1, ['id'=>'filter_status','class'=>'form-control invStatus', 'placeholder' => __('messages.common.all') ]) }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('client-invoices')
                </div>
            </div>
        </div>
    </section>
    @include('invoices.templates.templates')
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let invoiceEdit = false;
        let isCreate = false;
        let canManageTax = "{{ (Auth::user()->can('manage_taxes')) ? true : false }}";
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/invoices.js') }}"></script>
    <script src="{{ mix('assets/js/clients/invoice/invoices.js') }}"></script>
@endsection

