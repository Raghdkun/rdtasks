<!-- Name Field -->
<div class="form-group col-sm-6">
    {{ Form::label('name', __('messages.user.name').':') }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {{ Form::label('email', __('messages.user.email').':') }}
    {{ Form::email('email', null, ['class' => 'form-control']) }}
</div>

<!-- Website Field -->
<div class="form-group col-sm-6">
    {{ Form::label('website', __('messages.user.website').':') }}
    {{ Form::text('website', null, ['class' => 'form-control']) }}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary']) }}
    <a href="{{ route('clients.index') }}" class="btn btn-default">{{ __('messages.common.cancel') }}</a>
</div>
