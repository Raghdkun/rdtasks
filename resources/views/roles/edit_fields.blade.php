<div class="row">

    <!-- Name Field -->
    <div class="form-group col-sm-12 col-md-6">
        {{ Form::label('name', __('messages.role.name').':') }}<span class="required">*</span>
        {{ Form::text('name', null, ['class' => 'form-control','required']) }}
    </div>

    <!-- Detail Field -->
    <div class="form-group col-sm-12 col-md-6">
        @if(!$roles->permissions()->where('name','=','role_client')->exists())
        {{ Form::label('permissions', __('messages.role.permissions').':') }}
        <div class="row">
            @foreach($permissions as $key=>$value)
                @if(in_array($key,$roles->permissions->pluck('id')->toArray()))
                    <div class="checkbox col-md-6 role-checkbox">

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permission{{ $key }}"
                                   name="permissions[]"
                                   value="{{$key}}" checked>
                            <label class="custom-control-label"
                                   for="permission{{ $key }}">{{$value}}</label>
                        </div>
                    </div>
                @else
                    <div class="checkbox col-md-6 role-checkbox">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permission{{ $key }}"
                                   name="permissions[]"
                                   value="{{$key}}">
                            <label class="custom-control-label"
                                   for="permission{{ $key }}">{{$value}}</label>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @else
            <input type="hidden" class="custom-control-input" id="permissionrole_client"
                   name="permissions[]"
                   value="{{$roles->permissions()->where('name','=','role_client')->value('id')}}">
        @endif
    </div>

</div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('description', __('messages.role.description').':') }}
        {{ Form::textarea('description', null, ['class' => 'form-control height-100','rows'=>5, 'id' =>'editDescription']) }}
    </div>
</div>
{{ Form::hidden('id',$roles->id) }}
<!-- Submit Field -->
<div class="form-group col-sm-12 rolesBtn">
    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    <a href="{{ route('roles.index') }}" class="btn btn-light ml-1">{{ __('messages.common.cancel') }}</a>
</div>
