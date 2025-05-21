@extends('layouts.app')
@section('title')
    {{ __('messages.invoice.invoice_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.invoice_details') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="ml-auto">
                    @if($invoice->status == \App\Models\Invoice::STATUS_SENT || $invoice->status == \App\Models\Invoice::STATUS_DRAFT)
                        <a href="javascript:void(0)"
                           class="btn btn-warning pull-right mt-3 mark-as-paid"
                           data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing..."
                           data-id="{{ $invoice->id }}"> {{__('messages.common.mark_paid')}}</a>
                    @endif
                    @if($invoice->status == \App\Models\Invoice::STATUS_SENT || $invoice->status == \App\Models\Invoice::STATUS_PAID)
                        <a href="{{ route('invoices.pdf',['invoice' => $invoice->id])  }}"
                           class="btn btn-success pull-right mt-3 ml-2"
                           target="_blank">{{ __('messages.invoice.print_invoice') }}</a>
                    @endif
                    @if($invoice->status != \App\Models\Invoice::STATUS_PAID)
                        <a href="{{ route('invoices.edit', ['invoice' => $invoice->id]) }}"
                           class="btn btn-primary form-btn ml-2 mt-3">
                            {{ __('messages.common.edit') }}
                        </a>
                    @endif
                    <a href="{{ route('invoices.index') }}" class="btn btn-light form-btn ml-2  mt-3">
                        {{ __('messages.common.back') }}
                    </a>
                </div>
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
                @include('invoices.show_fields')
            </div>
        </div>
    </section>
    @include('invoices.templates.templates')
@endsection
@section('page_scripts')

@endsection
@section('scripts')
    <script>
        let invoicesUrl = "{{ route('invoices.index') }}";
        let invoiceEdit = true;
        let isCreate = false;
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('web/js/currency.js') }}"></script>
    <script src="{{ asset('web/js/jquery.validate.min.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/invoices-show.js') }}"></script>
@endsection
