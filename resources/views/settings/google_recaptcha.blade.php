<div class="row">
    <div class="form-group col-sm-12 mb-5 mt-10">
        <label class="custom-switch pl-0 d-block">
            <input type="checkbox" name="show_recaptcha" class="custom-switch-input" value="1"
                   {{$settings['show_recaptcha'] == 1 ? "checked" : ""}}  id="showRecaptcha" tabindex="10">
            <span class="custom-switch-indicator"></span>&nbsp;&nbsp;{{ __('messages.setting_menu.show_recaptcha')}}
        </label>
    </div>
</div>
<div class="row google_captcha_key">
    <!-- Company Name Field -->
    <div class="form-group col-sm-12">
        {{ Form::label('google_recaptcha_site_key', __('messages.setting_menu.google_recaptcha_site_key').':') }}<span class="required">*</span>
        {{ Form::text('google_recaptcha_site_key', $settings['google_recaptcha_site_key'], ['class' => 'form-control google-recaptcha-site-key',  ]) }}
    </div>

    <!-- company Email Field -->
    <div class="form-group col-sm-12">
        {{ Form::label('google_recaptcha_secret_key', __('messages.setting_menu.google_recaptcha_secret_key').':') }}<span class="required">*</span>
        {{ Form::text('google_recaptcha_secret_key', $settings['google_recaptcha_secret_key'], ['class' => 'form-control google-recaptcha-secret-key',]) }}
    </div>
</div>

<div class="clearfix"></div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        {{ Form::reset(__('messages.common.cancel'), ['class' => 'btn btn-light ml-1']) }}
    </div>
</div>

