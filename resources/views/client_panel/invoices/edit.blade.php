@extends('client_panel.layouts.app')
@section('title')
    {{ __('messages.invoice.edit_invoice') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.edit_invoice') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="{{ route('client.invoices.index') }}" class="btn btn-light form-btn">
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
                {{ Form::open(['route' => ['invoices.update', $invoice->id], 'validated' => false, 'method' => 'PUT', 'id' => 'editInvoiceForm']) }}
                @include('client_panel.invoices.edit_fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('invoices.templates.templates')
@endsection
@section('page_scripts')

@endsection
@section('scripts')
    <script>
        let taxRatesArr = JSON.parse('@json($invoiceSyncList['taxesArr'])');
        let invoicesUrl = "{{ route('invoices.index') }}";
        let clientInvoicesUrl = "{{ url('client/invoices') }}";
        let invoiceEdit = true;
        let isCreate = false;
        let currentCurrency = "{{ getCurrenciesClass() }}";
        let userIsClient = "{{Auth::user()->hasRole('Client')?true:false}}";
        let canManageTax = "{{ (Auth::user()->can('manage_taxes')) ? true : false }}";
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('web/js/currency.js') }}"></script>
    <script src="{{ asset('web/js/jquery.validate.min.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/invoices.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection
