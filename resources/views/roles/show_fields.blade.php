<div class="card-body">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.role.name').':', ['class' => 'font-weight-bold']) }}
                        <p>{{ html_entity_decode($role->name) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('created_at', __('messages.common.created_on').(':'),['class'=>'font-weight-bold']) }}
                        <br>
                        <span data-toggle="tooltip" data-placement="right"
                              title="{{ date('jS M, Y', strtotime($role->created_at)) }}">{{ $role->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('created_at', __('messages.common.last_updated').(':'),['class'=>'font-weight-bold']) }}
                        <br>
                        <span data-toggle="tooltip" data-placement="right"
                              title="{{ date('jS M, Y', strtotime($role->updated_at)) }}">{{ $role->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if(!$role->permissions()->where('name','=','role_client')->exists())
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.role.permissions').':', ['class' => 'font-weight-bold']) }}
                        <br>
                        @forelse($permissionLists as $permissionList)
                            <span>{{ $permissionList}}<br></span>
                        @empty
                            <span>{{  __('messages.common.n/a') }}</span>
                        @endforelse
                    </div>
                </div>
                @endif
                <div class="col-md-8">
                    <div class="form-group">
                        {{ Form::label('name', __('messages.role.description').':', ['class' => 'font-weight-bold']) }}
                        <br>{!! !empty($role->description) ? html_entity_decode($role->description) : 'N/A' !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
