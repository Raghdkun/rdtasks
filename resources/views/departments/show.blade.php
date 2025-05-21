<div class="modal fade" tabindex="-1" role="dialog" id="showModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.department.department_details') }}</h5>
                <button type="button" class="close outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="row details-page">
                    <div class="form-group col-sm-12">
                        {{ Form::label('name', __('messages.department.name').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        <span id="showName"></span>
                    </div>
                    <div class="form-group col-sm-12 mb-1">
                        {{ Form::label('description', __('messages.common.description').(':'),['class'=>'font-weight-bold']) }}
                        <br>
                        <div id="showDescription" style="max-height: 150px;overflow: auto"></div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

