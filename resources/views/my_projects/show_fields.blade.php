<div class="card-body">
    <ul class="nav nav-tabs mb-2" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="projectSummary" data-toggle="tab" href="#summery"
               role="tab" aria-selected="true">{{ __('messages.project.summary') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="projectActivity" data-toggle="tab" href="#activity"
               role="tab" aria-selected="false">{{__('messages.project.activity')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="projectTasks" data-toggle="tab" href="#tasks"
               role="tab" aria-selected="false">{{ __('messages.tasks') }}</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="summery" role="tabpanel" aria-labelledby="projectSummary">
            <div class="row project-details-card">
                <div class="col-md-8">
                    @php
                        $inStyle = 'style';
                        $style = 'border-top: 3px solid';
                    @endphp
                    <div class="card project-details" {{$inStyle}}="{{$style}} {{ $project->color }}">
                    <div class="card-header">
                        <h4 class="text-dark pr-1">{{$project->name}}</h4>
                        <span class="pr-3">({{$project->prefix}})</span>
                        <span class="badge {{ \App\Models\Project::STATUS_BADGE[$project->status] }} text-uppercase">
                            {{\App\Models\Project::STATUS[$project->status]}}
                         </span>
                    </div>
                    <hr>
                    <div class="card-body pt-1">
                        <label class="mb-2 font-weight-bold">{{__('messages.project.project_overview')}} : </label>
                        <div class="project-description">
                            {!! (!empty($project->description)) ? html_entity_decode($project->description) : __('messages.common.n/a') !!}
                        </div>
                    </div>
                    <hr>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-bold">{{__('messages.project.project_progress')}}:</label><br>
                                <div class="myProgress">
                                    <div class="bar-overflow" role="progressbar" aria-valuenow="70"
                                         aria-valuemin="0" aria-valuemax="100">
                                        <div class="bar"></div>
                                    </div>
                                    <span class="ml-2">{{$project->projectProgress()}}</span>%
                                </div>
                            </div>
                            <div class=" col-md-4">
                                {{ Form::label('created_at', __('messages.common.created_on').(':'),['class' => 'font-weight-bold']) }}
                                <br>
                                <p><span data-toggle="tooltip" data-placement="right"
                                         title="{{ date('jS M, Y', strtotime($project->created_at)) }}">{{ $project->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                {{ Form::label('updated_on', __('messages.common.last_updated').(':'),['class' => 'font-weight-bold']) }}
                                <br>
                                <p><span data-toggle="tooltip" data-placement="right"
                                         title="{{ date('jS M, Y', strtotime($project->updated_at)) }}">{{ $project->updated_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card user-card" {{$inStyle}}="{{$style}} {{ $project->color }}">
                        <div class="card-header">
                            <h4>{{__('messages.task.project')}} {{__('messages.project.members')}}
                                ({{count($project->users)}})</h4>
                        </div>
                        <div class="card-body">
                            <ul class="pl-1 users-list">
                                @foreach($project->users->where('is_active','=',true) as  $user)
                                    <li class="d-flex mb-4">
                                        <img src="{{$user->img_avatar}}" class="mr-3 clientProjectDetailsUserAvatar"/>
                                        <div>
                                            <h6>{{html_entity_decode($user->name)}}</h6>
                                            <span class="text-muted">{{$user->email}}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card client-card" {{$inStyle}}="{{$style}} {{ $project->color }}">
                    <div class="card-header">
                        <h4>{{ __('messages.project.client')}}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="pl-1 users-list">
                            <li class="d-flex mb-4">
                                <img src="{{$project->client->avatar}}" class="mr-3 clientProjectDetailsUserAvatar"/>
                                <div>
                                    <h6>{{html_entity_decode($project->client->name)}}</h6>
                                    <span class="text-muted">{{$project->client->email}}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-statistic-2 project-details-box">
                    <div class="card-icon shadow-primary bg-pending-tasks">
                        <i class="fa fa-tasks text-white"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header pt-3">
                            <label>{{__('messages.tasks')}}</label>
                        </div>
                        <div class="card-body">
                            {{count($project->openTasks)}}/{{count($project->tasks)}}
                            <span>{{__('messages.task.pending')}} {{__('messages.tasks')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
<div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="projectActivity">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-0">
                <div class="card-body pl-0 pt-0 pb-0">
                    <div class="card">
                        <div class="card-body project-activities">
                            <div class="col-sm-12">
                                @foreach($data['activities'] as $activity)
                                    <div class="row">
                                        <div class="col-sm-1 form-group pr-0 mb-2 icon">
                                            <i class="fa fa-tasks text-primary font-size-20px"></i>
                                        </div>
                                        <div class="col-sm-11 form-group pl-0 mb-2">
                                            <span class="font-size-15px">{{$activity->log_name}}</span>
                                            <span class="float-right"><small
                                                        class="font-size-12px">{{$activity->created_at->diffForHumans()}}</small></span><br>
                                            <label class="font-size-14px">{{$activity->createdBy->name}} {{ html_entity_decode($activity->description) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="projectTasks">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <input hidden id="projectId" value="{{$project->id}}">
                <div>
                    <a href="#" data-id="{{$project->id}}" class="btn btn-primary filter-container__btn float-right" id="addTask">{{ __('messages.task.new_task') }}</a>
                </div>
                <div class="card-body pt-0">
                    @livewire('project-details-tasks',['projectId' => $project->id])
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
