<div class="modal fade" tabindex="-1" role="dialog" id="showModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.client.client_details') }}</h5>
                <button type="button" class="close outline-none" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="row details-page">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.client.name').':', ['class' => 'font-weight-bold']) }}<br>
                        <span id="showName"></span>
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.client.department').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        <span id="showDepartment"></span>
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('email', __('messages.client.email').':', ['class' => 'font-weight-bold']) }}<br>
                        <span id="showEmail"></span>
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('website', __('messages.client.website').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        <span id="showWebsite"></span>
                    </div>
                        <div class="form-group col-sm-6 pb-0">
                            {{ Form::label('',__('messages.project.finished').' '.__('messages.projects'). ':' , ['class' => 'font-weight-bold']) }}<br>
                            <span class="progressProjectFinished"></span>
                            <div class="progress height-3px width-60">
                                <div class="progress-bar bg-primary progress-bar-striped"   role="progressbar"   aria-valuemin="0" aria-valuemax="100" id="progressProjectFinished">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group col-sm-6 pb-0">
                            {{ Form::label('',__('messages.project.on_going').' '.__('messages.projects'). ':' , ['class' => 'font-weight-bold']) }}<br>
                            <span class="progressProjectInProgress"></span>
                            <div class="progress height-3px width-60">
                                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" id="progressProjectInProgress">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 pb-0">
                            {{ Form::label('',__('messages.project.on_hold').' '.__('messages.projects'). ':' , ['class' => 'font-weight-bold']) }}
                            <br>
                            <span class="progressProjectHold"></span>
                            <div class="progress height-3px width-60">
                                <div class="progress-bar bg-danger progress-bar-striped" role="progressbar"  aria-valuemin="0" aria-valuemax="100" id="progressProjectHold">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 pb-0">
                            {{ Form::label('',__('messages.project.archived').' '.__('messages.projects'). ':' , ['class' => 'font-weight-bold']) }}<br>
                            <span class="progressProjectArchived"></span>
                            <div class="progress height-3px width-60">
                                <div class="progress-bar bg-info progress-bar-striped"   role="progressbar"   aria-valuemin="0" aria-valuemax="100" id="progressProjectArchived">
                                </div>
                            </div>
                        </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('customer_image', __('messages.client.image').':',['class' => 'font-weight-bold'] )}}<br>
                        <img id='showImage' class="img-thumbnail thumbnail-preview"
                             src="{{asset('assets/img/user-avatar.png')}}"/>
                        <label id="noImage">{{__('messages.common.n/a')}}</label>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
