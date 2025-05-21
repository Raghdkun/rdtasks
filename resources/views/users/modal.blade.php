<div id="AddModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.user.new_user') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'addNewForm','files'=>true]) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="validationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.user.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', null, ['id'=>'name','class' => 'form-control','required', 'autofocus', 'tabindex' => "1"]) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('phone', __('messages.user.phone').':') }}
                        {{ Form::tel('phone', null, ['id'=>'phone','class' => 'form-control', 'tabindex' => "2",'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('email', __('messages.user.email').':') }}<span class="required">*</span>
                        {{ Form::email('email', null, ['id'=>'email','class' => 'form-control','required', 'tabindex' => "3"]) }}
                    </div>
                </div>
                @can('manage_users')
                    <div class="row">
                        <div class="form-group col-sm-6">
                            {{ Form::label('password', __('messages.user.new_password').':') }}<span
                                    class="required confirm-pwd">*</span>
                            <div class="input-group">
                                <input class="form-control input-group__addon" id="new_password" type="password"
                                       name="password" tabindex="4">
                                <div class="input-group-append input-group__icon">
                                <span class="input-group-text changeType">
                                    <i class="icon-ban icons"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            {{ Form::label('password_confirmation', __('messages.user.confirm_password').':') }}<span
                                    class="required confirm-pwd">*</span>
                            <div class="input-group">
                                <input class="form-control input-group__addon" id="new_confirm_password" type="password"
                                       name="password_confirmation" tabindex="5">
                                <div class="input-group-append input-group__icon">
                                <span class="input-group-text changeType">
                                    <i class="icon-ban icons"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                <div class="row">
                    <div class="form-group col-sm-12 user-projects">
                        {{ Form::label('project_id', __('messages.user.project').':') }}
                        {{ Form::select('project_ids[]', $projects, null, ['class' => 'form-control', 'id' => 'projectId','multiple'=>true, 'tabindex' => "6"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('role_id', __('messages.user.role').':') }}<span class="required">*</span>
                        {{ Form::select('role_id', $roles, null, ['class' => 'form-control', 'id' => 'roleId','placeholder'=>'Select Role', 'required', 'tabindex' => "7"]) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('salary', __('messages.user.salary').':') }}
                        {{ Form::text('salary', null, ['id'=>'salary','class' => 'form-control price-input', 'autocomplete' => 'off',  'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'tabindex' => "8"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{ Form::label('photo', __('messages.user.profile_image').':') }}
                                    <br>
                                    <label class="image__file-upload btn btn-primary text-color-white" tabindex="9">
                                        {{ __('messages.setting_menu.choose') }}
                                        {{ Form::file('photo',['id'=>'userProfile','class' => 'd-none']) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class=" preview-image-video-container">
                                    <img id='previewImage' class="img-thumbnail thumbnail-preview"
                                         src="{{asset('assets/img/user-avatar.png')}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group ">
                            {{ Form::label('active', __('messages.task.status').':') }}
                            <label class="custom-switch pl-0 d-block">
                                <input type="checkbox" name="is_active" class="custom-switch-input" value="1"
                                       checked="" tabindex="10">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing...", 'tabindex' => "11"]) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
