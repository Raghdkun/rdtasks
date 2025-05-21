<div class="row">
    <!-- Name Field -->
    <div class="form-group col-sm-12 col-md-6">
        {{ Form::label('name', __('messages.role.name').':') }}<span class="required">*</span>
        {{ Form::text('name', null, ['class' => 'form-control','required']) }}
    </div>

    <!-- Detail Field -->
    <div class="form-group col-sm-12 col-md-6">
        {{ Form::label('detail', __('messages.role.permissions').':') }}
        <div class="row">
            @foreach($permissions as $key=>$value)
                <div class="checkbox col-md-6 role-checkbox">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="permission{{ $key }}"
                               name="permissions[]"
                               value="{{$key}}">
                        <label class="custom-control-label"
                               for="permission{{ $key }}">{{$value}}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('detail', __('messages.role.description').':') }}
        {{ Form::textarea('description', null, ['class' => 'form-control height-100', 'id' =>'description', 'rows'=>5]) }}
    </div>
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12 rolesBtn">
    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    <a href="{{ route('roles.index') }}" class="btn btn-light ml-1">{{ __('messages.common.cancel') }}</a>
</div>
