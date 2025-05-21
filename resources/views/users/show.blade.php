@extends('layouts.app')
@section('title')
    {{ __('messages.user.user_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.user.user_details') }}</h1>
            <div class="section-header-breadcrumb justify-content-end">
                <a href="#" class="btn btn-primary form-btn edit-btn" data-id="{{ $user->id }}">
                    {{ __('messages.common.edit') }}
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-light form-btn ml-3">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                @include('users.show_fields')
            </div>
        </div>
        @include('users.edit_modal')
    </section>
@endsection

@section('scripts')
    <script src="{{ mix('assets/js/users/user.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection
