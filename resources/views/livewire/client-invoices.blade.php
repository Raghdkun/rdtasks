<div class="row">
    @if($totalInvoices != 0)
        <div class="col-lg-6 col-md-6 col-sm-12 pt-2 d-flex mt-0 mb-3">
            <button class="btn btn-primary invoice-sent-button">{{__('messages.invoice.sent_invoices')}}
                <span class="badge-transparent badge"> {{ $sentInvoiceCount }}</span>
            </button>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 pt-2 d-flex mt-0 mb-3 pr-0">
            <div class="ml-auto row justify-content-md-end pr-3 mt-0 mb-3 searchBox">
                <input wire:model.debounce.100ms="search" type="search" class="form-control"
                       placeholder="{{ __('messages.common.search') }}"
                       id="search">
            </div>
        </div>
        <div class="col-md-12">
            <div wire:loading id="live-wire-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
    @endif
    @forelse($invoices as $invoice)
        <div class="col-12 col-md-6 col-lg-4 extra-large">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded invoice-card-height removeMarginX hover-card">
                <div class="card-header d-flex justify-content-between align-items-center itemCon p-3">
                    <div class="d-flex">
                        <a href="{{ url('client/invoices', ['invoice' => $invoice->id]) }}" class="d-flex flex-wrap">
                            (<small
                                    class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ __('messages.invoice.invoice_prefix') }}{{ $invoice->invoice_number }}</small>)
                            <h4 class="{{ $loop->odd ? 'text-primary' : 'text-dark'}} invoice-clients invoice_title">
                                {{html_entity_decode($invoice->name)}}
                            </h4>
                        </a>
                    </div>
{{--                    <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"--}}
{{--                                                                class="notification-toggle action-dropdown itemDrp d-none mr-1"><i--}}
{{--                                    class="fas fa-ellipsis-v"></i></a>--}}
{{--                        <div class="dropdown-menu dropdown-menu-right">--}}
{{--                            <div class="dropdown-l  ist-content dropdown-list-icons">--}}
{{--                                <a href="{{ route('client.invoices.edit', ['invoice' => $invoice->id]) }}"--}}
{{--                                   class="dropdown-item dropdown-item-desc edit-btn"><i--}}
{{--                                            class="fas fa-edit mr-2 card-edit-icon"></i>{{ __('messages.common.edit') }}--}}
{{--                                </a>--}}
{{--                                <a href="javascript:void(0)" class="dropdown-item dropdown-item-desc delete-btn"--}}
{{--                                   data-id="{{ $invoice->id }}"><i--}}
{{--                                            class="fas fa-trash mr-2 card-delete-icon"></i>{{ __('messages.common.delete') }}--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </a>--}}
                </div>
                <div class="card-body d-flex justify-content-between pt-1 px-3">
                    <div class="d-table w-100">
                        <span class="d-table-row w-100 invoice-project-tooltip-hover">
                            <span class="d-table-cell w-100 invoice-projects ">
                                <div class="invoice-project-tooltip">
                                     @foreach($invoice->invoiceProjects as $project)
                                        {{$loop->first ? '':', '}}{{ $project->name }}
                                    @endforeach
                                </div>
                                @foreach($invoice->invoiceProjects as $project)
                                    {{ $project->name }}<span class="comma">,</span>
                                @endforeach
                            </span>
                        </span>
                        <span class="d-table-row w-100">
                            <big class="d-table-cell w-50"><span
                                        class="font-weight-bold currency-icon-font">
                                    @if(count($invoice->invoiceProjects) > 0)
                                        &#{{ getCurrencyIcon($invoice->invoiceProjects[0]->currency) }}
                                    @else
                                        &#{{ getCurrencyIcon(1) }}
                                    @endif</span> {{ number_format( $invoice->amount, 2) }}
                            </big>
                            <span class="badge-{{ $invoice->statusTextColor }} badge text-uppercase">{{ $invoice->statusText }}</span>
                        </span>
                        @if(!empty($invoice->due_date))
                            <span class="d-table-row w-100 {{ Carbon\Carbon::now() > Carbon\Carbon::parse($invoice->due_date)  ? 'text-danger' : '' }}">
                                {{Carbon\Carbon::parse($invoice->due_date)->translatedFormat('jS M, Y')}}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.invoice.no_invoice_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.invoice.no_invoice_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                @if($totalInvoices != 0)
                    <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $invoices->firstItem() }}</span> -
                    <span class="font-weight-bold ml-1 mr-1">{{ $invoices->lastItem() }}</span> {{ __('messages.common.of') }}
                    <span class="font-weight-bold ml-1">{{ $invoices->total() }}</span>
                </span>
                @endif
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
