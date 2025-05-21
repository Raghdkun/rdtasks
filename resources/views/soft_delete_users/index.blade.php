@extends('layouts.app')
@section('title')
    {{__('messages.archived_users')}}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{__('messages.archived_users')}}</h1>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('soft_delete_users.table')
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
    <script>
        let asset = '{{asset('uploads')}}';
    </script>
    <script src="{{ mix('assets/js/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/soft_delete/soft-delete.js') }}"></script>
    <script src="{{ mix('assets/js/input_price_format.js') }}"></script>
@endsection
