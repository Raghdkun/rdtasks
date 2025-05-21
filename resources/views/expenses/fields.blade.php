<div class="alert alert-danger display-none" id="validationErrorsBox"></div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('description', __('messages.common.description').':')}}
        <textarea id="expenseDescription" name="description" class="form-control expenseDescription"></textarea>
    </div>
    
    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('date',__('messages.time_entry.date').':') }}
        {{ Form::text('date', null, ['class' => 'form-control','autocomplete' => 'off']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('amount', __('messages.invoice.amount').':') }}<span class="required">*</span>
        {{ Form::number('amount', null, ['class' => 'form-control','id'=>'amount','required', 'autocomplete' => 'off','step' => '0.01']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('category',__('messages.expense.category').':') }}
        {{ Form::select('category',$category, ['class' => 'form-control','id' => 'category']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('clientId', __('messages.report.client').':') }}<span class="required">*</span>
        {{ Form::select('client_id', $clients,null ,['class' => 'form-control','id' => 'clientId','required','placeholder' => 'Select Client']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('projectId', __('messages.report.project').':') }}<span class="required">*</span>
        {{ Form::select('project_id',$projects,null,['class' => 'form-control','id' => 'projectId','required','placeholder' => 'Select Project']) }}
    </div>
    
    <div class="form-group col-lg-4 col-md-4 col-sm-12 mt-2">
        <div class="custom-control custom-checkbox mt-4" id="billable_checkbox">
            <input type="checkbox" class="custom-control-input" id="billable" value="1" name="billable">
            <label class="custom-control-label"
                   for="billable">{{__('messages.expense.billable')}}?</label>
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-12 col-lg-12">
        {{ Form::label('attachment', __('messages.task.attachments').':') }}
        <span data-toggle="tooltip" title="You can add multiple images & files"><i class="fas fa-question-circle"></i></span>
        <br>
        <label for="attachment" class="image__file-upload btn btn-primary text-color-white">{{__('messages.setting_menu.choose')}}
            {{ Form::file('attachment[]',['id' => 'attachment','class' => 'd-none','multiple']) }}
        </label>
        <label class="font-weight-bold files-count ml-2 pt-0"></label>
        <div id='attachmentPicturePreview' class="mb-3"></div>
    </div>
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        <a href="{{ route('expenses.index') }}" class="btn btn-light ml-1">{{ __('messages.common.cancel') }}</a>
      
    </div>
</div>
