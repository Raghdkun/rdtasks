@extends('layouts.app')
@section('title')
    Days Off Settings
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Days Off Settings</h1>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(['id' => 'daysOffSettingsForm']) }}
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    {{ Form::label('days_per_week', 'Days Per Week:') }}<span class="required">*</span>
                                    {{ Form::number('days_per_week', $settings->days_per_week ?? 2, ['id' => 'daysPerWeek', 'class' => 'form-control', 'min' => 1, 'max' => 7, 'required']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('start_week_day', 'Week Start Day:') }}<span class="required">*</span>
                                    {{ Form::select('start_week_day', $weekDays, $settings->start_week_day ?? 1, ['id' => 'startWeekDay', 'class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('description', 'Description:') }}
                                    {{ Form::textarea('description', $settings->description ?? '', ['id' => 'description', 'class' => 'form-control', 'rows' => 3]) }}
                                </div>
                            </div>
                            <div class="text-right">
                                {{ Form::button($settings ? 'Update Settings' : 'Save Settings', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave']) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $('#daysOffSettingsForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            let url = @if($settings) '{{ route("days-off-settings.update", $settings->id) }}' @else '{{ route("days-off-settings.store") }}' @endif;
            let method = @if($settings) 'PUT' @else 'POST' @endif;
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    displaySuccessMessage(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    displayErrorMessage(xhr.responseJSON.message);
                }
            });
        });
    </script>
@endsection