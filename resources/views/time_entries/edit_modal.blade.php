<div id="editTimeEntryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.time_entry.edit_time_entry') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'editTimeEntryForm','class'=>'editTimeEntryForm','files'=>true]) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="teEditValidationErrorsBox"></div>
                {{ Form::hidden('entry_id',null,['id'=>'entryId']) }}
                <div class="row">
                    @if(Auth::user()->can('manage_time_entries'))
                        <div class="form-group col-sm-12">
                            {{ Form::label('User', __('messages.time_entry.user').':') }}<span
                                    class="required">*</span>
                            {{ Form::select('user_id', $users, null, ['id' => 'editTimeUserId', 'class' => 'form-control', 'required', 'placeholder' => 'Select User']) }}
                        </div>
                    @else
                        <input type="hidden" name="user_id" value="{{ getLoggedInUserId() }}">
                    @endif
                    <div class="form-group col-sm-4">
                        {{ Form::label('project', __('messages.time_entry.project').':') }}<span
                                class="required">*</span>
                        {{ Form::select('project_id', $projects, null, ['id'=>'editTimeProjectId','class' => 'form-control','required', 'placeholder'=>'Select Project']) }}
                    </div>
                    <div class="form-group col-sm-8">
                        {{ Form::label('task', __('messages.time_entry.task').':') }}<span class="required">*</span>
                        {{ Form::select('task_id', $tasks, null, ['id'=>'editTaskId','class' => 'form-control','required','placeholder'=>'Select Task']) }}
                    </div>
                    <div class="form-group col-sm-4">
                        {{ Form::label('start_time', __('messages.time_entry.start_time').':') }}<span
                                class="required">*</span>
                        <div id="dvEditStartTime">
                            {{ Form::text('start_time', null, ['class' => 'form-control','id'=>'editStartTime', 'autocomplete' => 'off', 'required']) }}
                        </div>
                    </div>
                    <div class="form-group col-sm-8">
                        {{ Form::label('Activity Type', __('messages.time_entry.activity_type').':') }}<span
                                class="required">*</span>
                        {{ Form::select('activity_type_id', $activityTypes, null, ['id'=>'editActivityTypeId','class' => 'form-control','required','placeholder'=>'Select Task']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 p-0">
                        <div class="form-group col-sm-12">
                            {{ Form::label('end_time', __('messages.time_entry.end_time').':') }}<span
                                    class="required">*</span>
                            <div id="dvEditEndTime">
                                {{ Form::text('end_time', null, ['class' => 'form-control','id'=>'editEndTime', 'autocomplete' => 'off', 'required']) }}
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('duration', __('messages.time_entry.duration_minutes').':') }}
                            <div id="dvEditDuration">
                                {{ Form::number('duration', null, ['class' => 'form-control','id' => 'editDuration', 'readonly']) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-8">
                        {{ Form::label('note', __('messages.time_entry.note').':') }}
                        {{ Form::textarea('note', null, ['class' => 'form-control time-modal-note','id' => 'editNote']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal">{{  __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
