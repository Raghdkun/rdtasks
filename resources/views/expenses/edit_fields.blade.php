<div class="alert alert-danger display-none" id="validationErrorsBox"></div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('description',  __('messages.common.description').':' )}}
        {{ Form::textarea('description', null, ['class' => 'form-control height-100 expenseDescription', 'id' =>'expenseDescription']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('date',__('messages.time_entry.date').':') }}
        {{ Form::text('date', null, ['class' => 'form-control','autocomplete' => 'off']) }}
    </div>
    
    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('amount', __('messages.invoice.amount').':') }}<span class="required">*</span>
        {{ Form::number('amount', isset($expense->amount) ? $expense->amount : null, ['class' => 'form-control','id'=>'amount','required', 'autocomplete' => 'off','step' => '0.01']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('category',__('messages.expense.category').':') }}
        {{ Form::select('category',$data['category'],isset($expense->category) ? $expense->category : null, ['class' => 'form-control','id' => 'category']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('clientId', __('messages.report.client').':') }}<span class="required">*</span>
        {{ Form::select('client_id', $data['clients'],isset($expense->client_id) ? $expense->client_id :null ,['class' => 'form-control','id' => 'clientId','required','placeholder' => 'Select Client']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('projectId', __('messages.report.project').':') }}<span class="required">*</span>
        {{ Form::select('project_id',$data['projects'],isset($expense->project_id) ? $expense->project_id :null,['class' => 'form-control','id' => 'projectId','required','placeholder' => 'Select Projects']) }}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-12 mt-2">
        <div class="custom-control custom-checkbox mt-4" id="billable_checkbox">
            <input type="checkbox"  class="custom-control-input" id="billable" name="billable" {{($expense->billable == 1) ? 'checked' :''}}>
            <label class="custom-control-label"
                   for="billable">{{__('messages.expense.billable')}}?</label>
        </div>
    </div>
    
    <div class="form-group col-sm-12 col-md-12 col-lg-12">
        {{ Form::label('attachment', __('messages.task.attachments').':') }}
        <span data-toggle="tooltip" title="You can add multiple images & files"><i class="fas fa-question-circle"></i></span>
        <br>
        <label for="attachment" class="image__file-upload btn btn-primary text-color-white">{{__('messages.setting_menu.choose')}}
            {{ Form::file('attachment[]',['id'=>'attachment','class' => 'd-none','multiple']) }}
        </label>
        <label class="font-weight-bold files-count ml-2 pt-0"></label>
        <div id='attachmentPicturePreview' class="m-2"></div>
    @if(count($expense->media) > 0)
            <div class="row expense-attachments-row">
                @foreach($expense->media as $media)
                    <div class="col-md-1 col-sm-1 mb-3">
                        <a href="{{ $media->getFullUrl() }}" target="_blank">
                        <img id='previewImage' class="img-thumbnail expense-thumbnail-preview"
                             src="{{ mediaUrlEndsWith($media->getFullUrl()) }}"/>
                        </a>
                        <br>
                        <a href="#" data-id="{{$media->id}}" class="delete-attachment"><i class="fas fa-trash text-danger" title="{{__('messages.common.delete')}}"></i></a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        <a href="{{ route('expenses.index') }}" class="btn btn-light ml-1">{{ __('messages.common.cancel') }}</a>

    </div>
</div>
