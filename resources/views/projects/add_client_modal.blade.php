<div id="addClientModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.client.new_client') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addNewClientForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="clientValidationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.client.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', null, ['id' => 'clientName', 'class' => 'form-control', 'required','tabindex' => '1']) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('department_id', __('messages.client.department').':') }}<span class="required">*</span>
                        <div class="input-group flex-nowrap">
                            {{ Form::select('department_id', $departments, null, ['id' => 'department_id', 'class' => 'form-control', 'required','placeholder'=>'Select Department','tabindex' => '2']) }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnClientSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..." , 'tabindex' => '3']) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal" tabindex="9">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
