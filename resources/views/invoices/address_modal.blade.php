<div id="addModal" class="modal fade address-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <div class="row" id="addressForm">
                    <div class="form-group col-sm-12">
                        {{ Form::label('street',__('messages.address.street').':') }}
                        {{ Form::textarea('street[]', isset($addresses[0]->street) ? nl2br(e($addresses[0]->street)) : null, ['class' => 'form-control street']) }}
                    </div>
                    <div class="form-group col-sm-12 col-lg-6">
                        {{ Form::label('city',__('messages.address.city').':') }}
                        {{ Form::text('city[]', isset($addresses[0]->city) ? $addresses[0]->city : null, ['class' => 'form-control city']) }}
                    </div>
                    <div class="form-group col-sm-12 col-lg-6">
                        {{ Form::label('state',__('messages.address.state').':') }}
                        {{ Form::text('state[]', isset($addresses[0]->state) ? $addresses[0]->state : null, ['class' => 'form-control state']) }}
                    </div>
                    <div class="form-group col-sm-12 col-lg-6">
                        {{ Form::label('zip_code',__('messages.address.zip_code').':') }}
                        {{ Form::text('zip_code[]', isset($addresses[0]->zip_code) ? $addresses[0]->zip_code : null, ['class' => 'form-control zip-code']) }}
                    </div>
                    <div class="form-group col-sm-12 col-lg-6">
                        {{ Form::label('country',__('messages.address.country').':') }}
                        {{ Form::text('country[]', isset($addresses[0]->country) ? $addresses[0]->country : null, ['class' => 'form-control country']) }}
                    </div>
                </div>
                <hr>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="shippingAddressEnable">
                    <label class="custom-control-label"
                           for="shippingAddressEnable">{{ __('messages.invoice.add_shipping_address') }}</label>
                </div>
                <br>
                <div id="shippingAddressForm" class="d-none">
                    <div class="row" id="addressForm">
                        <div class="form-group col-sm-12">
                            {{ Form::label('street',__('messages.address.street').':') }}
                            {{ Form::textarea('street[]', isset($addresses[1]->street) ? nl2br(e($addresses[1]->street)) : null, ['class' => 'form-control street']) }}
                        </div>
                        <div class="form-group col-sm-12 col-lg-6">
                            {{ Form::label('city',__('messages.address.city').':') }}
                            {{ Form::text('city[]', isset($addresses[1]->city) ? $addresses[1]->city : null, ['class' => 'form-control city']) }}
                        </div>
                        <div class="form-group col-sm-12 col-lg-6">
                            {{ Form::label('state',__('messages.address.state').':') }}
                            {{ Form::text('state[]', isset($addresses[1]->state) ? $addresses[1]->state : null, ['class' => 'form-control state']) }}
                        </div>
                        <div class="form-group col-sm-12 col-lg-6">
                            {{ Form::label('zip_code',__('messages.address.zip_code').':') }}
                            {{ Form::text('zip_code[]', isset($addresses[1]->zip_code) ? $addresses[1]->zip_code : null, ['class' => 'form-control zip-code']) }}
                        </div>
                        <div class="form-group col-sm-12 col-lg-6">
                            {{ Form::label('country',__('messages.address.country').':') }}
                            {{ Form::text('country[]', isset($addresses[1]->country) ? $addresses[1]->country : null, ['class' => 'form-control country']) }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" id="btnCancel" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
