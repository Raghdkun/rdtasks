@extends('layouts.app')
@section('title')
    {{ __('messages.tags') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        @include('flash::message')
        <div class="section-header">
            <h1 class="page__heading">{{ __('messages.tags') }}</h1>
            <div class="filter-container section-header-breadcrumb">
                <div class="ml-auto">
                    <a href="#" class="btn btn-primary addBulkTags addTags mr-md-3 mr-1" onclick="setBulkTags()"
                       data-toggle="modal"
                       data-target="#AddModal">{{ __('messages.tag.bulk_tags') }} <i class="fas fa-plus"></i></a>
                    <a href="#" class="btn btn-primary addTags" data-toggle="modal"
                       data-target="#AddModal">{{ __('messages.tag.new_tag') }} <i class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('tags')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('tags.modal')
        @include('tags.edit_modal')
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let addBulkTag = "{{ __('messages.tag.add_bulk_tag') }}";
        let newTag = "{{ __('messages.tag.new_tag') }}";
    </script>
    <script src="{{ mix('assets/js/tags/tag.js') }}"></script>
@endsection

