@extends('client_panel.layouts.app')
@section('title')
    {{ __('messages.invoice.invoice_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.invoice_details') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="ml-auto">
                    @if($invoice->status == \App\Models\Invoice::STATUS_SENT || $invoice->status == \App\Models\Invoice::STATUS_PAID)
                        <a href="{{ route('invoices.pdf',['invoice' => $invoice->id])  }}"
                           class="btn btn-success pull-right mt-3 ml-2"
                           target="_blank">{{ __('messages.invoice.print_invoice') }}</a>
                    @endif
{{--                        @if($invoice->status != \App\Models\Invoice::STATUS_PAID)--}}
{{--                            <button class="btn btn-warning dropdown-toggle mt-3 ml-2" type="button" id="dropdownMenuButton"--}}
{{--                                    data-toggle="dropdown"--}}
{{--                                    aria-haspopup="true" aria-expanded="true">--}}
{{--                                {{ __('messages.common.more') }}--}}
{{--                            </button>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right">--}}
{{--                                @if($invoice->status != \App\Models\Invoice::STATUS_SENT)--}}
{{--                                    <a class="dropdown-item text-content-wrap" href="#" id="markAsSent"--}}
{{--                                       data-status="1" data-toggle="tooltip"--}}
{{--                                       data-placement="bottom" title="{{ __('messages.common.mark_sent') }}"--}}
{{--                                       data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.common.mark_sent') }}</a>--}}
{{--                                @endif--}}
{{--                                @if($invoice->status != \App\Models\Invoice::STATUS_PAID)--}}
{{--                                    <a class="dropdown-item text-content-wrap" href="#" id="markAsPaid"--}}
{{--                                       data-status="2" data-toggle="tooltip"--}}
{{--                                       data-placement="bottom" title="{{ __('messages.common.mark_paid') }}"--}}
{{--                                       data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.common.mark_paid') }}</a>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    @if($invoice->status != \App\Models\Invoice::STATUS_PAID)--}}
{{--                        <a href="{{ route('client.invoices.edit', ['invoice' => $invoice->id]) }}"--}}
{{--                           class="btn btn-primary form-btn ml-2 mt-3">--}}
{{--                            {{ __('messages.common.edit') }}--}}
{{--                        </a>--}}
{{--                    @endif--}}
                    <a href="{{  url('client/invoices') }}" class="btn btn-light form-btn ml-2  mt-3">
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
                @include('client_panel.invoices.show_fields')
            </div>
        </div>
    </section>
    @include('invoices.templates.templates')
@endsection
@section('page_scripts')

@endsection
@section('scripts')
    <script>
        let invoiceId = "{{ $invoice->id }}";
        let invoicesUrl = "{{ url('client/invoices') }}";
        let changeInvoiceStatus = "{{$invoice->id}}";
        let isCreate = false;
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('web/js/currency.js') }}"></script>
    <script src="{{ asset('web/js/jquery.validate.min.js') }}"></script>
    <script src="{{ mix('assets/js/clients/invoice/invoices.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/invoices-show.js') }}"></script>
@endsection
