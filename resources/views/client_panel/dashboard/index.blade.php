@extends('client_panel.layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1>{{ __('messages.dashboard') }}</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="page-header">
                                <h5>{{ __('messages.dashboard_menu.project_status') }}</h5>
                            </div>
                            <div id="project-status-container" class="pt-2">
                                <canvas id="client-project-status"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="page-header">
                                <h5>{{ __('messages.dashboard_menu.invoice_status') }}</h5>
                            </div>
                            <div id="client-invoices-container" class="pt-2">
                                <canvas id="client-invoices"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_js')
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
@endsection

@section('scripts')
    <script src="{{ mix('assets/js/clients/dashboard/dashboard.js') }}"></script>
    <script src="{{ mix('assets/js/clients/dashboard/project-status.js') }}"></script>
    <script src="{{ mix('assets/js/clients/dashboard/invoice-status.js') }}"></script>
@endsection
