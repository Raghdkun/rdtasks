<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end">
        <div class="p-2">
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
    @forelse($reports as $report)
        <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="livewire-card card {{ $loop->odd ? 'card-primary' : 'card-dark'}} shadow mb-5 rounded">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex">
                        <div class="card-report-name w-100">
                            <a href="{{ route('reports.show', ['report' => $report->id]) }}">
                                <h4 class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ html_entity_decode($report->name) }}
                                    (<small
                                            class="{{ $loop->odd ? 'text-primary' : 'text-dark'}}">{{ ($report->report_type == 1)? __('messages.report.dynamic') : __('messages.report.static')  }}</small>)
                                </h4>
                            </a>
                        </div>
                    </div>
                    <a class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                                                                class="notification-toggle action-dropdown d-none mr-1"><i
                                    class="fas fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-list-content dropdown-list-icons">
                                <a href="{{ route('reports.edit', ['report' => $report->id]) }}"
                                   class="dropdown-item dropdown-item-desc edit-btn"
                                   data-id="{{ $report->id }}"><i
                                            class="fas fa-pencil-alt mr-2 btn-sm btn-warning"></i> {{ __('messages.common.edit') }}
                                </a>
                                <a href="#" class="dropdown-item dropdown-item-desc delete-btn"
                                   data-id="{{ $report->id }}"><i
                                            class="fas fa-trash mr-2 btn-sm btn-danger"></i>{{ __('messages.common.delete') }}
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="mr-auto">
                            <b>{{ __('messages.report.start_date') }}: </b>
                            {{ Carbon\Carbon::parse($report->start_date)->translatedFormat('jS M, Y') }}
                        </div>
                        <div class="mt-1">
                            <b>{{ __('messages.report.end_date') }}: </b>
                            {{ Carbon\Carbon::parse($report->end_date)->translatedFormat('jS M, Y') }}
                        </div>
                        <div class="mt-1">
                            <b>{{ __('messages.common.created_by') }}: </b>
                            {{ $report->user->name  }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
            <div class="p-2">
                @if(empty($search))
                    <p class="text-dark">{{ __('messages.report.no_report_available') }}</p>
                @else
                    <p class="text-dark">{{ __('messages.report.no_report_found') }}</p>
                @endif
            </div>
        </div>
    @endforelse

    <div class="mt-0 mb-5 col-12 d-flex justify-content-end">
        <div class="p-2">
            {{ $reports->links() }}
        </div>
    </div>
</div>
