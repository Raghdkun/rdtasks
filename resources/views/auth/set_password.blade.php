@extends('layouts.auth_app')
@section('title')
    Set Password
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('web/css/web.css') }}" type="text/css">
@endsection
@section('content')
    <div class="card card-primary">
        <div class="card-header"><h4>Set Password</h4></div>

        <div class="card-body pt-1">
            @if( Session::has( 'error' ))
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <li>{{ Session::get( 'error' ) }}</li>
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ url('set-password') }}">
                {{ csrf_field() }}
                @if ($errors->any())
                    <div class="alert alert-danger p-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    <label for="email">Enter Email</label><span class="text-danger">*</span>
                    <input type="email" class="form-control" disabled name="email"
                           value="{{ $user->email }}"
                           placeholder="Enter Email" autofocus tabindex="1">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Password</label><span class="text-danger">*</span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required
                           tabindex="2" id="password">
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Confirm password</label><span class="text-danger">*</span>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Confirm password" id="confirmPassword" required tabindex="3">
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
