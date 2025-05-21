@extends('layouts.app')
@section('title')
{{ __('messages.reports') }}
@endsection
@section('page_css')
    <link href="{{mix('assets/style/css/report.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('css')
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.reports') }}</h1>
            <div class="filter-container section-header-breadcrumb d-block d-md-flex">
               @if(Auth::user()->hasRole('Admin'))
                    <div class="mr-3 align-items-center">
                        <label class="lbl-block mr-2"><b>{{ __('messages.common.created_by') }}</b></label>
                        {{Form::select('created_by', $users, getLoggedInUserId(),['id'=>'filterCreatedBy','class'=>'form-control tasks-w-150', 'placeholder' => 'All'])  }}
                    </div>
                @endcan
                <a href="{{ route('reports.create') }}" class="btn btn-primary mt-4 filter-container__btn float-right">
                    {{ __('messages.report.new_report') }} <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('reports.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        let projectsOfClient = "{{ url('projects-of-client') }}";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
    <script src="{{ mix('assets/js/custom-datatable.js') }}"></script>
@endsection

