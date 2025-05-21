@extends('layouts.app')
@section('title')
    {{ __('messages.role.role_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>    {{ __('messages.role.role_details') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="ml-auto">
                    <a href="{{ route('roles.edit',['role' => $role->id]) }}" class="btn btn-primary form-btn">
                        {{ __('messages.common.edit') }}
                    </a>
                    <a href="{{ route('roles.index') }}" class="btn btn-light form-btn ml-3">
                        {{ __('messages.common.back') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                @include('roles.show_fields')
            </div>
        </div>
    </section>
@endsection
