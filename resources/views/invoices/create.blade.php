@extends('layouts.app')
@section('title')
    {{ __('messages.invoice.new_invoice') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.new_invoice') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="{{ url()->previous() }}" class="btn btn-light form-btn">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @if ($errors->any())
                <div class="alert alert-danger p-0">
                    <ul>
                        <li>{{ $errors->first() }}</li>
                    </ul>
                </div>
            @endif
            <div class="card">
                {{ Form::open(['route' => 'invoices.store', 'id' => 'invoiceForm']) }}
                @include('invoices.fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('taxes.add_modal')
    @include('invoices.templates.templates')
@endsection
@section('page_scripts')

@endsection
@section('scripts')
    <script>
        let taxRatesArr = JSON.parse('@json($taxesArr)');
        let invoicesUrl = "{{ route('invoices.index') }}";
        let isCreate = true;
        let invoiceEdit = false;
        let currentCurrency = "{{ getCurrenciesClass() }}";
        let canManageTax = "{{ (Auth::user()->can('manage_taxes')) ? true : false }}";
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('web/js/currency.js') }}"></script>
    <script src="{{ asset('web/js/jquery.validate.min.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/invoices.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection
