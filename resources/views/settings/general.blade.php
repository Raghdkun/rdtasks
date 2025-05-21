<div class="row">
    <input name="group" type="hidden" value="{{ \App\Models\Setting::GROUP_GENERAL }}">
    <!-- App Name Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('app_name', __('messages.setting_menu.app_name').':') }}<span class="required">*</span>
        {{ Form::text('app_name', $settings['app_name'], ['class' => 'form-control', 'required']) }}
    </div>

    <!-- Company Name Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('company_name', __('messages.setting_menu.company_name').':') }}<span class="required">*</span>
        {{ Form::text('company_name', $settings['company_name'], ['class' => 'form-control', 'required']) }}
    </div>

    <!-- company Email Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('company_email', __('messages.setting_menu.company_email').':') }}<span class="required">*</span>
        {{ Form::email('company_email', $settings['company_email'], ['class' => 'form-control', 'required']) }}
    </div>

    <!-- company Phone Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('company_phone', __('messages.setting_menu.company_phone').':') }}<span
                class="required">*</span><br>
        {{ Form::text('company_phone', $settings['company_phone'], ['class' => 'form-control','id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','required']) }}
    </div>

    <!-- company working_days_of_month Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('working_days_of_month', __('messages.setting_menu.working_days_of_month').':') }}<span
                class="required">*</span><br>
        {{ Form::number('working_days_of_month', $settings['working_days_of_month'], ['class' => 'form-control', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','required','min' => '1', 'max' => '31', 'id' => 'workingDays']) }}
    </div>

    <!-- company working_hours_of_day Field -->
    <div class="form-group col-sm-4">
        {{ Form::label('working_hours_of_day', __('messages.setting_menu.working_hours_of_day').':') }}<span
                class="required">*</span><br>
        {{ Form::number('working_hours_of_day', $settings['working_hours_of_day'], ['class' => 'form-control', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','required', 'min' => '1','max' => '24', 'id' => 'workingHours']) }}
    </div>

    <!-- Address Field -->
    <div class="form-group col-sm-12">
        {{ Form::label('company_address', __('messages.setting_menu.address').':') }}<span class="required">*</span>
        {{ Form::textarea('company_address', $settings['company_address'], ['id' => 'companyAddress', 'class' => 'form-control height-auto', 'rows' => 4, 'required']) }}
    </div>

</div>
<div class="row">
    <!-- App Logo Field -->
    <div class="form-group col-sm-4">
        <div class="row">
            <div class="col-md-12 col-sm-6 form-group d-flex align-items-center">
                <div class="d-flex flex-column">
                {{ Form::label('app_logo', __('messages.setting_menu.app_logo').':') }}
                <label class="image__file-upload btn btn-primary text-color-white"> {{ __('messages.setting_menu.choose') }}
                    {{ Form::file('app_logo',['id'=>'appLogo','class' => 'd-none']) }}
                </label>
                </div>
                <img id='previewImage' class="img-thumbnail thumbnail-preview ml-4"
                     src="{{($settings['app_logo']) ? asset($settings['app_logo']) : asset('assets/img/default_image.jpg')}}"/>
            </div>
        </div>
    </div>

    <div class="col-sm-4 form-group ">
        <div class="row">
            <div class="col-sm-12 col-md-12 form-group">
                {{ Form::label('favicon', __('messages.setting_menu.favicon').':') }}
                <br>
                <label
                        class="image__file-upload btn btn-primary text-color-white"> {{ __('messages.setting_menu.choose') }}
                    {{ Form::file('favicon',['id'=>'favicon','class' => 'd-none']) }}
                </label>
                <img id='faviconPreview' class="img-thumbnail thumbnail-preview favicon-preview ml-4" width="70"
                     src="{{($settings['favicon']) ? asset($settings['favicon']) : asset('assets/img/infyom-logo.png')}}">
            </div>
        </div>
    </div>
    <div class="col-sm-4 form-group ">
        <div class="row">
            
            <div class="col-sm-12 col-md-12 form-group d-flex flex-column">
                {{ Form::label('favicon', __('messages.setting_menu.task_status').':') }}
                <select name="default_task_status" class="form-control select2" placeholder="Select Task Status">
                    @foreach($taskStatus as $statusItem)
                    <option value="{{$statusItem->status}}"  @if($settings['default_task_status'] == $statusItem->status) selected @endif>{{$statusItem->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
{{--    <div class="col-sm-2 form-group ">--}}
{{--        <div class="row">--}}
{{--            <div class="col-sm-12 col-md-12 form-group">--}}
{{--                {{ Form::label('version', __('messages.setting_menu.current_version').':') }}--}}
{{--                <br>--}}
{{--                <span>{{ $currentVersion }}</span>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
<div class="clearfix"></div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary save-btn', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        {{ Form::reset(__('messages.common.cancel'), ['class' => 'btn btn-light ml-1']) }}
    </div>
</div>
