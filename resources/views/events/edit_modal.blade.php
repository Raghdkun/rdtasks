<div id="EditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('messages.event.edit_event')}}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'editForm']) }}
            {{ Form::hidden('id',null,['id'=>'Id']) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="editValidationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('title', __('messages.task.title').':') }}<span class="required">*</span>
                        {{ Form::text('title', '', ['id' => 'editTitle', 'class' => 'form-control', 'required']) }}
                    </div>
                    <div class="form-group col-xl-6 col-sm-12">
                        {{ Form::label('start_date',  __('messages.report.start_date').':') }}<span
                                class="required">*</span>
                        {{ Form::text('start_date', null, ['id'=>'editStartDate','class' => 'form-control', 'autocomplete' => 'off','required']) }}
                    </div>
                    <div class="form-group col-xl-6 col-sm-12">
                        {{ Form::label('end_date', __('messages.report.end_date').':') }}<span class="required">*</span>
                        {{ Form::text('end_date', null, ['id'=>'editEndDate','class' => 'form-control', 'autocomplete' => 'off','required']) }}
                    </div>
                    <div class="form-group col-xl-6 col-sm-12">
                        {{ Form::label('type', __('messages.time_entry.type').':') }}<span class="required">*</span>
                        {{ Form::select('type', \App\Models\Event::EVENTS, null, ['id'=>'editType','class' => 'form-control', 'autocomplete' => 'off','required']) }}
                    </div>
                    <div class="form-group d-flex flex-column col-sm-12">
                        {{ Form::label('description', __('messages.common.description').':') }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'id' => 'editDescription']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
