@extends('layouts.app')
@section('title')
    {{__('messages.expense.edit_expense')}}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{__('messages.expense.edit_expense')}}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a class="btn btn-light ml-1" href="{{route('expenses.index')}}">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('flash::message')
                            {{ Form::model($expense, ['route' => ['expenses.update', $expense->id], 'method' => 'put','class' => 'expense-form','files'=>true]) }}

                            @include('expenses.edit_fields')

                            {{ Form::close() }}
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
<script>
    let deleteAttachment = "{{ (__('messages.task.attachments')) }}";
</script>
@section('scripts')
    <script src="{{ mix('assets/js/expense/expense.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection
