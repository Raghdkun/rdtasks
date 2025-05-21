<div id="EditModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.user.edit_user') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'editForm','files'=>true]) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="editValidationErrorsBox"></div>
                {{ Form::hidden('user_id',null,['id'=>'userId']) }}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.user.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', null, ['id'=>'edit_name','class' => 'form-control','required', 'autofocus', 'tabindex' => "1"]) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('phone', __('messages.user.phone').':') }}
                        {{ Form::text('phone', null, ['id'=>'edit_phone','class' => 'form-control','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','minlength=10','maxlength=10','tabindex' => "2"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('email', __('messages.user.email').':') }}<span class="required">*</span>
                        {{ Form::email('email', null, ['id'=>'edit_email','class' => 'form-control','required',"autocomplete"=>"new-password", 'tabindex' => "3"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 user-projects">
                        {{ Form::label('project_id', __('messages.user.project').':') }}
                        {{ Form::select('project_ids[]', $projects, null, ['class' => 'form-control','id' => 'editProjectId', 'multiple'=>true, 'tabindex' => "4"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('active', __('messages.user.role').':') }}<span class="required">*</span>
                        {{ Form::select('role_id', $roles, null, ['class' => 'form-control', 'id' => 'editRoleId', 'tabindex' => "5"]) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('salary',  __('messages.user.salary').':') }}
                        {{ Form::text('salary', null, ['id'=>'edit_salary','class' => 'form-control price-input', 'autocomplete' => 'off',  'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'tabindex' => "6"]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{ Form::label('photo', __('messages.user.profile_image').':') }} <br>
                                    <label
                                            class="image__file-upload btn btn-primary text-color-white"
                                            tabindex="7"> {{ __('messages.setting_menu.choose') }}
                                        {{ Form::file('photo',['id'=>'userEditProfile','class' => 'd-none']) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class=" preview-image-video-container">
                                    <img id='editPreviewImage' class="img-thumbnail thumbnail-preview"
                                         src="{{asset('assets/img/user-avatar.png')}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('role_id', __('messages.task.status').':') }}<br>
                            <label class="custom-switch pl-0">
                                <input type="checkbox" name="is_active" class="custom-switch-input" id="edit_is_active"
                                       tabindex="8">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('email_verified_at', __('messages.user.email_is_verified').':') }}<br>
                            <label class="custom-switch pl-0">
                                <input type="checkbox" name="email_verified_at" class="custom-switch-input" id="edit_email_verified_at"
                                       tabindex="9">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing...", 'tabindex' => "9"]) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
