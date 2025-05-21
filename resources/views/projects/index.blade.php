@extends('layouts.app')
@section('title')
    {{ __('messages.projects') }}
@endsection
@section('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.projects') }}</h1>
            <div class="filter-container section-header-breadcrumb d-block d-md-flex">
                <div class="mr-3 align-items-center">
                    <label for="clients" class="lbl-block mr-2"><b>{{ __('messages.project.client') }}</b></label>
                    {{Form::select('drp_client',$clients,null,['id'=>'filterClient','class'=>'form-control', 'placeholder' => 'All'])  }}
                </div>
                <a href="#" class="btn btn-primary filter-container__btn mt-4 float-right" data-toggle="modal"
                   data-target="#AddModal">{{ __('messages.project.new_project') }} <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('projects')
                            <div id="assignProjectUserModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('messages.project.edit_assignee') }}</h5>
                                            <button type="button" aria-label="Close" class="close outline-none"
                                                    data-dismiss="modal">×
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {!! Form::open(['id'=>'editProjectAssign']) !!}
                                            <div class="alert alert-danger display-none"
                                                 id="editValidationErrorsBox"></div>
                                            <div class="row">
                                                <input type="text" hidden id="hdnProjectId">
                                                <div class="form-group col-sm-12">
                                                    {{ Form::label('assign_to', __('messages.task.assign_to').':') }}
                                                    {{ Form::select('projects[]',$users,null,['class' => 'form-control projectName','id'=>'editProjectUser', 'multiple' => true]) }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary ml-1', 'id' => 'btnSaveAssigneesProject', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                                                <button type="button" class="btn btn-light ml-1" data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('projects.modal')
        @include('projects.add_client_modal')
        @include('projects.edit_modal')
    </section>
@endsection

@section('scripts')
 <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
        <script>
        let canManageClients = "{{ (Auth::user()->can('manage_clients')) ? true : false }}";
        let byDeleteThisProject = "{{ __('messages.project.delete_project_confirm') }}";
        let deleteProjectConfirm = "{{ __('messages.project.by_deleting_this_project') }}";
    </script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{ mix('assets/js/projects/project.js') }}"></script>
    <script src="{{mix('assets/js/input_price_format.js')}}"></script>
@endsection

