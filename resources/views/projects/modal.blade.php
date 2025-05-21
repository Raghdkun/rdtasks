<div id="AddModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.project.new_project') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'addNewForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('name', __('messages.project.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', null, ['id'=>'name','class' => 'form-control','required','autocomplete' => 'off']) }}
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('prefix', __('messages.project.prefix').':') }}<span class="required">*</span>
                        {{ Form::text('prefix', null, ['id'=>'prefix','class' => 'form-control','required','maxlength'=>'8','onkeypress'=>'return (event.charCode === 8 || (event.charCode >= 65 && event.charCode <= 90)||(event.charCode >= 95 && event.charCode <= 122))||(event.charCode === 0 || (event.charCode >= 48 && event.charCode <= 57))']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('client', __('messages.project.client').':') }}<span class="required">*</span>
                        @if(auth()->user()->can('manage_clients'))
                            <div class="input-group flex-nowrap">
                                {{ Form::select('client_id', $clients, null, ['id'=>'client_id','class' => 'form-control', 'placeholder' => 'Select Client', 'required']) }}
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <a href="#" data-toggle="modal" data-target="#addClientModal" title="{{ __('messages.client.new_client') }}" ><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{ Form::select('client_id', $clients, null, ['id'=>'client_id','class' => 'form-control', 'placeholder' => 'Select Client', 'required']) }}
                        @endif
                    </div>
                    <div class="form-group col-sm-2 col-md-6">
                        {{ Form::label('color', __('messages.common.color').':') }}
                        <div class="color-wrapper"></div>
                        {{ Form::text('color', '#3F51B5', ['id' => 'color', 'hidden', 'class' => 'form-control color']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 project-users">
                        {{ Form::label('user_id', __('messages.project.users').':') }}<span class="required">*</span>
                        {{ Form::select('user_ids[]', $users, null, ['id' => 'user_ids','class' => 'form-control', 'required', 'autocomplete' => 'off', 'multiple' => "multiple"]) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary mr-2','id'=>'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnCancel" class="btn btn-light"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
