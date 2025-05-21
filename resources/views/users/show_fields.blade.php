<div class="card-body">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.user.name').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ html_entity_decode($user->name) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.user.phone').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ !empty($user->phone)?$user->phone:'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.user.email').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.user.salary').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ !empty($user->salary)?number_format($user->salary,2):'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name',__('messages.user.role').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ html_entity_decode($user->roleNames) }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('open_tasks', __('messages.task.pending').' '.__('messages.tasks').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        <p>{{ $user->userActiveTask->count() }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.invoice.status').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ ($user->is_active)?__('messages.user.active'):__('messages.user.deactive') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('created_at', __('messages.common.created_on').(':'),['class'=>'font-weight-bold']) }}
                        <br>
                        <span data-toggle="tooltip" data-placement="right"
                              title="{{ date('jS M, Y', strtotime($user->created_at)) }}">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('created_at', __('messages.common.last_updated').(':'),['class'=>'font-weight-bold']) }}
                        <br>
                        <span data-toggle="tooltip" data-placement="right"
                              title="{{ date('jS M, Y', strtotime($user->updated_at)) }}">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('attachment', __('messages.user.profile').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        @if(!empty($user->img_avatar))
                            <img id='showImage' class="img-thumbnail thumbnail-preview" src="{{$user->img_avatar}}">
                        @else
                            {{'N/A'}}
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.user.project').':', ['class' => 'font-weight-bold']) }}<br>
                        @forelse($user->projects->pluck('name') as $project)
                            {{$loop->first ? '':', '}}
                            <span>{{html_entity_decode($project)}}</span>
                        @empty
                            <span>{{ __('messages.common.n/a') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
