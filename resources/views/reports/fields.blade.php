<!-- Name Field -->
<div class="alert alert-danger display-none" id="validationErrorsBox"></div>
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('name', __('messages.report.name').':') }}<span class="required">*</span>
        {{ Form::text('name', null, ['class' => 'form-control','required','id'=>'name']) }}
    </div>

    <!-- Department Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('department_id', __('messages.report.department').':') }}
        {{ Form::select('department_id', $departments, isset($departmentId) && count($departmentId) == 1?$departmentId:0 , ['class' => 'form-control','id' => 'department','placeholder' => 'All Departments']) }}
    </div>

    <!-- Start Time Field -->
    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('start_date', __('messages.report.start_date').':') }}<span class="required">*</span>
        {{ Form::text('start_date', null, ['class' => 'form-control','id'=>'start_date','required', 'autocomplete' => 'off']) }}
    </div>

    <!-- End Time Field -->
    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('end_date', __('messages.report.end_date').':') }}<span class="required">*</span>
        {{ Form::text('end_date', null, ['class' => 'form-control','id'=>'end_date','required', 'autocomplete' => 'off']) }}
    </div>

    <!-- Client Field -->
    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('clientId', __('messages.report.client').':') }}
        {{ Form::select('client_id', (isset($clients) ? $clients : []), isset($clientId) && count($clientId) == 1?$clientId:0, ['class' => 'form-control','id' => 'clientId','placeholder' => 'All Clients']) }}
    </div>

    <!-- Projects Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('projectIds', __('messages.report.project').':') }}
        {{ Form::select('projectIds[]', $projects, isset($projectIds) && $report->meta['all_projects'] != true?$projectIds:null, ['class' => 'form-control','id' => 'projectIds','multiple' => true,]) }}
    </div>

    <!-- Users Field -->
    @can('manage_reports')
    <div class="form-group col-sm-6">
        {{ Form::label('users', __('messages.report.users').':') }}
        {{ Form::select('userIds[]', $users, isset($userIds) && $report->meta['all_users'] != true?$userIds:null, ['class' => 'form-control','id'=>'userIds','multiple' => true]) }}
    </div>
@endcan

    <!-- tags Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('tags', __('messages.report.tags').':') }}
        {{ Form::select('tagIds[]', $tags,isset($tagIds)?$tagIds:null, ['class' => 'form-control','id'=>'tagIds','multiple' => true]) }}
    </div>

    <div class="col-sm-6 form-group">
        {{ Form::label('report_type', __('messages.report.report_type').':') }}<span class="required">*</span>
        &nbsp;<br>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-sm-12">
                <div class="custom-radio pl-4 pb-1">
                    <input type="radio" id="customRadio1" name="report_type" value="1" class="custom-control-input"
                           checked>
                    <label class="custom-control-label" for="customRadio1">{{ __('messages.report.dynamic') }}</label>
                    &nbsp;&nbsp; <span data-toggle="tooltip" data-html="true"
                                       title="It will auto populate the time changes on report."><i
                                class="fas fa-question-circle"></i></span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-12">
                <div class="custom-radio pl-4">
                    <input type="radio" id="customRadio2" name="report_type" value="2"
                           class="custom-control-input" {{ isset($report->report_type) && $report->report_type == 2 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="customRadio2">{{ __('messages.report.static') }}</label>
                    &nbsp;&nbsp; <span data-toggle="tooltip" data-html="true"
                                       title="It will not reflect the time changes once it's generated."><i
                                class="fas fa-question-circle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary rptBtn rptBtnAlign save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        <a href="{{ route('reports.index') }}" class="btn btn-light ml-1 rptBtn">{{ __('messages.common.cancel') }}</a>
        <a href="#" class="btn btn-success ml-1 preview-btn rptBtn"
           data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">{{ __('messages.common.preview') }}</a>
    </div>
</div>
