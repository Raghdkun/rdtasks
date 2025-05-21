<div id="addTrackerTaskModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.task.new_task') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'addTrackerTaskForm']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('title', __('messages.task.title').':') }}<span class="required">*</span>
                        {{ Form::text('title', null, ['id'=>'title','class' => 'form-control','required']) }}
                    </div>
                    <div class="form-group col-sm-12">
                        {{ Form::label('project_id', __('messages.task.project').':') }}<span
                                class="required">*</span>
                        {{ Form::select('project_id', [], null, ['class' => 'form-control','required', 'id' => 'trackerTaskProjectId', 'placeholder'=>'Select Project']) }}
                    </div>
                    <div class="ml-auto">
                        <div class=" form-group col-sm-12">
                            {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnTrackerTaskSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                            <button type="button" class="btn btn-light ml-1"
                                    data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
