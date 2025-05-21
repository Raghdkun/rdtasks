@extends('layouts.app')
@section('title')
    {{__('messages.expense.expense')}} {{__('messages.common.details')}}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{__('messages.expense.expense')}} {{__('messages.common.details')}}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a href="{{ route('expenses.edit', $expense->id) }}"
                   class="btn btn-primary filter-container__btn mr-2 report-action-btn">
                    {{ __('messages.common.edit') }}
                </a>
                <a class="btn btn-light ml-1 report-action-btn" href="{{route('expenses.index')}}">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('flash::message')
                            @include('expenses.show_fields')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_js')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>

@endsection
@section('scripts')
    <script src="{{ mix('assets/js/expense/expense.js') }}"></script>
    <script src="{{ mix('assets/js/input_price_format.js') }}"></script>
@endsection
