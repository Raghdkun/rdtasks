@extends('layouts.app')
@section('title')
    {{ 'Time Entries Calendar' }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/fullcalendar.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.time_entries') }} {{ __('messages.calendar') }}</h1>
            <div class="filter-container section-header-breadcrumb d-block d-md-flex">
                @if(getLoggedInUser()->hasRole('Admin'))
                    <div class="mr-3 align-items-center">
                        <label for="clients" class="lbl-block mr-2"><b>{{ __('messages.project.users') }}</b></label>
                        {{Form::select('drp_user',$users,null,['id'=>'calendarFilterUser','class'=>'form-control', 'placeholder' => 'All'])  }}
                    </div>
                @endif
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('time_entries.edit_modal')    </section>
@endsection
@section('scripts')
    <script src="{{mix('assets/js/time_entries_calender/time_entries_calender.js')}}"></script>
    <script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
    <script>
        let isShow = false;
        let timeEntryModule = false;
        let canManageEntries = false;
        let loginUserId = {{ getLoggedInUserId() }};
        let loginUserAdmin = "{{ getLoggedInUser()->hasRole('Admin') ? true : false }}";
        let timeLogged = "{{__('messages.time_entry.time_logged')}}";
        let loginUserName = "{{ getLoggedInUser()->name }}";
    </script>
@endsection
