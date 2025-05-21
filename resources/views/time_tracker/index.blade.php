<img class="img-stopwatch" id="imgTimer" alt="">
<div class="chat-popup card-body display-none" id="timeTracker">
    {{ Form::open(['id'=>'timeTrackerForm', 'class' => 'time-tracker-form']) }}
    <div class="modal-body time-tracker-modal">
        <div class="alert alert-danger display-none" id="timeTrackerValidationErrorsBox"></div>
        <div class="row">
            <div class="form-group col-sm-4">
                {{ Form::label('project_id', __('messages.task.project')) }}<span class="required">*</span>
                {{ Form::select('project_id', [], null, ['id' => 'tmProjectId','class' => 'form-control', 'placeholder' => 'Select Project', 'required']) }}
            </div>
            <div class="form-group col-sm-5">
                {{ Form::label('task_id', __('messages.time_entry.task')) }}<span class="required">*</span>
                <div class="input-group flex-nowrap">
                    {{ Form::select('task_id', [], null, ['id' => 'tmTaskId','class' => 'form-control', 'placeholder' => 'Select Task', 'required']) }}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <a href="javascript:void(0)" id="addTaskTracker" title="{{ __('messages.task.new_task') }}"><i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-3">
                {{ Form::label('activity_type_id', __('messages.time_entry.activity_type')) }}<span
                        class="required">*</span>
                {{ Form::select('activity_type_id', [], null, ['id'=>'tmActivityId','class' => 'form-control', 'placeholder' => 'Select Activity', 'required']) }}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-9">
                {{ Form::label('notes', __('messages.invoice.notes')) }} <span id="tmNotesErr"></span>
                {{ Form::textarea('note', null, ['class' => 'form-control', 'id' => 'tmNotes', 'rows' => 5]) }}
            </div>
            <div class="form-group col-sm-3">
                <div class="mt-5">
                    <h3 id="timer"><b>00:00:00</b></h3>
                    <div class="timer-button">
                        <button class="btn btn-success time-tracker-form__btn" id="startTimer">
                            <i id="startTimeTracker" class="far fa-play-circle"></i> {{ __('messages.task.start') }}
                        </button>
                        <button class="btn btn-danger time-tracker-form__btn display-none" id="stopTimer">
                            <i id="stopTimeTracker" class="far fa-stop-circle"></i> {{ __('messages.task.stop') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
