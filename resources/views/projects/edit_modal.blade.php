<div id="ProjectEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.project.edit_project') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'editFormProject','files'=>true]) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none editValidationErrorsBox" id="editValidationErrorsBox"></div>
                {{ Form::hidden('project_id',null,['id'=>'projectId']) }}
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('name', __('messages.project.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', '', ['id'=>'edit_name','class' => 'form-control','required']) }}
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('prefix', __('messages.project.prefix').':') }}<span class="required">*</span>
                        {{ Form::text('prefix', '', ['id'=>'edit_prefix','class' => 'form-control','required','maxlength'=>'8','onkeypress'=>'return (event.charCode === 8 || (event.charCode >= 65 && event.charCode <= 90)||(event.charCode >= 95 && event.charCode <= 122))||(event.charCode === 0 || (event.charCode >= 48 && event.charCode <= 57))']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6 project-users">
                        {{ Form::label('user_id', __('messages.project.users').':') }}<span class="required">*</span>
                        {{ Form::select('user_ids[]', $users, null, ['id' => 'edit_user_ids','class' => 'form-control', 'required', 'multiple']) }}
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        {{ Form::label('client', __('messages.project.client').':') }}<span class="required">*</span>
                        @if(auth()->user()->can('manage_clients'))
                            <div class="input-group flex-nowrap">
                                {{ Form::select('client_id', $clients, null, ['id'=>'edit_client_id','class' => 'form-control','required']) }}
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <a href='#' data-toggle='modal' data-target='#addClientModal' title='{{ __("messages.client.new_client") }}' ><i class='fa fa-plus'></i></a>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{ Form::select('client_id', $clients, null, ['id'=>'edit_client_id','class' => 'form-control','required']) }}
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-2 col-md-6">
                        {{ Form::label('price', __('messages.project.budget').':') }}<span class="required">*</span>
                        {{ Form::text('price', null, ['id'=>'editPrice','class' => 'form-control price-input mobile-project-budget','required','maxlength' => '15','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                    </div>
                    <div class="form-group col-sm-4 col-md-6">
                        {{ Form::label('price', __('messages.project.budget_type').':') }}<span
                                class="required">*</span>
                        <span class="project-tooltip-hover">
                                <sup><i class="fas fa-question-circle"></i></sup>
                                <div class="project-tooltip-popup">
                                   Hourly: Amount for task in the invoice will be counted as per hourly rate. eg.02:00 H * 20 rate/Hr<br> 
                                   Fix Rate: Invoice total amount will be taken as per fixed rate. no hourly calculation of tasks.
                                </div>
                        </span>
                        {{ Form::select('budget_type', $budgetTypes, null, ['id'=>'edit_budget_type','class' => 'form-control', 'placeholder' => 'Select Budget Type', 'required']) }}
                    </div>
                    <div class="form-group col-sm-4 col-md-6">
                        {{ Form::label('currency', __('messages.setting_menu.currency').':') }}<span
                                class="required">*</span>
                        <select id="editCurrency" data-show-content="true" class="form-control" name="currency"
                                required>
                            <option value="">Select Currency</option>
                            @foreach($currencies as $key => $currency)
                                <option value="{{$key}}" {{getCurrencyIcon($key) == $key ? 'selected' : ''}}>
                                    &#{{getCurrencyIcon($key)}}&nbsp;&nbsp;&nbsp; {{$currency}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-2 col-md-4">
                        {{ Form::label('status', __('messages.invoice.status').':') }}<span class="required">*</span>
                        {{ Form::select('status', $projectStatus, null, ['id'=>'editStatusProject','class' => 'form-control', 'placeholder' => 'Select Status', 'required']) }}
                    </div>
                    <div class="form-group col-sm-2 col-md-2">
                        {{ Form::label('color', __('messages.common.color').':') }}
                        <div class="color-wrapper"></div>
                        {{ Form::text('color', '', ['id' => 'edit_color', 'hidden', 'class' => 'form-control color']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group d-flex flex-column col-sm-12">
                        {{ Form::label('description', __('messages.common.description').':') }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'id' => 'editDescription']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
