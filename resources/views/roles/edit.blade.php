@extends('layouts.app')
@section('title')
    {{ __('messages.role.edit_role') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.role.edit_role') }}</h1>
            <div class="filter-container section-header-breadcrumb justify-content-end">
                <a class="btn btn-light ml-1" href="{{route('roles.index')}}">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            @if ($errors->any())
                <div class="alert alert-danger p-2">
                    <ul class="list-unstyled mb-0">
                        <li>{{ $errors->first() }}</li>
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::model($roles, ['route' => ['roles.update', $roles->id], 'method' => 'put', 'id'=>'editRoleForm']) }}
                            @include('roles.edit_fields')
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
    <script src="{{ mix('assets/js/roles/create-edit.js') }}"></script>
@endsection
