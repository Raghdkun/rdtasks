@extends('layouts.app')
@section('title')
    {{__('messages.events')}}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/style/css/events.css') }}">
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{__('messages.events')}}</h1>
            <div class="filter-container section-header-breadcrumb d-block d-md-flex">
                <div class="mr-3 align-items-center">
                    {{Form::text('eventsFilter',\Carbon\Carbon::now()->format('F/Y'),['id'=>'eventsCalendarFilter','class'=>'form-control', 'placeholder' => 'Select Month'])  }}
                </div>
                <br>
                @if(getLoggedInUser()->can('manage_events'))
                    <a href="#" class="btn btn-primary filter-container__btn mt-0 addEvent" data-toggle="modal"
                       data-target="#AddModal">{{__('messages.event.new_event')}}
                        <i class="fas fa-plus"></i></a>
                @endif
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="calenders"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('events.add_modal')
        @include('events.edit_modal')
    </section>
@endsection

@section('scripts')
    <script>
        let permission = "{{ Auth::user()->can('manage_events') ? true : false }}";
        let eventDeleteText = '{{__('messages.common.delete')}}';
    </script>
    <script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{mix('assets/js/events/events.js')}}"></script>
@endsection
