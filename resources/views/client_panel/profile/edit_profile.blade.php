<div id="EditProfileModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.user.edit_profile') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'editProfileForm','files'=>true]) }}
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="editProfileValidationErrorsBox"></div>
                {{ Form::hidden('client_id',null,['id' => 'pfClientId']) }}
                {{ Form::hidden('is_active',1) }}
                {{ Form::hidden('user_id',null,['id' => 'pfUserId']) }}
                {{csrf_field()}}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.user.name').':') }}<span class="required">*</span>
                        {{ Form::text('name', null, ['id'=>'pfName','class' => 'form-control','required', 'autofocus', 'tabindex' => "1"]) }}
                    </div>
                    <div class="form-group col-sm-6 d-flex">
                        <div class="col-sm-4 col-md-6 pl-0 form-group">
                            {{ Form::label('photo', __('messages.user.profile_image').':') }}
                            <br>
                            <label
                                    class="image__file-upload btn btn-primary text-color-white"
                                    tabindex="2"> {{ __('messages.setting_menu.choose') }}
                                {{ Form::file('photo',['id'=>'pfImage','class' => 'd-none']) }}
                            </label>
                        </div>
                        <div class="col-sm-3 preview-image-video-container float-right mt-1">
                            <img id='edit_preview_photo' class="img-thumbnail user-img user-profile-img profilePicture"
                                 src="{{asset('assets/img/user-avatar.png')}}"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('email', __('messages.user.email').':') }}<span class="required">*</span>
                        {{ Form::text('email', null, ['id'=>'pfEmail','class' => 'form-control','required', 'tabindex' => "3"]) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnPrEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing...", 'tabindex' => "5"]) }}
                    <button type="button" class="btn btn-light ml-1 edit-cancel-margin margin-left-5"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

