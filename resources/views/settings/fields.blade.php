<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="{{ route('settings.edit', ['group' => 'general']) }}"
                           class="nav-link {{ $groupName == 'general'? 'active' : ''}}">
                            {{ __('messages.setting_menu.general') }}
                        </a>
                        <a href="{{ route('settings.edit', ['group' => 'invoice_template']) }}"
                           class="nav-link tabText {{ $groupName == 'invoice_template'? 'active' : ''}}">
                            {{ __('messages.setting_menu.invoice_template') }}
                        </a>
                        <a href="{{ route('settings.edit', ['group' => 'google_recaptcha']) }}"
                           class="nav-link tabText {{ $groupName == 'google_recaptcha'? 'active' : ''}}">
                            {{ __('messages.setting_menu.google_recaptcha') }}
                        </a>

                    </li>
                </ul>
                @if(!empty($settings['default_invoice_template']))
                    {{ Form::open(['route' => ['invoice-settings.settings'], 'method' => 'post','id' => 'invoiceSetting','class' => 'invoice-settings']) }}
                    <input type="hidden" value="{{ \App\Models\Setting::INVOICE_TEMPLATE }}">
                    <div class="form-group mt-4">
                        {{ Form::label('invoice_template',__('messages.setting_menu.invoice_template').':') }}
                        <br>
                        {{ Form::select('default_invoice_template',$invoiceTemplateArray,$settings['default_invoice_template'],['class' => 'form-control','id' => 'invoiceTemplateId']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('invoice_color',__('messages.setting_menu.color'). ':') }}
                        <div class="color-wrapper"></div>
                        {{ Form::text('default_invoice_color', $settings['default_invoice_color'],['id' => 'invoiceColor', 'hidden', 'class' => 'form-control']) }}
                    </div>
                    {{ Form::button(__('messages.common.save'),['type' => 'submit','id' => 'btnSave', 'class' => 'btn btn-primary save-btn-invoice', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    {{ Form::close() }}
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-9 layout-responsive">
        @if($groupName == 'google_recaptcha')
            {{ Form::open(['route' => ['google-recaptcha.settings'], 'method' => 'post', 'files' => true, 'id' => 'createSetting', 'class' => 'settings']) }}
        @else
            {{ Form::open(['route' => ['settings.update'], 'method' => 'post', 'files' => true, 'id' => 'createSetting', 'class' => 'settings']) }}
        @endif
        @include("settings.$groupName")
        {{ Form::close() }}
    </div>
</div>
