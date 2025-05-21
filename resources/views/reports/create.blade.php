@extends('layouts.app')
@section('title')
    {{ __('messages.report.new_report') }}
@endsection
@section('page_css')
    <link href="{{mix('assets/style/css/report.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.report.new_report') }}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a class="btn btn-light ml-1" href="{{route('reports.index')}}">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('layouts.errors')
                            {{ Form::open(['route' => 'reports.store', 'class' => 'report-form']) }}

                            @include('reports.fields')

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        let taskUrl = '{{url('tasks')}}/';
        let reportStartDate = '';
        let reportEndDate = '';
        let taskDetailActionColumnIsVisible = false;
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection
