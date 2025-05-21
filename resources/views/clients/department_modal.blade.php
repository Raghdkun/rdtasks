<div id="addDepartmentModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.department.new_department') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'addNewDepartment']) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="departmentValidationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('name', __('messages.department.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', '', ['id' => 'name', 'class' => 'form-control', 'required']) }}
                    </div>
                    <div class="form-group d-flex flex-column col-sm-12">
                        <div class="color-wrapper"></div>
                        {{ Form::hidden('color', '#3F51B5', ['id' => 'color', 'hidden', 'class' => 'form-control color']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnDepartmentSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
