<div id="notificationModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.notification.notification_setting') }}
                    <span data-toggle="tooltip" data-placement="bottom"
                          title="{{__('messages.notification.notification_text')}}"><i
                                class="fas fa-question-circle"></i></span>
                </h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'notificationForm','method'=>'post']) }}
            @csrf
            <div class="modal-body">
                <div class="alert alert-danger display-none" id="editProfileValidationErrorsBox"></div>
                {{ Form::hidden('user_id',null,['id'=>'pfUserId']) }}
                {{ Form::hidden('is_active',1) }}
                {{csrf_field()}}
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>{{__('messages.notification.select_first_notification_time').':'}}<span class="required ml-1">*</span></label>
                        {{ Form::text('firstTime', null, ['id'=>'firstTimeHour','class' => 'form-control firstHour notificationPicker1','required', 'autofocus', 'tabindex' => "0"]) }}
                    </div>
                    <div class="form-group col-sm-4">
                        <label>{{ __('messages.notification.select_second_notification_time').':'}}<span class="required ml-1">*</span></label>
                        {{ Form::text('secondTime', null, ['id'=>'secondTimeHour','class' => 'form-control notificationPicker2 secondHour','required', 'autofocus', 'tabindex' => "1"]) }}
                    </div>
                    <div class="form-group col-sm-4">
                        <label>{{__('messages.notification.select_third_notification_time').':'}}<span class="required ml-1">*</span></label>
                        {{ Form::text('thirdTime', null, ['id'=>'thirdTimeHour','class' => 'form-control thirdHour notificationPicker3','required', 'autofocus', 'tabindex' => "2"]) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'notificationBtn','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing...", 'tabindex' => "5"]) }}
                    <button type="button" class="btn btn-light ml-1 edit-cancel-margin margin-left-5" data-dismiss="modal">{{ __('messages.common.cancel') }}
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

